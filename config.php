<?php
// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    // Session settings for security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Session lifetime (8 hours)
    ini_set('session.gc_maxlifetime', 28800);
    session_set_cookie_params(28800);
    
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}
$host = 'localhost';
$username = 'Libuser';
$password = 'Library@#321';
$database = 'library_management_pro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Don't expose database errors in production
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed");
}
?>