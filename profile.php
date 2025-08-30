<?php
session_start();
require_once 'php/functions.php';

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
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container { max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .profile-form { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .profile-form h2 { margin-top: 0; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group button { padding: 12px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Rent-a-Car</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Cars</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h1>My Profile</h1>

            <!-- Edit Profile Form -->
            <div class="profile-form">
                <h2>Edit Your Information</h2>
                <?php if (!empty($profile_message)): ?>
                    <div class="message <?php echo strpos($profile_message, 'success') !== false ? 'success' : 'error'; ?>">
                        <?php echo $profile_message; ?>
                    </div>
                <?php endif; ?>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="profile-form">
                <h2>Change Your Password</h2>
                <?php if (!empty($password_message)): ?>
                     <div class="message <?php echo strpos($password_message, 'success') !== false ? 'success' : 'error'; ?>">
                        <?php echo $password_message; ?>
                    </div>
                <?php endif; ?>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="change_password">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
