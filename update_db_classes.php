<?php
include 'db_connect.php';

// Add class_logo column
$sql = "ALTER TABLE classes ADD COLUMN class_logo VARCHAR(255) DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Column class_logo added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
