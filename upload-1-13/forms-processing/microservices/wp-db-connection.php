<?php
// Database connection parameters
$host = '173.252.167.60';
$db = 'hammadkh_mwlimits';
$user = 'hammadkh_mwlimits';
$password = 'tWXiyL.wLl*-';

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
