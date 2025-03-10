<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'connection.php'; // Ensure PDO connection

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error-log-one.log');

$uploadDir = __DIR__ . '/upload/grant-application/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle API Requests
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "POST":
        handlePost();
        break;
    case "GET":
        handleGet();
        break;
    case "PATCH":
        handlePatch();
        break;
    case "DELETE":
        handleDelete();
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
        break;
}

// ✅ POST: Upload files and store paths in DB
function handlePost()
{
    global $conn, $uploadDir;

    if (!isset($_POST['grant_application_id']) || empty($_FILES['files']['name'][0])) {
        http_response_code(400);
        echo json_encode(["error" => "grant_application_id and files are required"]);
        exit;
    }

    $grant_application_id = intval($_POST['grant_application_id']);
    $fileCount = count($_FILES['files']['name']);
    $savedFiles = [];

    for ($i = 0; $i < $fileCount; $i++) {
        $originalFileName = basename($_FILES['files']['name'][$i]);
        $newFileName = "{$grant_application_id}-" . ($i + 1) . "-{$originalFileName}";
        $filePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
            $relativePath = "/upload/grant-application/" . $newFileName;
            $stmt = $conn->prepare("INSERT INTO grant_application_finance_attachment (grant_application_id, file_path) VALUES (:grant_application_id, :file_path)");
            $stmt->execute([
                ':grant_application_id' => $grant_application_id,
                ':file_path' => $relativePath
            ]);
            $savedFiles[] = $relativePath;
        }
    }

    echo json_encode(["message" => "Files uploaded successfully", "files" => $savedFiles]);
}

// ✅ GET: Retrieve all files for a specific grant_application_id
function handleGet()
{
    global $conn;

    if (!isset($_GET['grant_application_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "grant_application_id is required"]);
        exit;
    }

    $grant_application_id = intval($_GET['grant_application_id']);
    $stmt = $conn->prepare("SELECT id, file_path FROM grant_application_finance_attachment WHERE grant_application_id = :grant_application_id");
    $stmt->execute([':grant_application_id' => $grant_application_id]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["files" => $files]);
}

// ✅ PATCH: Update a file path (rename or move file)
function handlePatch()
{
    global $conn, $uploadDir;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || !isset($data['new_file_name'])) {
        http_response_code(400);
        echo json_encode(["error" => "File ID and new file name are required"]);
        exit;
    }

    $id = intval($data['id']);
    $newFileName = basename($data['new_file_name']);

    // Get the old file path
    $stmt = $conn->prepare("SELECT file_path FROM grant_application_finance_attachment WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        http_response_code(404);
        echo json_encode(["error" => "File not found"]);
        exit;
    }

    $oldFullPath = __DIR__ . $file['file_path'];
    $newFullPath = $uploadDir . $newFileName;
    $newRelativePath = "/upload/grant-application/" . $newFileName;

    if (rename($oldFullPath, $newFullPath)) {
        $stmt = $conn->prepare("UPDATE grant_application_finance_attachment SET file_path = :file_path WHERE id = :id");
        $stmt->execute([
            ':file_path' => $newRelativePath,
            ':id' => $id
        ]);
        echo json_encode(["message" => "File renamed successfully", "new_path" => $newRelativePath]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to rename file"]);
    }
}

// ✅ DELETE: Remove a file (from DB and server)
function handleDelete()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "File ID is required"]);
        exit;
    }

    $id = intval($data['id']);

    // Fetch file path
    $stmt = $conn->prepare("SELECT file_path FROM grant_application_finance_attachment WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        http_response_code(404);
        echo json_encode(["error" => "File not found"]);
        exit;
    }

    $fullPath = __DIR__ . $file['file_path'];

    // Delete file from server
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }

    // Remove file record from DB
    $stmt = $conn->prepare("DELETE FROM grant_application_finance_attachment WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(["message" => "File deleted successfully"]);
}
?>
