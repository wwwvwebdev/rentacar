<?php
session_start();

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'php/functions.php';

$login_error = '';

// Check for registration success message
$registration_success_message = '';
if (isset($_SESSION['registration_success'])) {
    $registration_success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']); // Clear the message after displaying it
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $login_error = "Username and password are required.";
    } else {
        $user = verify_user_login($conn, $username, $password);
        if ($user) {
            // Login successful, store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // Redirect to the homepage
            header("Location: index.php");
            exit;
        } else {
            $login_error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rent-a-Car</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-form {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .auth-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .auth-form .register-link {
            text-align: center;
            margin-top: 1rem;
        }
        .auth-form .register-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Rent-a-Car</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Cars</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="login.php" class="btn-login">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="auth-form">
            <h2>Login to Your Account</h2>
            <form action="login.php" method="POST">
                <?php if (!empty($registration_success_message)): ?>
                    <p style="color: green; text-align: center;"><?php echo $registration_success_message; ?></p>
                <?php endif; ?>
                <?php if (!empty($login_error)): ?>
                    <p style="color: red; text-align: center;"><?php echo $login_error; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Rent-a-Car. All Rights Reserved.</p>
    </footer>
</body>
</html>
