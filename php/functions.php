<?php
// Include the database connection file
require_once 'database.php';

/**
 * Fetches all cars from the database.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of cars, or an empty array if no cars are found.
 */
function get_all_cars($conn, $city = null) {
    $cars = [];
    $sql = "SELECT * FROM cars WHERE is_available = TRUE";

    if ($city && $city !== 'all') {
        $sql .= " AND city = ?";
    }

    $sql .= " ORDER BY make, model";

    $stmt = $conn->prepare($sql);

    if ($city && $city !== 'all') {
        $stmt->bind_param("s", $city);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $cars = $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    return $cars;
}

/**
 * Updates a user's document paths in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to update.
 * @param string|null $cnic_path The path to the CNIC image.
 * @param string|null $license_path The path to the license image.
 * @return bool True on success, false on failure.
 */
function update_user_documents($conn, $user_id, $cnic_path, $license_path) {
    // Build the query dynamically based on which files were uploaded
    $sql = "UPDATE users SET ";
    $params = [];
    $types = "";

    if ($cnic_path) {
        $sql .= "cnic_image_path = ?";
        $params[] = $cnic_path;
        $types .= "s";
    }
    if ($license_path) {
        if ($cnic_path) $sql .= ", ";
        $sql .= "license_image_path = ?";
        $params[] = $license_path;
        $types .= "s";
    }

    // Also reset verification status when new documents are uploaded
    if ($cnic_path || $license_path) {
        $sql .= ", is_verified = 0";
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;

    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

/**
 * Fetches all bookings for the admin panel.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of all booking records.
 */
function get_all_bookings_admin($conn) {
    $bookings = [];
    $sql = "SELECT
                r.id, r.start_date, r.end_date, r.total_price, r.status, r.with_driver,
                c.make, c.model,
                u.full_name, u.email
            FROM rentals AS r
            JOIN cars AS c ON r.car_id = c.id
            JOIN users AS u ON r.user_id = u.id
            ORDER BY r.created_at DESC";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $bookings;
}

/**
 * Updates the status of a booking.
 *
 * @param mysqli $conn The database connection object.
 * @param int $rental_id The ID of the rental to update.
 * @param string $status The new status.
 * @return bool True on success, false on failure.
 */
function update_booking_status($conn, $rental_id, $status) {
    $sql = "UPDATE rentals SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("si", $status, $rental_id);
    return $stmt->execute();
}

/**
 * Fetches a distinct list of cities from the cars table.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of city names.
 */
function get_all_cities($conn) {
    $cities = [];
    $sql = "SELECT DISTINCT city FROM cars WHERE is_available = TRUE AND city IS NOT NULL AND city != '' ORDER BY city ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cities[] = $row['city'];
        }
    }
    return $cities;
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
    $sql = "INSERT INTO cars (make, model, city, year, price_per_day, driver_rate_per_day, image_url, is_available, with_driver_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("sssiddsii",
        $car_data['make'], $car_data['model'], $car_data['city'], $car_data['year'],
        $car_data['price_per_day'], $car_data['driver_rate_per_day'], $car_data['image_url'],
        $car_data['is_available'], $car_data['with_driver_available']
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
    $sql = "UPDATE cars SET make = ?, model = ?, city = ?, year = ?, price_per_day = ?, driver_rate_per_day = ?, image_url = ?, is_available = ?, with_driver_available = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("sssiddsiii",
        $car_data['make'], $car_data['model'], $car_data['city'], $car_data['year'],
        $car_data['price_per_day'], $car_data['driver_rate_per_day'], $car_data['image_url'],
        $car_data['is_available'], $car_data['with_driver_available'],
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
function create_rental($conn, $user_id, $car_id, $start_date, $end_date, $total_price, $with_driver) {
    $sql = "INSERT INTO rentals (user_id, car_id, start_date, end_date, total_price, with_driver) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("iissdi", $user_id, $car_id, $start_date, $end_date, $total_price, $with_driver);

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
                r.status,
                r.with_driver,
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

/**
 * Updates a user's profile information (full name and email).
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to update.
 * @param string $full_name The new full name.
 * @param string $email The new email address.
 * @return bool True on success, false on failure.
 */
function update_user_profile($conn, $user_id, $full_name, $email) {
    $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("ssi", $full_name, $email, $user_id);
    return $stmt->execute();
}

/**
 * Updates a user's password after verifying the current one.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to update.
 * @param string $current_password The user's current password.
 * @param string $new_password The new password.
 * @return bool True on success, false on failure (or if current password is wrong).
 */
function update_user_password($conn, $user_id, $current_password, $new_password) {
    // First, get the current password hash from the DB
    $sql_select = "SELECT password FROM users WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    if ($stmt_select === false) return false;
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows !== 1) {
        return false; // User not found
    }
    $user = $result->fetch_assoc();

    // Verify the current password
    if (password_verify($current_password, $user['password'])) {
        // If correct, update to the new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update === false) return false;
        $stmt_update->bind_param("si", $new_hashed_password, $user_id);
        return $stmt_update->execute();
    } else {
        // Current password was incorrect
        return false;
    }
}

/**
 * Retrieves a setting value from the database.
 *
 * @param mysqli $conn The database connection object.
 * @param string $setting_name The name of the setting to retrieve.
 * @return string|null The value of the setting, or null if not found.
 */
function get_setting($conn, $setting_name) {
    $sql = "SELECT setting_value FROM settings WHERE setting_name = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return null;
    $stmt->bind_param("s", $setting_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }
    return null;
}

/**
 * Updates a setting value in the database.
 *
 * @param mysqli $conn The database connection object.
 * @param string $setting_name The name of the setting to update.
 * @param string $setting_value The new value for the setting.
 * @return bool True on success, false on failure.
 */
function update_setting($conn, $setting_name, $setting_value) {
    $sql = "UPDATE settings SET setting_value = ? WHERE setting_name = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("ss", $setting_value, $setting_name);
    return $stmt->execute();
}

/**
 * Fetches all users who have uploaded documents but are not yet verified.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of user records.
 */
function get_unverified_users($conn) {
    $users = [];
    $sql = "SELECT id, full_name, username, email, cnic_image_path, license_image_path
            FROM users
            WHERE (cnic_image_path IS NOT NULL OR license_image_path IS NOT NULL) AND is_verified = FALSE";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $users;
}

/**
 * Marks a user as verified.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user to verify.
 * @return bool True on success, false on failure.
 */
function verify_user($conn, $user_id) {
    $sql = "UPDATE users SET is_verified = TRUE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}
?>
