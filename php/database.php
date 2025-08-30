<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rent_a_car";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // We will remove this line later
?>
