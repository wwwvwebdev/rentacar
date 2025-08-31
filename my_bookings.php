<?php
session_start();
require_once 'php/functions.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all bookings for the current user
$bookings = get_user_bookings($conn, $_SESSION['user_id']);

// Check for a booking success message
$booking_success_message = '';
if (isset($_SESSION['booking_success'])) {
    $booking_success_message = $_SESSION['booking_success'];
    unset($_SESSION['booking_success']); // Clear the message
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Rent-a-Car</title>
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
                        <li class="active"><a href="my_bookings.php">My Bookings</a></li>
                        <li><a href="profile.php">Profile</a></li>
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
                <h3 class="center-align">My Bookings</h3>
                <?php if (!empty($bookings)): ?>
                    <div class="row">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="col s12 m6">
                                <div class="card horizontal">
                                    <div class="card-image">
                                        <img src="<?php echo htmlspecialchars($booking['image_url'] ?: 'https://via.placeholder.com/400x300.png?text=No+Image'); ?>">
                                    </div>
                                    <div class="card-stacked">
                                        <div class="card-content">
                                            <span class="card-title"><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></span>
                                            <p><strong>From:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
                                            <p><strong>To:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>
                                            <p><strong>Total:</strong> $<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?>
                                                <?php if ($booking['with_driver']): ?>
                                                    <span class="grey-text">(with Driver)</span>
                                                <?php endif; ?>
                                            </p>
                                            <div class="chip <?php
                                                switch ($booking['status']) {
                                                    case 'confirmed': echo 'green white-text'; break;
                                                    case 'completed': echo 'blue white-text'; break;
                                                    case 'cancelled': echo 'red white-text'; break;
                                                    default: echo 'yellow darken-2 white-text'; break;
                                                }
                                            ?>">
                                                <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="center-align">You have no bookings yet. <a href="index.php">Find a car to rent!</a></p>
                <?php endif; ?>
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
    if (!empty($booking_success_message)) {
        echo "<script>M.toast({html: '{$booking_success_message}', classes: 'green'});</script>";
    }
    ?>
</body>
</html>
