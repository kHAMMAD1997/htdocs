<?php
header("Content-Type: application/json");
require_once "connection.php"; // Ensure this properly initializes a PDO connection

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Decode JSON request body
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['form_id'], $data['breakdown'])) {
            try {
                $stmt = $conn->prepare("INSERT INTO grant_application_finance_details (form_id, breakdown) VALUES (:form_id, :breakdown)");
                $stmt->bindValue(":form_id", $data['form_id'], PDO::PARAM_INT);
                $stmt->bindValue(":breakdown", $data['breakdown'], PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo json_encode(["message" => "Record created successfully", "id" => $conn->lastInsertId()]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to create record"]);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'GET':
        // Retrieve finance details by id or form_id
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;

        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM grant_application_finance_details WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        } elseif ($form_id) {
            $stmt = $conn->prepare("SELECT * FROM grant_application_finance_details WHERE form_id = :form_id");
            $stmt->bindValue(":form_id", $form_id, PDO::PARAM_INT);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing id or form_id"]);
            exit;
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;

    case 'PATCH':
        // Update finance details
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'], $data['breakdown'])) {
            try {
                $stmt = $conn->prepare("UPDATE grant_application_finance_details SET breakdown = :breakdown WHERE id = :id");
                $stmt->bindValue(":breakdown", $data['breakdown'], PDO::PARAM_STR);
                $stmt->bindValue(":id", $data['id'], PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Record updated successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to update record"]);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing id or breakdown"]);
        }
        break;

    case 'DELETE':
        // Delete finance detail by id
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            try {
                $stmt = $conn->prepare("DELETE FROM grant_application_finance_details WHERE id = :id");
                $stmt->bindValue(":id", $data['id'], PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Record deleted successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to delete record"]);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
