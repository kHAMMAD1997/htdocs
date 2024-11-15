<?php
// Include the connection to the first database for forms_metadata (connection.php in the /api folder)
include(__DIR__ . '/../api/connection.php');



// Step 1: Fetch the wpforms_id from the forms_metadata where form_name is 'Concept Note' or 'Grant Application'
$query = "SELECT wpform_id, form_name FROM forms_metadata WHERE form_name IN ('Concept Note', 'Grant Application')";
$stmt = $conn->prepare($query);
$stmt->execute();
$formsMetadata = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$formsMetadata) {
    echo "No matching forms found in forms_metadata table.";
    exit();
}


// Include the second connection for wp_wpforms_entries (wp-db-connection.php)
include('wp-db-connection.php');

// Include the project-title-email-processor.php in the same folder
include('helper.php');


// Store all wpform_ids to use in the next query
$wpformIds = [];
foreach ($formsMetadata as $form) {
    $wpformIds[] = $form['wpform_id'];
}

// Convert array to a comma-separated string for SQL query
$wpformIdsString = implode(',', $wpformIds);

// Step 2: Fetch entries from wp_wpforms_entries where form_id matches the wpforms_id
$query = "SELECT entry_id, form_id, date, fields FROM wp_wpforms_entries WHERE form_id IN ($wpformIdsString)";
$stmt = $conn->prepare($query);
$stmt->execute();
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$entries) {
    echo "No entries found in wp_wpforms_entries table.";
    exit();
}

// Include the second connection for forms_status (from /api/connection.php)
include(__DIR__ . '/../api/connection.php');

// Step 3: Loop through the entries and insert into forms_status table
foreach ($entries as $entry) {
    // Decode the fields from the JSON object
    $fieldsData = json_decode($entry['fields'], true);
    
    // Call the processFields function from project-title-email-processor.php to get project_title and primary_contact_email
    $apiResponseData = processFields($fieldsData);
    $projectTitle = $apiResponseData['project_title'] ?? 'Unknown';
    $primaryContactEmail = $apiResponseData['email'] ?? 'Unknown';

    // Step 4: Insert data into the forms_status table
    $insertQuery = "INSERT INTO form_status (entry_id, wpform_id, form_name, project_title, primary_contact_email, pending, approved, overdue, date) 
                    VALUES (:entry_id, :wpform_id, :form_name, :project_title, :primary_contact_email, :pending, :approved, :overdue, :date)";

    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->execute([
        ':entry_id' => $entry['entry_id'],
        ':wpform_id' => $entry['form_id'],
        ':form_name' => $formsMetadata[array_search($entry['form_id'], array_column($formsMetadata, 'wpform_id'))]['form_name'],
        ':project_title' => $projectTitle,
        ':primary_contact_email' => $primaryContactEmail,
        ':pending' => 1,  // Set pending to 1
        ':approved' => 0, // Set approved to 0
        ':overdue' => 0,  // Set overdue to 0
        ':date' => $entry['date'],
    ]);
}

echo "Data successfully inserted into the form_status table.";

?>
