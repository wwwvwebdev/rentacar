<?php
session_start();
// Include the functions file to get the database connection and functions
require_once 'php/functions.php';

// Fetch all available cars from the database
$cars = get_all_cars($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-a-Car</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Rent-a-Car</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Cars</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="my_bookings.php">My Bookings</a></li>
                    <li><a href="profile.php">Profile</a></li>
                <?php endif; ?>
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
        <section class="hero">
            <h2>Find Your Perfect Rental Car</h2>
            <p>High-quality vehicles at the best prices.</p>
        </section>

        <section class="car-listings">
            <h3>Our Fleet</h3>
            <div class="car-grid">
                <?php if (!empty($cars)): ?>
                    <?php foreach ($cars as $car): ?>
                        <a href="car.php?id=<?php echo $car['id']; ?>" class="car-card-link">
                            <div class="car-card">
                                <img src="<?php echo htmlspecialchars($car['image_url'] ?: 'images/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                                <h4><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h4>
                                <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                                <span class="price">$<?php echo htmlspecialchars($car['price_per_day']); ?>/day</span>
                                <span class="view-details-btn">View Details</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No cars available at the moment. Please check back later.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Rent-a-Car. All Rights Reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
