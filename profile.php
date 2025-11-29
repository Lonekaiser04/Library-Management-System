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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    
    if ($data['action'] === 'change_password') {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($data['currentPassword'], $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            exit;
        }
        
        // Update password
        $hashed_password = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, temp_password = FALSE WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        
        echo json_encode(['success' => true]);
        
    } elseif ($data['action'] === 'update_profile') {
        // Update profile
        $stmt = $pdo->prepare("UPDATE users SET email = ?, full_name = ? WHERE id = ?");
        $stmt->execute([$data['email'], $data['full_name'], $user_id]);
        
        // Update session
        $_SESSION['email'] = $data['email'];
        $_SESSION['full_name'] = $data['full_name'];
        
        echo json_encode(['success' => true]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>