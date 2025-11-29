<?php
session_start();
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit;
}

$error = '';
$login_type = $_GET['type'] ?? 'student'; // Default to student login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = $_POST['login_type'];
    
    if ($login_type === 'admin') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $admin_id = trim($_POST['admin_id']);
        
        if (!empty($username) && !empty($password) && !empty($admin_id)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND admin_id = ? AND role = 'admin'");
                $stmt->execute([$username, $admin_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['admin_id'] = $user['admin_id'];
                    
                    header('Location: admin_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid admin credentials!';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Please enter all admin credentials!';
        }
    } else {
        // Student login
        $student_id = trim($_POST['student_id']);
        $password = $_POST['password'];
        
        if (!empty($student_id) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT u.*, s.name as student_name FROM users u 
                                     LEFT JOIN students s ON u.student_id = s.student_id 
                                     WHERE u.student_id = ? AND u.role = 'student'");
                $stmt->execute([$student_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'] ?: $user['student_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['temp_password'] = $user['temp_password'];
                    
                    // Redirect to password change if temporary password
                    if ($user['temp_password']) {
                        header('Location: change_password.php');
                        exit;
                    }
                    
                    header('Location: student_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid student ID or password!';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Please enter both student ID and password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
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

        .auth-container {
            display: flex;
            gap: 30px;
            max-width: 1000px;
            width: 95%;
        }

        .welcome-section {
            flex: 1;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .form-container {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .login-type-tabs {
            display: flex;
            margin-bottom: 30px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 5px;
        }

        .login-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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

        .login-form {
            display: none;
        }

        .login-form.active {
            display: block;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            
            .welcome-section {
                text-align: center;
            }

            .welcome-section h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="welcome-section">
            <h1>📚 Welcome to Library Pro</h1>
            <p>Your comprehensive library management solution. Manage books, track transactions, and streamline your library operations with ease.</p>
            <p style="margin-top: 20px;">Students can track their borrowed books while admins manage the entire library system.</p>
        </div>

        <div class="form-container">
            <div class="login-type-tabs">
                <div class="login-tab <?php echo $login_type === 'student' ? 'active' : ''; ?>" onclick="switchLoginType('student')">
                    Student Login
                </div>
                <div class="login-tab <?php echo $login_type === 'admin' ? 'active' : ''; ?>" onclick="switchLoginType('admin')">
                    Admin Login
                </div>
            </div>

            <div class="form-header">
                <h2 id="formTitle"><?php echo $login_type === 'student' ? 'Student Sign In' : 'Admin Sign In'; ?></h2>
                <p id="formSubtitle"><?php echo $login_type === 'student' ? 'Access your student account' : 'Access admin dashboard'; ?></p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Student Login Form -->
            <form method="POST" action="" id="studentForm" class="login-form <?php echo $login_type === 'student' ? 'active' : ''; ?>">
                <input type="hidden" name="login_type" value="student">
                
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" id="student_id" name="student_id" required autofocus 
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-auth">Sign In</button>
            </form>

            <!-- Admin Login Form -->
            <form method="POST" action="" id="adminForm" class="login-form <?php echo $login_type === 'admin' ? 'active' : ''; ?>">
                <input type="hidden" name="login_type" value="admin">
                
                <div class="form-group">
                    <label for="admin_id">Admin ID:</label>
                    <input type="text" id="admin_id" name="admin_id" required 
                           value="<?php echo htmlspecialchars($_POST['admin_id'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="admin_username">Username:</label>
                    <input type="text" id="admin_username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="admin_password">Password:</label>
                    <input type="password" id="admin_password" name="password" required>
                </div>

                <button type="submit" class="btn-auth">Sign In</button>
            </form>
        </div>
    </div>

    <script>
        function switchLoginType(type) {
            // Update tabs
            document.querySelectorAll('.login-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update forms
            document.getElementById('studentForm').classList.remove('active');
            document.getElementById('adminForm').classList.remove('active');
            document.getElementById(type + 'Form').classList.add('active');
            
            // Update titles
            document.getElementById('formTitle').textContent = type === 'student' ? 'Student Sign In' : 'Admin Sign In';
            document.getElementById('formSubtitle').textContent = type === 'student' ? 'Access your student account' : 'Access admin dashboard';
            
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('type', type);
            window.history.replaceState({}, '', url);
        }
    </script>
</body>
</html>