<?php
session_start();
session_destroy();
setcookie("remember", "", time() - 3600, "/", "", true, true);
header('Location: auth.php');
exit;
