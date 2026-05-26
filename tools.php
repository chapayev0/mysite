<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Get selected category and search query
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : NULL;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all active categories
$categories_query = "SELECT * FROM tool_categories WHERE status = 'active' ORDER BY display_order ASC";
$categories_result = $conn->query($categories_query);

// Build tools query based on filters
$tools_query = "SELECT t.*, c.name as category_name, c.icon as category_icon 
                FROM tools t 
                LEFT JOIN tool_categories c ON t.category_id = c.id 
                WHERE t.status = 'active'";
$params = [];
$types = "";

if ($selected_category) {
    $tools_query .= " AND t.category_id = ?";
    $params[] = $selected_category;
    $types .= "i";
}

if (!empty($search_query)) {
    $tools_query .= " AND (t.title LIKE ? OR t.description LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$tools_query .= " ORDER BY t.created_at DESC";

if (!empty($params)) {
    $stmt = $conn->prepare($tools_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $tools_result = $stmt->get_result();
} else {
    $tools_result = $conn->query($tools_query);
}

// Get category details if selected
$selected_cat_data = NULL;
if ($selected_category) {
    $cat_stmt = $conn->prepare("SELECT * FROM tool_categories WHERE id = ?");
    $cat_stmt->bind_param("i", $selected_category);
    $cat_stmt->execute();
    $selected_cat_data = $cat_stmt->get_result()->fetch_assoc();
    $cat_stmt->close();
}
?>
<?php
$seo_title = "Educational Tools & Utilities | ICT with Dilhara";
$seo_description = "Explore our extensive library of digital tools, utilities, and calculators to assist your ICT studies and digital projects.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_seo.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        :root {
            --itools-bg: #F5F5F7;
            --itools-dark: #1D1D1F;
            --itools-gray: #86868B;
            --itools-light: #FFFFFF;
            --itools-blue: #0066CC;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--itools-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(0, 102, 204, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(124, 58, 237, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--itools-dark);
            min-height: 100vh;
        }

        .navbar-spacer { height: 90px; }

        .tools-header {
            text-align: center;
            padding: 4rem 2rem 2rem;
            position: relative;
            z-index: 10;
        }

        .tools-header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1D1D1F 0%, #434345 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tools-header p {
            font-size: 1.2rem;
            color: var(--itools-gray);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Search and Filter Bar */
        .controls-container {
            max-width: 1200px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .search-bar {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .search-bar input {
            width: 100%;
            padding: 1.2rem 1.5rem 1.2rem 3.5rem;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            font-size: 1.1rem;
            font-family: 'Outfit', sans-serif;
            color: var(--itools-dark);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15);
            border-color: var(--itools-blue);
            background: var(--itools-light);
        }

        .search-bar i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--itools-gray);
            font-size: 1.2rem;
        }

        /* Categories Pills */
        .categories-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            justify-content: center;
        }
        
        .categories-scroll::-webkit-scrollbar { display: none; }

        .category-pill {
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: var(--itools-dark);
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: var(--itools-light);
        }

        .category-pill.active {
            background: var(--itools-dark);
            color: var(--itools-light);
            border-color: var(--itools-dark);
        }

        .category-pill.active i { color: var(--itools-light); }
        .category-pill i { color: var(--itools-gray); transition: color 0.3s; }

        /* Tools Grid */
        .tools-grid-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        /* iTools Glassmorphism Card */
        .tool-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .tool-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            z-index: 1;
            pointer-events: none;
            border-radius: 24px;
        }

        .tool-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-hover);
            background: var(--itools-light);
            border-color: rgba(0, 102, 204, 0.2);
        }

        .tool-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--itools-light);
            box-shadow: 0 8px 16px rgba(0,0,0,0.06);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .tool-card.playground .tool-icon-wrapper {
            background: linear-gradient(135deg, #FF9500 0%, #FF2D55 100%);
            color: white;
            font-size: 2.5rem;
        }

        .tool-icon-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tool-icon-wrapper i.default-icon {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--itools-blue) 0%, #32ADE6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tool-info {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .tool-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--itools-dark);
        }

        .tool-desc {
            font-size: 0.95rem;
            color: var(--itools-gray);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .tool-cat {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--itools-blue);
            font-weight: 600;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem 2rem;
            background: var(--glass-bg);
            border-radius: 24px;
            backdrop-filter: blur(20px);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--itools-gray);
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: var(--itools-blue);
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .empty-state a:hover { background: #0055AA; }

        @media (max-width: 768px) {
            .tools-header h1 { font-size: 2.5rem; }
            .tools-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; }
            .tool-card { padding: 1.5rem; }
            .categories-scroll { justify-content: flex-start; padding: 0.5rem; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="navbar-spacer"></div>

    <div class="tools-header">
        <h1>Tools Hub</h1>
        <p>Your collection of powerful utilities and educational resources.</p>
    </div>

    <div class="controls-container">
        <!-- Search -->
        <form action="tools.php" method="GET" class="search-bar">
            <i class="fas fa-search"></i>
            <?php if ($selected_category): ?>
                <input type="hidden" name="category" value="<?php echo $selected_category; ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Search for tools..." value="<?php echo htmlspecialchars($search_query); ?>">
        </form>

        <!-- Categories -->
        <div class="categories-scroll">
            <a href="tools.php" class="category-pill <?php echo !$selected_category ? 'active' : ''; ?>">
                <i class="fas fa-border-all"></i> All Tools
            </a>
            <?php if ($categories_result->num_rows > 0): ?>
                <?php while($cat = $categories_result->fetch_assoc()): ?>
                    <a href="tools.php?category=<?php echo $cat['id']; ?><?php echo $search_query ? '&search='.urlencode($search_query) : ''; ?>" 
                       class="category-pill <?php echo ($selected_category == $cat['id']) ? 'active' : ''; ?>">
                        <i class="fas <?php echo htmlspecialchars($cat['icon']); ?>"></i> 
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="tools-grid-container">
        <div class="tools-grid">
            <?php if ($tools_result->num_rows > 0): ?>
                <?php while($tool = $tools_result->fetch_assoc()): ?>
                    <a href="<?php echo htmlspecialchars($tool['tool_url']); ?>" 
                       class="tool-card <?php echo $tool['is_playground'] ? 'playground' : ''; ?>"
                       <?php echo (strpos($tool['tool_url'], 'http') === 0) ? 'target="_blank"' : ''; ?>>
                        
                        <div class="tool-icon-wrapper">
                            <?php if ($tool['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($tool['image_path']); ?>" alt="<?php echo htmlspecialchars($tool['title']); ?>">
                            <?php elseif ($tool['is_playground']): ?>
                                <i class="fas fa-gamepad"></i>
                            <?php else: ?>
                                <i class="fas <?php echo htmlspecialchars($tool['category_icon']); ?> default-icon"></i>
                            <?php endif; ?>
                        </div>

                        <div class="tool-info">
                            <h3 class="tool-title"><?php echo htmlspecialchars($tool['title']); ?></h3>
                            <p class="tool-desc"><?php echo htmlspecialchars($tool['description']); ?></p>
                            <div class="tool-cat"><?php echo htmlspecialchars($tool['category_name']); ?></div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No tools found</h3>
                    <p>Try adjusting your search or category filter.</p>
                    <a href="tools.php">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
