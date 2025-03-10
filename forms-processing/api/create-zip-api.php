<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["files"]) || empty($data["files"])) {
        echo json_encode(["success" => false, "message" => "No files selected."]);
        exit;
    }

    $files = $data["files"];
    $zip = new ZipArchive();
    
    // Generate a timestamped ZIP file
    $timestamp = date("Y-m-d_H-i-s");
    $zipFileName = "bulk_download_{$timestamp}.zip";
    $zipFilePath = "zip_files/" . $zipFileName;  // Ensure this directory exists and is writable

    if (!is_dir(__DIR__ . "/zip_files")) {
        mkdir(__DIR__ . "/zip_files", 0777, true);
    }

    if ($zip->open(__DIR__ . "/" . $zipFilePath, ZipArchive::CREATE) !== true) {
        echo json_encode(["success" => false, "message" => "Could not create ZIP file."]);
        exit;
    }

    // Add selected files to the ZIP
    foreach ($files as $file) {
        $filePath = __DIR__ . "/$file";  // Adjust path if needed
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        }
    }

    $zip->close();

    echo json_encode(["success" => true, "zipFile" => $zipFilePath]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
