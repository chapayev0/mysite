<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Handle adding a new link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_link') {
    $name = $conn->real_escape_string($_POST['name']);
    $url = $conn->real_escape_string($_POST['url']);
    $position = $conn->real_escape_string($_POST['position']);
    $order_index = (int)$_POST['order_index'];
    
    $sql = "INSERT INTO menu_links (name, url, position, is_custom, order_index) VALUES ('$name', '$url', '$position', 1, $order_index)";
    $conn->query($sql);
    header("Location: admin_menus.php?msg=link_added");
    exit();
}

// Handle toggling visibility
if (isset($_GET['toggle_id'])) {
    $id = (int)$_GET['toggle_id'];
    $conn->query("UPDATE menu_links SET is_visible = NOT is_visible WHERE id = $id");
    header("Location: admin_menus.php?msg=visibility_toggled");
    exit();
}

// Handle deleting custom link
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM menu_links WHERE id = $id AND is_custom = 1");
    header("Location: admin_menus.php?msg=link_deleted");
    exit();
}

function getLinksByPosition($conn, $position) {
    $sql = "SELECT * FROM menu_links WHERE position = '$position' AND parent_id IS NULL ORDER BY order_index ASC";
    return $conn->query($sql);
}

$navbar_links = getLinksByPosition($conn, 'navbar');
$footer_quick_links = getLinksByPosition($conn, 'footer_quick_links');
$footer_classes = getLinksByPosition($conn, 'footer_classes');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menus - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Space Grotesk', sans-serif;
        }

        body {
            background-color: #F8FAFC;
            color: #0F172A;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem 3rem;
            transition: all 0.3s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1E293B;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card h2 {
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
        }

        th {
            background: #F1F5F9;
            font-weight: 600;
            color: #475569;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-visible {
            background: #DCFCE7;
            color: #166534;
        }

        .badge-hidden {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .badge-custom {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .badge-default {
            background: #F1F5F9;
            color: #475569;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #0066FF;
            color: white;
        }

        .btn-primary:hover {
            background: #0052CC;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #CBD5E1;
            color: #475569;
        }

        .btn-outline:hover {
            background: #F1F5F9;
        }

        .btn-danger {
            background: #FEF2F2;
            color: #EF4444;
            border: 1px solid #FCA5A5;
        }

        .btn-danger:hover {
            background: #FEE2E2;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #0066FF;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-col {
            flex: 1;
        }
        
        .msg-alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Menus</h1>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="msg-alert">
                <?php 
                    if($_GET['msg'] == 'link_added') echo 'New link added successfully.';
                    elseif($_GET['msg'] == 'visibility_toggled') echo 'Link visibility updated.';
                    elseif($_GET['msg'] == 'link_deleted') echo 'Custom link deleted successfully.';
                ?>
            </div>
        <?php endif; ?>

        <!-- Add New Link Form -->
        <div class="card">
            <h2>Add Custom Link</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_link">
                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label">Link Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Special Offer">
                    </div>
                    <div class="form-col">
                        <label class="form-label">URL / Path</label>
                        <input type="text" name="url" class="form-control" required placeholder="e.g. offers.php">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-control" required>
                            <option value="navbar">Navbar</option>
                            <option value="footer_quick_links">Footer - Quick Links</option>
                            <option value="footer_classes">Footer - Classes</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="form-label">Order Index</label>
                        <input type="number" name="order_index" class="form-control" value="10" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Link</button>
            </form>
        </div>

        <?php
        $sections = [
            'Navbar Links' => $navbar_links,
            'Footer - Quick Links' => $footer_quick_links,
            'Footer - Classes' => $footer_classes
        ];

        foreach($sections as $title => $links):
        ?>
        <div class="card">
            <h2><?= $title ?></h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($link = $links->fetch_assoc()): ?>
                        <tr>
                            <td><?= $link['order_index'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($link['name']) ?></strong>
                                <?php
                                // Check if it has children
                                $child_sql = "SELECT count(*) as c FROM menu_links WHERE parent_id = " . $link['id'];
                                $child_res = $conn->query($child_sql);
                                $child_row = $child_res->fetch_assoc();
                                if($child_row['c'] > 0) echo " <small style='color:#64748B'>(has dropdown items)</small>";
                                ?>
                            </td>
                            <td><code><?= htmlspecialchars($link['url']) ?></code></td>
                            <td>
                                <?php if($link['is_custom']): ?>
                                    <span class="badge badge-custom">Custom</span>
                                <?php else: ?>
                                    <span class="badge badge-default">Default</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($link['is_visible']): ?>
                                    <span class="badge badge-visible">Visible</span>
                                <?php else: ?>
                                    <span class="badge badge-hidden">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?toggle_id=<?= $link['id'] ?>" class="btn btn-outline">
                                    <?= $link['is_visible'] ? 'Hide' : 'Show' ?>
                                </a>
                                <?php if($link['is_custom']): ?>
                                    <a href="?delete_id=<?= $link['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this custom link?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

    </main>

</body>
</html>
