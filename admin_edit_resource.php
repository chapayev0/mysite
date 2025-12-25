<?php
session_start();
include 'db_connect.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$resource_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$resource_id) {
    header("Location: admin_resources.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_resource'])) {
    $title = trim($_POST['title']);
    $lesson_number = trim($_POST['lesson_number']);
    $description = trim($_POST['description']);
    // Grade and Category changes would require moving files, so we'll keep it simple for now and only allow editing metadata unless a new file is uploaded, or implementing file move logic.
    // Let's implement full move logic for completeness.
    $new_grade = $_POST['grade'];
    $new_category = $_POST['category'];

    // Get current data
    $stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $current_res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $file_change = isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK;
    $path_change = ($new_grade != $current_res['grade'] || $new_category != $current_res['category']);
    
    $final_filepath = $current_res['filepath'];
    $final_filename = $current_res['filename'];
    
    // Logic:
    // 1. If File Change -> Upload new file to New Path, Delete old file.
    // 2. If NO File Change but Path Change -> Move old file to New Path.
    // 3. Update DB.

    $target_dir = __DIR__ . '/classes/' . $new_grade . '/' . $new_category;
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if ($file_change) {
        $file = $_FILES['file'];
        $safe_name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($file['name']));
        $target_path = $target_dir . '/' . $safe_name;
        
        if (file_exists($target_path)) {
            $safe_name = time() . '_' . $safe_name;
            $target_path = $target_dir . '/' . $safe_name;
        }

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Delete old file
            $old_path = __DIR__ . '/' . $current_res['filepath'];
            if (file_exists($old_path)) {
                @unlink($old_path);
            }
            $final_filepath = 'classes/' . $new_grade . '/' . $new_category . '/' . $safe_name;
            $final_filename = $safe_name;
        } else {
            $error_msg = "Failed to upload new file.";
        }
    } elseif ($path_change) {
        // Move existing file
        $old_full_path = __DIR__ . '/' . $current_res['filepath'];
        $safe_name = $current_res['filename'];
        $target_path = $target_dir . '/' . $safe_name;
        
        // Handle name collision in new dir
        if (file_exists($target_path)) {
            $safe_name = time() . '_' . $safe_name;
            $target_path = $target_dir . '/' . $safe_name;
        }

        if (rename($old_full_path, $target_path)) {
            $final_filepath = 'classes/' . $new_grade . '/' . $new_category . '/' . $safe_name;
            $final_filename = $safe_name;
        } else {
            $error_msg = "Failed to move file to new folder.";
        }
    }

    if (empty($error_msg)) {
        $stmt = $conn->prepare("UPDATE resources SET grade=?, category=?, lesson_number=?, title=?, description=?, filename=?, filepath=? WHERE id=?");
        $stmt->bind_param("issssssi", $new_grade, $new_category, $lesson_number, $title, $description, $final_filename, $final_filepath, $resource_id);
        
        if ($stmt->execute()) {
            $success_msg = "Resource updated successfully!";
        } else {
            $error_msg = "DB Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch (refreshed) data
$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$resource = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$resource) {
    echo "Resource not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resource | TechLearn Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; }
        .sidebar { width: 250px; background: var(--dark); color: white; min-height: 100vh; padding: 2rem; position: fixed; left: 0; top: 0; }
        .logo { font-size: 1.5rem; font-weight: 800; margin-bottom: 3rem; color: var(--primary); }
        .nav-link { display: block; color: rgba(255,255,255,0.7); text-decoration: none; padding: 1rem 0; transition: color 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; font-weight: 600; }
        .main-content { flex: 1; padding: 3rem; margin-left: 290px; }
        .card { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 800px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: var(--dark); font-weight: 600; }
        .form-control { width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn { background: var(--primary); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); margin-left: 1rem; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-error { background: #FEE2E2; color: #991B1B; }
        .current-file { background: #f1f5f9; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1>Edit Resource</h1>
        </div>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label class="form-label">Grade</label>
                    <select name="grade" class="form-control" required>
                        <?php for($i=6; $i<=11; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if($resource['grade'] == $i) echo 'selected'; ?>>Grade <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control" required>
                        <option value="theory" <?php if($resource['category'] == 'theory') echo 'selected'; ?>>Theory</option>
                        <option value="paper" <?php if($resource['category'] == 'paper') echo 'selected'; ?>>Paper</option>
                        <option value="tute" <?php if($resource['category'] == 'tute') echo 'selected'; ?>>Tute</option>
                        <option value="materials" <?php if($resource['category'] == 'materials') echo 'selected'; ?>>Materials</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lesson Number</label>
                    <input type="text" name="lesson_number" class="form-control" value="<?php echo htmlspecialchars($resource['lesson_number']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($resource['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($resource['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Current File</label>
                    <div class="current-file">
                        <strong>Filename:</strong> <?php echo htmlspecialchars($resource['filename']); ?><br>
                        <strong>Path:</strong> <?php echo htmlspecialchars($resource['filepath']); ?>
                    </div>
                    <label class="form-label">Replace File (Optional)</label>
                    <input type="file" name="file" class="form-control">
                </div>
                
                <button type="submit" name="update_resource" class="btn">Update Resource</button>
                <a href="admin_resources.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
