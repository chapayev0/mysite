<?php
$servername = "localhost";
$username = "u529770620_Ictdilhara";
$password = "jH;4DB7ubu@r";
$dbname = "u529770620_Ictdilhara";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
