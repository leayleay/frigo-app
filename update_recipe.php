<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Méthode non autorisée']); exit;
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'Non connecté']); exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id       = $data['id']        ?? 0;
$name     = trim($data['name'] ?? '');
$ingArray = $data['ingredients'] ?? [];

if (!$id || !$name || !$ingArray) {
    echo json_encode(['success'=>false,'message'=>'Champs manquants']); exit;
}

$js = json_encode($ingArray, JSON_UNESCAPED_UNICODE);

try {
    $sql = 'UPDATE recipes
            SET name = ?, ingredients_js = ?
            WHERE id = ? AND user_id = ?';
    $stmt = $pdo->prepare($sql);
    $ok   = $stmt->execute([$name, $js, $id, $_SESSION['user_id']]);

    echo json_encode(['success'=>$ok, 'message'=>$ok?'Recette mise à jour':'Erreur update']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Erreur serveur']);
}
