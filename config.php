<?php
$servername = "localhost"; // or try "127.0.0.1"
$username = "root";
$password = ""; // Leave empty if no password
$dbname = "furniture_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3306); // Port 3306 is default

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected successfully";
}
?>
