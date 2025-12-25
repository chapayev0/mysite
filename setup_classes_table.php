<?php
include 'db_connect.php';

// Create classes table
$sql = "CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade INT NOT NULL,
    institute_name VARCHAR(255) NOT NULL,
    institute_address VARCHAR(255) NOT NULL,
    institute_phone VARCHAR(50) NOT NULL,
    class_day VARCHAR(50) NOT NULL,
    start_time VARCHAR(50) NOT NULL,
    end_time VARCHAR(50) NOT NULL,
    description TEXT,
    logo_emoji VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'classes' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
    exit;
}

// Clear existing data to avoid duplicates during dev
$conn->query("TRUNCATE TABLE classes");

// Seed data for Grades 6-11
$grades = [6, 7, 8, 9, 10, 11];
$institutes = ['Institute A', 'Institute B'];
$logos = ['🏫', '🏛️'];

$stmt = $conn->prepare("INSERT INTO classes (grade, institute_name, institute_address, institute_phone, class_day, start_time, end_time, description, logo_emoji) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($grades as $grade) {
    // Data for Institute A
    $inst_name = "Institute A";
    $address = "123 Main Street, Colombo 05";
    $phone = "+94 77 123 4567";
    $day = "Saturday";
    $start = "8:00 AM";
    $end = "10:00 AM";
    $desc = "Join our vibrant Grade $grade batch at Institute A. We focus on building a strong foundation in ICT with practical sessions.";
    $logo = "🏫";
    
    $stmt->bind_param("issssssss", $grade, $inst_name, $address, $phone, $day, $start, $end, $desc, $logo);
    $stmt->execute();

    // Data for Institute B
    $inst_name = "Institute B";
    $address = "456 High Level Road, Nugegoda";
    $phone = "+94 71 987 6543";
    $day = "Sunday";
    $start = "2:30 PM";
    $end = "4:30 PM";
    $desc = "Institute B offers state-of-the-art facilities for Grade $grade students. Our exam-focused approach ensures excellent results.";
    $logo = "🏛️";
    
    $stmt->bind_param("issssssss", $grade, $inst_name, $address, $phone, $day, $start, $end, $desc, $logo);
    $stmt->execute();
    
    echo "Seeded Grade $grade classes.\n";
}

echo "All classes seeded successfully.\n";
$stmt->close();
$conn->close();
?>
