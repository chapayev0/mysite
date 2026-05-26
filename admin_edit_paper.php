<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header("Location: admin_papers.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_paper'])) {
    $title = trim($_POST['title']);
    $grade = $_POST['grade'];
    $year = $_POST['year'];
    $paper_type = $_POST['paper_type'];
    $description = trim($_POST['description']);

    if (empty($title) || empty($grade) || empty($year) || empty($paper_type)) {
        $error_msg = 'Please fill all required fields.';
    } else {
        $update_query = "UPDATE paper_archive SET title=?, grade=?, year=?, paper_type=?, description=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssissi", $title, $grade, $year, $paper_type, $description, $id);
        
        if ($stmt->execute()) {
            $success_msg = "Paper details updated successfully!";
            
            // Check if a new file is uploaded
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['file'];
                $dest_dir = __DIR__ . '/uploads/papers';
                if (!is_dir($dest_dir)) mkdir($dest_dir, 0755, true);
                
                $safe_name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($file['name']));
                $target_path = $dest_dir . '/' . time() . '_' . $safe_name;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $new_filepath = 'uploads/papers/' . basename($target_path);
                    
                    // Get old filepath to delete
                    $old_res = $conn->query("SELECT filepath FROM paper_archive WHERE id = $id");
                    if ($old_res && $old_row = $old_res->fetch_assoc()) {
                        @unlink(__DIR__ . '/' . $old_row['filepath']);
                    }
                    
                    // Update DB with new filepath
                    $conn->query("UPDATE paper_archive SET filepath = '$new_filepath' WHERE id = $id");
                    $success_msg = "Paper details and file updated successfully!";
                } else {
                    $error_msg = "Failed to upload new PDF file.";
                }
            }
        } else {
            $error_msg = "Error updating paper: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch current paper details
$stmt = $conn->prepare("SELECT * FROM paper_archive WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paper = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$paper) {
    header("Location: admin_papers.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Paper | ICT with Dilhara Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; }
        .main-content { flex: 1; padding: 3rem; margin-left: 290px; }
        .header { margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; }
        .btn-back { background: white; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; text-decoration: none; color: var(--dark); font-weight: 600; }
        .card { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 800px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: var(--dark); font-weight: 600; }
        .form-control { width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn { background: var(--primary); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-error { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <a href="admin_papers.php" class="btn-back">← Back</a>
            <h1>Edit Paper Details</h1>
        </div>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <select name="grade" class="form-control" required>
                            <?php for($i=6; $i<=13; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $paper['grade'] == $i ? 'selected' : ''; ?>>Grade <?php echo $i; ?></option>
                            <?php endfor; ?>
                            <option value="Other" <?php echo $paper['grade'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" min="1990" max="2099" value="<?php echo htmlspecialchars($paper['year']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Paper Type</label>
                    <select name="paper_type" class="form-control" required>
                        <option value="Past Paper" <?php echo $paper['paper_type'] == 'Past Paper' ? 'selected' : ''; ?>>Past Paper</option>
                        <option value="Model Paper" <?php echo $paper['paper_type'] == 'Model Paper' ? 'selected' : ''; ?>>Model Paper</option>
                        <option value="Term Test" <?php echo $paper['paper_type'] == 'Term Test' ? 'selected' : ''; ?>>Term Test</option>
                        <option value="Tutorial" <?php echo $paper['paper_type'] == 'Tutorial' ? 'selected' : ''; ?>>Tutorial</option>
                        <option value="Other" <?php echo $paper['paper_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($paper['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($paper['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Replace PDF File (Optional)</label>
                    <input type="file" name="file" class="form-control" accept="application/pdf">
                    <small style="color:var(--gray); display:block; margin-top:5px;">Leave empty to keep the current file. <a href="<?php echo htmlspecialchars($paper['filepath']); ?>" target="_blank">View current file</a></small>
                </div>
                
                <button type="submit" name="update_paper" class="btn">Update Paper</button>
            </form>
        </div>
    </div>
</body>
</html>
