<?php
session_start();

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'php/functions.php';

$registration_error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $registration_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = "Invalid email format.";
    } else {
        // Attempt to create the user
        if (create_user($conn, $full_name, $username, $email, $password)) {
            // Redirect to login page on success
            $_SESSION['registration_success'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit;
        } else {
            // Check if the error is due to a duplicate entry
            if ($conn->errno === 1062) { // 1062 is the MySQL error code for duplicate entry
                $registration_error = "Username or email already exists.";
            } else {
                $registration_error = "An error occurred during registration. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rent-a-Car</title>
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
        .auth-form .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .auth-form .login-link a {
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
            <h2>Create an Account</h2>
            <form action="register.php" method="POST" id="registrationForm">
                <?php if (!empty($registration_error)): ?>
                    <p style="color: red; text-align: center;"><?php echo $registration_error; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Rent-a-Car. All Rights Reserved.</p>
    </footer>
</body>
</html>
