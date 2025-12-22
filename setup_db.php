<?php
include 'db_connect.php';

// Create Users Table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating table 'users': " . $conn->error . "<br>";
}

// Create Students Table
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    phone VARCHAR(20),
    grade VARCHAR(50),
    address TEXT,
    gender ENUM('Male', 'Female', 'Other'),
    parent_name VARCHAR(100),
    parent_contact VARCHAR(20),
    parent_relationship VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql_students) === TRUE) {
    echo "Table 'students' created successfully.<br>";
} else {
    echo "Error creating table 'students': " . $conn->error . "<br>";
}

// Create Default Admin
$admin_email = "admin@school.com";
$admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
$admin_role = "admin";

$check_admin = "SELECT * FROM users WHERE email = '$admin_email'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    $sql_admin = "INSERT INTO users (email, password, role) VALUES ('$admin_email', '$admin_pass', '$admin_role')";
    if ($conn->query($sql_admin) === TRUE) {
        echo "Default admin created successfully.<br>";
    } else {
        echo "Error creating default admin: " . $conn->error . "<br>";
    }
} else {
    echo "Default admin already exists.<br>";
}

$conn->close();
?>
