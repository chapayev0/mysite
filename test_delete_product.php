<?php
include 'db_connect.php';
$stmt = $conn->prepare("DELETE FROM store_products WHERE name = ?");
$name = "Test Product with Image";
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    echo "Test product deleted.";
}
$stmt->close();
$conn->close();
?>
