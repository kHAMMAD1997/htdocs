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
    // Initialize variables for DataTables parameters
    $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumnIndex = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir'])== 'desc' ? 'DESC' : 'ASC';

    // Columns that can be sorted (must match your database columns)
    $columns = ["user_id", "username", "email", "is_admin", "created_at"];
    $orderColumn = $columns[$orderColumnIndex] ?? "user_id"; // Default to `user_id`

    // Base query with optional global search
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE 1=1";
    $params = [];

    if (!empty($searchValue)) {
        $query .= " AND (username LIKE :search OR email LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Append sorting
    $query .= " ORDER BY $orderColumn $orderDir";

    // Append pagination
    $query .= " LIMIT :start, :length";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(":start", $start, PDO::PARAM_INT);
    $stmt->bindValue(":length", $length, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the total number of records (without filters)
    $totalQuery = "SELECT COUNT(*) FROM users";
    $totalRecords = (int)$conn->query($totalQuery)->fetchColumn();

    // Get the total number of records after filtering
    $filteredQuery = "SELECT FOUND_ROWS()";
    $filteredRecords = (int)$conn->query($filteredQuery)->fetchColumn();

    // Create the response in DataTables format
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $results
    ];

    // Send JSON response
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}

elseif ($requestMethod== "POST") {
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

} elseif ($requestMethod== "PATCH") {
    // Handle partial update for user
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        jsonResponse("error", "user_id is required for updating");
    }

    $fields = [];
    foreach ($data as $key => $value) {
        if ($key !== 'user_id') {
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

} elseif ($requestMethod== "DELETE") {
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
