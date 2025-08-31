<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';
$cars = get_all_cars_admin($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Cars</title>
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
                    <li class="active"><a href="index.php">Manage Cars</a></li>
                    <li><a href="manage_bookings.php">Manage Bookings</a></li>
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
                <h3>Manage Cars</h3>
                <div class="fixed-action-btn">
                    <a href="car_form.php" class="btn-floating btn-large waves-effect waves-light blue"><i class="material-icons">add</i></a>
                </div>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>City</th>
                            <th>Year</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cars)): ?>
                            <?php foreach ($cars as $car): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($car['id']); ?></td>
                                    <td><?php echo htmlspecialchars($car['make']); ?></td>
                                    <td><?php echo htmlspecialchars($car['model']); ?></td>
                                    <td><?php echo htmlspecialchars($car['city']); ?></td>
                                    <td><?php echo htmlspecialchars($car['year']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($car['price_per_day'], 2)); ?></td>
                                    <td>
                                        <?php if ($car['is_available']): ?>
                                            <span class="new badge green" data-badge-caption="Available"></span>
                                        <?php else: ?>
                                            <span class="new badge red" data-badge-caption="Unavailable"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="car_form.php?id=<?php echo $car['id']; ?>" class="btn-small waves-effect waves-light">Edit</a>
                                        <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn-small waves-effect waves-light red" onclick="return confirm('Are you sure you want to delete this car?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="center-align">No cars found. Click the '+' button to add one.</td>
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
