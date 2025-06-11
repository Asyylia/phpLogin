<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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