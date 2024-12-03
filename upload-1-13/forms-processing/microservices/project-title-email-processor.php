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
if ($data== null) {
    // If the JSON is invalid, return an error
    $response['error'] = "Invalid JSON provided";
} else {
    // Check if the Project Title exists in the decoded data
    if (isset($data['2']['value'])) {
        $response['project_title'] = $data['2']['value'];
    } else {
        // If no project title is found
        $response['project_title'] = "Project Title not found";
    }
    
    // Check if the email key exists in the decoded data
    if (isset($data['26']['value'])) {
        $response['email'] = $data['26']['value'];
    } else {
        // If no email is found
        $response['email'] = "Email not found";
    }
}

// Return the response as JSON
echo json_encode($response);
?>
