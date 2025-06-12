<?php
session_start();
require 'db.php';


// Si il n'y a pas d'utilisateur connecté et qu'il possède un cookie "remember_me"
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember_me']);

    $stmt = $pdo->prepare("SELECT * FROM remember_tokens WHERE selector = ? AND expires >= NOW()");
    $stmt->execute([$selector]);
    $token = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($token && hash_equals($token["hashed_validator"], hash('sha256', $validator))) {
        // ok, login
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$token["user_id"]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
        }
    }
}

// empêche l'accès au dashboard si l'utilisateur n'est pas connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tableau de bord</title>
</head>

<body>
    <div>
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</h2>
        <p>Ceci est votre page d'accueil</p>
        <a href="logout.php">Se déconnecter</a>
    </div>
</body>

</html>