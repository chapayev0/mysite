<?php
include 'db_connect.php';

// Create product_images table
$sql = "CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES store_products(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'product_images' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    exit;
}

// Migrate existing images
$result = $conn->query("SELECT id, image_url FROM store_products WHERE image_url IS NOT NULL AND image_url != ''");
if ($result->num_rows > 0) {
    $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 1)");
    while ($row = $result->fetch_assoc()) {
        $stmt->bind_param("is", $row['id'], $row['image_url']);
        if (!$stmt->execute()) {
             echo "Error migrating product " . $row['id'] . ": " . $stmt->error . "<br>";
        }
    }
    $stmt->close();
    echo "Existing images migrated.<br>";
} else {
    echo "No existing images to migrate.<br>";
}

// Optional: Drop the old column if you want, but safer to keep it for now as fallback or backup
// $conn->query("ALTER TABLE store_products DROP COLUMN image_url");

$conn->close();
?>
