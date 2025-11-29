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


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT u.id, u.username, u.email, u.full_name, u.role, u.student_id, u.admin_id, 
                   u.temp_password, u.created_at, s.name as student_name 
            FROM users u 
            LEFT JOIN students s ON u.student_id = s.student_id 
            ORDER BY u.created_at DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
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
        $student_id = $data['student_id'];
        $full_name = $data['full_name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $address = $data['address'] ?? '';
        $course = $data['course'] ?? '';
        $semester = $data['semester'] ?? '';
        
        // Check if student ID already exists
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        if ($stmt->fetch()) {
            throw new Exception("Student ID '$student_id' already exists");
        }
        
        // Check if email already exists in users table
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Email '$email' is already registered");
        }
        
        // Generate temporary password
        $temp_password = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 8);
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
        
        // Insert into students table
        $stmt = $pdo->prepare("
            INSERT INTO students (student_id, name, email, phone, address, course, semester, join_date, books_borrowed) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), 0)
        ");
        $stmt->execute([$student_id, $full_name, $email, $phone, $address, $course, $semester]);
        
        // Insert into users table
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, email, full_name, role, student_id, temp_password, created_at) 
            VALUES (?, ?, ?, ?, 'student', ?, TRUE, NOW())
        ");
        $stmt->execute([$student_id, $hashed_password, $email, $full_name, $student_id]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'temp_password' => $temp_password,
            'message' => 'Student account created successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];
    
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting user: ' . $e->getMessage()]);
    }
}
?>