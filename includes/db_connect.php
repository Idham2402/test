<?php
// Database configuration
$host = "localhost";
$db_name = "inbank";
$username = "root";
$password = "NetByteSec_inbank123!";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
