<?php
// login.php
session_start();
require_once 'db.php';   

/* -------------------------------------------------------------
   Toutes les réponses sont en JSON
------------------------------------------------------------- */
header('Content-Type: application/json');

/* -------------------------------------------------------------
   1) Vérifier la méthode
------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

/* -------------------------------------------------------------
   2) Récupérer & valider les champs
------------------------------------------------------------- */
$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+$/i', $email)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}


/* -------------------------------------------------------------
   3) Rechercher l’utilisateur
------------------------------------------------------------- */
try {
    $sql  = 'SELECT id, email, password_hash, role
             FROM users
             WHERE email = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* Utilisateur trouvé & mot de passe correct ? */
    if (!$user || !password_verify($pass, $user['password_hash'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Identifiants invalides'
        ]);
        exit;
    }

    /* ---------------------------------------------------------
       4) Auth OK ⇒ stocker en session
       --------------------------------------------------------- */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];   // 'admin' ou NULL

    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie'
    ]);

} catch (PDOException $e) {
    // Log interne si besoin : error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
