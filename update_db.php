<?php
include 'db_connect.php';

// Add username column if it doesn't exist
$sql_check = "SHOW COLUMNS FROM users LIKE 'username'";
$result = $conn->query($sql_check);

if ($result->num_rows == 0) {
    // Add username column
    $sql_alter = "ALTER TABLE users ADD COLUMN username VARCHAR(255) UNIQUE AFTER id";
    if ($conn->query($sql_alter) === TRUE) {
        echo "Column 'username' added successfully.<br>";
        
        // Update existing admin
        $sql_update_admin = "UPDATE users SET username = 'admin' WHERE role = 'admin'";
        if ($conn->query($sql_update_admin) === TRUE) {
            echo "Admin username set to 'admin'.<br>";
        } else {
            echo "Error setting admin username: " . $conn->error . "<br>";
        }

        // Make email nullable (optional, but good practice if not needed for login)
        // For now, we'll keep email mandatory as per original strictness, 
        // but we might want to make it non-unique if we drop it for students later.
        // Let's modify email to be NOT UNIQUE if desired, but sticking to plan: just add username.
        
    } else {
        echo "Error adding 'username' column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'username' already exists.<br>";
}

$conn->close();
?>
