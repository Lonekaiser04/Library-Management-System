<?php
// index.php - Main entry point with proper routing
session_start();
require_once 'config.php';
require_once 'auth.php';

// If not logged in, redirect to login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Route based on role
if (isAdmin()) {
    header('Location: admin_dashboard.php');
} else {
    header('Location: student_dashboard.php');
}
exit;
?>