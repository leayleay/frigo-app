<?php
// get_recipe.php
session_start();
require_once 'db.php';          // → $pdo (connexion PDO)

header('Content-Type: application/json');

/* -------------------------------------------------------------
   1. Vérifier que l’utilisateur est connecté
------------------------------------------------------------- */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);       // invité : aucune recette
    exit;
}

$user_id = $_SESSION['user_id'];

/* -------------------------------------------------------------
   2. Récupérer les recettes du user
------------------------------------------------------------- */
try {
    $sql = 'SELECT id, name, ingredients_js
            FROM recipes
            WHERE user_id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ---------------------------------------------------------
       3. Convertir le champ JSON → tableau PHP
       --------------------------------------------------------- */
    foreach ($recipes as &$rec) {
        $rec['ingredients'] = json_decode($rec['ingredients_js'], true);
        unset($rec['ingredients_js']);           
    }

    echo json_encode($recipes);

} catch (PDOException $e) {
    error_log($e->getMessage());                
    http_response_code(500);
    echo json_encode(['error' => 'server']);
}
