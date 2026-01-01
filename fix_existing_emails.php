<?php
// Use local XAMPP credentials as db_connect.php has production credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school_db"; // Assuming school_db based on setup_db.php content

echo "Connecting to $dbname on $servername...\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully. Starting email cleanup...\n";

// Update existing empty string emails to NULL
$sql = "UPDATE users SET email = NULL WHERE email = ''";

if ($conn->query($sql) === TRUE) {
    echo "Successfully updated empty emails to NULL. Rows affected: " . $conn->affected_rows . "\n";
} else {
    echo "Error updating emails: " . $conn->error . "\n";
}

$conn->close();
?>
