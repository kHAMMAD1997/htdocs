<?php
// Include the constants file
require_once 'system-emails.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Iterate through email definitions dynamically and create an associative array of email constants
$emailConstants = [];
foreach (get_defined_constants() as $key => $value) {
    if (str_starts_with($key, 'EMAIL_')) { // Filter constants that start with 'EMAIL_'
        $emailConstants[strtolower($key)] = $value;
    }
}

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Return the dynamically collected email constants as JSON
    echo json_encode($emailConstants);
    exit;
} else {
    // Handle unsupported methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only GET method is allowed']);
    exit;
}
}
?>


