<?php
// Set content-type to JSON
header('Content-Type: application/json');

// Include database connection details
include(__DIR__ . '/wp-db-connection.php');

// Check if entry_id is provided
if (!isset($_GET['entry_id'])) {
    echo json_encode(["error" => "No entry_id provided"]);
    exit();
}

// Get entry_id from request
$entryId = $_GET['entry_id'];
$formId = isset($_GET['form_id']) ? $_GET['form_id'] : null;

try {
    // Build the base query
    $query = "SELECT fields FROM wp_wpforms_entries WHERE entry_id = :entry_id";

    // Add form_id condition if form_id is provided
    if ($formId !== null) {
        $query .= " AND form_id = :form_id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':entry_id', $entryId, PDO::PARAM_INT);

    // Bind form_id if provided
    if ($formId !== null) {
        $stmt->bindParam(':form_id', $formId, PDO::PARAM_INT);
    }

    // Execute the query
    $stmt->execute();

    // Fetch the fields data
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Decode the fields data since it's in JSON format
        $fieldsData = json_decode($result['fields'], true);

        // Return the decoded JSON fields data
        echo json_encode(["fields" => $fieldsData]);
    } else {
        // Entry not found
        echo json_encode(["error" => "Entry not found"]);
    }
} catch (PDOException $e) {
    // Handle database error
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
