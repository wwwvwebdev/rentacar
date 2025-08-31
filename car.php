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
        $with_driver = isset($_POST['with_driver']) ? 1 : 0;

        // Basic validation
        if (empty($start_date_str) || empty($end_date_str)) {
            $booking_error = "Please select both a start and end date.";
        } else {
            $start_date = new DateTime($start_date_str);
            $end_date = new DateTime($end_date_str);
            $today = new DateTime();
            $today->setTime(0, 0, 0);

            if ($start_date < $today) {
                $booking_error = "Start date cannot be in the past.";
            } elseif ($end_date <= $start_date) {
                $booking_error = "End date must be after the start date.";
            } else {
                // Calculate total price
                $interval = $start_date->diff($end_date);
                $days = $interval->days;
                $total_price = $days * $car['price_per_day'];
                if ($with_driver && $car['with_driver_available']) {
                    $total_price += $days * $car['driver_rate_per_day'];
                }

                // Create the rental
                if (create_rental($conn, $_SESSION['user_id'], $car_id, $start_date_str, $end_date_str, $total_price, $with_driver)) {
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
            <?php if ($car): ?>
                <div class="section">
                    <div class="row">
                        <div class="col s12 m6">
                            <img class="responsive-img materialboxed" src="<?php echo htmlspecialchars($car['image_url'] ?: 'https://via.placeholder.com/600x400.png?text=No+Image'); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                        </div>
                        <div class="col s12 m6">
                            <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                            <h5>Year: <?php echo htmlspecialchars($car['year']); ?></h5>
                            <h4 class="teal-text text-darken-2" id="price-display">$<?php echo htmlspecialchars($car['price_per_day']); ?> / day</h4>
                            <div class="card-panel">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <h5>Book this Car</h5>
                                    <form action="car.php?id=<?php echo $car_id; ?>" method="POST" id="bookingForm"
                                          data-car-price="<?php echo $car['price_per_day']; ?>"
                                          data-driver-price="<?php echo $car['driver_rate_per_day']; ?>">
                                        <div class="input-field">
                                            <input type="text" class="datepicker" id="start_date" name="start_date" required>
                                            <label for="start_date">Start Date</label>
                                        </div>
                                        <div class="input-field">
                                            <input type="text" class="datepicker" id="end_date" name="end_date" required>
                                            <label for="end_date">End Date</label>
                                        </div>
                                        <?php if ($car['with_driver_available']): ?>
                                        <p>
                                            <label>
                                                <input type="checkbox" name="with_driver" id="with_driver" value="1" />
                                                <span>Include Driver (+ $<?php echo htmlspecialchars($car['driver_rate_per_day']); ?>/day)</span>
                                            </label>
                                        </p>
                                        <?php endif; ?>
                                        <h5 class="right-align" id="total-price-display">Total: $0.00</h5>
                                        <button type="submit" class="btn waves-effect waves-light green right">Confirm Booking</button>
                                    </form>
                                <?php else: ?>
                                    <p>Please <a href="login.php">login</a> to book this car.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="section center-align">
                    <h2>Car Not Found</h2>
                    <p>Sorry, the car you are looking for does not exist.</p>
                    <a href="index.php" class="btn waves-effect waves-light">Back to Homepage</a>
                </div>
            <?php endif; ?>
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
    if (!empty($booking_error)) {
        echo "<script>M.toast({html: '{$booking_error}', classes: 'red'});</script>";
    }
    ?>
</body>
</html>
