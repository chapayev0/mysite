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

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $display_order = intval($_POST['display_order']) ?? 0;

    if (empty($name)) {
        $error_msg = "Category name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tool_categories (name, icon, display_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $icon, $display_order);
        
        if ($stmt->execute()) {
            $success_msg = "Category added successfully!";
        } else {
            $error_msg = "Error adding category: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $category_id = intval($_POST['category_id']);
    
    $stmt = $conn->prepare("DELETE FROM tool_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    
    if ($stmt->execute()) {
        $success_msg = "Category deleted successfully!";
    } else {
        $error_msg = "Error deleting category: " . $conn->error;
    }
    $stmt->close();
}

// Handle Edit Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $display_order = intval($_POST['display_order']) ?? 0;
    $status = $_POST['status'];

    if (empty($name)) {
        $error_msg = "Category name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE tool_categories SET name = ?, icon = ?, display_order = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $name, $icon, $display_order, $status, $category_id);
        
        if ($stmt->execute()) {
            $success_msg = "Category updated successfully!";
        } else {
            $error_msg = "Error updating category: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all categories
$categories_result = $conn->query("SELECT * FROM tool_categories ORDER BY display_order ASC");

// Get edit category data if editing
$edit_category = NULL;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM tool_categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_category = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tool Categories | Admin</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light);
            color: var(--dark);
        }

        .main-content {
            flex: 1;
            padding: 3rem;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--dark);
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #0052CC; transform: translateY(-2px); }
        .btn-secondary { background: var(--secondary); color: white; }
        .btn-secondary:hover { background: #6D28D9; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #DC2626; }
        .btn-small { padding: 0.5rem 1rem; font-size: 0.85rem; }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success { background: #D1FAE5; color: #065F46; border-left: 4px solid var(--success); }
        .alert-error { background: #FEE2E2; color: #7F1D1D; border-left: 4px solid var(--danger); }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .form-section, .categories-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .form-section h2, .categories-section h2 {
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-size: 1.3rem;
        }

        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark); }
        input[type="text"], input[type="number"], select {
            width: 100%; padding: 0.7rem; border: 1px solid #E5E7EB; border-radius: 6px;
            font-family: 'Outfit', sans-serif; font-size: 0.95rem; transition: border-color 0.3s ease;
        }
        input:focus, select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1); }

        .form-buttons { display: flex; gap: 1rem; margin-top: 2rem; }
        .form-buttons .btn { flex: 1; justify-content: center; }

        .categories-section { grid-column: 1 / -1; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 1rem; text-align: left; font-weight: 600; color: var(--dark); border-bottom: 2px solid #E5E7EB; background: var(--light); }
        td { padding: 1rem; border-bottom: 1px solid #E5E7EB; }
        tr:hover { background: #FAFBFC; }

        .status-badge { display: inline-block; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-active { background: #D1FAE5; color: #065F46; }
        .status-inactive { background: #FEE2E2; color: #7F1D1D; }
        .actions { display: flex; gap: 0.5rem; }

        .icon-select { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .icon-option {
            padding: 0.5rem; border: 2px solid #E5E7EB; border-radius: 6px; cursor: pointer;
            transition: all 0.3s ease; width: 50px; height: 50px; display: flex;
            align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .icon-option.selected { border-color: var(--primary); background: rgba(0, 102, 255, 0.1); color: var(--primary); }

        @media (max-width: 1024px) {
            .container { grid-template-columns: 1fr; }
            .main-content { margin-left: 0; padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-wrench"></i> Tool Categories</h1>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i><span><?php echo $success_msg; ?></span></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><span><?php echo $error_msg; ?></span></div>
        <?php endif; ?>

        <div class="container">
            <div class="form-section">
                <h2><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h2>
                <form method="POST">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="edit_category" value="1">
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_category" value="1">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" id="name" name="name" required value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Select Icon</label>
                        <div class="icon-select">
                            <?php
                                $icons = ['fa-wrench', 'fa-calculator', 'fa-font', 'fa-code', 'fa-image', 'fa-globe', 'fa-gamepad', 'fa-cogs', 'fa-magic', 'fa-laptop'];
                                $current_icon = $edit_category ? $edit_category['icon'] : 'fa-wrench';
                                foreach($icons as $ic) {
                                    $selected = ($current_icon == $ic) ? 'selected' : '';
                                    echo "<div class='icon-option $selected' data-icon='$ic'><i class='fas $ic'></i></div>";
                                }
                            ?>
                        </div>
                        <input type="hidden" id="icon" name="icon" value="<?php echo htmlspecialchars($current_icon); ?>">
                    </div>

                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="<?php echo $edit_category ? intval($edit_category['display_order']) : 0; ?>">
                    </div>

                    <?php if ($edit_category): ?>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?php echo $edit_category['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $edit_category['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        <?php if ($edit_category): ?>
                            <a href="admin_tool_categories.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="categories-section">
                <h2>All Categories (<?php echo $categories_result->num_rows; ?>)</h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                                            <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo $cat['display_order']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $cat['status']; ?>">
                                            <?php echo ucfirst($cat['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="?edit=<?php echo $cat['id']; ?>" class="btn btn-secondary btn-small">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="delete_category" value="1">
                                                <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
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
    </div>

    <script>
        document.querySelectorAll('.icon-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('icon').value = this.dataset.icon;
            });
        });
    </script>
</body>
</html>
