<?php
// Include database connection
include 'connection.php';

// Set content type to JSON
header("Content-Type: application/json");

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Helper function to send JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

// Handle GET request
if ($requestMethod== "GET") {
    try {
        // SQL query to calculate counts for each status column where value is 1
        $query = "
            SELECT 
                SUM(CASE WHEN pending = 1 THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN overdue = 1 THEN 1 ELSE 0 END) AS overdue_count,
                SUM(CASE WHEN declined = 1 THEN 1 ELSE 0 END) AS declined_count,
                SUM(CASE WHEN revise = 1 THEN 1 ELSE 0 END) AS revise_count
            FROM short_concept_note
        ";

        // Execute the query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch results
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send JSON response with the counts
        jsonResponse("success", "Analytics data retrieved successfully", $result);
    } catch (PDOException $e) {
        jsonResponse("error", "Database error: " . $e->getMessage());
    }
} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
