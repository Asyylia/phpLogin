<?php
session_start();
session_unset();
session_destroy();

// Suppression cookie "remember"
setcookie("remember_token", "", time(), "/");

header("Location: login.php");
exit;