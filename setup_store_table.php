<?php
include 'db_connect.php';

// Create store_products table
$sql = "CREATE TABLE IF NOT EXISTS store_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'store_products' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
    exit;
}

// Clear existing data
$conn->query("TRUNCATE TABLE store_products");

// Seed data
$stmt = $conn->prepare("INSERT INTO store_products (name, description, price, image_url) VALUES (?, ?, ?, ?)");

$products = [
    [
        'name' => 'Complete O/L ICT Guide',
        'description' => 'Comprehensive textbook covering entire O/L ICT syllabus with practice questions',
        'price' => 1500.00,
        'image_url' => '📖'
    ],
    [
        'name' => 'Past Paper Collection',
        'description' => '10 years of O/L ICT past papers with marking schemes and solutions',
        'price' => 800.00,
        'image_url' => '📝'
    ],
    [
        'name' => 'Video Lesson Pack',
        'description' => '50+ hours of recorded lessons covering all topics with practical examples',
        'price' => 2500.00,
        'image_url' => '💿'
    ],
    [
        'name' => 'Revision Notes Bundle',
        'description' => 'Concise notes and mind maps for quick revision before examinations',
        'price' => 600.00,
        'image_url' => '🎯'
    ]
];

foreach ($products as $p) {
    $stmt->bind_param("ssds", $p['name'], $p['description'], $p['price'], $p['image_url']);
    $stmt->execute();
}

echo "All products seeded successfully.\n";
$stmt->close();
$conn->close();
?>
