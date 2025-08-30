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
    'id' => '', 'make' => '', 'model' => '', 'year' => '',
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
        'year' => (int)$_POST['year'],
        'price_per_day' => (float)$_POST['price_per_day'],
        'image_url' => trim($_POST['image_url']),
        'is_available' => isset($_POST['is_available']) ? 1 : 0
    ];

    // Basic validation
    if (empty($car_data['make']) || empty($car_data['model']) || empty($car_data['year']) || empty($car_data['price_per_day'])) {
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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; }
        .admin-container h1 { text-align: center; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-group input[type="text"], .form-group input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group input[type="checkbox"] { width: auto; margin-right: 10px; }
        .form-actions { margin-top: 1.5rem; }
        .form-actions button { background-color: #28a745; color: #fff; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .form-actions a { color: #555; margin-left: 1rem; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="../index.php" class="logo">Rent-a-Car (Admin)</a>
            <ul>
                <li><a href="../index.php">View Site</a></li>
                <li><a href="index.php">Manage Cars</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <h1><?php echo $page_title; ?></h1>
            <form action="<?php echo $form_action; ?>" method="POST">
                <?php if (!empty($error_message)): ?>
                    <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="make">Make</label>
                    <input type="text" id="make" name="make" value="<?php echo htmlspecialchars($car['make']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($car['year']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="price_per_day">Price per Day</label>
                    <input type="number" id="price_per_day" name="price_per_day" step="0.01" value="<?php echo htmlspecialchars($car['price_per_day']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($car['image_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="is_available">
                        <input type="checkbox" id="is_available" name="is_available" value="1" <?php echo ($car['is_available'] ? 'checked' : ''); ?>>
                        Is Available
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit">Save Car</button>
                    <a href="index.php">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
