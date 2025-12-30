<?php
include 'db_connect.php';

$grade = 6;
$institute_name = "Test Institute via Script";
$institute_address = "123 Test St";
$institute_phone = "555-0199";
$class_day = "Monday";
$start_time = "10:00 AM";
$end_time = "12:00 PM";
$description = "Test class with logo url";
$logo_emoji = "🧪";
$class_logo = "https://placehold.co/200x200/orange/white?text=TestLogo";

$stmt = $conn->prepare("INSERT INTO classes (grade, institute_name, institute_address, institute_phone, class_day, start_time, end_time, description, logo_emoji, class_logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssssss", $grade, $institute_name, $institute_address, $institute_phone, $class_day, $start_time, $end_time, $description, $logo_emoji, $class_logo);

if ($stmt->execute()) {
    echo "Test class inserted successfully. ID: " . $stmt->insert_id;
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
