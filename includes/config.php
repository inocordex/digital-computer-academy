<?php
// Database configuration
$host = "localhost";
$dbname = "digital_academy";
$username = "root";
$password = "";

// Create a database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
