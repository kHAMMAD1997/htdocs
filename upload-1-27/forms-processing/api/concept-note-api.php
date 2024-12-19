<?php
include 'connection.php'; // Include database connection

header("Content-Type: application/json");

// Disable error display and enable logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'concept-note-error.log'); // Change this path as needed

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Helper function to send JSON response
function jsonResponse($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

try {
    if ($requestMethod== "GET") {
        $query = "SELECT * FROM concept_note WHERE 1=1";
        $params = [];
        $allowedParams = [
            "concept_note_id", "activityRows", "contactEmail", "contactName", "contactPhone", "contactTitle", 
            "countryProject", "organization", "projectLocation", "projectTitle", "sector", "girls", "boys", 
            "households", "men", "women", "pwds", "table_1", "table_2", "table_3", "table_4", "table_5", 
            "table_6", "table_7", "table_8", "pending", "approved", "overdue", "declined", "revise", 
            "totalBudget", "submissionDate", "user_id"
        ];
    
        // Apply filters based on provided parameters
        foreach ($allowedParams as $param) {
            if (isset($_GET[$param])) {
                $query .= " AND $param = :$param";
                $params[$param] = $_GET[$param];
            }
        }
    
        // Set pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
    
        // Add LIMIT and OFFSET to the query
        $query .= " LIMIT :limit OFFSET :offset";
    
        $stmt = $conn->prepare($query);
    
        // Bind filter parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        // Bind pagination parameters
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Get the total count of records for pagination
        $countQuery = "SELECT COUNT(*) as total FROM concept_note WHERE 1=1";
        foreach ($allowedParams as $param) {
            if (isset($_GET[$param])) {
                $countQuery .= " AND $param = :$param";
            }
        }
        $countStmt = $conn->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(":$key", $value);
        }
        $countStmt->execute();
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
        // Calculate total pages
        $totalPages = ceil($totalRecords / $limit);
    
        jsonResponse("success", "Data retrieved successfully", [
            "data" => $results,
            "totalRecords" => $totalRecords,
            "totalPages" => $totalPages,
            "currentPage" => $page
        ]);
    }
     elseif ($requestMethod== "POST") {
        // Handle creating a new record
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if $data is valid
        if (!$data) {
            jsonResponse("error", "Invalid JSON input");
        }
    
        // Map keys from `table-<number>` to `table_<number>`
        for ($i = 1; $i <= 8; $i++) {
            $data["table_$i"] = $data["table-$i"] ?? null;  // Use null if `table-<number>` is missing
            unset($data["table-$i"]);  // Remove the old key to avoid confusion
        }
        // Map `totalBudget` to `total_budget`
        $data["totalBudget"] = $data["total_budget"] ?? null;
        unset($data["total_budget"]);
    
        // Define the query with all expected placeholders
        $query = "INSERT INTO concept_note (activityRows, contactEmail, contactName, contactPhone, contactTitle, 
                  countryProject, organization, projectLocation, projectTitle, sector, girls, boys, households, men, 
                  women, pwds, table_1, table_2, table_3, table_4, table_5, table_6, table_7, table_8, pending, approved, 
                  overdue, declined, revise, totalBudget, submissionDate, user_id) 
                  VALUES (:activityRows, :contactEmail, :contactName, :contactPhone, :contactTitle, :countryProject, 
                  :organization, :projectLocation, :projectTitle, :sector, :girls, :boys, :households, :men, :women, 
                  :pwds, :table_1, :table_2, :table_3, :table_4, :table_5, :table_6, :table_7, :table_8, :pending, 
                  :approved, :overdue, :declined, :revise, :totalBudget, :submissionDate, :user_id)";
        
        $stmt = $conn->prepare($query);
    
        // List all expected parameters with defaults to avoid undefined errors
        $expectedParams = [
            "activityRows", "contactEmail", "contactName", "contactPhone", "contactTitle", 
            "countryProject", "organization", "projectLocation", "projectTitle", "sector", "girls", "boys", 
            "households", "men", "women", "pwds", "table_1", "table_2", "table_3", "table_4", "table_5", 
            "table_6", "table_7", "table_8", "pending", "approved", "overdue", "declined", "revise", 
            "totalBudget", "submissionDate", "user_id"
        ];
    
        // Bind values and set default values for missing fields
        foreach ($expectedParams as $param) {
            $stmt->bindValue(":$param", $data[$param] ?? null);
        }
    
        // Execute the statement
        if ($stmt->execute()) {
            // Fetch the last inserted ID
            $lastInsertId = $conn->lastInsertId();
    
            // Retrieve the full record based on the last inserted ID
            $retrieveQuery = "SELECT * FROM concept_note WHERE concept_note_id = :id";
            $retrieveStmt = $conn->prepare($retrieveQuery);
            $retrieveStmt->bindValue(":id", $lastInsertId, PDO::PARAM_INT);
            $retrieveStmt->execute();
    
            // Fetch the full record
            $record = $retrieveStmt->fetch(PDO::FETCH_ASSOC);
    
            if ($record) {
                jsonResponse("success", "Record created successfully", $record);
            } else {
                jsonResponse("error", "Record created but could not be retrieved");
            }
        } else {
            jsonResponse("error", "Failed to create record");
        }
    }
    
    
    elseif ($requestMethod== "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);
    
        // Check if $data is valid
        if (!$data || !isset($data['concept_note_id'])) {
            jsonResponse("error", "Invalid input or concept_note_id missing for update");
        }
    
        // Map keys from `table-<number>` to `table_<number>`
        for ($i = 1; $i <= 8; $i++) {
            $data["table_$i"] = $data["table-$i"] ?? null;  // Use null if `table-<number>` is missing
            unset($data["table-$i"]);  // Remove the old key to avoid confusion
        }
    
        // Map `totalBudget` to `total_budget`
        $data["totalBudget"] = $data["total_budget"] ?? null;
        unset($data["total_budget"]);
    
        $conceptNoteId = $data['concept_note_id'];
        unset($data['concept_note_id']);
    
        $query = "UPDATE concept_note SET ";
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $query .= implode(", ", $fields);
        $query .= " WHERE concept_note_id = :concept_note_id";
    
        $stmt = $conn->prepare($query);
        $stmt->bindValue(":concept_note_id", $conceptNoteId);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        if ($stmt->execute()) {
            jsonResponse("success", "Record updated successfully");
        } else {
            jsonResponse("error", "Failed to update record");
        }
    }
     elseif ($requestMethod== "DELETE") {
        if (!isset($_GET['concept_note_id'])) {
            jsonResponse("error", "concept_note_id is required for deleting");
        }

        $conceptNoteId = $_GET['concept_note_id'];
        $stmt = $conn->prepare("DELETE FROM concept_note WHERE concept_note_id = :concept_note_id");
        $stmt->bindValue(":concept_note_id", $conceptNoteId);

        if ($stmt->execute()) {
            jsonResponse("success", "Record deleted successfully");
        } else {
            jsonResponse("error", "Failed to delete record");
        }

    } else {
        jsonResponse("error", "Method not allowed");
    }

} catch (Exception $e) {
    // Handle any unexpected errors
    jsonResponse("error", "Unexpected server error: " . $e->getMessage());
}
?>
