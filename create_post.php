<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publish'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $student_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($content)) {
        // Handle Featured Image Upload
        $image_path = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $uploadDir = 'uploads/wall_thumbnails/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['featured_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                $image_path = $targetPath;
            }
        }

        $stmt = $conn->prepare("INSERT INTO wall_posts (student_id, title, content, image_path, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isss", $student_id, $title, $content, $image_path);
        if ($stmt->execute()) {
            // Redirect with success message (maybe add query param)
            header("Location: wall_of_talent.php?msg=pending");
            exit();
        } else {
            $error = "Failed to publish post.";
        }
    } else {
        $error = "Title and content are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | Wall of Talent</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Optional for icons -->
    <style>
        :root { --primary: #0066FF; --secondary: #7C3AED; --dark: #0F172A; --light: #F8FAFC; --gray: #64748B; }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; }
        .main-content { flex: 1; padding: 3rem; margin-left: 250px; }
        
        .card { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        .label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark); }
        .input-text { width: 100%; padding: 0.8rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box; }
        
        /* Toolbar */
        .toolbar {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: #f1f5f9;
            border-radius: 8px 8px 0 0;
            border: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .tool-btn {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .tool-btn:hover { background: #e2e8f0; }

        /* Editor */
        #editor {
            min-height: 400px;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 8px 8px;
            padding: 1rem;
            outline: none;
            overflow-y: auto;
            border-top: none;
        }
        #editor img { max-width: 100%; height: auto; display: block; margin: 1rem 0; }

        .btn-publish {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
            width: 100%;
        }
        .btn-publish:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <?php include 'student_sidebar.php'; ?>
    <div class="main-content">
        <div class="card">
            <h1 style="margin-top: 0;">Create New Post</h1>
            <p style="color: var(--gray); margin-bottom: 2rem;">Posts will be reviewed by admin before appearing on the public wall.</p>
            
            <form method="POST" id="postForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="label">Title</label>
                    <input type="text" name="title" class="input-text" required placeholder="Enter post title...">
                </div>

                <div class="form-group">
                    <label class="label">Featured Image (Thumbnail)</label>
                    <input type="file" name="featured_image" class="input-text" accept="image/*">
                    <small style="color: var(--gray);">This image will be shown on the Wall card.</small>
                </div>

                <div class="form-group">
                    <label class="label">Content</label>
                    
                    <div class="toolbar">
                        <button type="button" class="tool-btn" onclick="execCmd('bold')"><b>B</b></button>
                        <button type="button" class="tool-btn" onclick="execCmd('italic')"><i>I</i></button>
                        <button type="button" class="tool-btn" onclick="execCmd('justifyLeft')">Left</button>
                        <button type="button" class="tool-btn" onclick="execCmd('justifyCenter')">Center</button>
                        <button type="button" class="tool-btn" onclick="execCmd('justifyRight')">Right</button>
                        <select class="tool-btn" onchange="execCmd('fontSize', this.value)">
                            <option value="3">Normal</option>
                            <option value="5">Large</option>
                            <option value="7">Huge</option>
                        </select>
                        <button type="button" class="tool-btn" onclick="triggerImageUpload()">Insert Image</button>
                    </div>

                    <div id="editor" contenteditable="true"></div>
                    <textarea name="content" id="hiddenContent" style="display:none;"></textarea>
                    <input type="file" id="imageInput" style="display: none;" accept="image/*" onchange="uploadImage(this)">
                </div>

                <button type="submit" name="publish" class="btn-publish">Publish Post</button>
            </form>
        </div>
    </div>

    <script>
        function execCmd(command, value = null) {
            document.execCommand(command, false, value);
        }

        function triggerImageUpload() {
            document.getElementById('imageInput').click();
        }

        function uploadImage(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('file', input.files[0]);

                fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.location) {
                        const imgHtml = `<img src="${data.location}" alt="Uploaded Image">`;
                        document.execCommand('insertHTML', false, imgHtml);
                    } else {
                        alert('Upload failed: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Upload failed.');
                });
            }
        }

        document.getElementById('postForm').onsubmit = function() {
            document.getElementById('hiddenContent').value = document.getElementById('editor').innerHTML;
        };
    </script>
</body>
</html>
