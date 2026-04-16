<?php
/**
 * Cultural Heritage Website - Admin Auth Check
 * Include this file at the top of every admin page (except login).
 */
require_once __DIR__ . '/../config/app.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('index.php');
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

$adminPage = basename($_SERVER['PHP_SELF']);
?>
