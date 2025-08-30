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
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bookings-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .booking-card {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            background: #fff;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .booking-card img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .booking-details {
            flex-grow: 1;
        }
        .booking-details h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
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
                <li><a href="my_bookings.php">My Bookings</a></li>
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
        <div class="bookings-container">
            <h2>My Bookings</h2>

            <?php if (!empty($booking_success_message)): ?>
                <p class="success-message"><?php echo $booking_success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <img src="<?php echo htmlspecialchars($booking['image_url'] ?: 'images/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?>">
                        <div class="booking-details">
                            <h3><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></h3>
                            <p><strong>From:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>
                            <p><strong>Total Price:</strong> $<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no bookings yet.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Rent-a-Car. All Rights Reserved.</p>
    </footer>
</body>
</html>
