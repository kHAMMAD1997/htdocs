<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'connection.php'; // Ensure database connection

$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch ($method) {
        case 'GET':
            handleGet($conn);
            break;
        case 'POST':
            handlePost($conn);
            break;
        case 'PATCH':
            handlePatch($conn);
            break;
        case 'DELETE':
            handleDelete($conn);
            break;
        default:
            echo json_encode(["error" => "Invalid request method"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

/**
 * Handle GET requests: Retrieve metadata records.
 */
function handleGet($conn) {
    $sql = "SELECT * FROM concept_note_metadata WHERE 1=1";
    $params = [];

    if (!empty($_GET['metadata_id'])) {
        $sql .= " AND metadata_id = :metadata_id";
        $params[':metadata_id'] = $_GET['metadata_id'];
    }
    if (!empty($_GET['concept_note_id'])) {
        $sql .= " AND concept_note_id = :concept_note_id";
        $params[':concept_note_id'] = $_GET['concept_note_id'];
    }
    if (isset($_GET['approve'])) {
        $sql .= " AND approve = :approve";
        $params[':approve'] = $_GET['approve'];
    }
    if (isset($_GET['revise'])) {
        $sql .= " AND revise = :revise";
        $params[':revise'] = $_GET['revise'];
    }
    if (isset($_GET['finance'])) {
        $sql .= " AND finance = :finance";
        $params[':finance'] = $_GET['finance'];
    }
    if (isset($_GET['declined'])) {
        $sql .= " AND declined = :declined";
        $params[':declined'] = $_GET['declined'];
    }

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data ?: []);
}

/**
 * Handle POST requests: Insert a new metadata record.
 */
function handlePost($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['concept_note_id'])) {
        echo json_encode(["error" => "concept_note_id is required"]);
        return;
    }

    $sql = "INSERT INTO concept_note_metadata (concept_note_id, approve, revise, finance, declined) 
            VALUES (:concept_note_id, :approve, :revise, :finance, :declined)";
    $stmt = $conn->prepare($sql);

    $stmt->bindValue(':concept_note_id', $input['concept_note_id'], PDO::PARAM_INT);
    $stmt->bindValue(':approve', $input['approve'] ?? null, PDO::PARAM_INT);
    $stmt->bindValue(':revise', $input['revise'] ?? null, PDO::PARAM_INT);
    $stmt->bindValue(':finance', $input['finance'] ?? null, PDO::PARAM_INT);
    $stmt->bindValue(':declined', $input['declined'] ?? null, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Metadata inserted successfully", "metadata_id" => $conn->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to insert metadata"]);
    }
}

/**
 * Handle PATCH requests: Update metadata fields.
 */
function handlePatch($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['metadata_id'])) {
        echo json_encode(["error" => "metadata_id is required for update"]);
        return;
    }

    $updates = [];
    $params = [':metadata_id' => $input['metadata_id']];

    if (isset($input['approve'])) {
        $updates[] = "approve = :approve";
        $params[':approve'] = $input['approve'];
    }
    if (isset($input['revise'])) {
        $updates[] = "revise = :revise";
        $params[':revise'] = $input['revise'];
    }
    if (isset($input['finance'])) {
        $updates[] = "finance = :finance";
        $params[':finance'] = $input['finance'];
    }
    if (isset($input['declined'])) {
        $updates[] = "declined = :declined";
        $params[':declined'] = $input['declined'];
    }

    if (empty($updates)) {
        echo json_encode(["error" => "No fields to update"]);
        return;
    }

    $sql = "UPDATE concept_note_metadata SET " . implode(", ", $updates) . " WHERE metadata_id = :metadata_id";
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }

    if ($stmt->execute()) {
        echo json_encode(["message" => "Metadata updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update metadata"]);
    }
}

/**
 * Handle DELETE requests: Remove a metadata record.
 */
function handleDelete($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input['metadata_id'])) {
        echo json_encode(["error" => "metadata_id is required for deletion"]);
        return;
    }

    $sql = "DELETE FROM concept_note_metadata WHERE metadata_id = :metadata_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':metadata_id', $input['metadata_id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Metadata deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete metadata"]);
    }
}
?>
