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

if (!$student_id) {
    echo json_encode([
        'error' => 'Student ID not found. Please contact admin.',
        'stats' => [
            'borrowedBooks' => 0,
            'overdueBooks' => 0,
            'totalAvailableBooks' => 0,
            'totalTransactions' => 0
        ],
        'currentBooks' => []
    ]);
    exit;
}

try {
    $stats = [];
    
    // Get borrowed books count - FIXED QUERY
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE student_id = ? AND status = 'issued'");
    $stmt->execute([$student_id]);
    $stats['borrowedBooks'] = $stmt->fetch()['count'] ?? 0;
    
    // Get overdue books count - FIXED QUERY
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE student_id = ? AND status = 'issued' AND due_date < CURDATE()");
    $stmt->execute([$student_id]);
    $stats['overdueBooks'] = $stmt->fetch()['count'] ?? 0;
    
    // Get total available books in library
    $stmt = $pdo->query("SELECT SUM(available) as total FROM books");
    $stats['totalAvailableBooks'] = $stmt->fetch()['total'] ?? 0;
    
    // Get total transactions for this student - FIXED QUERY
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $stats['totalTransactions'] = $stmt->fetch()['count'] ?? 0;
    
    // Get current borrowed books - FIXED QUERY
    $stmt = $pdo->prepare("
        SELECT b.title, b.author, t.issue_date, t.due_date, t.status, t.fine
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        WHERE t.student_id = ? AND t.status = 'issued'
        ORDER BY t.issue_date DESC
    ");
    $stmt->execute([$student_id]);
    $currentBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate fines for overdue books
    foreach ($currentBooks as &$book) {
        $due_date = new DateTime($book['due_date']);
        $today = new DateTime();
        if ($today > $due_date && $book['status'] === 'issued') {
            $diff = $today->diff($due_date)->days;
            $book['fine'] = $diff * 5; // ₹5 per day
        } else {
            $book['fine'] = $book['fine'] ?? 0;
        }
    }
    
    echo json_encode([
        'stats' => $stats,
        'currentBooks' => $currentBooks
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'stats' => [
            'borrowedBooks' => 0,
            'overdueBooks' => 0,
            'totalAvailableBooks' => 0,
            'totalTransactions' => 0
        ],
        'currentBooks' => []
    ]);
}
?>