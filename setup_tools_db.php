<?php
include 'db_connect.php';

$sql1 = "CREATE TABLE IF NOT EXISTS `tool_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql2 = "CREATE TABLE IF NOT EXISTS `tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `tool_url` varchar(500) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_playground` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `tools_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tool_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql1) === TRUE) {
    echo "Table tool_categories created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

if ($conn->query($sql2) === TRUE) {
    echo "Table tools created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// Insert Tools into menu_links
$menu_sql = "INSERT INTO menu_links (name, url, position, is_custom, is_visible, order_index) 
             SELECT 'Tools', 'tools.php', 'navbar', 1, 1, 6
             WHERE NOT EXISTS (SELECT 1 FROM menu_links WHERE url = 'tools.php')";

if ($conn->query($menu_sql) === TRUE) {
    echo "Tools link added to menu_links.\n";
} else {
    echo "Error adding menu link: " . $conn->error . "\n";
}

// Check if playground category exists
$check_cat = $conn->query("SELECT id FROM tool_categories WHERE name = 'Games'");
if ($check_cat && $check_cat->num_rows == 0) {
    $conn->query("INSERT INTO tool_categories (name, icon, display_order) VALUES ('Games', 'fa-gamepad', 1)");
    $cat_id = $conn->insert_id;
    // Add playground tool
    $conn->query("INSERT INTO tools (title, description, tool_url, category_id, is_playground) VALUES ('Playground', 'Explore fun and educational games.', 'playground.php', $cat_id, 1)");
    echo "Default Playground tool added.\n";
}
?>
