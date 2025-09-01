<?php
session_start();
require_once 'php/functions.php';

$whatsapp_number = get_setting($conn, 'whatsapp_number');

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$profile_message = '';
$password_message = '';

// Handle profile update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    if (empty($full_name) || empty($email)) {
        $profile_message = "Full name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_message = "Invalid email format.";
    } else {
        if (update_user_profile($conn, $user_id, $full_name, $email)) {
            $profile_message = "Profile updated successfully!";
            // Update session variables
            $_SESSION['full_name'] = $full_name;
        } else {
            $profile_message = "Error updating profile. The email may already be in use.";
        }
    }
}

// Handle password change form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_message = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $password_message = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $password_message = "New password must be at least 6 characters long.";
    } else {
        if (update_user_password($conn, $user_id, $current_password, $new_password)) {
            $password_message = "Password changed successfully!";
        } else {
            $password_message = "Incorrect current password.";
        }
    }
}

// Get current user data to pre-fill form
$sql = "SELECT full_name, username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Rent-a-Car</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="my_bookings.php">My Bookings</a></li>
                        <li class="active"><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php" class="waves-effect waves-light btn">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="waves-effect waves-light btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Cars</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </header>

    <main>
        <div class="container">
            <div class="section">
                <h3 class="center-align">My Profile</h3>
                <div class="row">
                    <div class="col s12 l6">
                        <div class="card-panel">
                            <h5>Edit Your Information</h5>
                            <form action="profile.php" method="POST">
                                <div class="input-field">
                                    <input id="username" type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <label for="username">Username</label>
                                </div>
                                <div class="input-field">
                                    <input id="full_name" type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="validate" required>
                                    <label for="full_name">Full Name</label>
                                </div>
                                <div class="input-field">
                                    <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="validate" required>
                                    <label for="email">Email</label>
                                </div>
                                <button type="submit" name="update_profile" class="btn waves-effect waves-light">Update Profile</button>
                            </form>
                        </div>
                    </div>
                    <div class="col s12 l6">
                        <div class="card-panel">
                            <h5>Change Your Password</h5>
                            <form action="profile.php" method="POST">
                                <div class="input-field">
                                    <input id="current_password" type="password" name="current_password" class="validate" required>
                                    <label for="current_password">Current Password</label>
                                </div>
                                <div class="input-field">
                                    <input id="new_password" type="password" name="new_password" class="validate" required>
                                    <label for="new_password">New Password</label>
                                </div>
                                <div class="input-field">
                                    <input id="confirm_password" type="password" name="confirm_password" class="validate" required>
                                    <label for="confirm_password">Confirm New Password</label>
                                </div>
                                <button type="submit" name="change_password" class="btn waves-effect waves-light">Change Password</button>
                            </form>
                        </div>
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
    if (!empty($profile_message)) {
        $msg_class = strpos($profile_message, 'success') !== false ? 'green' : 'red';
        echo "<script>M.toast({html: '{$profile_message}', classes: '{$msg_class}'});</script>";
    }
    if (!empty($password_message)) {
        $msg_class = strpos($password_message, 'success') !== false ? 'green' : 'red';
        echo "<script>M.toast({html: '{$password_message}', classes: '{$msg_class}'});</script>";
    }
    ?>
</body>
</html>
