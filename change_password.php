<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

if (!isLoggedIn() || !isset($_SESSION['temp_password']) || !$_SESSION['temp_password']) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ?, temp_password = FALSE WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            
            // Update session
            $_SESSION['temp_password'] = false;
            
            $success = 'Password changed successfully! Redirecting to dashboard...';
            header('Refresh: 2; URL=student_dashboard.php');
        } catch (PDOException $e) {
            $error = 'Error changing password: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Library Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .change-password-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 10px;
            outline: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
        }

        .btn-auth {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background: #d1eddd;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #c3e6cb;
        }

        .info-box {
            background: #e2e3e5;
            color: #383d41;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #d6d8db;
        }
    </style>
</head>
<body>
    <div class="change-password-container">
        <div class="form-header">
            <h2>Change Password</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Please set your new password.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php else: ?>
            <div class="info-box">
                <p><strong>Note:</strong> You are using a temporary password. Please set a new password for your account.</p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required 
                           placeholder="Minimum 6 characters">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn-auth">Change Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>