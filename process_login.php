<?php
session_start();
require 'db.php';

// Vérifie si le formulaire est de type 'POST'
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fabrique la requête, parcourt les utilisateurs et trouve par rapport à l'email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Permet de récupérer les informations et les stocker dans "user"

    // Si le 'user' est valide, et si le mot de passe correspond au mdp de l'input
    if ($user && password_verify($password, $user['password'])) {
        // Crée la session utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Si la superglobale n'est pas vide (donc cochée)
        if (!empty($_POST['remember_me'])) {
            // crée une variable qui stocke un token converti en données binaires avec représentations héxadécimales
            $token = bin2hex(random_bytes(16));
            // ajoute dans les cookies le token avec une date d'expiration de 30 jours
            setcookie("remember_token", $token, time() + 86400 * 30, "/", "", true, true);
        }

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Identifiants invalides";
    }
}
