<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';

// Initialize variables
$car = [
    'id' => '', 'make' => '', 'model' => '', 'city' => '', 'year' => '',
    'price_per_day' => '', 'image_url' => '', 'is_available' => 1
];
$page_title = "Add New Car";
$form_action = "car_form.php";

// Check if this is an edit request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $car_id = (int)$_GET['id'];
    $existing_car = get_car_by_id($conn, $car_id);
    if ($existing_car) {
        $car = $existing_car;
        $page_title = "Edit Car";
        $form_action = "car_form.php?id=" . $car_id;
    }
}

// Form submission logic
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_data = [
        'make' => trim($_POST['make']),
        'model' => trim($_POST['model']),
        'city' => trim($_POST['city']),
        'year' => (int)$_POST['year'],
        'price_per_day' => (float)$_POST['price_per_day'],
        'image_url' => trim($_POST['image_url']),
        'is_available' => isset($_POST['is_available']) ? 1 : 0
    ];

    // Basic validation
    if (empty($car_data['make']) || empty($car_data['model']) || empty($car_data['city']) || empty($car_data['year']) || empty($car_data['price_per_day'])) {
        $error_message = "Please fill in all required fields.";
    } else {
        if (isset($_GET['id'])) {
            // Update existing car
            $car_data['id'] = (int)$_GET['id'];
            if (update_car($conn, $car_data)) {
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Failed to update car.";
            }
        } else {
            // Create new car
            if (create_car($conn, $car_data)) {
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Failed to create car.";
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
    <title><?php echo $page_title; ?> - Admin Panel</title>
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
                    <li><a href="../index.php">View Site</a></li>
                    <li><a href="../logout.php" class="waves-effect waves-light btn red">Logout</a></li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="index.php">Manage Cars</a></li>
            <li><a href="../index.php">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </header>

    <main>
        <div class="container">
            <div class="section">
                <h3><?php echo $page_title; ?></h3>
                <div class="card-panel">
                    <form action="<?php echo $form_action; ?>" method="POST">
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input id="make" type="text" name="make" value="<?php echo htmlspecialchars($car['make']); ?>" class="validate" required>
                                <label for="make">Make</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="model" type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" class="validate" required>
                                <label for="model">Model</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="city" type="text" name="city" value="<?php echo htmlspecialchars($car['city']); ?>" class="validate" required>
                                <label for="city">City</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="year" type="number" name="year" value="<?php echo htmlspecialchars($car['year']); ?>" class="validate" required>
                                <label for="year">Year</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="price_per_day" type="number" name="price_per_day" step="0.01" value="<?php echo htmlspecialchars($car['price_per_day']); ?>" class="validate" required>
                                <label for="price_per_day">Price per Day</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="image_url" type="text" name="image_url" value="<?php echo htmlspecialchars($car['image_url']); ?>" class="validate">
                                <label for="image_url">Image URL</label>
                            </div>
                            <div class="col s12">
                                <div class="switch">
                                    <label>
                                        Unavailable
                                        <input type="checkbox" name="is_available" value="1" <?php echo ($car['is_available'] ? 'checked' : ''); ?>>
                                        <span class="lever"></span>
                                        Available
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="center-align" style="margin-top: 2rem;">
                            <button type="submit" class="btn waves-effect waves-light green">Save Car</button>
                            <a href="index.php" class="btn waves-effect waves-light grey">Cancel</a>
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
    if (!empty($error_message)) {
        echo "<script>M.toast({html: '{$error_message}', classes: 'red'});</script>";
    }
    ?>
</body>
</html>
