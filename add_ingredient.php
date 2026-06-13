<?php
/* -------------------------------------------------------------
   add_ingredient.php – Ajout via JSON
   Requête : POST + JSON
   {
     "name":"Tomate",
     "quantity":3,
     "expiry_date":"2025-07-18",
     "category":"Légume"
   }
------------------------------------------------------------- */

require_once 'db.php';               // → $pdo + session_start()

header('Content-Type: application/json');

/* -------- Vérifier la session -------- */
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

/* -------- Lire et valider le JSON -------- */
$data = json_decode(file_get_contents('php://input'), true);

$name     = trim($data['name']       ?? '');
$qty      = intval($data['quantity'] ?? 0);
$expiry   = $data['expiry_date']     ?? '';
$category = trim($data['category']   ?? '');

if (!$name || !$qty || !$expiry || !$category) {
    echo json_encode(['success' => false, 'message' => 'Champs manquants']);
    exit;
}

/* -------- Insérer -------- */
try {
    $sql  = 'INSERT INTO ingredients (user_id, name, quantity, expiry_date, category)
             VALUES (?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $name, $qty, $expiry, $category]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());           // log interne
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
