<?php
/* -------------------------------------------------------------
   get_ingredients.php – Renvoie la liste JSON des ingrédients
------------------------------------------------------------- */
require_once 'db.php';               // → $pdo + session_start()

header('Content-Type: application/json');

/* -------- Vérifier la session -------- */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);            // invité : liste vide
    exit;
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, name, quantity, expiry_date, category
         FROM ingredients
         WHERE user_id = ?'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    error_log($e->getMessage());     // log interne
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
