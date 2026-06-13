<?php
/* ------------------------------------------------------------------
   update_ingredient.php – Mise à jour via JSON
   {
     "id": 17,
     "name": "Tomate",
     "quantity": 4,
     "expiry_date": "2025-07-20",
     "category": "Légume"
   }
------------------------------------------------------------------- */

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

$id       = intval($data['id']        ?? 0);
$name     = trim($data['name']        ?? '');
$qty      = intval($data['quantity']  ?? 0);
$expiry   = $data['expiry_date']      ?? '';
$category = trim($data['category']    ?? '');

if (!$id || !$name || !$qty || !$expiry || !$category) {
    echo json_encode(['success' => false, 'message' => 'Champs manquants ou invalides']);
    exit;
}

/* -------- Mettre à jour -------- */
try {
    $sql = 'UPDATE ingredients
            SET name = ?, quantity = ?, expiry_date = ?, category = ?
            WHERE id = ? AND user_id = ?';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $qty, $expiry, $category, $id, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());          // log interne
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
