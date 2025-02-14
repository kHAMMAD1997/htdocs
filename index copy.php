<?php
session_start();

// Allow access to login page without restriction
$public_pages = ['login.html'];

// Get the requested file
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri) ?? []; // Ensure it doesn't return null
$path = $parsed_url['path'] ?? ''; // Default to empty string if undefined
$file_path = __DIR__ . $path;
$file_name = basename($path);

// If accessing a page under forms-processing and it's not login.html, enforce authentication
if (strpos($path, '/forms-processing/') === 0 && !in_array($file_name, $public_pages)) {
    if (!isset($_COOKIE['user'])) {
        // Redirect unauthorized users to login
        header("Location: /forms-processing/login.html");
        exit;
    }
}

// Allow direct access to .html files, even with query parameters
if (preg_match('/\/forms-processing\/[a-zA-Z0-9\-]+\.html$/', $path)) {
    if (file_exists($file_path) && is_file($file_path)) {
        // Serve the HTML file
        readfile($file_path);
        exit;
    }
}

// If file doesn't exist, handle 404 but redirect root `/` to login
if ($path === '/' || $path === '/index.php') {
    header("Location: /forms-processing/login.html");
    exit;
}

// Show 404 error for all other invalid files
http_response_code(404);
echo "404 Not Found";
?>
