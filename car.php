<?php
session_start();
require_once 'php/functions.php';

// Check for car ID in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$car_id = (int)$_GET['id'];
$car = get_car_by_id($conn, $car_id);

$booking_error = '';
$page_title = "Error"; // Default page title

if ($car) {
    $page_title = htmlspecialchars($car['make'] . ' ' . $car['model']);

    // Handle booking form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
        $start_date_str = $_POST['start_date'];
        $end_date_str = $_POST['end_date'];

        // Basic validation
        if (empty($start_date_str) || empty($end_date_str)) {
            $booking_error = "Please select both a start and end date.";
        } else {
            $start_date = new DateTime($start_date_str);
            $end_date = new DateTime($end_date_str);
            $today = new DateTime();

            if ($start_date < $today) {
                $booking_error = "Start date cannot be in the past.";
            } elseif ($end_date <= $start_date) {
                $booking_error = "End date must be after the start date.";
            } else {
                // Calculate total price
                $interval = $start_date->diff($end_date);
                $days = $interval->days;
                $total_price = $days * $car['price_per_day'];

                // Create the rental
                if (create_rental($conn, $_SESSION['user_id'], $car_id, $start_date_str, $end_date_str, $total_price)) {
                    $_SESSION['booking_success'] = "Your booking was successful!";
                    header("Location: my_bookings.php");
                    exit;
                } else {
                    $booking_error = "There was an error processing your booking. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Rent-a-Car</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .car-details-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .car-image img {
            width: 100%;
            border-radius: 8px;
        }
        .car-info h2 {
            margin-top: 0;
        }
        .car-info .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
            margin: 1rem 0;
        }
        .rental-form {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        .rental-form .form-group {
            margin-bottom: 1rem;
        }
        .rental-form label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .rental-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .rental-form button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
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
        <?php if ($car): ?>
            <div class="car-details-container">
                <div class="car-image">
                    <img src="<?php echo htmlspecialchars($car['image_url'] ?: 'images/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                </div>
                <div class="car-info">
                    <h2><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h2>
                    <p><strong>Year:</strong> <?php echo htmlspecialchars($car['year']); ?></p>
                    <p class="price">$<?php echo htmlspecialchars($car['price_per_day']); ?> / day</p>

                    <div class="rental-form">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <h3>Book this Car</h3>
                            <form action="car.php?id=<?php echo $car_id; ?>" method="POST" id="bookingForm">
                                <?php if(!empty($booking_error)): ?>
                                    <p style="color: red;"><?php echo $booking_error; ?></p>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit">Confirm Booking</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p>Please <a href="login.php">login</a> to book this car.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem;">
                <h2>Car Not Found</h2>
                <p>Sorry, the car you are looking for does not exist.</p>
                <a href="index.php">Back to Homepage</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Rent-a-Car. All Rights Reserved.</p>
    </footer>
</body>
</html>
