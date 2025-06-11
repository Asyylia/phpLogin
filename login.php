<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Connexion</title>
</head>

<body>
    <div id="carreconnexion">
        <h1>Connexion</h1>
        <form method="POST" action="process_login.php">
            <input class="form" type="email" name="email" placeholder="Adresse email" required><br>

            <input class="form" type="password" name="password" placeholder="Mot de passe" required><br>

            <div class="checkk"><label class="check"><input type="checkbox" class="check" name="remember_me"> Se
                    souvenir de
                    moi</label><br></div>
            <button class="btn" type="submit">Me connecter</button>
        </form>
    </div>
</body>

</html>