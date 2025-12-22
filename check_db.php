<?php
include 'db_connect.php';

$email = 'student1@school.com';

$stmt = $conn->prepare('SELECT u.id as user_id, u.email, u.role, s.id as student_id, s.first_name, s.last_name, s.grade FROM users u LEFT JOIN students s ON u.id = s.user_id WHERE u.email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "User ID: " . $row['user_id'] . "\n";
        echo "Email: " . $row['email'] . "\n";
        echo "Role: " . $row['role'] . "\n";
        echo "Student ID: " . $row['student_id'] . "\n";
        echo "Name: " . $row['first_name'] . " " . $row['last_name'] . "\n";
        echo "Grade: " . $row['grade'] . "\n";
        echo "-----------------------\n";
    }
} else {
    echo "No user found with email $email\n";
}

$stmt->close();
$conn->close();
?>
