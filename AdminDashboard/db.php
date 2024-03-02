
<?php
// db.php - Database Connection File

$host = "localhost";
$username = "root";
$password = "";
$database = "portfoliohub";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
