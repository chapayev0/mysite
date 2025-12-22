<?php
include 'db_connect.php';

$email = 'student1@school.com';
$new = 'student1';

$stmt = $conn->prepare('UPDATE users SET username = ? WHERE email = ?');
$stmt->bind_param('ss', $new, $email);
if ($stmt->execute()) {
    echo "Updated username to $new for $email\n";
} else {
    echo "Error updating username: " . $conn->error . "\n";
}
$stmt->close();
$conn->close();
?>
