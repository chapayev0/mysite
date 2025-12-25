<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop database if exists to clean up
$new_db_name = "school_db";
$sql_drop = "DROP DATABASE IF EXISTS $new_db_name";
if ($conn->query($sql_drop) === TRUE) {
    echo "Database $new_db_name dropped/cleared.\n";
} else {
    echo "Error dropping database: " . $conn->error . "\n";
}

// Create database
$sql = "CREATE DATABASE $new_db_name";
if ($conn->query($sql) === TRUE) {
    echo "Database $new_db_name created successfully.\n";
} else {
    die("Error creating database: " . $conn->error . "\n");
}

// Select database
$conn->select_db($new_db_name);

// Create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";

if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully.\n";
} else {
    die("Error creating table 'users': " . $conn->error . "\n");
}

// Create students table
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE,
    phone VARCHAR(20),
    grade VARCHAR(20),
    address TEXT,
    gender VARCHAR(10),
    parent_name VARCHAR(100),
    parent_contact VARCHAR(20),
    parent_relationship VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB";

if ($conn->query($sql_students) === TRUE) {
    echo "Table 'students' created successfully.\n";
} else {
    die("Error creating table 'students': " . $conn->error . "\n");
}

// Create resources table
$sql_resources = "CREATE TABLE IF NOT EXISTS resources (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    lesson_number VARCHAR(50),
    title VARCHAR(100) NOT NULL,
    description TEXT,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    grade VARCHAR(20),
    uploaded_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB";

if ($conn->query($sql_resources) === TRUE) {
    echo "Table 'resources' created successfully.\n";
} else {
    die("Error creating table 'resources': " . $conn->error . "\n");
}

// Create default admin user
$admin_user = 'admin';
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$admin_role = 'admin';

// Check if admin exists
$check_admin = "SELECT * FROM users WHERE username = '$admin_user'";
$result = $conn->query($check_admin);

if ($result && $result->num_rows == 0) {
    $sql_admin = "INSERT INTO users (username, password, role) VALUES ('$admin_user', '$admin_pass', '$admin_role')";
    if ($conn->query($sql_admin) === TRUE) {
        echo "Default admin user created successfully.\n";
    } else {
        echo "Error creating admin user: " . $conn->error . "\n";
    }
} else {
    echo "Admin user already exists or error checking: " . $conn->error . "\n";
}

$conn->close();
?>
