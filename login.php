<?php
session_start();

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'php/functions.php';

$whatsapp_number = get_setting($conn, 'whatsapp_number');
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
            $_SESSION['is_verified'] = $user['is_verified'];

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
                        <h4 class="center-align">Login</h4>
                        <form action="login.php" method="POST">
                            <div class="input-field">
                                <i class="material-icons prefix">account_circle</i>
                                <input id="username" type="text" name="username" class="validate" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">lock</i>
                                <input id="password" type="password" name="password" class="validate" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="center-align">
                                <button type="submit" class="btn waves-effect waves-light">Login</button>
                            </div>
                        </form>
                        <p class="center-align">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php if ($whatsapp_number): ?>
    <div class="fixed-action-btn">
        <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>" target="_blank" class="btn-floating btn-large green">
            <i class="large material-icons">message</i>
        </a>
    </div>
    <?php endif; ?>

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
    if (!empty($registration_success_message)) {
        echo "<script>M.toast({html: '{$registration_success_message}', classes: 'green'});</script>";
    }
    if (!empty($login_error)) {
        echo "<script>M.toast({html: '{$login_error}', classes: 'red'});</script>";
    }
    ?>
</body>
</html>
