<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Set JSON header first
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated', 'session_expired' => true]);
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
            SELECT t.*, b.title as book_title, b.author, 
                   s.name as student_name, s.student_id
            FROM transactions t 
            JOIN books b ON t.book_id = b.id 
            JOIN students s ON t.student_id = s.student_id
            ORDER BY t.created_at DESC
        ");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($transactions);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        $book_id = $data['book_id'];
        $student_id = $data['student_id'];
        $issue_date = $data['issue_date'];
        $due_date = $data['due_date'];
        
        // Check if book is available
        $stmt = $pdo->prepare("SELECT available, quantity, title FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();
        
        if (!$book) {
            throw new Exception('Book not found');
        }
        
        if ($book['available'] <= 0) {
            throw new Exception('Book "' . $book['title'] . '" is not available for issuing');
        }
        
        // Check if student exists
        $stmt = $pdo->prepare("SELECT student_id, name FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();
        
        if (!$student) {
            throw new Exception('Student with ID "' . $student_id . '" not found');
        }
        
        // Check if student already has this book issued
        $stmt = $pdo->prepare("SELECT id FROM transactions WHERE book_id = ? AND student_id = ? AND status = 'issued'");
        $stmt->execute([$book_id, $student_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            throw new Exception('Student already has this book issued');
        }
        
        // Create transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (book_id, student_id, issue_date, due_date, status) VALUES (?, ?, ?, ?, 'issued')");
        $stmt->execute([$book_id, $student_id, $issue_date, $due_date]);
        
        // Update book availability
        $stmt = $pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
        $stmt->execute([$book_id]);
        
        // Update student's borrowed books count
        $stmt = $pdo->prepare("UPDATE students SET books_borrowed = books_borrowed + 1 WHERE student_id = ?");
        $stmt->execute([$student_id]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'transaction_id' => $pdo->lastInsertId(),
            'message' => 'Book issued successfully to ' . $student['name']
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Transaction ID required']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        $transaction_id = $data['id'];
        
        // Get transaction details first
        $stmt = $pdo->prepare("SELECT book_id, student_id, due_date FROM transactions WHERE id = ? AND status = 'issued'");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch();
        
        if (!$transaction) {
            throw new Exception('Transaction not found or already returned');
        }
        
        $book_id = $transaction['book_id'];
        $student_id = $transaction['student_id'];
        $due_date = $transaction['due_date'];
        $return_date = date('Y-m-d');
        
        // Calculate fine
        $fine = 0;
        $return_timestamp = strtotime($return_date);
        $due_timestamp = strtotime($due_date);
        
        if ($return_timestamp > $due_timestamp) {
            $diff_days = ceil(($return_timestamp - $due_timestamp) / (60 * 60 * 24));
            $fine = $diff_days * 5; // ₹5 per day
        }
        
        // Update transaction with return date and fine
        $stmt = $pdo->prepare("UPDATE transactions SET return_date = ?, status = 'returned', fine = ? WHERE id = ?");
        $stmt->execute([$return_date, $fine, $transaction_id]);
        
        // Update book availability
        $stmt = $pdo->prepare("UPDATE books SET available = available + 1 WHERE id = ?");
        $stmt->execute([$book_id]);
        
        // Update student's borrowed books count
        $stmt = $pdo->prepare("UPDATE students SET books_borrowed = books_borrowed - 1 WHERE student_id = ?");
        $stmt->execute([$student_id]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'fine' => $fine,
            'message' => 'Book returned successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Method not allowed
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>