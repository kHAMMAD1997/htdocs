<?php
include 'connection.php'; // Include the database connection

header('Content-Type: application/json');

// Helper function to return JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

// Fetch query parameters
$requestMethod = $_SERVER['REQUEST_METHOD'];
$params = $_GET ?? [];
$page = isset($params['page']) ? (int)$params['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

if ($requestMethod== 'GET') {
    // Check if the request is for status counts only (for all records)
    if (isset($params['count_status']) && $params['count_status'] == 'true') {
        // Count of each status type for all form_status records
        $statusCountStmt = $conn->query("
            SELECT 
                SUM(pending) AS pending, 
                SUM(approved) AS approved, 
                SUM(overdue) AS overdue, 
                SUM(declined) AS declined 
            FROM form_status
        ");
        $statusCounts = $statusCountStmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse('success', 'Status counts retrieved successfully', $statusCounts);
    }

    // Check if the request is for status counts by form_name (count_status_perform)
    elseif (isset($params['count_status_per_form'])) {
        $formName = $params['count_status_per_form'];
        $statusCountStmt = $conn->prepare("
            SELECT 
                SUM(pending) AS pending, 
                SUM(approved) AS approved, 
                SUM(overdue) AS overdue, 
                SUM(declined) AS declined 
            FROM form_status
            WHERE form_name = :form_name
        ");
        $statusCountStmt->bindParam(':form_name', $formName);
        $statusCountStmt->execute();
        $statusCounts = $statusCountStmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse('success', 'Status counts retrieved successfully for form: ' . $formName, $statusCounts);
    }

    // Check for both primary_contact_email and form_name
    elseif (isset($params['primary_contact_email']) && isset($params['form_name'])) {
        $email = $params['primary_contact_email'];
        $formName = $params['form_name'];
        $stmt = $conn->prepare("SELECT * FROM form_status WHERE primary_contact_email = :email AND form_name = :form_name LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':form_name', $formName);
    }

    // Fetch by primary_contact_email only
    elseif (isset($params['primary_contact_email'])) {
        $email = $params['primary_contact_email'];
        $stmt = $conn->prepare("SELECT * FROM form_status WHERE primary_contact_email = :email LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':email', $email);
    }

    // Fetch by form_status_id only
    elseif (isset($params['form_status_id'])) {
        $formStatusId = (int)$params['form_status_id'];
        $stmt = $conn->prepare("SELECT * FROM form_status WHERE form_status_id = :form_status_id LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':form_status_id', $formStatusId, PDO::PARAM_INT);
    }

    // Fetch by form_name only
    elseif (isset($params['form_name'])) {
        $formName = $params['form_name'];
        $stmt = $conn->prepare("SELECT * FROM form_status WHERE form_name = :form_name LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':form_name', $formName);
    }

    // Fetch by entry_id only
    elseif (isset($params['entry_id'])) {
        $entryId = (int)$params['entry_id'];
        $stmt = $conn->prepare("SELECT * FROM form_status WHERE entry_id = :entry_id LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':entry_id', $entryId, PDO::PARAM_INT);
    }

    // Fetch all with pagination (without status_counts)
    else {
        $stmt = $conn->prepare("SELECT * FROM form_status LIMIT :limit OFFSET :offset");
    }

    // Pagination logic
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count all form statuses (total records only)
    $countStmt = $conn->query("SELECT COUNT(*) AS total FROM form_status");
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Return only the data and total count (without status_counts)
    jsonResponse('success', 'Data retrieved successfully', [
        'data' => $results,
        'total' => $total
    ]);
}
 elseif ($requestMethod== 'POST') {
    // Create a new form_status
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $conn->prepare("
        INSERT INTO form_status (entry_id, wpform_id, form_name, project_title, primary_contact_email, pending, approved, overdue, declined, date)
        VALUES (:entry_id, :wpform_id, :form_name, :project_title, :primary_contact_email, :pending, :approved, :overdue, :declined, :date)
    ");
    $stmt->execute([
        ':entry_id' => $data['entry_id'],
        ':wpform_id' => $data['wpform_id'],
        ':form_name' => $data['form_name'],
        ':project_title' => $data['project_title'],
        ':primary_contact_email' => $data['primary_contact_email'],
        ':pending' => $data['pending'],
        ':approved' => $data['approved'],
        ':overdue' => $data['overdue'],
        ':declined' => $data['declined'],
        ':date' => $data['date']
    ]);

    jsonResponse('success', 'Record created successfully');

} elseif ($requestMethod== 'PUT') {
    // Update an existing form_status
    $data = json_decode(file_get_contents('php://input'), true);
    $formStatusId = $data['form_status_id'];

    $stmt = $conn->prepare("
        UPDATE form_status 
        SET entry_id = :entry_id, wpform_id = :wpform_id, form_name = :form_name, project_title = :project_title, primary_contact_email = :primary_contact_email, 
            pending = :pending, approved = :approved, overdue = :overdue, declined = :declined, date = :date
        WHERE form_status_id = :form_status_id
    ");
    $stmt->execute([
        ':entry_id' => $data['entry_id'],
        ':wpform_id' => $data['wpform_id'],
        ':form_name' => $data['form_name'],
        ':project_title' => $data['project_title'],
        ':primary_contact_email' => $data['primary_contact_email'],
        ':pending' => $data['pending'],
        ':approved' => $data['approved'],
        ':overdue' => $data['overdue'],
        ':declined' => $data['declined'],
        ':date' => $data['date'],
        ':form_status_id' => $formStatusId
    ]);

    jsonResponse('success', 'Record updated successfully');

} elseif ($requestMethod== 'DELETE') {
    // Delete a form_status by form_status_id
    $formStatusId = (int)$params['form_status_id'];

    $stmt = $conn->prepare("DELETE FROM form_status WHERE form_status_id = :form_status_id");
    $stmt->bindParam(':form_status_id', $formStatusId, PDO::PARAM_INT);
    $stmt->execute();

    jsonResponse('success', 'Record deleted successfully');
} else {
    jsonResponse('error', 'Invalid request method');
}
