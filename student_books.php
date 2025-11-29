<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Set JSON header first
header('Content-Type: application/json');

if (!isLoggedIn() || !isStudent()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated or student access required']);
    exit;
}

$student_id = $_SESSION['student_id'];

if (isset($_GET['available']) && $_GET['available'] == 1) {
    // Get available books
    $stmt = $pdo->query("SELECT * FROM books WHERE available > 0");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
} else {
    // Get student's books using student_id directly from transactions table
    $stmt = $pdo->prepare("
        SELECT b.*, t.issue_date, t.due_date, t.return_date, t.status, t.fine 
        FROM transactions t 
        JOIN books b ON t.book_id = b.id 
        WHERE t.student_id = ?
        ORDER BY t.issue_date DESC
    ");
    $stmt->execute([$student_id]);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}
?>