<?php
include 'connection.php'; // Include database connection

header("Content-Type: application/json");

// Helper function to send JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

// Get the request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "POST") {
    // Handle creating a new short concept note
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO short_concept_note (activityRows, contactEmail, contactName, contactPhone, contactTitle, countryProject, organization, projectLocation, projectTitle, sector, girls, boys, households, men, women, pwds, pending, approved, overdue, declined, revise, totalBudget, submissionDate, user_id) 
              VALUES (:activityRows, :contactEmail, :contactName, :contactPhone, :contactTitle, :countryProject, :organization, :projectLocation, :projectTitle, :sector, :girls, :boys, :households, :men, :women, :pwds, :pending, :approved, :overdue, :declined, :revise, :totalBudget, :submissionDate, :user_id)";
    
    $stmt = $conn->prepare($query);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Short concept note created successfully", ["concept_note_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create short concept note");
    }

} elseif ($requestMethod === "GET") {
    // Initialize DataTables parameters
    $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumnIndex = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';

    // Columns that can be sorted (must match your database columns)
    $columns = ["concept_note_id", "contactEmail", "projectTitle", "sector", "user_id", "submissionDate", "pending", "approved", "overdue", "declined", "revise"];
    $orderColumn = $columns[$orderColumnIndex] ?? "concept_note_id"; // Default to `concept_note_id`

    // Base query
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM short_concept_note WHERE 1=1";
    $params = [];

    // Filtering parameters
    $allowedParams = ["concept_note_id", "contactEmail", "projectTitle", "sector", "user_id", "submissionDate", "pending", "approved", "overdue", "declined", "revise"];
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param]) && $_GET[$param] !== '') {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Global search
    if (!empty($searchValue)) {
        $query .= " AND (projectTitle LIKE :search OR contactEmail LIKE :search OR sector LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Sorting
    $query .= " ORDER BY $orderColumn $orderDir";

    // Pagination
    $query .= " LIMIT :start, :length";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(":start", $start, PDO::PARAM_INT);
    $stmt->bindValue(":length", $length, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total records (without filtering)
    $totalQuery = "SELECT COUNT(*) FROM short_concept_note";
    $totalRecords = (int)$conn->query($totalQuery)->fetchColumn();

    // Get filtered records count
    $filteredQuery = "SELECT FOUND_ROWS()";
    $filteredRecords = (int)$conn->query($filteredQuery)->fetchColumn();

    // Prepare DataTables response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,       // Total number of records in the table
        "recordsFiltered" => $filteredRecords, // Total number of filtered records
        "data" => $results                      // Data for the current page
    ];

    // Send the JSON response
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}
elseif ($requestMethod === "GET") {
    // Handle DataTables server-side processing with search
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;

    try {
        // Base query
        $query = "SELECT * FROM short_concept_note";
        $countQuery = "SELECT COUNT(*) as total FROM short_concept_note";

        // Add search functionality
        if (!empty($searchValue)) {
            $searchQuery = " WHERE concept_note_id = :exactSearchValue
                             OR CAST(concept_note_id AS CHAR) LIKE :searchValue
                             OR organization LIKE :searchValue
                             OR projectTitle LIKE :searchValue
                             OR projectLocation LIKE :searchValue
                             OR CAST(totalBudget AS CHAR) LIKE :searchValue
                             OR submissionDate LIKE :searchValue";
            $query .= $searchQuery;
            $countQuery .= $searchQuery;
        }

        // Add ordering and pagination
        $query .= " LIMIT :start, :length";

        // Prepare and execute the count query
        $countStmt = $conn->prepare($countQuery);
        if (!empty($searchValue)) {
            $countStmt->bindValue(':searchValue', '%' . $searchValue . '%', PDO::PARAM_STR);
            $countStmt->bindValue(':exactSearchValue', $searchValue, PDO::PARAM_INT);
        }
        $countStmt->execute();
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Prepare and execute the main query
        $stmt = $conn->prepare($query);
        if (!empty($searchValue)) {
            $stmt->bindValue(':searchValue', '%' . $searchValue . '%', PDO::PARAM_STR);
            $stmt->bindValue(':exactSearchValue', $searchValue, PDO::PARAM_INT);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Respond with DataTables-compatible JSON
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    } catch (PDOException $e) {
        // Log database error and respond with error message
        error_log("Database error: " . $e->getMessage());
        jsonResponse("error", "Database error: " . $e->getMessage());
    } catch (Exception $e) {
        // Log unexpected error and respond with error message
        error_log("Unexpected error: " . $e->getMessage());
        jsonResponse("error", "An unexpected error occurred: " . $e->getMessage());
    }
}

 elseif ($requestMethod === "PATCH") {
    // Handle partially updating an existing short concept note
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['concept_note_id'])) {
        jsonResponse("error", "concept_note_id is required for updating");
    }

    $conceptNoteId = $data['concept_note_id'];
    unset($data['concept_note_id']);

    $query = "UPDATE short_concept_note SET ";
    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }
    $query .= implode(", ", $fields);
    $query .= " WHERE concept_note_id = :concept_note_id";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(":concept_note_id", $conceptNoteId, PDO::PARAM_INT);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Short concept note partially updated successfully");
    } else {
        jsonResponse("error", "Failed to partially update short concept note");
    }

} elseif ($requestMethod === "DELETE") {
    // Handle deleting a short concept note
    if (!isset($_GET['concept_note_id'])) {
        jsonResponse("error", "concept_note_id is required for deleting");
    }

    $conceptNoteId = (int)$_GET['concept_note_id'];

    $stmt = $conn->prepare("DELETE FROM short_concept_note WHERE concept_note_id = :concept_note_id");
    $stmt->bindValue(":concept_note_id", $conceptNoteId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Short concept note deleted successfully");
    } else {
        jsonResponse("error", "Failed to delete short concept note");
    }

} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
?>
