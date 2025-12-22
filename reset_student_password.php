<?php
include 'db_connect.php';

$email = 'student1@school.com';
$new = password_hash('student123', PASSWORD_DEFAULT);

$stmt = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
$stmt->bind_param('ss', $new, $email);
if ($stmt->execute()) {
    echo "Password reset for $email\n";
} else {
    echo "Error resetting password: " . $conn->error . "\n";
}
$stmt->close();
$conn->close();
?>
