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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_contact_settings'])) {
        $new_number = trim($_POST['whatsapp_number']);
        if (!empty($new_number)) {
            if (update_setting($conn, 'whatsapp_number', $new_number)) {
                $update_message = "Contact settings updated successfully!";
            } else {
                $update_message = "Failed to update contact settings.";
            }
        } else {
            $update_message = "WhatsApp number cannot be empty.";
        }
    }

    if (isset($_POST['update_bank_settings'])) {
        update_setting($conn, 'bank_name', trim($_POST['bank_name']));
        update_setting($conn, 'bank_account_title', trim($_POST['bank_account_title']));
        update_setting($conn, 'bank_account_number', trim($_POST['bank_account_number']));
        update_setting($conn, 'bank_iban', trim($_POST['bank_iban']));
        $update_message = "Bank details updated successfully!";
    }
}

// Get current setting values
$whatsapp_number = get_setting($conn, 'whatsapp_number');
$bank_name = get_setting($conn, 'bank_name');
$bank_account_title = get_setting($conn, 'bank_account_title');
$bank_account_number = get_setting($conn, 'bank_account_number');
$bank_iban = get_setting($conn, 'bank_iban');
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
                            <button type="submit" name="update_contact_settings" class="btn waves-effect waves-light">Save Contact Info</button>
                        </div>
                    </form>
                </div>

                <div class="card-panel">
                    <form action="settings.php" method="POST">
                        <h5>Bank Transfer Details</h5>
                        <div class="input-field">
                            <input id="bank_name" type="text" name="bank_name" value="<?php echo htmlspecialchars($bank_name); ?>">
                            <label for="bank_name">Bank Name</label>
                        </div>
                        <div class="input-field">
                            <input id="bank_account_title" type="text" name="bank_account_title" value="<?php echo htmlspecialchars($bank_account_title); ?>">
                            <label for="bank_account_title">Account Title</label>
                        </div>
                        <div class="input-field">
                            <input id="bank_account_number" type="text" name="bank_account_number" value="<?php echo htmlspecialchars($bank_account_number); ?>">
                            <label for="bank_account_number">Account Number</label>
                        </div>
                        <div class="input-field">
                            <input id="bank_iban" type="text" name="bank_iban" value="<?php echo htmlspecialchars($bank_iban); ?>">
                            <label for="bank_iban">IBAN</label>
                        </div>
                        <div class="center-align" style="margin-top: 2rem;">
                            <button type="submit" name="update_bank_settings" class="btn waves-effect waves-light">Save Bank Details</button>
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
