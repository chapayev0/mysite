<?php
include 'db_connect.php';

// Create Utilities category if it doesn't exist
$cat_sql = "INSERT INTO tool_categories (name, icon, display_order, status)
SELECT 'Utilities', 'fa-wrench', 2, 'active'
WHERE NOT EXISTS (SELECT 1 FROM tool_categories WHERE name = 'Utilities')";

if ($conn->query($cat_sql) === TRUE) {
    echo "Category check/insert successful.\n";
} else {
    echo "Error inserting category: " . $conn->error . "\n";
}

// Insert the tool
$tool_sql = "INSERT INTO tools (title, description, tool_url, category_id, is_playground)
SELECT 'CSS Glassmorphism Generator', 'Create beautiful, frosted glass UI effects instantly with real-time visual editing.', 'glassmorphism_generator.php', id, 0
FROM tool_categories 
WHERE name = 'Utilities' AND NOT EXISTS (SELECT 1 FROM tools WHERE tool_url = 'glassmorphism_generator.php')
LIMIT 1";

if ($conn->query($tool_sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo "Glassmorphism Generator successfully inserted into database!\n";
    } else {
        echo "Glassmorphism Generator already exists in the database.\n";
    }
} else {
    echo "Error inserting tool: " . $conn->error . "\n";
}

$conn->close();
?>
