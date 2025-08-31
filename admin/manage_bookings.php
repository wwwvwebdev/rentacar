<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $rental_id = (int)$_POST['rental_id'];
    $new_status = $_POST['status'];
    // A basic security check could be added here to validate the status value
    update_booking_status($conn, $rental_id, $new_status);
    // Redirect to the same page to prevent form resubmission
    header("Location: manage_bookings.php");
    exit;
}

$bookings = get_all_bookings_admin($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Bookings</title>
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
                    <li class="active"><a href="manage_bookings.php">Manage Bookings</a></li>
                    <li><a href="../index.php">View Site</a></li>
                    <li><a href="../logout.php" class="waves-effect waves-light btn red">Logout</a></li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
             <li><a href="index.php">Manage Cars</a></li>
            <li><a href="manage_bookings.php">Manage Bookings</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </header>

    <main>
        <div class="container">
            <div class="section">
                <h3>Manage Bookings</h3>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Car</th>
                            <th>Dates</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['full_name']); ?><br><small><?php echo htmlspecialchars($booking['email']); ?></small></td>
                                    <td><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['start_date']); ?> to <?php echo htmlspecialchars($booking['end_date']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                                    <td>
                                        <form action="manage_bookings.php" method="POST" style="display: flex; gap: 10px;">
                                            <input type="hidden" name="rental_id" value="<?php echo $booking['id']; ?>">
                                            <div class="input-field" style="min-width: 150px;">
                                                <select name="status">
                                                    <option value="pending" <?php echo ($booking['status'] === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                                    <option value="confirmed" <?php echo ($booking['status'] === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                                                    <option value="completed" <?php echo ($booking['status'] === 'completed' ? 'selected' : ''); ?>>Completed</option>
                                                    <option value="cancelled" <?php echo ($booking['status'] === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                                                </select>
                                            </div>
                                            <button type="submit" name="update_status" class="btn-small waves-effect waves-light">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="center-align">No bookings found.</td>
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
