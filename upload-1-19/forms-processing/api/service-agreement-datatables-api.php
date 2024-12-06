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
    // Initialize DataTables parameters
    $draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumnIndex = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir'])== 'desc' ? 'DESC' : 'ASC';

    // Define sortable columns
    $columns = ["service_agreement_id", "organization_name", "project_title", "project_location", "amount", "submission_date", "attachment"];
    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : "service_agreement_id";

    // Base query
    $query = "SELECT * FROM service_agreement WHERE 1=1";
    $params = [];

    // Filtering
    $allowedFields = $columns;
    foreach ($allowedFields as $field) {
        if (isset($_GET[$field]) && $_GET[$field] !== '') {
            $query .= " AND $field = :$field";
            $params[$field] = $_GET[$field];
        }
    }

    // Global search
    if (!empty($searchValue)) {
        $query .= " AND (organization_name LIKE :search OR project_title LIKE :search OR project_location LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Add ordering
    $query .= " ORDER BY $orderColumn $orderDir";

    // Add pagination
    $query .= " LIMIT :start, :length";

    try {
        // Execute query
        $stmt = $conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":start", $start, PDO::PARAM_INT);
        $stmt->bindValue(":length", $length, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total records without filtering
        $totalQuery = "SELECT COUNT(*) FROM service_agreement";
        $totalRecords = (int)$conn->query($totalQuery)->fetchColumn();

        // Get total records after filtering
        $filteredQuery = "SELECT COUNT(*) FROM service_agreement WHERE 1=1";
        if (!empty($searchValue)) {
            $filteredQuery .= " AND (organization_name LIKE :search OR project_title LIKE :search OR project_location LIKE :search)";
        }
        foreach ($allowedFields as $field) {
            if (isset($_GET[$field]) && $_GET[$field] !== '') {
                $filteredQuery .= " AND $field = :$field";
            }
        }
        $filteredStmt = $conn->prepare($filteredQuery);
        if (!empty($searchValue)) {
            $filteredStmt->bindValue(":search", '%' . $searchValue . '%');
        }
        foreach ($params as $key => $value) {
            $filteredStmt->bindValue(":$key", $value);
        }
        $filteredStmt->execute();
        $filteredRecords = (int)$filteredStmt->fetchColumn();

        // DataTables response
        $response = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $results
        ];

        // Send JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    } catch (PDOException $e) {
        jsonResponse("error", "Failed to fetch data: " . $e->getMessage());
    }
}
 elseif ($requestMethod== "POST") {
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
