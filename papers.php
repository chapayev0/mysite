<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Fetch filters from GET request
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$grade_filter = isset($_GET['grade']) ? $_GET['grade'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

// Build the query
$query = "SELECT * FROM paper_archive WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND (title LIKE ? OR description LIKE ? OR year LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($grade_filter !== '') {
    $query .= " AND grade = ?";
    $params[] = $grade_filter;
    $types .= "s";
}

if ($type_filter !== '') {
    $query .= " AND paper_type = ?";
    $params[] = $type_filter;
    $types .= "s";
}

$query .= " ORDER BY year DESC, id DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$papers = [];
while ($row = $result->fetch_assoc()) {
    $papers[] = $row;
}
$stmt->close();
?>
<?php
$seo_title = "ICT Past Papers & Tutorials | Dilhara Academy";
$seo_description = "Access a comprehensive archive of ICT past papers, model papers, term tests, and tutorials for O/L and A/L students.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_seo.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; color: var(--dark); padding-top: 80px; }
        .hero { background: linear-gradient(135deg, var(--dark) 0%, #1E293B 100%); color: white; padding: 4rem 2rem; text-align: center; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; font-weight: 800; }
        .hero p { font-size: 1.2rem; color: #CBD5E1; max-width: 600px; margin: 0 auto; }
        
        .search-container { max-width: 800px; margin: -2rem auto 3rem auto; padding: 0 1rem; position: relative; z-index: 10; }
        .search-box { background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; gap: 1rem; flex-wrap: wrap; }
        .search-input { flex: 1; min-width: 200px; padding: 1rem; border: 2px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: 1rem; }
        .filter-select { padding: 1rem; border: 2px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: 1rem; background: white; min-width: 150px; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 700; cursor: pointer; transition: transform 0.2s; font-size: 1rem; }
        .btn-search:hover { transform: translateY(-2px); }

        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        /* Category Quick Links */
        .categories { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 2rem; }
        .cat-pill { background: white; padding: 0.8rem 1.5rem; border-radius: 99px; text-decoration: none; color: var(--dark); font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05); white-space: nowrap; transition: all 0.2s; border: 1px solid #E2E8F0; }
        .cat-pill:hover, .cat-pill.active { background: var(--primary); color: white; border-color: var(--primary); }

        /* Paper Tiles */
        .papers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; }
        .paper-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; color: inherit; display: flex; flex-direction: column; border: 1px solid #F1F5F9; }
        .paper-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .paper-header { background: #F8FAFC; padding: 1.5rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: flex-start; }
        .paper-badge { background: #DBEAFE; color: #1E40AF; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
        .paper-year { color: var(--gray); font-weight: 700; font-size: 1.1rem; }
        .paper-body { padding: 1.5rem; flex: 1; }
        .paper-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.4; color: #1E293B; }
        .paper-grade { color: var(--secondary); font-weight: 600; margin-bottom: 1rem; display: block; }
        .paper-footer { padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; background: #F8FAFC; display: flex; justify-content: space-between; align-items: center; color: var(--primary); font-weight: 600; }
        .view-btn { display: flex; align-items: center; gap: 0.5rem; }
        
        .no-results { text-align: center; padding: 4rem 2rem; color: var(--gray); font-size: 1.2rem; }
        
        @media (max-width: 768px) {
            .search-box { flex-direction: column; }
            .btn-search { width: 100%; }
            .hero h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="hero">
        <h1>Paper Archive</h1>
        <p>Access past papers, model papers, and term tests to boost your ICT knowledge.</p>
    </div>
    
    <div class="search-container">
        <form class="search-box" method="GET" action="papers.php">
            <input type="text" name="search" class="search-input" placeholder="Search by name or year..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="grade" class="filter-select">
                <option value="">All Grades</option>
                <?php for($i=6; $i<=13; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $grade_filter == $i ? 'selected' : ''; ?>>Grade <?php echo $i; ?></option>
                <?php endfor; ?>
                <option value="Other" <?php echo $grade_filter == 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <select name="type" class="filter-select">
                <option value="">All Types</option>
                <option value="Past Paper" <?php echo $type_filter == 'Past Paper' ? 'selected' : ''; ?>>Past Paper</option>
                <option value="Model Paper" <?php echo $type_filter == 'Model Paper' ? 'selected' : ''; ?>>Model Paper</option>
                <option value="Term Test" <?php echo $type_filter == 'Term Test' ? 'selected' : ''; ?>>Term Test</option>
                <option value="Tutorial" <?php echo $type_filter == 'Tutorial' ? 'selected' : ''; ?>>Tutorial</option>
            </select>
            <button type="submit" class="btn-search">Search</button>
            <?php if($search || $grade_filter || $type_filter): ?>
                <a href="papers.php" style="padding: 1rem; color: var(--gray); text-decoration: none; display: flex; align-items: center; font-weight: 600;">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="container">
        <!-- Quick Categories -->
        <div class="categories">
            <a href="papers.php" class="cat-pill <?php echo ($grade_filter == '' && $type_filter == '') ? 'active' : ''; ?>">All Papers</a>
            <a href="papers.php?type=Past+Paper" class="cat-pill <?php echo $type_filter == 'Past Paper' ? 'active' : ''; ?>">Past Papers</a>
            <a href="papers.php?type=Model+Paper" class="cat-pill <?php echo $type_filter == 'Model Paper' ? 'active' : ''; ?>">Model Papers</a>
            <a href="papers.php?grade=10" class="cat-pill <?php echo $grade_filter == '10' ? 'active' : ''; ?>">Grade 10</a>
            <a href="papers.php?grade=11" class="cat-pill <?php echo $grade_filter == '11' ? 'active' : ''; ?>">Grade 11</a>
            <a href="papers.php?grade=12" class="cat-pill <?php echo $grade_filter == '12' ? 'active' : ''; ?>">A/L ICT</a>
        </div>
        
        <?php if (empty($papers)): ?>
            <div class="no-results">
                <p>No papers found matching your criteria.</p>
                <a href="papers.php" style="color: var(--primary); font-weight: 600;">View all papers</a>
            </div>
        <?php else: ?>
            <div class="papers-grid">
                <?php foreach($papers as $paper): ?>
                    <a href="paper_details.php?id=<?php echo $paper['id']; ?>" class="paper-card">
                        <div class="paper-header">
                            <span class="paper-badge"><?php echo htmlspecialchars($paper['paper_type']); ?></span>
                            <span class="paper-year"><?php echo htmlspecialchars($paper['year']); ?></span>
                        </div>
                        <div class="paper-body">
                            <span class="paper-grade">Grade <?php echo htmlspecialchars($paper['grade']); ?></span>
                            <h3 class="paper-title"><?php echo htmlspecialchars($paper['title']); ?></h3>
                            <?php if(!empty($paper['description'])): ?>
                                <p style="color: var(--gray); font-size: 0.9rem; line-height: 1.5; margin-top: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($paper['description']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="paper-footer">
                            <span class="view-btn">View PDF →</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
