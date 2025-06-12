<?php
session_start();
require 'csrf.php';
require 'db.php';

// Système de protection CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    die("Tentative CSRF détectée !");
}

function setRememberMe($user_id, $pdo)
{
    $selector = bin2hex(random_bytes(6)); // index de position (prénom)
    $validator = bin2hex(random_bytes(32)); // contenu du token (nom de famille)
    $hashed_validator = hash('sha256', $validator); // token crypté (nom de famille crypté)
    $expires = date('Y-m-d H:i:s', time() + 86400 * 30);

    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, selector, hashed_validator, expires) VALUES ( ?,?,?,?)");
    $stmt->execute([$user_id, $selector, $hashed_validator, $expires]);

    setCookie(
        'remember_me',
        "$selector:$validator",
        time() + 86400 * 30,
        "/",
        '',
        true,
        true
    );
}

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
            setRememberMe($user['id'], $pdo);
        }

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Identifiants invalides";
    }
}
