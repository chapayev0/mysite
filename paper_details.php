<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header("Location: papers.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM paper_archive WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paper = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$paper) {
    header("Location: papers.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($paper['title']); ?> | Paper Archive</title>
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
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; display: flex; flex-direction: column; gap: 2rem; }
        
        .back-link { display: inline-flex; align-items: center; color: var(--gray); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .back-link:hover { color: var(--primary); }
        
        .paper-header { background: white; padding: 2.5rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .meta-tags { display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .meta-tag { background: #F1F5F9; color: #475569; padding: 0.4rem 1rem; border-radius: 99px; font-weight: 600; font-size: 0.9rem; border: 1px solid #E2E8F0; }
        .meta-tag.type { background: #DBEAFE; color: #1E40AF; border-color: #BFDBFE; }
        
        .paper-title { font-size: 2.5rem; font-weight: 800; margin: 0 0 1rem 0; color: #1E293B; line-height: 1.2; }
        .paper-desc { font-size: 1.1rem; color: var(--gray); line-height: 1.6; margin-bottom: 2rem; max-width: 800px; }
        
        .btn-download { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-decoration: none; padding: 1rem 2.5rem; border-radius: 10px; font-weight: 700; font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.8rem; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3); }
        .btn-download:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 102, 255, 0.4); }
        
        .pdf-container { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: 800px; display: flex; flex-direction: column; border: 1px solid #E2E8F0; }
        .pdf-header { background: #F8FAFC; padding: 1rem 1.5rem; border-bottom: 1px solid #E2E8F0; font-weight: 700; color: var(--dark); display: flex; justify-content: space-between; align-items: center; }
        
        iframe { width: 100%; height: 100%; border: none; flex: 1; }
        
        @media (max-width: 768px) {
            .paper-title { font-size: 1.8rem; }
            .pdf-container { height: 500px; }
            .container { padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div>
            <a href="papers.php" class="back-link">← Back to Archive</a>
        </div>
        
        <div class="paper-header">
            <div class="meta-tags">
                <span class="meta-tag type"><?php echo htmlspecialchars($paper['paper_type']); ?></span>
                <span class="meta-tag">Grade <?php echo htmlspecialchars($paper['grade']); ?></span>
                <span class="meta-tag">Year <?php echo htmlspecialchars($paper['year']); ?></span>
            </div>
            
            <h1 class="paper-title"><?php echo htmlspecialchars($paper['title']); ?></h1>
            
            <?php if(!empty($paper['description'])): ?>
                <p class="paper-desc"><?php echo nl2br(htmlspecialchars($paper['description'])); ?></p>
            <?php endif; ?>
            
            <a href="<?php echo htmlspecialchars($paper['filepath']); ?>" download class="btn-download">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download PDF
            </a>
        </div>
        
        <div class="pdf-container">
            <div class="pdf-header">
                <span>Document Preview</span>
                <a href="<?php echo htmlspecialchars($paper['filepath']); ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">Open in new tab ↗</a>
            </div>
            <iframe src="<?php echo htmlspecialchars($paper['filepath']); ?>#toolbar=0" title="PDF Preview"></iframe>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
