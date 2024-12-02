<?php
include 'connection.php'; // Include the database connection

header("Content-Type: application/json");

// Helper function to send JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

// Get the request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "POST") {
    // Handle creating a new user
    $data = json_decode(file_get_contents("php://input"), true);

    // Basic validation
    if (empty($data['email']) || empty($data['password']) || empty($data['username'])) {
        jsonResponse("error", "Missing required fields: email, password, or username");
    }

    // Prepare SQL query
    $query = "INSERT INTO users (email, password, username, is_admin) VALUES (:email, :password, :username, :is_admin)";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_BCRYPT), PDO::PARAM_STR); // Hash password
    $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
    $stmt->bindValue(':is_admin', isset($data['is_admin']) ? $data['is_admin'] : 0, PDO::PARAM_INT); // Default is_admin to 0 if not set

    // Execute and respond
    if ($stmt->execute()) {
        jsonResponse("success", "User created successfully", ["user_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create user");
    }

} elseif ($requestMethod === "GET") {
    // Handle retrieving users with optional filtering
    $query = "SELECT user_id, email, username, is_admin, created_at FROM users WHERE 1=1";
    $params = [];

    $allowedFilters = ["user_id", "email", "username", "is_admin"];

    // Add filters if provided
    foreach ($allowedFilters as $filter) {
        if (isset($_GET[$filter])) {
            $query .= " AND $filter = :$filter";
            $params[$filter] = $_GET[$filter];
        }
    }

    $stmt = $conn->prepare($query);

    // Bind filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        jsonResponse("success", "Users retrieved successfully", $results);
    } else {
        jsonResponse("error", "No users found");
    }

} elseif ($requestMethod === "PUT") {
    // Handle updating a user's details
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        jsonResponse("error", "user_id is required for updating");
    }

    $userId = $data['user_id'];
    unset($data['user_id']); // Remove ID from data to prevent accidental changes

    // Build the update query dynamically
    $fields = [];
    foreach ($data as $key => $value) {
        if ($key == 'password') {
            // Hash the password if it’s being updated
            $fields[] = "$key = :$key";
            $data[$key] = password_hash($value, PASSWORD_BCRYPT);
        } else {
            $fields[] = "$key = :$key";
        }
    }

    $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "User updated successfully");
    } else {
        jsonResponse("error", "Failed to update user");
    }

} elseif ($requestMethod === "DELETE") {
    // Handle deleting a user
    if (!isset($_GET['user_id'])) {
        jsonResponse("error", "user_id is required for deleting");
    }

    $userId = (int)$_GET['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "User deleted successfully");
    } else {
        jsonResponse("error", "Failed to delete user");
    }

} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
?>
