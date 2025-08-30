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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { max-width: 1100px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; }
        .admin-container h1 { text-align: center; margin-bottom: 2rem; }
        .admin-actions { margin-bottom: 1.5rem; text-align: right; }
        .admin-actions a { background-color: #007bff; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .admin-table th { background-color: #f2f2f2; }
        .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
        .admin-table .actions a { margin-right: 10px; }
        .status-available { color: green; font-weight: bold; }
        .status-unavailable { color: red; font-weight: bold; }
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
            <h1>Manage Cars</h1>
            <div class="admin-actions">
                <a href="car_form.php">Add New Car</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Make</th>
                        <th>Model</th>
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
                                <td><?php echo htmlspecialchars($car['year']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($car['price_per_day'], 2)); ?></td>
                                <td>
                                    <?php if ($car['is_available']): ?>
                                        <span class="status-available">Available</span>
                                    <?php else: ?>
                                        <span class="status-unavailable">Unavailable</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="car_form.php?id=<?php echo $car['id']; ?>">Edit</a>
                                    <a href="delete_car.php?id=<?php echo $car['id']; ?>" onclick="return confirm('Are you sure you want to delete this car?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">No cars found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
