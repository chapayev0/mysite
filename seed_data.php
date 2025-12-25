<?php
include 'db_connect.php';

echo "Seeding database...\n";

// 1. Ensure Admin exists
$admin_user = 'admin';
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$check_admin = $conn->query("SELECT id FROM users WHERE username = '$admin_user'");
if ($check_admin->num_rows == 0) {
    $conn->query("INSERT INTO users (username, password, role) VALUES ('$admin_user', '$admin_pass', 'admin')");
    echo "Created Admin User: admin / admin123\n";
} else {
    echo "Admin user user already exists.\n";
}

// 2. Create Dummy Student
$student_user = 'student';
$student_pass = password_hash('student123', PASSWORD_DEFAULT);
$check_student = $conn->query("SELECT id FROM users WHERE username = '$student_user'");
$student_id = 0;

if ($check_student->num_rows == 0) {
    $conn->query("INSERT INTO users (username, password, role) VALUES ('$student_user', '$student_pass', 'student')");
    $student_id = $conn->insert_id;
    echo "Created Student User: student / student123\n";

    // Create Student Profile
    $sql_profile = "INSERT INTO students (user_id, first_name, last_name, dob, phone, grade, address, gender, parent_name, parent_contact, parent_relationship) 
                    VALUES ('$student_id', 'John', 'Doe', '2010-05-15', '0771234567', 'Grade 10', '123 School Lane, Colombo', 'Male', 'Jane Doe', '0777654321', 'Mother')";
    if ($conn->query($sql_profile)) {
        echo "Created Student Profile for John Doe.\n";
    } else {
        echo "Error creating student profile: " . $conn->error . "\n";
    }
} else {
    $student_id = $check_student->fetch_assoc()['id'];
    echo "Student user already exists.\n";
}

// 3. Create Dummy Resources
// We need an admin ID for 'uploaded_by'
$admin_id = $conn->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch_assoc()['id'];

$resources = [
    ['Lesson 1', 'Introduction to Science', 'Basic concepts of science.', 'science_intro.pdf', 'classes/Grade 10/theory/science_intro.pdf', 'theory', 'Grade 10'],
    ['Lesson 2', 'Algebra Basics', 'Introduction to Algebra.', 'math_algebra.pdf', 'classes/Grade 10/tute/math_algebra.pdf', 'tute', 'Grade 10'],
    ['Paper 2023', 'History Past Paper', '2023 Term test paper.', 'history_2023.pdf', 'classes/Grade 11/paper/history_2023.pdf', 'paper', 'Grade 11']
];

foreach ($resources as $res) {
    // Check if exists
    $check_res = $conn->query("SELECT id FROM resources WHERE title = '$res[1]'");
    if ($check_res->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO resources (lesson_number, title, description, filename, filepath, category, grade, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $res[0], $res[1], $res[2], $res[3], $res[4], $res[5], $res[6], $admin_id);
        if ($stmt->execute()) {
            echo "Added resource: $res[1]\n";
        } else {
            echo "Error adding resource: " . $conn->error . "\n";
        }
    } else {
        echo "Resource '$res[1]' already exists.\n";
    }
}

echo "Seeding complete.\n";
?>
