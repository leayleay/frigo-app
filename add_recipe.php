<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['name'] ?? '');
$ingredients = $input['ingredients'] ?? [];

if (!$name || !count($ingredients)) {
    echo json_encode(['success' => false, 'message' => 'Champs manquants ou invalides.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO recipes (user_id, name, ingredients_js) VALUES (?, ?, ?)");
    $stmt->execute([
        $user_id,
        $name,
        json_encode($ingredients, JSON_UNESCAPED_UNICODE)
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'Erreur serveur lors de l\'ajout.']);
}
?>
