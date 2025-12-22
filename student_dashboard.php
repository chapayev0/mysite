<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // Should ideally not happen if data integrity is maintained
    $error = "Student profile not found. Please contact admin.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | TechLearn</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light);
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: var(--dark);
            color: white;
            min-height: 100vh;
            padding: 2rem;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            color: var(--primary);
        }
        .nav-link {
            display: block;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 1rem 0;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            font-weight: 600;
        }
        .main-content {
            flex: 1;
            padding: 3rem;
        }
        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 800px;
        }
        h2 {
            margin-top: 0;
            color: var(--dark);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .profile-item {
            margin-bottom: 1rem;
        }
        .label {
            display: block;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .value {
            color: var(--dark);
            font-weight: 600;
            font-size: 1.1rem;
        }
        .full-width {
            grid-column: span 2;
        }
        .section-header {
            grid-column: span 2;
            color: var(--primary);
            font-weight: 700;
            margin-top: 1rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">TechLearn Student</div>
        <nav>
            <a href="student_dashboard.php" class="nav-link active">My Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </nav>
    </div>
    <div class="main-content">
        <div class="card">
            <?php if(isset($student)): ?>
                <h2>My Profile</h2>
                
                <div class="profile-grid">
                    <div class="section-header">Personal Information</div>
                    
                    <div class="profile-item">
                        <span class="label">First Name</span>
                        <div class="value"><?php echo htmlspecialchars($student['first_name']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Last Name</span>
                        <div class="value"><?php echo htmlspecialchars($student['last_name']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Grade</span>
                        <div class="value"><?php echo htmlspecialchars($student['grade']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Date of Birth</span>
                        <div class="value"><?php echo htmlspecialchars($student['dob']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Phone</span>
                        <div class="value"><?php echo htmlspecialchars($student['phone']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Gender</span>
                        <div class="value"><?php echo htmlspecialchars($student['gender']); ?></div>
                    </div>
                    <div class="profile-item full-width">
                        <span class="label">Address</span>
                        <div class="value"><?php echo htmlspecialchars($student['address']); ?></div>
                    </div>

                    <div class="section-header">Parent Information</div>
                    
                    <div class="profile-item">
                        <span class="label">Parent Name</span>
                        <div class="value"><?php echo htmlspecialchars($student['parent_name']); ?></div>
                    </div>
                    <div class="profile-item">
                        <span class="label">Relationship</span>
                        <div class="value"><?php echo htmlspecialchars($student['parent_relationship']); ?></div>
                    </div>
                    <div class="profile-item full-width">
                        <span class="label">Parent Contact</span>
                        <div class="value"><?php echo htmlspecialchars($student['parent_contact']); ?></div>
                    </div>
                </div>
            <?php else: ?>
                <p>Profile information unavailable.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
