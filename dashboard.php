<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Set JSON header FIRST
header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated', 'session_expired' => true]);
    exit;
}

// For admin-only endpoints
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}


$stats = [];

try {
    // Total books
    $stmt = $pdo->query("SELECT SUM(quantity) as total FROM books");
    $stats['totalBooks'] = $stmt->fetch()['total'] ?? 0;

    // Available books
    $stmt = $pdo->query("SELECT SUM(available) as available FROM books");
    $stats['availableBooks'] = $stmt->fetch()['available'] ?? 0;

    // Issued books
    $stmt = $pdo->query("SELECT COUNT(*) as issued FROM transactions WHERE status = 'issued'");
    $stats['issuedBooks'] = $stmt->fetch()['issued'] ?? 0;

    // Total members (students)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
    $stats['totalMembers'] = $stmt->fetch()['total'] ?? 0;

    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['totalUsers'] = $stmt->fetch()['total'] ?? 0;

    // Overdue books
    $stmt = $pdo->query("SELECT COUNT(*) as overdue FROM transactions WHERE status = 'issued' AND due_date < CURDATE()");
    $stats['overdueBooks'] = $stmt->fetch()['overdue'] ?? 0;

    echo json_encode($stats);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>