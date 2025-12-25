<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade 6 ICT Classes | TechLearn</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000000;
            --primary-hover: #333333;
            --secondary: #F8F8FB;
            --accent: #E5E5E8;
            --dark: #1A1A1A;
            --light: #FFFFFF;
            --gray: #6B7280;
            --gray-light: #F3F4F6;
            --border: #E5E7EB;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Navbar Styles (Copied from index.php) */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid var(--border);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            font-family: 'Space Mono', monospace;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.8rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline {
            background: var(--light);
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .btn-outline:hover {
            background: var(--secondary);
            border-color: var(--accent);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--light);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        /* Page Content Styles */
        .page-header {
            margin-top: 80px;
            padding: 6rem 2rem 4rem;
            background: var(--secondary);
            text-align: center;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.2rem;
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }

        .institutes-grid {
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
        }

        .institute-card {
            background: var(--light);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .institute-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: var(--secondary);
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .institute-logo-placeholder {
            width: 80px;
            height: 80px;
            background: var(--light);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .institute-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }

        .card-body {
            padding: 2rem;
            flex-grow: 1;
        }

        .info-row {
            display: flex;
            margin-bottom: 1rem;
            align-items: flex-start;
        }

        .info-icon {
            width: 24px;
            margin-right: 1rem;
            text-align: center;
            font-size: 1.2rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 500;
        }

        .description {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Dilhara Section Styles */
        .dilhara-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 60px 20px;
            background-color: var(--light);
            position: relative;
            overflow: hidden;
            border-top: 1px solid var(--border);
        }

        .dilhara-text {
            text-align: left;
            color: var(--dark);
            line-height: 1;
            position: relative;
            z-index: 2;
        }

        .dilhara-text .small-text {
            font-size: 12vw;
            font-weight: 500;
            letter-spacing: -0.02em;
            margin-bottom: -20px;
            color: var(--gray);
        }

        .dilhara-text .large-text {
            font-size: 28vw;
            font-weight: 600;
            letter-spacing: -0.03em;
            color: var(--primary);
        }

        /* Footer Styles */
        .footer {
            background: var(--secondary);
            color: var(--dark);
            padding: 4rem 2rem 2rem;
            border-top: 1px solid var(--border);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            font-family: 'Space Mono', monospace;
            color: var(--primary);
        }

        .footer-brand p {
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            border: 1px solid var(--border);
        }

        .footer-section h4 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: var(--gray);
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            color: var(--gray);
        }

        @media (max-width: 1024px) {
            .institutes-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
             .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .footer-content { grid-template-columns: 1fr; }
            .dilhara-text .small-text { font-size: 14vw; }
            .dilhara-text .large-text { font-size: 30vw; }
        }
        
        /* Floating particles */
        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #60A5FA;
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
            opacity: 0.6;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(10px); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">TechLearn</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#classes">Classes</a></li>
                <li><a href="index.php#online">Online Classes</a></li>
                <li><a href="index.php#store">Store</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="login.php" class="btn btn-outline">Login</a>
                <button class="btn btn-primary">Register</button>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <h1 class="page-title">Grade 6 ICT Classes</h1>
        <p class="page-subtitle">Start your journey into the digital world with our comprehensive Grade 6 curriculum available at these leading institutes.</p>
    </header>

    <!-- Institutes Cards -->
    <section class="institutes-grid">
        <!-- Card 1 -->
        <div class="institute-card">
            <div class="card-header">
                <div class="institute-logo-placeholder">🏫</div>
                <h3 class="institute-name">Institute A</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <div class="info-label">Address</div>
                        <div class="info-value">123 Main Street, Colombo 05</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <div class="info-label">Phone</div>
                        <div class="info-value">+94 77 123 4567</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div class="info-content">
                        <div class="info-label">Class Day</div>
                        <div class="info-value">Saturday</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">⏰</div>
                    <div class="info-content">
                        <div class="info-label">Time</div>
                        <div class="info-value">8:00 AM - 10:00 AM</div>
                    </div>
                </div>
                <div class="description">
                    Join our vibrant Grade 6 batch at Institute A. We focus on building a strong foundation in computer basics, typing skills, and introduction to coding in a friendly environment.
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="institute-card">
            <div class="card-header">
                <div class="institute-logo-placeholder">🏛️</div>
                <h3 class="institute-name">Institute B</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <div class="info-label">Address</div>
                        <div class="info-value">456 High Level Road, Nugegoda</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <div class="info-label">Phone</div>
                        <div class="info-value">+94 71 987 6543</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div class="info-content">
                        <div class="info-label">Class Day</div>
                        <div class="info-value">Sunday</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">⏰</div>
                    <div class="info-content">
                        <div class="info-label">Time</div>
                        <div class="info-value">2:30 PM - 4:30 PM</div>
                    </div>
                </div>
                <div class="description">
                    Institute B offers a state-of-the-art computer lab for Grade 6 students. Our practical-first approach ensures every student gets hands-on experience with modern tools.
                </div>
            </div>
        </div>
    </section>

    <!-- Dilhara Section -->
    <section class="dilhara-section">
        <div class="particle" style="top: 15%; right: 20%;"></div>
        <div class="particle" style="top: 25%; right: 15%; animation-delay: 0.5s;"></div>
        <div class="particle" style="top: 35%; right: 25%; animation-delay: 1s;"></div>
        <div class="particle" style="top: 20%; right: 30%; animation-delay: 1.5s;"></div>
        
        <div class="dilhara-text">
            <div class="small-text">ICT with</div>
            <div class="large-text">Dilhara</div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <h3>TechLearn</h3>
                <p>Sri Lanka's leading ICT education academy, committed to nurturing digital excellence and empowering students to excel in technology.</p>
                <div class="social-links">
                    <a href="#" class="social-link">📘</a>
                    <a href="#" class="social-link">📺</a>
                    <a href="#" class="social-link">💬</a>
                    <a href="#" class="social-link">📷</a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#classes">Classes</a></li>
                    <li><a href="index.php#online">Online Classes</a></li>
                    <li><a href="index.php#store">Store</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Classes</h4>
                <ul class="footer-links">
                    <li><a href="grade6_details.php">Grade 6 ICT</a></li>
                    <li><a href="#">Grade 7 ICT</a></li>
                    <li><a href="#">Grade 8 ICT</a></li>
                    <li><a href="#">Grade 9 ICT</a></li>
                    <li><a href="#">O/L ICT</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul class="footer-links">
                    <li>📍 123, Galle Road, Colombo 03</li>
                    <li>📞 +94 77 123 4567</li>
                    <li>📧 <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="e881868e87a89c8d8b80848d899a86c68483">[email&#160;protected]</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 TechLearn ICT Academy. All rights reserved. | Designed with passion for education</p>
        </div>
    </footer>
</body>
</html>
