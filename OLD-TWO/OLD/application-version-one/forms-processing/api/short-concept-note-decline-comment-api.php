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
    // Handle creating a new comment
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO short_concept_note_decline_comment (concept_note_id, comment, submission_date, author_id)
              VALUES (:concept_note_id, :comment, :submission_date, :author_id)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':concept_note_id', $data['concept_note_id'], PDO::PARAM_INT);
    $stmt->bindParam(':comment', $data['comment'], PDO::PARAM_STR);
    $stmt->bindParam(':submission_date', $data['submission_date']);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Comment created successfully", ["comment_id" => $conn->lastInsertId()]);
    } else {
        jsonResponse("error", "Failed to create comment");
    }

} elseif ($requestMethod== "PUT") {
    // Handle updating an existing comment
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['comment_id'])) {
        jsonResponse("error", "comment_id is required for updating");
    }

    $query = "UPDATE short_concept_note_decline_comment 
              SET concept_note_id = :concept_note_id, comment = :comment, submission_date = :submission_date, author_id = :author_id 
              WHERE comment_id = :comment_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':concept_note_id', $data['concept_note_id'], PDO::PARAM_INT);
    $stmt->bindParam(':comment', $data['comment'], PDO::PARAM_STR);
    $stmt->bindParam(':submission_date', $data['submission_date']);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);
    $stmt->bindParam(':comment_id', $data['comment_id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Comment updated successfully");
    } else {
        jsonResponse("error", "Failed to update comment");
    }

} elseif ($requestMethod== "DELETE") {
    // Handle deleting a comment
    if (!isset($_GET['comment_id'])) {
        jsonResponse("error", "comment_id is required for deleting");
    }

    $commentId = (int)$_GET['comment_id'];
    $stmt = $conn->prepare("DELETE FROM short_concept_note_decline_comment WHERE comment_id = :comment_id");
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        jsonResponse("success", "Comment deleted successfully");
    } else {
        jsonResponse("error", "Failed to delete comment");
    }

} elseif ($requestMethod== "GET") {
    // Handle retrieving comments based on provided parameters
    $query = "SELECT * FROM short_concept_note_decline_comment WHERE 1=1";
    $params = [];

    $allowedParams = ["comment_id", "concept_note_id", "comment", "submission_date", "author_id"];

    foreach ($allowedParams as $param) {
        if (isset($_GET[$param])) {
            $query .= " AND $param = :$param";
            $params[$param] = $_GET[$param];
        }
    }

    $stmt = $conn->prepare($query);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        jsonResponse("success", "Data retrieved successfully", $results);
    } else {
        jsonResponse("error", "No data found");
    }

} else {
    // Method not allowed
    jsonResponse("error", "Method not allowed");
}
?>
