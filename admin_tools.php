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

// Create upload directory if it doesn't exist
$upload_dir = 'uploads/tools/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fetch categories for dropdown
$categories_res = $conn->query("SELECT * FROM tool_categories ORDER BY display_order ASC");
$categories = [];
while ($cat = $categories_res->fetch_assoc()) {
    $categories[] = $cat;
}

// Handle Add Tool
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tool'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $tool_url = trim($_POST['tool_url']);
    $category_id = intval($_POST['category_id']);
    $is_playground = isset($_POST['is_playground']) ? 1 : 0;
    
    // Image upload handling
    $image_path = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            } else {
                $error_msg = "Failed to upload image.";
            }
        } else {
            $error_msg = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
        }
    }

    if (empty($title) || empty($tool_url) || empty($category_id)) {
        $error_msg = "Title, URL, and Category are required.";
    } elseif (empty($error_msg)) {
        $stmt = $conn->prepare("INSERT INTO tools (title, description, image_path, tool_url, category_id, is_playground) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $description, $image_path, $tool_url, $category_id, $is_playground);
        
        if ($stmt->execute()) {
            $success_msg = "Tool added successfully!";
        } else {
            $error_msg = "Error adding tool: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Delete Tool
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tool'])) {
    $tool_id = intval($_POST['tool_id']);
    
    // Get image path to delete file
    $stmt = $conn->prepare("SELECT image_path FROM tools WHERE id = ?");
    $stmt->bind_param("i", $tool_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $tool = $res->fetch_assoc();
    if ($tool && $tool['image_path'] && file_exists($tool['image_path'])) {
        unlink($tool['image_path']);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM tools WHERE id = ?");
    $stmt->bind_param("i", $tool_id);
    
    if ($stmt->execute()) {
        $success_msg = "Tool deleted successfully!";
    } else {
        $error_msg = "Error deleting tool: " . $conn->error;
    }
    $stmt->close();
}

// Handle Edit Tool
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_tool'])) {
    $tool_id = intval($_POST['tool_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $tool_url = trim($_POST['tool_url']);
    $category_id = intval($_POST['category_id']);
    $status = $_POST['status'];
    $is_playground = isset($_POST['is_playground']) ? 1 : 0;
    
    // Check existing image
    $stmt = $conn->prepare("SELECT image_path FROM tools WHERE id = ?");
    $stmt->bind_param("i", $tool_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $image_path = $existing['image_path'];
    $stmt->close();

    // Handle new image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Delete old image if exists
                if ($image_path && file_exists($image_path)) {
                    unlink($image_path);
                }
                $image_path = $destination;
            } else {
                $error_msg = "Failed to upload new image.";
            }
        } else {
            $error_msg = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
        }
    }

    if (empty($title) || empty($tool_url) || empty($category_id)) {
        $error_msg = "Title, URL, and Category are required.";
    } elseif (empty($error_msg)) {
        $stmt = $conn->prepare("UPDATE tools SET title = ?, description = ?, image_path = ?, tool_url = ?, category_id = ?, is_playground = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssiisi", $title, $description, $image_path, $tool_url, $category_id, $is_playground, $status, $tool_id);
        
        if ($stmt->execute()) {
            $success_msg = "Tool updated successfully!";
        } else {
            $error_msg = "Error updating tool: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all tools
$tools_result = $conn->query("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN tool_categories c ON t.category_id = c.id ORDER BY t.created_at DESC");

// Get edit tool data if editing
$edit_tool = NULL;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM tools WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_tool = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tools | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--light); color: var(--dark); }

        .main-content { flex: 1; padding: 3rem; margin-left: 250px; transition: margin-left 0.3s ease; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-header h1 { font-size: 2rem; color: var(--dark); }

        .btn {
            padding: 0.7rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
            transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #0052CC; transform: translateY(-2px); }
        .btn-secondary { background: var(--secondary); color: white; }
        .btn-secondary:hover { background: #6D28D9; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #DC2626; }
        .btn-small { padding: 0.5rem 1rem; font-size: 0.85rem; }

        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; }
        .alert-success { background: #D1FAE5; color: #065F46; border-left: 4px solid var(--success); }
        .alert-error { background: #FEE2E2; color: #7F1D1D; border-left: 4px solid var(--danger); }

        .form-section, .list-section { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 2rem; }
        .form-section h2, .list-section h2 { margin-bottom: 1.5rem; color: var(--dark); font-size: 1.3rem; }

        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark); }
        input[type="text"], input[type="url"], select, textarea {
            width: 100%; padding: 0.7rem; border: 1px solid #E5E7EB; border-radius: 6px;
            font-family: 'Outfit', sans-serif; font-size: 0.95rem; transition: border-color 0.3s ease;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1); }
        textarea { resize: vertical; min-height: 80px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        
        .form-buttons { display: flex; gap: 1rem; margin-top: 2rem; }
        
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 1rem; text-align: left; font-weight: 600; color: var(--dark); border-bottom: 2px solid #E5E7EB; background: var(--light); }
        td { padding: 1rem; border-bottom: 1px solid #E5E7EB; vertical-align: middle; }
        tr:hover { background: #FAFBFC; }

        .status-badge { display: inline-block; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-active { background: #D1FAE5; color: #065F46; }
        .status-inactive { background: #FEE2E2; color: #7F1D1D; }
        .actions { display: flex; gap: 0.5rem; }

        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #E5E7EB; }

        @media (max-width: 1024px) {
            .form-row { grid-template-columns: 1fr; }
            .main-content { margin-left: 0; padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tools"></i> Manage Tools</h1>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i><span><?php echo $success_msg; ?></span></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><span><?php echo $error_msg; ?></span></div>
        <?php endif; ?>

        <div class="form-section">
            <h2><?php echo $edit_tool ? 'Edit Tool' : 'Add New Tool'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_tool): ?>
                    <input type="hidden" name="edit_tool" value="1">
                    <input type="hidden" name="tool_id" value="<?php echo $edit_tool['id']; ?>">
                <?php else: ?>
                    <input type="hidden" name="add_tool" value="1">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Tool Title *</label>
                        <input type="text" id="title" name="title" required value="<?php echo $edit_tool ? htmlspecialchars($edit_tool['title']) : ''; ?>" placeholder="e.g. Unicode Converter">
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_tool && $edit_tool['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Short description of the tool..."><?php echo $edit_tool ? htmlspecialchars($edit_tool['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tool_url">Tool URL / Link *</label>
                        <input type="text" id="tool_url" name="tool_url" required value="<?php echo $edit_tool ? htmlspecialchars($edit_tool['tool_url']) : ''; ?>" placeholder="e.g. https://example.com/tool">
                    </div>
                    <div class="form-group">
                        <label for="image">Tile Image <?php echo $edit_tool ? '(Leave blank to keep current)' : ''; ?></label>
                        <input type="file" id="image" name="image" accept="image/*" style="width: 100%; padding: 0.5rem;">
                        <?php if ($edit_tool && $edit_tool['image_path']): ?>
                            <div style="margin-top: 10px;">
                                <img src="<?php echo htmlspecialchars($edit_tool['image_path']); ?>" alt="Current Image" style="height: 50px; border-radius: 5px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_playground" name="is_playground" <?php echo ($edit_tool && $edit_tool['is_playground']) ? 'checked' : ''; ?>>
                        <label for="is_playground" style="margin-bottom: 0;">Is this the special "Playground" tile?</label>
                    </div>
                </div>

                <?php if ($edit_tool): ?>
                    <div class="form-group" style="max-width: 300px;">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo $edit_tool['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $edit_tool['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $edit_tool ? 'Update Tool' : 'Add Tool'; ?>
                    </button>
                    <?php if ($edit_tool): ?>
                        <a href="admin_tools.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="list-section">
            <h2>All Tools (<?php echo $tools_result->num_rows; ?>)</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tool = $tools_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($tool['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($tool['image_path']); ?>" class="img-preview" alt="Tool Image">
                                    <?php else: ?>
                                        <div class="img-preview" style="background: #E5E7EB; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-tools" style="color: #9CA3AF;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($tool['title']); ?></strong>
                                    <?php if ($tool['is_playground']): ?>
                                        <br><span style="font-size: 0.8rem; color: var(--primary);"><i class="fas fa-star"></i> Playground Tile</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($tool['category_name']); ?></td>
                                <td><a href="<?php echo htmlspecialchars($tool['tool_url']); ?>" target="_blank" style="color: var(--primary);">Link <i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i></a></td>
                                <td>
                                    <span class="status-badge status-<?php echo $tool['status']; ?>">
                                        <?php echo ucfirst($tool['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="?edit=<?php echo $tool['id']; ?>" class="btn btn-secondary btn-small">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tool?');">
                                            <input type="hidden" name="delete_tool" value="1">
                                            <input type="hidden" name="tool_id" value="<?php echo $tool['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-small">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
