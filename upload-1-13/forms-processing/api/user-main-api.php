<?php
include 'connection.php'; // Include database connection

header("Content-Type: application/json");

// Helper function to send JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

// Get the request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod== "GET") {
    // Handle fetching users with filtering and pagination
    $query = "SELECT * FROM users WHERE 1=1";
    $params = [];
    
    // Filtering parameters
    $allowedParams = ["user_id", "email", "username", "is_admin", "created_at"];
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param])) {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $query .= " LIMIT :offset, :limit";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse("success", "Users retrieved successfully", $results);

} elseif ($requestMethod== "POST") {
    // Handle creating a new user
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO users (email, password, username, is_admin) VALUES (:email, :password, :username, :is_admin)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':password', $data['password']); // Ensure password is hashed in client-side or before
    $stmt->bindParam(':username', $data['username']);
    $stmt->bindParam(':is_admin', $data['is_admin']);

    if ($stmt->execute()) {
        jsonResponse("success", "User created successfully", ["user_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create user");
    }

} elseif ($requestMethod== "PUT") {
    // Handle updating an existing user
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        jsonResponse("error", "user_id is required for updating");
    }

    $query = "UPDATE users SET email = :email, password = :password, username = :username, is_admin = :is_admin WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':user_id', $data['user_id']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':password', $data['password']); // Ensure password is hashed in client-side or before
    $stmt->bindParam(':username', $data['username']);
    $stmt->bindParam(':is_admin', $data['is_admin']);

    if ($stmt->execute()) {
        jsonResponse("success", "User updated successfully");
    } else {
        jsonResponse("error", "Failed to update user");
    }

} elseif ($requestMethod == "PATCH") {
    // Handle partial update for user
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        jsonResponse("error", "user_id is required for updating");
    }

    $fields = [];
    foreach ($data as $key => $value) {
        if ($key !== 'user_id') {
            if ($key === 'password') {
                // Hash the password and update the $data array
                $data[$key] = password_hash($value, PASSWORD_BCRYPT);
            }
            $fields[] = "$key = :$key";
        }
    }

    if (empty($fields)) {
        jsonResponse("error", "No fields provided for update");
    }

    $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $data['user_id']);

    foreach ($data as $key => $value) {
        if ($key !== 'user_id') {
            $stmt->bindValue(":$key", $value);
        }
    }

    if ($stmt->execute()) {
        jsonResponse("success", "User updated successfully");
    } else {
        jsonResponse("error", "Failed to update user");
    }
}

elseif ($requestMethod== "DELETE") {
    // Handle deleting a user
    if (!isset($_GET['user_id'])) {
        jsonResponse("error", "user_id is required for deletion");
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
