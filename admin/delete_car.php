<?php
session_start();
// Redirect if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../php/functions.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $car_id = (int)$_GET['id'];

    // Attempt to delete the car
    // A more robust system might add a success/error message to the session
    delete_car($conn, $car_id);
}

// Redirect back to the admin dashboard
header("Location: index.php");
exit;
?>
