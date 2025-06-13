# Prérequis :

- Installer Xampp, activer "apache" et "mySQL"
- Ouvrir VisualStudioCode, et créer des fichiers en .php

# Création de la base de données

- Se connecter sur le localhost correspondant à son port apache, exemple : localhost:8080/phpmyadmin
- Créer la base de données phplogin dans localhost:8080/phpmyadmin
- Créer la table de données 'users' grâce au morceau de code présent dans insertCommand.sql ( id, username, email, password, created_at)

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

```

- Créer la table de données "remember_me" grâce au morceau de code présent dans insertCommand.sql (id, user_id, selector, hashed_validator, expires)

```
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector CHAR(12) NOT NULL,
    hashed_validator CHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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

- Il contient de l'html afin de créer l'interface "connecté".
- Il contient également le système de token "remember_me" afin d'être connecté automatiquement en ouvrant la page depuis le navigateur

```
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
```

- Ici, 'htmlspecialchars' convertit les caractères spéciaux en entités HTML lors de l'injection du nom de l'utilisateur connecté. C'est une sécurité :

```
<body>
    <div>
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</h2>
        <p>Ceci est votre page d'accueil</p>
        <a href="logout.php">Se déconnecter</a>
    </div>
</body>
```

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

- Ensuite le morceau de code ci-dessous permet de créer une variable qui stocke un token converti en données binaires avec représentations héxadécimales, ça permets de conserver des données, comme des cookies, de façon sécurisée, afin de garder l'utilisateur connecté lorsque l'on coche "se souvenir de moi" par exemple.

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

# Création du système de sécurité CSRF

Il permet de sécuriser les données comme les connexions automatiques, les connexions et les inscriptions dans des tokens cryptés générés au moment de ces actions.

## Créer csrf.php

- Nous créeons les lignes de codes qui générent les tokens, et les vérifient.

```
<?php
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
```

- Insertion de ces lignes de codes dans : 'register.php', afin de générer le token à l'inscription.

```
        require 'csrf.php';
        $token = generateCsrfToken();
```
