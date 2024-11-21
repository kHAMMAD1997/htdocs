<?php
include 'connection.php'; // Include the database connection

include(__DIR__ . '/sync/helper.php');

//forms_fetcher();---------------------Deprecated


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Retrieve form data from the request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate that both fields are provided
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide both email and password']);
        exit();
    }

    try {
        // Fetch the user from the database based on the email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // If password matches, login is successful
            echo json_encode([
                'status' => 'success', 
                'message' => 'Login successful',
                'user' => ['user_id' => $user['user_id'], 'email' => $user['email'], 'is_admin' => $user['is_admin']]
            ]);
        } else {
            // Invalid credentials
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
        }
    } catch (PDOException $e) {
        // Return error message
        echo json_encode(['status' => 'error', 'message' => 'Login failed: ' . $e->getMessage()]);
    }
} else {
    // If the request is not POST, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
