<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Inscription</title>
</head>

<body>
    <div id="carreinscription">
        <h1>Inscription</h1>
        <form method="POST" action="process_register.php">
            <label for="name">Nom d'utilisateur</label><br>
            <input class="form" type="text" name="username" required><br>
            <label for="email">Email</label><br>
            <input class="form" type="email" name="email" required><br>
            <label for="password">Mot de passe</label><br>
            <input class="form" type="password" name="password" required><br>
            <button class="btn" type="submit">M'enregistrer</button>
        </form>
    </div>

</body>

</html>