<?php
require 'db.php';
session_start();


if (isset($_COOKIE['remember_me'])) { // si la clé "remember_me" du cookie est présente
    list($selector, $validator) = explode(':', $_COOKIE['remember_me']); // fabrique une liste à partir du token CRSF
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE selector = ?");
    $stmt->execute([$selector]);


    // Changer en négatif la durée de vie de la clé du cookie 
    setcookie("remember_token", "", time() - 3600, "/");
}


session_unset();
session_destroy();

// Redirection vers la page de connexion
header("Location: login.php");
exit;
