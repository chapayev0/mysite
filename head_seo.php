<?php
// Default SEO values if not provided
$seo_title = isset($seo_title) ? $seo_title : 'ICT with Dilhara ICT Academy | Excellence in Digital Education';
$seo_description = isset($seo_description) ? $seo_description : 'Welcome to Dilhara ICT Academy. Discover our comprehensive ICT classes, interactive playground, and extensive tools library.';
$seo_keywords = isset($seo_keywords) ? $seo_keywords : 'ICT, Dilhara ICT Academy, computer science, online learning, educational tools';
$seo_author = isset($seo_author) ? $seo_author : 'Dilhara ICT Academy';
$seo_image = isset($seo_image) ? $seo_image : 'assest/logo/logo1.png';

// Build the canonical URL safely
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$current_url = $protocol . $host . $uri;
$canonical_url = isset($seo_canonical) ? $seo_canonical : $current_url;
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($seo_title); ?></title>

<!-- Standard SEO Meta Tags -->
<meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
<meta name="author" content="<?php echo htmlspecialchars($seo_author); ?>">
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
<link rel="icon" type="image/png" href="assest/logo/logo1.png">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($protocol . $host . '/' . ltrim($seo_image, '/')); ?>">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
<meta property="twitter:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta property="twitter:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<meta property="twitter:image" content="<?php echo htmlspecialchars($protocol . $host . '/' . ltrim($seo_image, '/')); ?>">
