<?php
// Include the constants file
require_once 'system-emails.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Prepare the constants as an associative array
    $data = [
        'programs_email' => PROGRAMS_EMAIL,
        'finance_email' => FINANCE_EMAIL,
    ];

    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Return the data as JSON
    echo json_encode($data);
    exit;
} else {
    // Handle unsupported methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only GET method is allowed']);
    exit;
}
?>
