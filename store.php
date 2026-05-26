<?php
session_start();
include 'db_connect.php';

// Pagination setup
$limit = 12; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Filter and Search variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sidebar_search = isset($_GET['sidebar_search']) ? $_GET['sidebar_search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Base query
$sql = "SELECT p.*, 
        (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image 
        FROM store_products p WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM store_products p WHERE 1=1";

$params = [];
$types = "";

// Apply top search OR sidebar search
$active_search = !empty($search) ? $search : (!empty($sidebar_search) ? $sidebar_search : '');

if (!empty($active_search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $count_sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%" . $active_search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Apply Category Filter
if (!empty($category)) {
    $sql .= " AND p.category = ?";
    $count_sql .= " AND p.category = ?";
    $params[] = $category;
    $types .= "s";
}

// Apply Price Filter
if ($min_price > 0) {
    $sql .= " AND p.price >= ?";
    $count_sql .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price > 0) {
    $sql .= " AND p.price <= ?";
    $count_sql .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";

// Get Total Items
if (!empty($params)) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_items = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} else {
    $total_items = $conn->query($count_sql)->fetch_assoc()['total'];
}

$total_pages = ceil($total_items / $limit);

// Execute final query
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    // Append limit and offset
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (empty($row['primary_image'])) {
            $img_res = $conn->query("SELECT image_url FROM product_images WHERE product_id = " . $row['id'] . " LIMIT 1");
            if ($img_row = $img_res->fetch_assoc()) {
                $row['primary_image'] = $img_row['image_url'];
            } else {
                 $row['primary_image'] = '📦';
            }
        }
        $products[] = $row;
    }
}
if(isset($stmt)) $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store | ICT with Dilhara Academy</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --accent: #EC4899;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --border: #E2E8F0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--light);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .store-header {
            padding: 8rem 2rem 4rem; /* Top padding to account for fixed navbar */
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.05) 0%, rgba(124, 58, 237, 0.05) 100%);
            text-align: center;
        }

        .store-header h1 {
            font-size: 3rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .store-header p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Top Search Bar */
        .top-search-container {
            max-width: 800px;
            margin: 2rem auto 0;
        }

        .search-form {
            display: flex;
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .search-form:focus-within {
            box-shadow: 0 10px 25px rgba(0, 102, 255, 0.1);
            border-color: var(--primary);
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-family: inherit;
            border-radius: 50px;
            outline: none;
            background: transparent;
        }

        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: #0052cc;
            transform: translateY(-2px);
        }

        .store-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 3rem;
            flex: 1;
            align-items: start;
            box-sizing: border-box;
        }

        /* Sidebar Filter */
        .sidebar {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .filter-group {
            margin-bottom: 2rem;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .sidebar-search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-list li {
            margin-bottom: 0.5rem;
        }

        .category-link {
            display: block;
            padding: 0.5rem 0;
            color: var(--gray);
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .category-link:hover, .category-link.active {
            color: var(--primary);
            padding-left: 5px;
        }

        .price-inputs {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .price-inputs input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            box-sizing: border-box;
        }

        .btn-filter {
            width: 100%;
            background: var(--dark);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #1e293b;
        }

        .btn-clear {
            width: 100%;
            background: transparent;
            color: var(--gray);
            border: 1px solid var(--border);
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: #f1f5f9;
            color: var(--dark);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: rgba(0, 102, 255, 0.3);
        }

        .product-image {
            height: 220px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 1rem;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-emoji {
            font-size: 5rem;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-emoji {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--secondary);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            text-transform: uppercase;
        }

        .product-info {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
        }

        .product-desc {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--dark);
        }

        .btn-view {
            background: rgba(0, 102, 255, 0.1);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .product-card:hover .btn-view {
            background: var(--primary);
            color: white;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .store-container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="store-header">
        <h1>Our Store</h1>
        <p>Explore our premium resources, courses, and tools designed to boost your ICT skills.</p>
        
        <!-- Top Search Bar -->
        <div class="top-search-container">
            <form action="store.php" method="GET" class="search-form">
                <?php if (!empty($category)): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>
                <?php if ($min_price > 0): ?><input type="hidden" name="min_price" value="<?php echo $min_price; ?>"><?php endif; ?>
                <?php if ($max_price > 0): ?><input type="hidden" name="max_price" value="<?php echo $max_price; ?>"><?php endif; ?>
                <input type="text" name="search" class="search-input" placeholder="Search for products, courses, or resources..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </div>

    <div class="store-container">
        <!-- Sidebar Filter -->
        <aside class="sidebar">
            <form action="store.php" method="GET">
                <!-- Preserve top search if it was used, though usually they override each other. We use sidebar_search here. -->
                <?php if (!empty($search)): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>

                <div class="filter-group">
                    <div class="filter-title">Search</div>
                    <input type="text" name="sidebar_search" class="sidebar-search-input" placeholder="Keyword..." value="<?php echo htmlspecialchars($sidebar_search); ?>">
                </div>

                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    <ul class="category-list">
                        <?php
                        $categories_list = ['General', 'Books', 'Courses', 'Tools', 'Past Papers'];
                        foreach ($categories_list as $cat) {
                            $is_active = ($category === $cat) ? 'active' : '';
                            // Build URL for category link to preserve other filters
                            $params_arr = $_GET;
                            $params_arr['category'] = $cat;
                            $url = 'store.php?' . http_build_query($params_arr);
                            echo "<li><a href=\"$url\" class=\"category-link $is_active\">$cat</a></li>";
                        }
                        ?>
                    </ul>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                </div>

                <div class="filter-group">
                    <div class="filter-title">Price Range</div>
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Min" min="0" step="100" value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                        <span style="color: var(--gray);">-</span>
                        <input type="number" name="max_price" placeholder="Max" min="0" step="100" value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
                    </div>
                </div>

                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="store.php" class="btn-clear">Clear All</a>
            </form>
        </aside>

        <!-- Product Grid -->
        <main>
            <div class="product-grid">
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <span style="font-size: 3rem; margin-bottom: 1rem; display: block;">🔍</span>
                        <h3>No products found</h3>
                        <p>We couldn't find any products matching your filters.</p>
                        <a href="store.php" class="btn-filter" style="display: inline-block; width: auto; padding: 0.8rem 2rem;">Clear Filters</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-card">
                            <div class="product-image">
                                <?php if ($product['category'] !== 'General'): ?>
                                    <div class="product-badge"><?php echo htmlspecialchars($product['category']); ?></div>
                                <?php endif; ?>
                                
                                <?php 
                                $img = $product['primary_image'];
                                if (filter_var($img, FILTER_VALIDATE_URL) || file_exists($img) || strpos($img, 'assest/') === 0): ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span class="product-emoji"><?php echo htmlspecialchars($img); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars(isset($product['category']) ? $product['category'] : 'General'); ?></div>
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-desc"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-footer">
                                    <div class="product-price">Rs. <?php echo number_format($product['price'], 0); ?></div>
                                    <span class="btn-view">View Details</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <!-- Optional Pagination can be added here -->
                <div style="margin-top: 3rem; text-align: center;">
                    <p style="color: var(--gray);">Page <?php echo $page; ?> of <?php echo $total_pages; ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
