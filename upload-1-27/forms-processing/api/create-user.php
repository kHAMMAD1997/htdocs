<?php
include 'connection.php'; // Include the database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data from the request
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Permissions (0 = no permission, 1 = permission granted)
    // Casting the values to integers to ensure correct data type
    $concept_note = isset($_POST['concept_note']) ? (int)$_POST['concept_note'] : 0;
    $short_concept_note = isset($_POST['short_concept_note']) ? (int)$_POST['short_concept_note'] : 0;
    $narrative_report = isset($_POST['narrative_report']) ? (int)$_POST['narrative_report'] : 0;
    $grant_application = isset($_POST['grant_application']) ? (int)$_POST['grant_application'] : 0;

    // Debugging: Log permission values to PHP error log
    error_log("Short Concept Note: " . $short_concept_note);
    error_log("Concept Note: " . $concept_note);
    error_log("Narrative Report: " . $narrative_report);
    error_log("Grant Application: " . $grant_application);

    // Simple validation
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide all required fields']);
        exit();
    }

    try {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // If the email already exists, return an error message
            echo json_encode(['status' => 'error', 'message' => 'User already exists']);
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (:username, :email, :password, 0)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // Get the last inserted user ID
        $user_id = $conn->lastInsertId();

        // Insert permissions into the permissions table
        $stmt = $conn->prepare("
            INSERT INTO permissions (user_id, short_concept_note, concept_note, narrative_report, grant_application)
            VALUES (:user_id, :short_concept_note, :concept_note, :narrative_report, :grant_application)
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':short_concept_note', $short_concept_note, PDO::PARAM_INT);
        $stmt->bindParam(':concept_note', $concept_note, PDO::PARAM_INT);
        $stmt->bindParam(':narrative_report', $narrative_report, PDO::PARAM_INT);
        $stmt->bindParam(':grant_application', $grant_application, PDO::PARAM_INT);
        $stmt->execute();

        // Return success message
        echo json_encode(['status' => 'success', 'message' => 'User created successfully','user_id' => $user_id]);
    } catch (PDOException $e) {
        // Return error message
        echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
}
 else {
    // If the request is not POST, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
