<?php
session_start();
header('Content-Type: text/plain');
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'Not set') . "\n";
echo "Session role: " . ($_SESSION['role'] ?? 'Not set') . "\n";
var_dump($_SESSION);
?>
