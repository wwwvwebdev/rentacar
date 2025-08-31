<?php
session_start();
// Include the functions file to get the database connection and functions
require_once 'php/functions.php';

// Fetch all available cars from the database
$cities = get_all_cities($conn);
$selected_city = $_GET['city'] ?? 'all';
$cars = get_all_cars($conn, $selected_city);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-a-Car</title>
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
            <div class="section">
                <h2 class="center-align">Find Your Perfect Rental Car</h2>
                <p class="center-align flow-text">High-quality vehicles at the best prices.</p>
            </div>
            <div class="divider"></div>
            <div class="section">
                <h3 class="center-align">Our Fleet</h3>
                <div class="row">
                    <div class="col s12 m6 offset-m3">
                        <form action="index.php" method="GET">
                            <div class="input-field">
                                <select name="city" id="city_filter">
                                    <option value="all" <?php echo ($selected_city === 'all' ? 'selected' : ''); ?>>All Cities</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?php echo htmlspecialchars($city); ?>" <?php echo ($selected_city === $city ? 'selected' : ''); ?>>
                                            <?php echo htmlspecialchars($city); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Filter by City</label>
                            </div>
                            <div class="center-align">
                                <button type="submit" class="btn waves-effect waves-light">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <?php if (!empty($cars)): ?>
                        <?php foreach ($cars as $car): ?>
                            <div class="col s12 m6 l4">
                                <div class="card hoverable">
                                    <div class="card-image">
                                        <img src="<?php echo htmlspecialchars($car['image_url'] ?: 'https://via.placeholder.com/400x300.png?text=No+Image'); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                                        <span class="card-title"><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></span>
                                    </div>
                                    <div class="card-content">
                                        <p><strong>Year:</strong> <?php echo htmlspecialchars($car['year']); ?></p>
                                        <p><strong>Price:</strong> $<?php echo htmlspecialchars($car['price_per_day']); ?>/day</p>
                                    </div>
                                    <div class="card-action">
                                        <a href="car.php?id=<?php echo $car['id']; ?>">View Details & Rent</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="center-align">No cars available at the moment. Please check back later.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="page-footer">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">Rent-a-Car</h5>
                    <p class="grey-text text-lighten-4">Your adventure starts here. Quality cars for every occasion.</p>
                </div>
            </div>
        </div>
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
</body>
</html>
