<?php
include 'function.php';

if (!isset($_SESSION['log'])) {
    header('Location: login.php');
    exit;
}