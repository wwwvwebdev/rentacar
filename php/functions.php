<?php
// Include the database connection file
require_once 'database.php';

/**
 * Fetches all cars from the database.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of cars, or an empty array if no cars are found.
 */
function get_all_cars($conn) {
    $cars = [];
    $sql = "SELECT * FROM cars WHERE is_available = TRUE ORDER BY make, model";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Fetch all rows into an associative array
        $cars = $result->fetch_all(MYSQLI_ASSOC);
    }

    return $cars;
}

/**
 * Fetches all cars from the database for the admin panel.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of cars, or an empty array if no cars are found.
 */
function get_all_cars_admin($conn) {
    $cars = [];
    $sql = "SELECT * FROM cars ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $cars = $result->fetch_all(MYSQLI_ASSOC);
    }

    return $cars;
}

/**
 * Creates a new car in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param array $car_data An associative array of car data.
 * @return bool True on success, false on failure.
 */
function create_car($conn, $car_data) {
    $sql = "INSERT INTO cars (make, model, year, price_per_day, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("ssidsi",
        $car_data['make'], $car_data['model'], $car_data['year'],
        $car_data['price_per_day'], $car_data['image_url'], $car_data['is_available']
    );
    return $stmt->execute();
}

/**
 * Updates an existing car in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param array $car_data An associative array of car data including the id.
 * @return bool True on success, false on failure.
 */
function update_car($conn, $car_data) {
    $sql = "UPDATE cars SET make = ?, model = ?, year = ?, price_per_day = ?, image_url = ?, is_available = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("ssidsii",
        $car_data['make'], $car_data['model'], $car_data['year'],
        $car_data['price_per_day'], $car_data['image_url'], $car_data['is_available'],
        $car_data['id']
    );
    return $stmt->execute();
}

/**
 * Deletes a car from the database.
 *
 * @param mysqli $conn The database connection object.
 * @param int $car_id The ID of the car to delete.
 * @return bool True on success, false on failure.
 */
function delete_car($conn, $car_id) {
    $sql = "DELETE FROM cars WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("i", $car_id);
    return $stmt->execute();
}

/**
 * Creates a new user in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param string $full_name The user's full name.
 * @param string $username The user's chosen username.
 * @param string $email The user's email address.
 * @param string $password The user's plain-text password.
 * @return bool True on successful creation, false on failure.
 */
function create_user($conn, $full_name, $username, $email, $password) {
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an SQL statement to prevent SQL injection
    $sql = "INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle prepare error
        return false;
    }

    // Bind the parameters
    $stmt->bind_param("ssss", $full_name, $username, $email, $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        return true;
    } else {
        // Handle execute error (e.g., duplicate username/email)
        return false;
    }
}

/**
 * Verifies user credentials and returns user data on success.
 *
 * @param mysqli $conn The database connection object.
 * @param string $username The username to verify.
 * @param string $password The password to verify.
 * @return array|false The user's data as an associative array on success, otherwise false.
 */
function verify_user_login($conn, $username, $password) {
    // Prepare a statement to select the user by username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, return user data
            return $user;
        }
    }

    // If user not found or password is incorrect
    return false;
}

/**
 * Fetches a single car by its ID.
 *
 * @param mysqli $conn The database connection object.
 * @param int $car_id The ID of the car to fetch.
 * @return array|false The car's data as an associative array on success, otherwise false.
 */
function get_car_by_id($conn, $car_id) {
    $sql = "SELECT * FROM cars WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }

    return false;
}

/**
 * Creates a new rental record in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user renting the car.
 * @param int $car_id The ID of the car being rented.
 * @param string $start_date The start date of the rental (YYYY-MM-DD).
 * @param string $end_date The end date of the rental (YYYY-MM-DD).
 * @param float $total_price The total price of the rental.
 * @return bool True on success, false on failure.
 */
function create_rental($conn, $user_id, $car_id, $start_date, $end_date, $total_price) {
    $sql = "INSERT INTO rentals (user_id, car_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("iissd", $user_id, $car_id, $start_date, $end_date, $total_price);

    return $stmt->execute();
}

/**
 * Fetches all bookings for a specific user.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user whose bookings to fetch.
 * @return array An array of booking records.
 */
function get_user_bookings($conn, $user_id) {
    $bookings = [];
    $sql = "SELECT
                r.start_date,
                r.end_date,
                r.total_price,
                c.make,
                c.model,
                c.image_url
            FROM rentals AS r
            JOIN cars AS c ON r.car_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.start_date DESC";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return $bookings;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
    }

    return $bookings;
}
?>
