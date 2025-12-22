<?php
// Simple CLI test for authentication logic
include 'db_connect.php';

$identifier = $argv[1] ?? 'student1@school.com';
$password = $argv[2] ?? 'student123';

$stmt = $conn->prepare('SELECT id, email, IFNULL(username, "") as username, password, role FROM users WHERE email = ? OR username = ? LIMIT 1');
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows == 1) {
    $row = $res->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        echo "AUTH OK: id={$row['id']} email={$row['email']} username={$row['username']} role={$row['role']}\n";
    } else {
        echo "AUTH FAIL: invalid password\n";
    }
} else {
    echo "AUTH FAIL: user not found\n";
}
$stmt->close();
$conn->close();
?>
