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
if ($requestMethod== "POST") {
    // Handle creating a new grant application
    $data = json_decode(file_get_contents("php://input"), true);

    // Map keys from `table-<number>` to `table_<number>`
    for ($i = 1; $i <= 8; $i++) {
        $data["table_$i"] = $data["table-$i"] ?? null;
        unset($data["table-$i"]); // Remove the original `table-<number>` key
    }

    $query = "INSERT INTO grant_application (approved, pending, overdue, declined, revise, total_budget, officialName, address, website, missionStatement, yearFounded, taxStatus, primaryContactName, primaryContactTitle, primaryContactTelephone, primaryContactEmail, projectTitle, projectSummary, projectStartDate, projectEndDate, projectLocation, seniorProjectManager, fieldManager, regularStaff, partnerships, objectives, expectedOutcomes, activityRows, monitoringStrategy, sustainability, lessonsLearned, households, men, boys, girls, pwds, selectionCriteria, beneficiaryInvolvement, postProjectMeasures, recurringCostManagement, exitStrategy, ownershipTangibleOutputs, table_1, table_2, table_3, table_4, table_5, table_6, table_7, table_8, media_path, otherFundingSources, declarationName, declarationTitle, declarationDate, declarationSignature, women ,user_id) VALUES (:approved, :pending, :overdue, :declined, :revise, :total_budget, :officialName, :address, :website, :missionStatement, :yearFounded, :taxStatus, :primaryContactName, :primaryContactTitle, :primaryContactTelephone, :primaryContactEmail, :projectTitle, :projectSummary, :projectStartDate, :projectEndDate, :projectLocation, :seniorProjectManager, :fieldManager, :regularStaff, :partnerships, :objectives, :expectedOutcomes, :activityRows, :monitoringStrategy, :sustainability, :lessonsLearned, :households, :men, :boys, :girls, :pwds, :selectionCriteria, :beneficiaryInvolvement, :postProjectMeasures, :recurringCostManagement, :exitStrategy, :ownershipTangibleOutputs, :table_1, :table_2, :table_3, :table_4, :table_5, :table_6, :table_7, :table_8, :media_path, :otherFundingSources, :declarationName, :declarationTitle, :declarationDate, :declarationSignature, :women, :user_id)";

    $stmt = $conn->prepare($query);

// Bind parameters
foreach ($data as $key => $value) {
    if ($value== null) {
        $stmt->bindValue(":" . $key, null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":" . $key, $value);
    }
}

try {
    if ($stmt->execute()) {
        jsonResponse("success", "Grant application created successfully", ["grant_application_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create grant application");
    }
} catch (PDOException $e) {
    jsonResponse("error", "Database error: " . $e->getMessage());
}
}


elseif ($requestMethod== "GET") {
    // Initialize DataTables parameters
    $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumnIndex = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : null;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir'])== 'desc' ? 'DESC' : 'ASC';

    // Define columns that can be sorted
    $columns = [
        "grant_application_id", "approved", "pending", "overdue", "declined", "revise", "total_budget", 
        "officialName", "address", "website", "missionStatement", "yearFounded", "taxStatus", 
        "primaryContactName", "primaryContactTitle", "primaryContactTelephone", "primaryContactEmail", 
        "projectTitle", "projectSummary", "projectStartDate", "projectEndDate", "projectLocation", 
        "seniorProjectManager", "fieldManager", "regularStaff", "partnerships", "objectives", 
        "expectedOutcomes", "activityRows", "monitoringStrategy", "sustainability", "lessonsLearned", 
        "households", "men", "boys", "girls", "pwds", "selectionCriteria", "beneficiaryInvolvement", 
        "postProjectMeasures", "recurringCostManagement", "exitStrategy", "ownershipTangibleOutputs", 
        "table_1", "table_2", "table_3", "table_4", "table_5", "table_6", "table_7", "table_8", 
        "media_path", "otherFundingSources", "declarationName", "declarationTitle", "declarationDate", 
        "declarationSignature", "user_id"
    ];
    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : "grant_application_id";

    // Base query
    $query = "SELECT * FROM grant_application WHERE 1=1";
    $params = [];

    // Apply filters
    $allowedParams = $columns; // Use the defined columns as allowed parameters
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param]) && $_GET[$param] !== '') {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Apply global search
    if (!empty($searchValue)) {
        $query .= " AND (officialName LIKE :search OR projectTitle LIKE :search OR primaryContactName LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Add sorting
    $query .= " ORDER BY $orderColumn $orderDir";

    // Add pagination
    $query .= " LIMIT :start, :length";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }
    $stmt->bindValue(":start", $start, PDO::PARAM_INT);
    $stmt->bindValue(":length", $length, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total number of records (without filtering)
    $totalQuery = "SELECT COUNT(*) FROM grant_application";
    $totalRecords = (int)$conn->query($totalQuery)->fetchColumn();

    // Get total number of filtered records
    $filteredQuery = "SELECT COUNT(*) FROM grant_application WHERE 1=1";
    if (!empty($searchValue)) {
        $filteredQuery .= " AND (officialName LIKE :search OR projectTitle LIKE :search OR primaryContactName LIKE :search)";
    }
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param]) && $_GET[$param] !== '') {
            $filteredQuery .= " AND $param = :$param";
        }
    }
    $filteredStmt = $conn->prepare($filteredQuery);
    if (!empty($searchValue)) {
        $filteredStmt->bindValue(":search", '%' . $searchValue . '%');
    }
    foreach ($params as $key => $value) {
        $filteredStmt->bindValue(":" . $key, $value);
    }
    $filteredStmt->execute();
    $filteredRecords = (int)$filteredStmt->fetchColumn();

    // Prepare DataTables response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $results
    ];

    // Send JSON response
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}
elseif ($requestMethod== "PUT") {
    // Handle updating an existing grant application
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['grant_application_id'])) {
        jsonResponse("error", "grant_application_id is required for updating");
    }

    $grantApplicationId = $data['grant_application_id'];
    unset($data['grant_application_id']);

    $query = "UPDATE grant_application SET ";
    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }
    $query .= implode(", ", $fields);
    $query .= " WHERE grant_application_id = :grant_application_id";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(":grant_application_id", $grantApplicationId, PDO::PARAM_INT);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application updated successfully");
    } else {
        jsonResponse("error", "Failed to update grant application");
    }
}  elseif ($requestMethod== "PATCH") {
    // Handle updating specific fields in an existing grant application
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['grant_application_id'])) {
        jsonResponse("error", "grant_application_id is required for updating");
    }

    $grantApplicationId = $data['grant_application_id'];
    unset($data['grant_application_id']);

    // Mapping table keys from table-<number> to table_<number> only if they exist
    for ($i = 1; $i <= 8; $i++) {
        if (isset($data["table-$i"])) {
            $data["table_$i"] = $data["table-$i"]; // Map table-<number> to table_<number>
            unset($data["table-$i"]); // Remove original table-<number> key
        }
    }

    $query = "UPDATE grant_application SET ";
    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }
    $query .= implode(", ", $fields);
    $query .= " WHERE grant_application_id = :grant_application_id";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(":grant_application_id", $grantApplicationId, PDO::PARAM_INT);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application updated successfully");
    } else {
        jsonResponse("error", "Failed to update grant application");
    }
}
elseif ($requestMethod== "DELETE") {
    // Handle deleting a grant application
    if (!isset($_GET['grant_application_id'])) {
        jsonResponse("error", "grant_application_id is required for deleting");
    }

    $grantApplicationId = (int)$_GET['grant_application_id'];
    $stmt = $conn->prepare("DELETE FROM grant_application WHERE grant_application_id = :grant_application_id");
    $stmt->bindValue(":grant_application_id", $grantApplicationId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application deleted successfully");
    } else {
        jsonResponse("error", "Failed to delete grant application");
    }
} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
?>
