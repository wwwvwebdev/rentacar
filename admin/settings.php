<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';

$update_message = '';
$setting_name = 'whatsapp_number';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_settings'])) {
    $new_number = trim($_POST['whatsapp_number']);
    if (!empty($new_number)) {
        if (update_setting($conn, $setting_name, $new_number)) {
            $update_message = "Settings updated successfully!";
        } else {
            $update_message = "Failed to update settings.";
        }
    } else {
        $update_message = "WhatsApp number cannot be empty.";
    }
}

// Get current setting value
$whatsapp_number = get_setting($conn, $setting_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Settings</title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/custom.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-wrapper">
                <a href="index.php" class="brand-logo">Admin Panel</a>
                <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="index.php">Manage Cars</a></li>
                    <li><a href="manage_bookings.php">Manage Bookings</a></li>
                    <li><a href="verify_users.php">Verify Users</a></li>
                    <li class="active"><a href="settings.php">Settings</a></li>
                    <li><a href="../index.php">View Site</a></li>
                    <li><a href="../logout.php" class="waves-effect waves-light btn red">Logout</a></li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="index.php">Manage Cars</a></li>
            <li><a href="manage_bookings.php">Manage Bookings</a></li>
            <li><a href="verify_users.php">Verify Users</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </header>

    <main>
        <div class="container">
            <div class="section">
                <h3>Application Settings</h3>
                <div class="card-panel">
                    <form action="settings.php" method="POST">
                        <h5>Contact Information</h5>
                        <div class="input-field">
                            <i class="material-icons prefix">phone</i>
                            <input id="whatsapp_number" type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($whatsapp_number); ?>" class="validate" required>
                            <label for="whatsapp_number">WhatsApp Contact Number</label>
                            <span class="helper-text">Include country code, e.g., 923001234567</span>
                        </div>
                        <div class="center-align" style="margin-top: 2rem;">
                            <button type="submit" name="update_settings" class="btn waves-effect waves-light">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../js/custom.js"></script>
    <?php
    if (!empty($update_message)) {
        $msg_class = strpos($update_message, 'success') !== false ? 'green' : 'red';
        echo "<script>M.toast({html: '{$update_message}', classes: '{$msg_class}'});</script>";
    }
    ?>
</body>
</html>
