<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';

// Handle verification submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_user'])) {
    $user_to_verify = (int)$_POST['user_id'];
    verify_user($conn, $user_to_verify);
    // Redirect to the same page to see the updated list
    header("Location: verify_users.php");
    exit;
}

$unverified_users = get_unverified_users($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Verify Users</title>
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
                    <li class="active"><a href="verify_users.php">Verify Users</a></li>
                    <li><a href="settings.php">Settings</a></li>
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
                <h3>Verify User Documents</h3>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Documents</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($unverified_users)): ?>
                            <?php foreach ($unverified_users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php if ($user['cnic_image_path']): ?>
                                            <a href="../<?php echo htmlspecialchars($user['cnic_image_path']); ?>" target="_blank">View CNIC</a>
                                        <?php endif; ?>
                                        <br>
                                        <?php if ($user['license_image_path']): ?>
                                            <a href="../<?php echo htmlspecialchars($user['license_image_path']); ?>" target="_blank">View License</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="verify_users.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="verify_user" class="btn waves-effect waves-light green">Verify</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="center-align">No users are currently awaiting verification.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../js/custom.js"></script>
</body>
</html>
