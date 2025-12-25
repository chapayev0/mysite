<?php
session_start();
include 'db_connect.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get class ID
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$class_id) {
    header("Location: admin_classes.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_class'])) {
    $grade = $_POST['grade'];
    $institute_name = $_POST['institute_name'];
    $institute_address = $_POST['institute_address'];
    $institute_phone = $_POST['institute_phone'];
    $class_day = $_POST['class_day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];
    $logo_emoji = $_POST['logo_emoji'];

    $stmt = $conn->prepare("UPDATE classes SET grade=?, institute_name=?, institute_address=?, institute_phone=?, class_day=?, start_time=?, end_time=?, description=?, logo_emoji=? WHERE id=?");
    $stmt->bind_param("issssssssi", $grade, $institute_name, $institute_address, $institute_phone, $class_day, $start_time, $end_time, $description, $logo_emoji, $class_id);

    if ($stmt->execute()) {
        $success_msg = "Class updated successfully!";
        // Refresh data
    } else {
        $error_msg = "Error updating class: " . $conn->error;
    }
    $stmt->close();
}

// Fetch class data
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();
$stmt->close();

if (!$class) {
    echo "Class not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class | TechLearn Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
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
            position: fixed;
            left: 0;
            top: 0;
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
            margin-left: 290px;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 800px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
        }
        .btn {
            background: var(--primary);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            margin-left: 1rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .alert-success {
            background: #D1FAE5;
            color: #065F46;
        }
        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1>Edit Class</h1>
        </div>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST">
                <div style="display: flex; gap: 2rem;">
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Grade</label>
                            <select name="grade" class="form-control" required>
                                <option value="6" <?php if($class['grade'] == 6) echo 'selected'; ?>>Grade 6</option>
                                <option value="7" <?php if($class['grade'] == 7) echo 'selected'; ?>>Grade 7</option>
                                <option value="8" <?php if($class['grade'] == 8) echo 'selected'; ?>>Grade 8</option>
                                <option value="9" <?php if($class['grade'] == 9) echo 'selected'; ?>>Grade 9</option>
                                <option value="10" <?php if($class['grade'] == 10) echo 'selected'; ?>>Grade 10 (O/L)</option>
                                <option value="11" <?php if($class['grade'] == 11) echo 'selected'; ?>>Grade 11 (A/L)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Institute Name</label>
                            <input type="text" name="institute_name" class="form-control" value="<?php echo htmlspecialchars($class['institute_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Institute Address</label>
                            <input type="text" name="institute_address" class="form-control" value="<?php echo htmlspecialchars($class['institute_address']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="institute_phone" class="form-control" value="<?php echo htmlspecialchars($class['institute_phone']); ?>" required>
                        </div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="form-group">
                            <label class="form-label">Class Day</label>
                            <select name="class_day" class="form-control" required>
                                <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                foreach ($days as $day) {
                                    $selected = ($class['class_day'] == $day) ? 'selected' : '';
                                    echo "<option value='$day' $selected>$day</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Start Time</label>
                                <input type="text" name="start_time" class="form-control" value="<?php echo htmlspecialchars($class['start_time']); ?>" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">End Time</label>
                                <input type="text" name="end_time" class="form-control" value="<?php echo htmlspecialchars($class['end_time']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Logo Emoji</label>
                            <input type="text" name="logo_emoji" class="form-control" value="<?php echo htmlspecialchars($class['logo_emoji']); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($class['description']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="update_class" class="btn">Update Class</button>
                <a href="admin_classes.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
