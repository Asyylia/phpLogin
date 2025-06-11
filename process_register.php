<?php
require 'db.php';


// Si le formulaire est de type "POST"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Récupère la superglobale et on cherche la clé 'username'
    $email = trim($_POST['email']); // Récupère la superglobale et on cherche la clé 'email'
    $password = trim($_POST['password']); // Récupère la superglobale et on cherche la clé 'password'

    // Vérifie si la clé 'email' est un type d'email valide (@.)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email invalide');
    }


    // Vérifie si le mot de passe est trop court
    if (strlen($password) < 6) {
        die('Mot de passe trop court (6 caractère minimum)');
    }


    // Crée une variable qui stocke le mot de passe et le hash avec la fonction 'password_hash'
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);


    // Crée une variable pour injection de requête SQL
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    try { // Essaye d'éxecuter la requête
        $stmt->execute([$username, $email, $passwordHash]);
        echo "Inscription réussie, vous pouvez vous <a href='login.php'>connecter</a>;";

    } catch (PDOException $e) { // Récupère l'erreur 1049 ( qui corresponds au 23000)
        if ($e->getCode() == 23000) {
            echo "Nom d'utilisateur ou email déjà utilisé";
        } else {
            die("Erreur : " . $e->getMessage());
        }
    }
}