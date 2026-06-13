<?php
// register.php
session_start();
require_once 'db.php'; // Fichier contenant la connexion à la base

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+$/i', $email)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        exit;
    }
    

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Mot de passe trop court']);
        exit;
    }

    
    $hash = password_hash($password, PASSWORD_DEFAULT);


    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $stmt->execute([$email, $hash]);

        echo json_encode(['success' => true, 'message' => 'Compte créé avec succès']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Email déjà utilisé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
