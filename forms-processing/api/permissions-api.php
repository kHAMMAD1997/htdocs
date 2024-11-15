<?php
include 'connection.php'; // Ensure your database connection is included

header("Content-Type: application/json");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === "GET") {
    // GET request to retrieve permissions by user_id
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        
        $stmt = $conn->prepare("SELECT * FROM permissions WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $permissions = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($permissions) {
            echo json_encode(["status" => "success", "data" => $permissions]);
        } else {
            echo json_encode(["status" => "error", "message" => "No permissions found for the given user_id"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "user_id is required"]);
    }
} elseif ($requestMethod === "PUT") {
    // PUT request to update permissions by user_id
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id'])) {
        $user_id = $data['user_id'];
        $concept_note = $data['concept_note'] ?? 0;
        $narrative_report = $data['narrative_report'] ?? 0;
        $grant_application = $data['grant_application'] ?? 0;

        $stmt = $conn->prepare("UPDATE permissions SET concept_note = :concept_note, narrative_report = :narrative_report, grant_application = :grant_application WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":concept_note", $concept_note, PDO::PARAM_INT);
        $stmt->bindParam(":narrative_report", $narrative_report, PDO::PARAM_INT);
        $stmt->bindParam(":grant_application", $grant_application, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Permissions updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update permissions"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "user_id is required"]);
    }
} else {
    // Method not allowed
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>
