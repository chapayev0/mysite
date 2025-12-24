<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$message = '';

// detect grade directories from classes/
$classes_dir = __DIR__ . '/classes';
$grades = [];
if (is_dir($classes_dir)) {
    $items = scandir($classes_dir);
    foreach ($items as $it) {
        if ($it === '.' || $it === '..') continue;
        if (is_dir($classes_dir . '/' . $it)) $grades[] = $it;
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? '';
    $category = $_POST['category'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($grade) || empty($category) || empty($title) || !isset($_FILES['file'])) {
        $message = 'Please fill all required fields and choose a file.';
    } else {
                // Validate file (allow any file type but keep sanitization)
                $file = $_FILES['file'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $message = 'File upload error.';
                } else {
                // Ensure destination dir exists: classes/<grade>/<category>
                $dest_dir = __DIR__ . '/classes/' . $grade . '/' . $category;
                if (!is_dir($dest_dir)) {
                    if (!mkdir($dest_dir, 0755, true)) {
                        $message = 'Failed to create destination directory.';
                    }
                }
                if (empty($message)) {
                    // sanitize filename
                    $safe_name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($file['name']));
                    $target_path = $dest_dir . '/' . $safe_name;
                    // if exists, append timestamp
                    if (file_exists($target_path)) {
                        $safe_name = time() . '_' . $safe_name;
                        $target_path = $dest_dir . '/' . $safe_name;
                    }
                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        // store record
                        $relpath = 'classes/' . $grade . '/' . $category . '/' . $safe_name;
                        $lesson_number = $_POST['lesson_number'] ?? '';
                        $stmt = $conn->prepare('INSERT INTO resources (lesson_number, title, description, filename, filepath, category, grade, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                        $stmt->bind_param('sssssssi', $lesson_number, $title, $description, $safe_name, $relpath, $category, $grade, $_SESSION['user_id']);
                        if ($stmt->execute()) {
                            $message = 'Resource uploaded successfully.';
                        } else {
                            $message = 'DB error: ' . $conn->error;
                            // rollback file
                            @unlink($target_path);
                        }
                        $stmt->close();
                    } else {
                        $message = 'Failed to move uploaded file.';
                    }
                }
            }
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resource | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Outfit',sans-serif;background:#f8fafc;margin:0;padding:20px}
        .container{max-width:900px;margin:0 auto}
        .card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(15,23,42,0.06)}
        .row{display:flex;gap:12px}
        .col{flex:1}
        label{display:block;margin-bottom:6px;font-weight:600}
        input[type=text],select,textarea{width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:8px}
        .btn{background:#0ea5e9;color:#fff;padding:10px 14px;border-radius:8px;border:0;cursor:pointer}
        .msg{margin:10px 0;padding:10px;border-radius:8px}
        .msg.success{background:#ecfccb;color:#15803d}
        .msg.error{background:#fee2e2;color:#b91c1c}
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Resource</h1>
        <div class="card">
            <?php if($message): ?>
                <div class="msg <?php echo (strpos($message,'successfully')!==false ? 'success' : 'error'); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="row" style="margin-bottom:12px">
                    <div class="col">
                        <label for="grade">Class / Grade</label>
                        <select id="grade" name="grade" required>
                            <option value="">-- Select Grade --</option>
                            <?php foreach($grades as $g): ?>
                                <option value="<?php echo htmlspecialchars($g); ?>"><?php echo htmlspecialchars($g); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="theory">Theory</option>
                            <option value="paper">Paper</option>
                            <option value="tute">Tute</option>
                            <option value="materials">Materials</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:12px">
                    <label for="lesson_number">Lesson Number</label>
                    <input type="text" id="lesson_number" name="lesson_number" placeholder="e.g. Lesson 1" required>
                </div>

                <div style="margin-bottom:12px">
                    <label for="title">Title / Name</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div style="margin-bottom:12px">
                    <label for="description">Small Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div style="margin-bottom:12px">
                    <label for="file">Choose File</label>
                    <input type="file" id="file" name="file" required>
                </div>

                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn">Upload</button>
                    <a href="admin_dashboard.php" style="align-self:center;text-decoration:none;color:#64748b">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
