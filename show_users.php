<?php
include 'db_connect.php';
$res = $conn->query('SELECT id, email, username FROM users');
if ($res) {
    while ($r = $res->fetch_assoc()) {
        echo "id={$r['id']} email={$r['email']} username=" . ($r['username'] ?? 'NULL') . "\n";
    }
} else {
    echo 'Query error: ' . $conn->error . "\n";
}
$conn->close();
?>
