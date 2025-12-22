<?php
include 'db_connect.php';

// Add username column if it doesn't exist
$check = "SHOW COLUMNS FROM users LIKE 'username'";
$res = $conn->query($check);

if ($res->num_rows == 0) {
    $alter = "ALTER TABLE users ADD COLUMN username VARCHAR(100) UNIQUE NULL";
    if ($conn->query($alter) === TRUE) {
        echo "Added 'username' column to users table.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
        exit;
    }
} else {
    echo "'username' column already exists.\n";
}

// Set a username for existing users if empty (use local part of email)
$sql = "SELECT id, email, username FROM users";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (empty($row['username'])) {
            $local = explode('@', $row['email'])[0];
            $local = preg_replace('/[^a-zA-Z0-9_]/', '', $local);
            // ensure uniqueness
            $candidate = $local;
            $i = 1;
            while (true) {
                $check = $conn->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
                $check->bind_param('si', $candidate, $row['id']);
                $check->execute();
                $check->store_result();
                if ($check->num_rows == 0) {
                    $check->close();
                    break;
                }
                $check->close();
                $candidate = $local . $i;
                $i++;
            }
            $upd = $conn->prepare('UPDATE users SET username = ? WHERE id = ?');
            $upd->bind_param('si', $candidate, $row['id']);
            $upd->execute();
            $upd->close();
            echo "Set username for user id {$row['id']} to {$candidate}\n";
        }
    }
}

$conn->close();
?>
