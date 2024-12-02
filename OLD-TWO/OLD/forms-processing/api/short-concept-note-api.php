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
    // Handle retrieving short concept notes with pagination and filtering
    $query = "SELECT * FROM short_concept_note WHERE 1=1";
    $params = [];
    $allowedParams = ["concept_note_id", "contactEmail", "projectTitle", "sector", "user_id", "submissionDate", "pending", "approved", "overdue", "declined", "revise"];
    
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param])) {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        jsonResponse("success", "Data retrieved successfully", $results);
    } else {
        jsonResponse("error", "No data found");
    }

} elseif ($requestMethod === "PUT") {
    // Handle updating an existing short concept note
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
        jsonResponse("success", "Short concept note updated successfully");
    } else {
        jsonResponse("error", "Failed to update short concept note");
    }

} elseif ($requestMethod === "PATCH") {
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
