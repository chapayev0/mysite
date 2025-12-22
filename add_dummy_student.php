<?php
include 'db_connect.php';

// Dummy student credentials
$email = 'student1@school.com';
$plain_password = 'student123';
$role = 'student';

// Check if user already exists
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "User with email $email already exists.\n";
    // Optionally fetch the user id
    $res = $conn->query("SELECT id FROM users WHERE email = '$email'");
    $row = $res->fetch_assoc();
    $user_id = $row['id'];
} else {
    $stmt->close();
    // Insert user
    $hashed = password_hash($plain_password, PASSWORD_DEFAULT);
    $ins = $conn->prepare('INSERT INTO users (email, password, role) VALUES (?, ?, ?)');
    $ins->bind_param('sss', $email, $hashed, $role);
    if ($ins->execute()) {
        $user_id = $ins->insert_id;
        echo "User created with ID: $user_id\n";
    } else {
        echo 'Error inserting user: ' . $conn->error . "\n";
        exit;
    }
    $ins->close();
}

// Insert student record if not exists
$chk = $conn->prepare('SELECT id FROM students WHERE user_id = ?');
$chk->bind_param('i', $user_id);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
    echo "Student record already exists for user_id $user_id.\n";
    $chk->close();
} else {
    $chk->close();
    $first = 'John';
    $last = 'Doe';
    $dob = '2008-05-15';
    $phone = '555-0101';
    $grade = '6';
    $address = '123 School St';
    $gender = 'Male';
    $parent_name = 'Jane Doe';
    $parent_contact = '555-0199';
    $parent_rel = 'Mother';

    $ins2 = $conn->prepare('INSERT INTO students (user_id, first_name, last_name, dob, phone, grade, address, gender, parent_name, parent_contact, parent_relationship) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $ins2->bind_param('issssssssss', $user_id, $first, $last, $dob, $phone, $grade, $address, $gender, $parent_name, $parent_contact, $parent_rel);
    if ($ins2->execute()) {
        echo "Student record created for user_id: $user_id\n";
    } else {
        echo 'Error inserting student: ' . $conn->error . "\n";
        exit;
    }
    $ins2->close();
}

$conn->close();

echo "\nLogin with:\nEmail: $email\nPassword: $plain_password\n";
?>
