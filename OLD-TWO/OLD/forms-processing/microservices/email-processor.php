<?php
// Set content-type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON input
$data = json_decode($inputData, true);

// Prepare response array
$response = [];

// Check if JSON was decoded correctly
if ($data === null) {
    // If the JSON is invalid, return an error
    $response['error'] = "Invalid JSON provided";
} else {
    // Check if the email key exists in the decoded data
    if (isset($data['26']['value'])) {
        $response['email'] = $data['26']['value'];
    } else {
        // If no email is found, return an appropriate message
        $response['error'] = "Email not found in the provided data";
    }
}

// Return the response as JSON
echo json_encode($response);
?>
