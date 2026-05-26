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

$success_msg = '';
$error_msg = '';

// Handle Delete Paper
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_paper'])) {
    $paper_id = $_POST['paper_id'];
    
    // Get file path first
    $stmt = $conn->prepare("SELECT filepath FROM paper_archive WHERE id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $file_data = $res->fetch_assoc();
    $stmt->close();
    
    if ($file_data) {
        $filepath = __DIR__ . '/' . $file_data['filepath'];
        
        // Delete from DB
        $stmt = $conn->prepare("DELETE FROM paper_archive WHERE id = ?");
        $stmt->bind_param("i", $paper_id);
        if ($stmt->execute()) {
            // Delete file
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
            $success_msg = "Paper deleted successfully!";
        } else {
            $error_msg = "Error deleting paper: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Add Paper
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_paper'])) {
    $title = trim($_POST['title']);
    $grade = $_POST['grade'];
    $year = $_POST['year'];
    $paper_type = $_POST['paper_type'];
    $description = trim($_POST['description']);

    if (empty($title) || empty($grade) || empty($year) || empty($paper_type)) {
        $error_msg = 'Please fill all required fields.';
    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'Please choose a valid PDF file.';
    } else {
        $file = $_FILES['file'];
        // Destination
        $dest_dir = __DIR__ . '/uploads/papers';
        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }
        
        $safe_name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($file['name']));
        $target_path = $dest_dir . '/' . $safe_name;
        
        // Unique name
        if (file_exists($target_path)) {
            $safe_name = time() . '_' . $safe_name;
            $target_path = $dest_dir . '/' . $safe_name;
        }
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $filepath = 'uploads/papers/' . $safe_name;
            
            $stmt = $conn->prepare("INSERT INTO paper_archive (title, grade, year, paper_type, description, filepath, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssi", $title, $grade, $year, $paper_type, $description, $filepath, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $success_msg = "Paper uploaded successfully!";
            } else {
                $error_msg = "DB Error: " . $conn->error;
                @unlink(__DIR__ . '/' . $filepath);
            }
            $stmt->close();
        } else {
            $error_msg = "Failed to move uploaded file.";
        }
    }
}

// Fetch all papers
$papers = [];
$result = $conn->query("SELECT p.*, u.username as uploader_name FROM paper_archive p LEFT JOIN users u ON p.uploaded_by = u.id ORDER BY p.year DESC, p.grade ASC, p.id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $papers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Paper Archive | ICT with Dilhara Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --danger: #EF4444;
            --success: #10B981;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; }
        .main-content { flex: 1; padding: 3rem; margin-left: 290px; }
        .header { margin-bottom: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .tabs { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .tab-btn { background: white; border: 1px solid #e2e8f0; padding: 0.8rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--gray); transition: all 0.3s; }
        .tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: var(--dark); font-weight: 600; }
        .form-control { width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn { background: var(--primary); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: var(--danger); }
        .btn-edit { background: var(--secondary); margin-right: 0.5rem; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        th { color: var(--gray); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-error { background: #FEE2E2; color: #991B1B; }
        .hidden { display: none; }
        .badge { padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600; background: #E2E8F0; color: #475569; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Paper Archive</h1>
            <p style="color: var(--gray);">Upload and manage past papers, model papers, and term tests.</p>
        </div>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('view')">View Papers</button>
            <button class="tab-btn" onclick="switchTab('add')">Add New Paper</button>
        </div>
        
        <!-- View Tab -->
        <div id="view-tab">
            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Grade & Year</th>
                                <th>Paper Info</th>
                                <th>Type</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($papers)): ?>
                                <tr><td colspan="5" style="text-align:center; color:var(--gray);">No papers found in the archive.</td></tr>
                            <?php else: ?>
                                <?php foreach ($papers as $p): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:700;">Grade <?php echo htmlspecialchars($p['grade']); ?></div>
                                            <div style="font-size:0.9rem; color:var(--gray);"><?php echo htmlspecialchars($p['year']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600;"><?php echo htmlspecialchars($p['title']); ?></div>
                                            <div style="font-size:0.8rem; color: #64748B;">Uploaded by: <?php echo htmlspecialchars($p['uploader_name'] ?? 'Unknown'); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge"><?php echo htmlspecialchars($p['paper_type']); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo htmlspecialchars($p['filepath']); ?>" target="_blank" style="color:var(--primary); text-decoration:none;">View PDF</a>
                                        </td>
                                        <td>
                                            <div style="display:flex;">
                                                <a href="admin_edit_paper.php?id=<?php echo $p['id']; ?>" class="btn btn-edit">Edit</a>
                                                <form method="POST" onsubmit="return confirm('Delete this paper permanently?');">
                                                    <input type="hidden" name="paper_id" value="<?php echo $p['id']; ?>">
                                                    <button type="submit" name="delete_paper" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Add Tab -->
        <div id="add-tab" class="hidden">
            <div class="card">
                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Grade</label>
                            <select name="grade" class="form-control" required>
                                <option value="">Select Grade</option>
                                <?php for($i=6; $i<=13; $i++): ?>
                                    <option value="<?php echo $i; ?>">Grade <?php echo $i; ?></option>
                                <?php endfor; ?>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" min="1990" max="2099" value="<?php echo date('Y'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Paper Type</label>
                        <select name="paper_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Past Paper">Past Paper</option>
                            <option value="Model Paper">Model Paper</option>
                            <option value="Term Test">Term Test</option>
                            <option value="Tutorial">Tutorial</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. 2023 Grade 10 Term 1 ICT Paper" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description or instructions..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">PDF File</label>
                        <input type="file" name="file" class="form-control" accept="application/pdf" required>
                    </div>
                    
                    <button type="submit" name="add_paper" class="btn">Upload Paper</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tabName) {
            document.getElementById('view-tab').classList.add('hidden');
            document.getElementById('add-tab').classList.add('hidden');
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
