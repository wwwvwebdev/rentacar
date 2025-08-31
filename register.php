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
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-wrapper">
                <a href="index.php" class="brand-logo">Rent-a-Car</a>
                <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Cars</a></li>
                    <li><a href="login.php" class="waves-effect waves-light btn">Login</a></li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Cars</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </header>

    <main>
        <div class="container">
            <div class="row">
                <div class="col s12 m8 offset-m2 l6 offset-l3">
                    <div class="card-panel auth-card">
                        <h4 class="center-align">Create an Account</h4>
                        <form action="register.php" method="POST" id="registrationForm">
                            <div class="input-field">
                                <i class="material-icons prefix">person</i>
                                <input id="full_name" type="text" name="full_name" class="validate" required>
                                <label for="full_name">Full Name</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">account_circle</i>
                                <input id="username" type="text" name="username" class="validate" required data-length="20">
                                <label for="username">Username</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">email</i>
                                <input id="email" type="email" name="email" class="validate" required>
                                <label for="email">Email</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">lock</i>
                                <input id="password" type="password" name="password" class="validate" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="center-align">
                                <button type="submit" class="btn waves-effect waves-light">Register</button>
                            </div>
                        </form>
                        <p class="center-align">Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="page-footer">
        <div class="footer-copyright">
            <div class="container">
                &copy; 2025 Rent-a-Car. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/custom.js"></script>
    <?php
    if (!empty($registration_error)) {
        echo "<script>M.toast({html: '{$registration_error}', classes: 'red'});</script>";
    }
    ?>
</body>
</html>
