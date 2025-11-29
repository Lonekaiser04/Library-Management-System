<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated', 'session_expired' => true]);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

function requireAdmin() {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated', 'session_expired' => true]);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
    
    if (!isAdmin()) {
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

function requireStudent() {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated', 'session_expired' => true]);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
    
    if (!isStudent()) {
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Student access required']);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function getStudentId() {
    return $_SESSION['student_id'] ?? null;
}

function getAdminId() {
    return $_SESSION['admin_id'] ?? null;
}

function hasTempPassword() {
    return $_SESSION['temp_password'] ?? false;
}

function logout() {
    $_SESSION = array();
    
    // If it's desired to kill the session, also delete the session cookie.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header('Location: login.php');
    exit;
}
// Helper function to check if it's an API request
function isApiRequest() {
    // Check if it's a JSON request
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        return true;
    }
    
    // Check if it's an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }
    
    // Check if it's accessing an API file
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $apiFiles = ['books.php', 'dashboard.php', 'users.php', 'students.php', 'transactions.php', 'profile.php'];
    
    foreach ($apiFiles as $apiFile) {
        if (strpos($scriptName, $apiFile) !== false) {
            return true;
        }
    }
    
    return false;
}

// Validate and refresh session
function validateSession() {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Refresh session activity
    $_SESSION['last_activity'] = time();
    
    return true;
}
?>