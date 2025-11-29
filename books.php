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

// Check admin access for all operations except GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM books");
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($books);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO books (title, author, category, isbn, quantity, available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['author'], $data['category'], $data['isbn'], $data['quantity'], $data['quantity']]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, isbn=? WHERE id=?");
        $stmt->execute([$data['title'], $data['author'], $data['category'], $data['isbn'], $data['id']]);
        
        echo json_encode(['success' => true]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $id = $_GET['id'];
        
        $stmt = $pdo->prepare("DELETE FROM books WHERE id=?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>