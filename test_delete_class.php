<?php
include 'db_connect.php';
$stmt = $conn->prepare("DELETE FROM classes WHERE institute_name = ?");
$name = "Test Institute via Script";
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    echo "Test class deleted.";
}
$stmt->close();
$conn->close();
?>
