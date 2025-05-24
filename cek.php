<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['log'])) {
    header('Location:login.php');
    exit;
}
?>

