<?php
session_start();
include 'db_connect.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("Location: admin_products.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle Image
    $image_url = $_POST['current_image']; // Default to existing
     
    // Check for file upload
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assest/images/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = uniqid('product_') . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            } else {
                $error_msg = "Failed to upload image.";
            }
        } else {
            $error_msg = "Invalid file type.";
        }
    }
    // Check for URL (only if no file uploaded)
    elseif (!empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    }

    if (empty($error_msg)) {
        $stmt = $conn->prepare("UPDATE store_products SET name=?, description=?, price=?, image_url=? WHERE id=?");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_url, $product_id);

        if ($stmt->execute()) {
            $success_msg = "Product updated successfully!";
            // Update the fetched product data to show new image immediately
            $product['image_url'] = $image_url;
            $product['name'] = $name;
            $product['description'] = $description;
            $product['price'] = $price;
        } else {
            $error_msg = "Error updating product: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM store_products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "Product not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | TechLearn Admin</title>
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
            <h1>Edit Product</h1>
        </div>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Price (Rs.)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    
                    <!-- Current Image Preview -->
                    <div style="margin-bottom: 1rem; padding: 10px; border: 1px dashed #ccc; border-radius: 8px; text-align: center;">
                        <label style="display: block; font-size: 0.8rem; color: var(--gray); margin-bottom: 0.5rem;">Current Image:</label>
                        <div style="display: flex; justify-content: center;">
                            <div style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px; overflow: hidden; border: 1px solid #ddd;">
                                <?php if (filter_var($product['image_url'], FILTER_VALIDATE_URL) || file_exists($product['image_url']) || strpos($product['image_url'], 'assest/') === 0): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Logo" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <span style="font-size: 3rem;"><?php echo htmlspecialchars($product['image_url']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 0.5rem;">
                        <input type="text" name="image_url" class="form-control" placeholder="New Image URL" value="<?php echo (filter_var($product['image_url'], FILTER_VALIDATE_URL)) ? htmlspecialchars($product['image_url']) : ''; ?>">
                    </div>
                    <div style="text-align: center; margin: 0.5rem 0; color: var(--gray); font-size: 0.9rem; font-weight: bold;">- OR -</div>
                    <div>
                        <input type="file" name="image_upload" class="form-control" accept="image/*">
                    </div>
                    <small style="color: var(--gray); display: block; margin-top: 0.5rem;">Upload to replace. Leave both empty to keep current.</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <button type="submit" name="update_product" class="btn">Update Product</button>
                <a href="admin_products.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
