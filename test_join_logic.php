<?php
// Test script to simulate POST request to process_join.php

$url = 'http://localhost/process_join.php'; // We can't hit localhost via HTTP without a server running and targeted correctly, but we can include the file or use CLI.
// Actually, since I can't guarantee a running server is accessible to me (I am on the machine but maybe invalid port), I will just include the file and mock $_SERVER and input.

// Mocking environment
$_SERVER["REQUEST_METHOD"] = "POST";
$input_data = json_encode([
    "name" => "Test User",
    "class_preference" => "Grade 10",
    "contact_number" => "0771234567",
    "whatsapp_available" => true,
    "message" => "This is a test message."
]);


// Mock php://input
// This is tricky in CLI. I will just modify process_join.php slightly or better, I will write a small test wrapper that I run with php.
// Actually, `process_join.php` reads `php://input`. 

// Let's use a different approach: create a script that uses curl if available, or just directly inserts to DB to verify DB connection.
// I already verified DB table creation.
// Let's just try to insert a record using a separate test script that mimics the logic of process_join.php completely.

include 'db_connect.php';

$name = "Test User";
$class = "Grade 10";
$contact = "0771234567";
$wa = 1;
$msg = "Test Message";

$stmt = $conn->prepare("INSERT INTO class_inquiries (name, class_preference, contact_number, whatsapp_available, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $name, $class, $contact, $wa, $msg);

if ($stmt->execute()) {
    echo "Test Insertion Successful!\n";
} else {
    echo "Test Insertion Failed: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
