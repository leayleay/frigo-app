<?php
/* ------------------------------------------------------------------
   delete_ingredient.php – Suppression via JSON :
   { "id": 42 }
------------------------------------------------------------------- */

require_once 'db.php';               // → $pdo + session_start()

header('Content-Type: application/json');

/* -------- Vérifier la session -------- */
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

/* -------- Lecture et validation -------- */
$in  = json_decode(file_get_contents('php://input'), true);
$id  = intval($in['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    /* -------- Vérifier la propriété -------- */
    $chk = $pdo->prepare('SELECT id FROM ingredients WHERE id = ? AND user_id = ?');
    $chk->execute([$id, $user_id]);

    if ($chk->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Accès refusé ou ingrédient introuvable']);
        exit;
    }

    /* -------- Supprimer -------- */
    $del = $pdo->prepare('DELETE FROM ingredients WHERE id = ? AND user_id = ?');
    $del->execute([$id, $user_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
