# Prérequis :

- Installer Xampp, activer "apache" et "mySQL"
- Ouvrir VisualStudioCode, et créer des fichiers en .php

# Création de la base de données

- Se connecter sur le localhost correspondant à son port apache, exemple : localhost:8080/phpmyadmin
- Créer la base de données grâce au morceau de code présent dans insertCommand.sql ( id, username, email, password, created_at)

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

```

# Création du système d'inscription

## Créer db.php

C'est le fichier qui permet d'entrer les informations de la base de données.

```
<?php

$host = 'localhost';
$dbname = 'phplogin';
$user = 'root';
$pass = ''; //modifie selon ton système

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}
;
```

## Créer register.php

Il contient de l'html afin de créer l'interface d'inscription.

## Créer process_register.php

- Dans ce .php, plusieurs choses sont importantes :

```
    // Crée une variable qui stocke le mot de passe et le hash avec la fonction 'password_hash'
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
```

Ce morceau de code permet de hacher le mot de passe utilisateur ( donc de le crypter pour le sécuriser )

- Ensuite, le morceau de code ci-dessous permet d'afficher un message d'erreur correspondant à l'erreur rencontrée :

```
catch (PDOException $e) { // Récupère l'erreur 1049 ( qui corresponds au 23000)
        if ($e->getCode() == 23000) {
            echo "Nom d'utilisateur ou email déjà utilisé";
        } else {
            die("Erreur : " . $e->getMessage());
        }
    }
```

** Notre système d'inscription est fonctionnel. **

# Création du système de connexion

## Créer dashboard.php

Il contient de l'html afin de créer l'interface "connecté".

## Créer login.php

Il contient de l'html afin de créer l'interface de connexion.

## Créer process_login.php

- Dans ce .php plusieurs choses sont importantes :

```
    // Si le 'user' est valide, et si le mot de passe correspond au mdp de l'input
    if ($user && password_verify($password, $user['password'])) {
        // créer la session utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
```

Ce morceau de code permet de vérifier si l'user est valide, et si le mot de passe correspond au mot de passe de l'input.

- Ensuite le morceau de code ci-dessous permet de créer une variable qui stocke un token converti en données binaires avec représentations héxadécimales, ça permets de conserver des données, comme des cookies, de façon sécurisée.

```
        // Si la superglobale n'est pas vide (donc cochée)
        if (!empty($_POST['remember_me'])) {
            // crée une variable qui stocke un token converti en données binaires avec représentations héxadécimales
            $token = bin2hex(random_bytes(16));
            // ajoute dans les cookies le token avec une date d'expiration de 30 jours
            setcookie("remember_token", $token, time() + 86400 * 30, "/", "", true, true);
        }
```

## Créer logout.php

Il permet de se déconnecter de la session, d'enlever les réglages de connexion, et de détruire la session.
