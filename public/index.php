<?php
session_start();
require_once '../includes/auth.php';
auto_login();

header('Location: dashboard.php');
exit;