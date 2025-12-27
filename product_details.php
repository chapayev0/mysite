<?php
include 'db_connect.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM store_products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
}

// Redirect if product not found
if (!$product) {
    header("Location: index.php#store");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechLearn Academy</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        /* Product Details Section Specifics */
        .product-details-section {
            padding: 8rem 2rem 4rem; /* Top padding to account for fixed navbar */
            max-width: 1200px;
            margin: 0 auto;
            min-height: 80vh;
        }

        .breadcrumb {
            margin-bottom: 2rem;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .product-image-large {
            background: var(--secondary);
            border-radius: 16px;
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10rem;
            color: var(--dark);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .product-info-large h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
            line-height: 1.2;
        }

        .product-price-large {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 2rem;
        }

        .product-description-large {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 3rem;
            line-height: 1.8;
        }

        .action-buttons {
            display: flex;
            gap: 1.5rem;
        }

        .btn-large {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-large {
            background: var(--primary);
            color: var(--light);
        }

        .btn-primary-large:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary-large {
            background: var(--secondary);
            color: var(--dark);
            border: 1px solid var(--border);
        }

        .btn-secondary-large:hover {
            background: var(--border);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .product-image-large {
                font-size: 6rem;
            }

            .product-info-large h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="product-details-section">
        <div class="breadcrumb">
            <a href="index.php">Home</a> / <a href="index.php#store">Store</a> / <?php echo htmlspecialchars($product['name']); ?>
        </div>
        
        <div class="product-container">
            <div class="product-image-large">
                <?php echo htmlspecialchars($product['image_url']); ?>
            </div>
            <div class="product-info-large">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price-large">Rs. <?php echo number_format($product['price'], 0); ?></div>
                <p class="product-description-large"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="action-buttons">
                    <button class="btn-large btn-primary-large">Buy Now</button>
                    <button class="btn-large btn-secondary-large">Add to Cart</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Use common partials -->
    <?php include 'ict_section.php'; ?>
    <?php include 'footer.php'; ?>

    <script src="assest/js/main.js"></script>
</body>
</html>
