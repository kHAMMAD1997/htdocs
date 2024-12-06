<?php
// Database connection parameters
$host = '173.252.167.60';
$db = 'hammadkh_two_forms_processing';
$user = 'hammadkh_form_processing_user';
$password = 'mwlimits-form-processing';

try {
    // Create a new PDO instance and set error mode to exception
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connection Successful";
} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
