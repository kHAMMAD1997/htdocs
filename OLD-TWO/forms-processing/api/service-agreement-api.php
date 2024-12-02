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

if ($requestMethod== "GET") {
    // Handle GET with pagination and filtering
    $query = "SELECT * FROM service_agreement WHERE 1=1";
    $params = [];
    $allowedFields = ["service_agreement_id", "organization_name", "project_title", "project_location", "amount", "submission_date", "attachment"];

    // Filtering
    foreach ($allowedFields as $field) {
        if (isset($_GET[$field])) {
            $query .= " AND $field = :$field";
            $params[$field] = $_GET[$field];
        }
    }

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $query .= " LIMIT :limit OFFSET :offset";

    try {
        $stmt = $conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse("success", "Data retrieved successfully", $results);
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to fetch data: " . $e->getMessage());
    }
} elseif ($requestMethod== "POST") {
    // Handle creating a new service agreement
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO service_agreement (organization_name, project_title, project_location, amount, submission_date, attachment)
              VALUES (:organization_name, :project_title, :project_location, :amount, :submission_date, :attachment)";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':organization_name', $data['organization_name']);
        $stmt->bindParam(':project_title', $data['project_title']);
        $stmt->bindParam(':project_location', $data['project_location']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':submission_date', $data['submission_date']);
        $stmt->bindParam(':attachment', $data['attachment']);
        $stmt->execute();

        jsonResponse("success", "Service agreement created successfully", ["service_agreement_id" => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to create service agreement: " . $e->getMessage());
    }
} elseif ($requestMethod== "PUT") {
    // Handle full update of an existing service agreement
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['service_agreement_id'])) {
        jsonResponse("error", "service_agreement_id is required for updating");
    }

    $query = "UPDATE service_agreement
              SET organization_name = :organization_name, project_title = :project_title, project_location = :project_location,
                  amount = :amount, submission_date = :submission_date, attachment = :attachment
              WHERE service_agreement_id = :service_agreement_id";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':organization_name', $data['organization_name']);
        $stmt->bindParam(':project_title', $data['project_title']);
        $stmt->bindParam(':project_location', $data['project_location']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':submission_date', $data['submission_date']);
        $stmt->bindParam(':attachment', $data['attachment']);
        $stmt->bindParam(':service_agreement_id', $data['service_agreement_id'], PDO::PARAM_INT);
        $stmt->execute();

        jsonResponse("success", "Service agreement updated successfully");
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to update service agreement: " . $e->getMessage());
    }
} elseif ($requestMethod== "PATCH") {
    // Handle partial update of an existing service agreement
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['service_agreement_id'])) {
        jsonResponse("error", "service_agreement_id is required for updating");
    }

    $fields = [];
    $allowedFields = ["organization_name", "project_title", "project_location", "amount", "submission_date", "attachment"];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = :$field";
        }
    }

    if (empty($fields)) {
        jsonResponse("error", "No valid fields provided for update");
    }

    $query = "UPDATE service_agreement SET " . implode(", ", $fields) . " WHERE service_agreement_id = :service_agreement_id";

    try {
        $stmt = $conn->prepare($query);
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $stmt->bindValue(":$field", $data[$field]);
            }
        }
        $stmt->bindValue(':service_agreement_id', $data['service_agreement_id'], PDO::PARAM_INT);
        $stmt->execute();

        jsonResponse("success", "Service agreement updated successfully");
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to update service agreement: " . $e->getMessage());
    }
} elseif ($requestMethod== "DELETE") {
    // Handle deleting a service agreement
    if (!isset($_GET['service_agreement_id'])) {
        jsonResponse("error", "service_agreement_id is required for deleting");
    }

    $serviceAgreementId = (int)$_GET['service_agreement_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM service_agreement WHERE service_agreement_id = :service_agreement_id");
        $stmt->bindParam(':service_agreement_id', $serviceAgreementId, PDO::PARAM_INT);
        $stmt->execute();

        jsonResponse("success", "Service agreement deleted successfully");
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to delete service agreement: " . $e->getMessage());
    }
} else {
    // Handle unsupported methods
    jsonResponse("error", "Method not allowed");
}
?>
