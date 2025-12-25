<?php
include 'db_connect.php';

echo "Connected to database: " . $dbname . "\n";

$result = $conn->query("SHOW TABLES");
if ($result->num_rows > 0) {
    echo "Tables found:\n";
    while($row = $result->fetch_row()) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "No tables found in database $dbname.\n";
}

// Check for users
if ($conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0) {
    $count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    echo "Users count: $count\n";
}
?>
