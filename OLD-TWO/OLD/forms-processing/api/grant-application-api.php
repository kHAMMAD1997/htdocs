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
    if ($value === null) {
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


 elseif ($requestMethod === "GET") {
    // Handle retrieving grant applications with pagination
    $query = "SELECT * FROM grant_application WHERE 1=1";
    $params = [];

    $allowedParams = ["grant_application_id", "approved", "pending", "overdue", "declined", "revise", "total_budget", "officialName", "address", "website", "missionStatement", "yearFounded", "taxStatus", "primaryContactName", "primaryContactTitle", "primaryContactTelephone", "primaryContactEmail", "projectTitle", "projectSummary", "projectStartDate", "projectEndDate", "projectLocation", "seniorProjectManager", "fieldManager", "regularStaff", "partnerships", "objectives", "expectedOutcomes", "activityRows", "monitoringStrategy", "sustainability", "lessonsLearned", "households", "men", "boys", "girls", "pwds", "selectionCriteria", "beneficiaryInvolvement", "postProjectMeasures", "recurringCostManagement", "exitStrategy", "ownershipTangibleOutputs", "table_1", "table_2", "table_3", "table_4", "table_5", "table_6", "table_7", "table_8", "media_path", "otherFundingSources", "declarationName", "declarationTitle", "declarationDate", "declarationSignature", "user_id"];

    foreach ($allowedParams as $param) {
        if (isset($_GET[$param])) {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
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
}  elseif ($requestMethod === "PATCH") {
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
elseif ($requestMethod === "DELETE") {
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
