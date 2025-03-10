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
    // Handle creating a new grant application narrative report
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO grant_application_narrative_report (
                grant_application_id, submission_date, startDate, endDate, projectTitle, serviceAgreement,
                country, projectLocation, activityRows, households, men, women, boys, girls, pwds,
                mainChallenges, media_path, pending, approved, overdue, revise, declined
              ) VALUES (
                :grant_application_id, :submission_date, :startDate, :endDate, :projectTitle, :serviceAgreement,
                :country, :projectLocation, :activityRows, :households, :men, :women, :boys, :girls, :pwds,
                :mainChallenges, :media_path, :pending, :approved, :overdue, :revise, :declined
              )";

    $stmt = $conn->prepare($query);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application narrative report created successfully", ["report_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create grant application narrative report");
    }

} elseif ($requestMethod == "GET") {
    // Base query
    $query = "SELECT * FROM grant_application_narrative_report WHERE 1=1";
    $params = [];

    // Define allowed filter parameters
    $allowedParams = [
        "report_id", "grant_application_id", "submission_date", "startDate", "endDate", "projectTitle",
        "serviceAgreement", "country", "projectLocation", "activityRows", "households", "men",
        "women", "boys", "girls", "pwds", "mainChallenges", "media_path", "pending", "approved",
        "overdue", "revise", "declined"
    ];

    // Apply filters based on provided parameters
    foreach ($allowedParams as $param) {
        if (isset($_GET[$param])) {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    // Pagination settings
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) && $_GET['limit'] !== 'all' ? (int)$_GET['limit'] : null;
    $offset = $limit ? ($page - 1) * $limit : 0;

    // Add LIMIT and OFFSET only if limit is provided
    if ($limit) {
        $query .= " LIMIT :limit OFFSET :offset";
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);

    // Bind filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    // Bind pagination parameters only if limit is set
    if ($limit) {
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the total count of records for pagination
    $countQuery = "SELECT COUNT(*) as total FROM grant_application_narrative_report WHERE 1=1";
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

    // Calculate total pages (Avoid division by zero when fetching all records)
    $totalPages = $limit ? ceil($totalRecords / $limit) : 1;

    // JSON Response
    jsonResponse("success", "Data retrieved successfully", [
        "data" => $results,
        "totalRecords" => $totalRecords,
        "totalPages" => $totalPages,
        "currentPage" => $page
    ]);
}
 elseif ($requestMethod== "PUT") {
    // Handle updating a full grant application narrative report
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['report_id'])) {
        jsonResponse("error", "report_id is required for updating");
    }

    $reportId = $data['report_id'];
    unset($data['report_id']);

    $query = "UPDATE grant_application_narrative_report SET ";
    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }
    $query .= implode(", ", $fields);
    $query .= " WHERE report_id = :report_id";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(":report_id", $reportId, PDO::PARAM_INT);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application narrative report updated successfully");
    } else {
        jsonResponse("error", "Failed to update grant application narrative report");
    }

} elseif ($requestMethod== "PATCH") {
    // Handle partial update of a grant application narrative report
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['report_id'])) {
        jsonResponse("error", "report_id is required for updating");
    }

    $reportId = $data['report_id'];
    unset($data['report_id']);

    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }
    if (empty($fields)) {
        jsonResponse("error", "No valid fields provided for update");
    }

    $query = "UPDATE grant_application_narrative_report SET " . implode(", ", $fields) . " WHERE report_id = :report_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":report_id", $reportId, PDO::PARAM_INT);
    foreach ($data as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application narrative report updated successfully");
    } else {
        jsonResponse("error", "Failed to update grant application narrative report");
    }

} elseif ($requestMethod== "DELETE") {
    // Handle deleting a grant application narrative report
    if (!isset($_GET['report_id'])) {
        jsonResponse("error", "report_id is required for deleting");
    }

    $reportId = (int)$_GET['report_id'];
    $stmt = $conn->prepare("DELETE FROM grant_application_narrative_report WHERE report_id = :report_id");
    $stmt->bindValue(":report_id", $reportId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Grant application narrative report deleted successfully");
    } else {
        jsonResponse("error", "Failed to delete grant application narrative report");
    }

} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
?>
