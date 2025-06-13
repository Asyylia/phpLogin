<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

auto_login();
if (!user_logged_in()) {
    header('Location: auth.php');
    exit;
}

$user = current_user();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Bienvenue <?= e($user['username']) ?> ! </h1>
    <a href="logout.php">Déconnexion</a>
</body>

</html>