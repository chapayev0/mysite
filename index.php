<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechLearn ICT Academy | Excellence in Digital Education</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --accent: #EC4899;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.08);
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

        /* Header Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.8rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Hero Section with Slider */
        .hero-section {
            margin-top: 80px;
            height: 600px;
            position: relative;
            overflow: hidden;
        }

        .hero-slider {
            position: relative;
            height: 100%;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .slide.active {
            opacity: 1;
        }

        .slide-1 {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                        url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="%23667eea" width="100" height="100"/><circle cx="50" cy="50" r="30" fill="%23764ba2" opacity="0.3"/></svg>');
        }

        .slide-2 {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.9), rgba(0, 242, 254, 0.9)),
                        url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="%234facfe" width="100" height="100"/><polygon points="50,20 90,80 10,80" fill="%2300f2fe" opacity="0.3"/></svg>');
        }

        .slide-3 {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.9), rgba(239, 68, 68, 0.9)),
                        url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="%23ec4899" width="100" height="100"/><rect x="20" y="20" width="60" height="60" fill="%23ef4444" opacity="0.3"/></svg>');
        }

        .slide-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            max-width: 900px;
            padding: 2rem;
        }

        .slide-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            animation: slideUp 0.8s ease;
        }

        .slide-description {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            font-weight: 300;
            animation: slideUp 0.8s ease 0.2s backwards;
        }

        .slide-btn {
            animation: slideUp 0.8s ease 0.4s backwards;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }

        /* Section Container */
        .section {
            padding: 6rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Classes Section */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .class-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-1);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .class-card:hover::before {
            transform: scaleX(1);
        }

        .class-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .class-card:nth-child(2)::before,
        .class-card:nth-child(5)::before {
            background: var(--gradient-3);
        }

        .class-card:nth-child(3)::before,
        .class-card:nth-child(6)::before {
            background: var(--gradient-2);
        }

        .class-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .class-card:nth-child(2) .class-icon,
        .class-card:nth-child(5) .class-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .class-card:nth-child(3) .class-icon,
        .class-card:nth-child(6) .class-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .class-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .class-description {
            color: var(--gray);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .class-btn {
            background: transparent;
            color: var(--primary);
            padding: 0.7rem 1.5rem;
            border: 2px solid var(--primary);
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .class-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Institutes Section */
        .institutes-section {
            background: var(--dark);
            color: white;
            padding: 6rem 0;
            margin: 0;
            width: 100%;
        }

        .institutes-section .section-header {
            padding: 0 2rem;
        }

        .logo-slider {
            overflow: hidden;
            position: relative;
            padding: 2rem 0;
        }

        .logo-track {
            display: flex;
            gap: 4rem;
            animation: scroll 30s linear infinite;
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .institute-logo {
            min-width: 180px;
            height: 100px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark);
            box-shadow: var(--shadow-sm);
        }

        /* Online Classes Section */
        .online-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6rem 2rem;
            margin: 4rem 0;
            width: 100%;
        }

        .online-content {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .online-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .online-description {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 3rem;
            opacity: 0.95;
        }

        .contact-tiles {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-tile {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .contact-tile:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-5px);
        }

        .contact-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .contact-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .contact-value {
            opacity: 0.9;
            font-size: 1rem;
        }

        .btn-white {
            background: white;
            color: var(--secondary);
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .btn-white:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        /* Store Section */
        .store-carousel {
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
        }

        .products-wrapper {
            display: flex;
            gap: 2rem;
            transition: transform 0.5s ease;
        }

        .product-card {
            min-width: 350px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .product-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: white;
        }

        .product-card:nth-child(2) .product-image {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .product-card:nth-child(3) .product-image {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .product-card:nth-child(4) .product-image {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .product-info {
            padding: 2rem;
        }

        .product-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .product-description {
            color: var(--gray);
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .product-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-cart {
            flex: 1;
            background: var(--primary);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cart:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .carousel-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .carousel-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-btn:hover {
            background: var(--secondary);
            transform: scale(1.1);
        }

        /* Testimonials Section */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .testimonial-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            position: relative;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .quote-icon {
            font-size: 3rem;
            color: var(--primary);
            opacity: 0.2;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .testimonial-text {
            color: var(--gray);
            margin-bottom: 2rem;
            font-style: italic;
            line-height: 1.8;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .author-info h4 {
            font-weight: 700;
            color: var(--dark);
        }

        .author-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .stars {
            color: #FFC107;
            margin-top: 0.5rem;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 4rem 2rem 2rem;
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
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.7);
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
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-section h4 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .nav-links {
                gap: 1.5rem;
            }

            .classes-grid,
            .testimonials-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .contact-tiles {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .slide-title {
                font-size: 2.5rem;
            }

            .slide-description {
                font-size: 1rem;
            }

            .classes-grid,
            .testimonials-grid,
            .contact-tiles {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .product-card {
                min-width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">TechLearn</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#classes">Classes</a></li>
                <li><a href="#institutes">Institutes</a></li>
                <li><a href="#online">Online Classes</a></li>
                <li><a href="#store">Store</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <button class="btn btn-outline">Login</button>
                <button class="btn btn-primary">Register</button>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Slider -->
    <section class="hero-section" id="home">
        <div class="hero-slider">
            <div class="slide slide-1 active">
                <div class="slide-content">
                    <h1 class="slide-title">Master ICT Skills for the Digital Future</h1>
                    <p class="slide-description">Join Sri Lanka's premier ICT academy and unlock your potential with expert guidance and comprehensive curriculum</p>
                    <button class="btn btn-white slide-btn">Explore Classes</button>
                </div>
            </div>
            <div class="slide slide-2">
                <div class="slide-content">
                    <h1 class="slide-title">Learn from Industry Experts</h1>
                    <p class="slide-description">Our experienced instructors bring real-world knowledge to help you excel in O/L ICT examinations</p>
                    <button class="btn btn-white slide-btn">Meet Our Team</button>
                </div>
            </div>
            <div class="slide slide-3">
                <div class="slide-content">
                    <h1 class="slide-title">Flexible Online & Physical Classes</h1>
                    <p class="slide-description">Choose your learning path with our hybrid model - attend in person or join from anywhere in Sri Lanka</p>
                    <button class="btn btn-white slide-btn">Join Online</button>
                </div>
            </div>
        </div>
        <div class="slider-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
        </div>
    </section>

    <!-- Classes Section -->
    <section class="section" id="classes">
        <div class="section-header">
            <h2 class="section-title">Our ICT Classes</h2>
            <p class="section-subtitle">Comprehensive ICT education from Grade 6 to Advanced Level, designed to build strong foundations and excel in examinations</p>
        </div>
        <div class="classes-grid">
            <div class="class-card">
                <div class="class-icon">📚</div>
                <h3 class="class-title">Grade 6 ICT</h3>
                <p class="class-description">Introduction to computers, basic operations, and digital literacy fundamentals for young learners.</p>
                <button class="class-btn">Learn More</button>
            </div>
            <div class="class-card">
                <div class="class-icon">💻</div>
                <h3 class="class-title">Grade 7 ICT</h3>
                <p class="class-description">Building on basics with office applications, internet safety, and essential software skills.</p>
                <button class="class-btn">Learn More</button>
            </div>
            <div class="class-card">
                <div class="class-icon">🖥️</div>
                <h3 class="class-title">Grade 8 ICT</h3>
                <p class="class-description">Intermediate concepts including programming basics, databases, and digital communication.</p>
                <button class="class-btn">Learn More</button>
            </div>
            <div class="class-card">
                <div class="class-icon">⚡</div>
                <h3 class="class-title">Grade 9 ICT</h3>
                <p class="class-description">Advanced topics with focus on web development, algorithms, and computational thinking.</p>
                <button class="class-btn">Learn More</button>
            </div>
            <div class="class-card">
                <div class="class-icon">🎓</div>
                <h3 class="class-title">Grade 10 ICT</h3>
                <p class="class-description">Comprehensive O/L preparation covering all syllabus areas with exam-focused practice.</p>
                <button class="class-btn">Learn More</button>
            </div>
            <div class="class-card">
                <div class="class-icon">🚀</div>
                <h3 class="class-title">Grade 11 ICT</h3>
                <p class="class-description">Advanced Level foundation with programming, systems analysis, and project work.</p>
                <button class="class-btn">Learn More</button>
            </div>
        </div>
    </section>

    <!-- Institutes Section -->
    <section class="institutes-section" id="institutes">
        <div class="section-header">
            <h2 class="section-title" style="color: white;">Partner Institutes</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Trusted by leading educational institutions across Sri Lanka</p>
        </div>
        <div class="logo-slider">
            <div class="logo-track">
                <div class="institute-logo">Royal College</div>
                <div class="institute-logo">Ananda College</div>
                <div class="institute-logo">Visakha Vidyalaya</div>
                <div class="institute-logo">St. Joseph's College</div>
                <div class="institute-logo">Musaeus College</div>
                <div class="institute-logo">Nalanda College</div>
                <div class="institute-logo">Gateway College</div>
                <div class="institute-logo">Royal College</div>
                <div class="institute-logo">Ananda College</div>
                <div class="institute-logo">Visakha Vidyalaya</div>
                <div class="institute-logo">St. Joseph's College</div>
                <div class="institute-logo">Musaeus College</div>
            </div>
        </div>
    </section>

    <!-- Online Classes Section -->
    <section class="online-section" id="online">
        <div class="online-content">
            <h2 class="online-title">Learn from Anywhere</h2>
            <p class="online-description">Join our interactive online ICT classes with live sessions, recorded materials, and personalized attention. Perfect for students who prefer flexible learning schedules.</p>
        </div>
        <div class="contact-tiles">
            <div class="contact-tile">
                <div class="contact-icon">📞</div>
                <div class="contact-label">Call Us</div>
                <div class="contact-value">+94 77 123 4567</div>
            </div>
            <div class="contact-tile">
                <div class="contact-icon">💬</div>
                <div class="contact-label">WhatsApp</div>
                <div class="contact-value">+94 77 123 4567</div>
            </div>
            <div class="contact-tile">
                <div class="contact-icon">📧</div>
                <div class="contact-label">Email</div>
                <div class="contact-value"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="e881868e87a89c8d8b80848d899a86c68483">[email&#160;protected]</a></div>
            </div>
        </div>
        <div style="text-align: center;">
            <button class="btn btn-white">Join Online Classes Now</button>
        </div>
    </section>

    <!-- Store Section -->
    <section class="section" id="store">
        <div class="section-header">
            <h2 class="section-title">ICT Learning Resources</h2>
            <p class="section-subtitle">Premium study materials, textbooks, and resources to support your ICT learning journey</p>
        </div>
        <div class="store-carousel">
            <div class="products-wrapper" id="productsWrapper">
                <div class="product-card">
                    <div class="product-image">📖</div>
                    <div class="product-info">
                        <h3 class="product-name">Complete O/L ICT Guide</h3>
                        <p class="product-description">Comprehensive textbook covering entire O/L ICT syllabus with practice questions</p>
                        <div class="product-price">Rs. 1,500</div>
                        <div class="product-actions">
                            <button class="btn-cart">Buy Now</button>
                            <button class="btn-cart" style="background: var(--secondary)">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">📝</div>
                    <div class="product-info">
                        <h3 class="product-name">Past Paper Collection</h3>
                        <p class="product-description">10 years of O/L ICT past papers with marking schemes and solutions</p>
                        <div class="product-price">Rs. 800</div>
                        <div class="product-actions">
                            <button class="btn-cart">Buy Now</button>
                            <button class="btn-cart" style="background: var(--secondary)">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">💿</div>
                    <div class="product-info">
                        <h3 class="product-name">Video Lesson Pack</h3>
                        <p class="product-description">50+ hours of recorded lessons covering all topics with practical examples</p>
                        <div class="product-price">Rs. 2,500</div>
                        <div class="product-actions">
                            <button class="btn-cart">Buy Now</button>
                            <button class="btn-cart" style="background: var(--secondary)">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">🎯</div>
                    <div class="product-info">
                        <h3 class="product-name">Revision Notes Bundle</h3>
                        <p class="product-description">Concise notes and mind maps for quick revision before examinations</p>
                        <div class="product-price">Rs. 600</div>
                        <div class="product-actions">
                            <button class="btn-cart">Buy Now</button>
                            <button class="btn-cart" style="background: var(--secondary)">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-nav">
            <button class="carousel-btn" onclick="scrollProducts(-1)">‹</button>
            <button class="carousel-btn" onclick="scrollProducts(1)">›</button>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section" id="testimonials">
        <div class="section-header">
            <h2 class="section-title">What Our Students Say</h2>
            <p class="section-subtitle">Read success stories from students who achieved excellence with our guidance</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">TechLearn Academy transformed my understanding of ICT. The teachers are amazing and the materials are comprehensive. I scored an A in my O/Levels!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AS</div>
                    <div class="author-info">
                        <h4>Amara Silva</h4>
                        <p>Grade 10 Student</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">The online classes are incredibly convenient and interactive. I can learn at my own pace and the support from teachers is excellent. Highly recommend!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">KP</div>
                    <div class="author-info">
                        <h4>Kavindi Perera</h4>
                        <p>Grade 9 Student</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">Best ICT class in Colombo! The practical approach and exam-focused teaching helped me build confidence. My son improved from C to A in just 6 months.</p>
                <div class="testimonial-author">
                    <div class="author-avatar">NF</div>
                    <div class="author-info">
                        <h4>Nimal Fernando</h4>
                        <p>Parent</p>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
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
                    <li><a href="#home">Home</a></li>
                    <li><a href="#classes">Classes</a></li>
                    <li><a href="#online">Online Classes</a></li>
                    <li><a href="#store">Store</a></li>
                    <li><a href="#testimonials">Reviews</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Classes</h4>
                <ul class="footer-links">
                    <li><a href="#">Grade 6-8 ICT</a></li>
                    <li><a href="#">Grade 9 ICT</a></li>
                    <li><a href="#">O/L ICT (Grade 10)</a></li>
                    <li><a href="#">A/L ICT (Grade 11)</a></li>
                    <li><a href="#">Private Tuition</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul class="footer-links">
                    <li>📍 123, Galle Road, Colombo 03</li>
                    <li>📞 +94 77 123 4567</li>
                    <li>📧 <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d9b0b7bfb699adbcbab1b5bcb8abb7f7b5b2">[email&#160;protected]</a></li>
                    <li>⏰ Mon-Sat: 8AM - 8PM</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 TechLearn ICT Academy. All rights reserved. | Designed with passion for education</p>
        </div>
    </footer>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        // Hero Slider Functionality
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                dots[i].classList.remove('active');
            });
            slides[index].classList.add('active');
            dots[index].classList.add('active');
        }

        function nextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            showSlide(currentSlideIndex);
        }

        function currentSlide(index) {
            currentSlideIndex = index;
            showSlide(currentSlideIndex);
        }

        // Auto-advance slides every 5 seconds
        setInterval(nextSlide, 5000);

        // Product Carousel Functionality
        let scrollPosition = 0;
        const productsWrapper = document.getElementById('productsWrapper');
        const cardWidth = 370; // 350px + 20px gap

        function scrollProducts(direction) {
            const maxScroll = -(cardWidth * (productsWrapper.children.length - 3));
            scrollPosition += direction * cardWidth;
            
            if (scrollPosition > 0) scrollPosition = 0;
            if (scrollPosition < maxScroll) scrollPosition = maxScroll;
            
            productsWrapper.style.transform = `translateX(${scrollPosition}px)`;
        }

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });

        // Add animation on scroll for cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);