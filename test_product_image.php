<?php
include 'db_connect.php';

$name = "Test Product with Image";
$description = "This is a test product to verify image display.";
$price = 2500.00;
$image_url = "https://placehold.co/400x400/purple/white?text=Product+Image";

$stmt = $conn->prepare("INSERT INTO store_products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $name, $description, $price, $image_url);

if ($stmt->execute()) {
    echo "Test product inserted. ID: " . $stmt->insert_id;
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
