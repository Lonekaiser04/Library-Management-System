<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Set JSON header first
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT s.*, u.username, u.email as user_email 
            FROM students s 
            LEFT JOIN users u ON s.student_id = u.student_id 
            ORDER BY s.created_at DESC
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($students);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];
    
    try {
        // Check if student has borrowed books
        $stmt = $pdo->prepare("SELECT books_borrowed FROM students WHERE student_id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        
        if ($student && $student['books_borrowed'] > 0) {
            echo json_encode(['success' => false, 'error' => 'Student has borrowed books']);
        } else {
            // Delete user account first
            $stmt = $pdo->prepare("DELETE FROM users WHERE student_id = ?");
            $stmt->execute([$id]);
            
            // Then delete student
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>