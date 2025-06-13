<?php
require_once __DIR__ . '/../config/db.php';

function find_user_by_email($email) // 
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?'); // selectionne tout les utilisateurs dont les emails sont 
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function find_user_by_username($username)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?'); // selectionne tout les utilisateurs dont les pseudos sont
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_user($username, $email, $password)
{
    global $pdo;
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?,?,?)');
    return $stmt->execute([$username, $email, $hash]);
}

function login_user($user)
{
    $_SESSION['user_id'] = $user['id'];
}

function user_logged_in()
{
    return isset($_SESSION['user_id']);
}

function current_user()
{
    global $pdo;
    if (!user_logged_in())
        return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function remember_me($user_id)
{
    global $pdo;
    $selector = bin2hex(random_bytes(6));
    $validator = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 864000); // 10 jours

    $hashed_validator = hash('sha256', $validator);

    $pdo->prepare("INSERT INTO remember_tokens (user_id, selector, hashed_validator, expires) VALUES (?,?,?,?)")
        ->execute([$user_id, $selector, $hashed_validator, $expires]);

    setcookie('remember', "$selector:$validator", time() + 864000, "/", "", true, true);
}

function auto_login()
{
    global $pdo;
    if (isset($_SESSION['user_id']) || empty($_COOKIE['remember']))
        return;

    list($selector, $validator) = explode(':', $_COOKIE['remember']);
    $stmt = $pdo->prepare('SELECT * FROM remember_tokens WHERE selector = ? AND expires >= NOW()');
    $stmt->execute([$selector]);
    $token = $stmt->fetch();

    if ($token && hash_equals($token['hashed_validator'], hash('sha256', $validator))) {
        $_SESSION['user_id'] = $token['user_id'];
    }
}