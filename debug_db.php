<?php
$servername = "127.0.0.1"; // Try IP to force TCP
$username = "root";
$password = "";

echo "Attempting connection to $servername...\n";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully to MySQL server.\n";
echo "Host info: " . $conn->host_info . "\n";
echo "Server info: " . $conn->server_info . "\n";

// List all databases
echo "\nAvailable Databases:\n";
$result = $conn->query("SHOW DATABASES");
$found_school_db = false;
while($row = $result->fetch_row()) {
    echo "- " . $row[0] . "\n";
    if ($row[0] == 'school_db') {
        $found_school_db = true;
    }
}

if ($found_school_db) {
    echo "\nDatabase 'school_db' FOUND.\n";
    $conn->select_db('school_db');
    echo "Tables in 'school_db':\n";
    $tables = $conn->query("SHOW TABLES");
    if ($tables->num_rows > 0) {
        while($t = $tables->fetch_row()) {
            echo "- " . $t[0] . " (" . $conn->query("SELECT COUNT(*) FROM " . $t[0])->fetch_row()[0] . " rows)\n";
        }
    } else {
        echo "No tables found in school_db!\n";
    }
} else {
    echo "\nDatabase 'school_db' NOT FOUND.\n";
}

$conn->close();
?>
