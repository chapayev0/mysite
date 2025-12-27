    <footer class="footer" id="contact">
        <div class="footer-content">
            <div class="footer-brand">
            <div >
                <img src="assest/logo/logo1.png" alt="TechLearn Logo" style="height: 70px;">
            </div>
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
                    <li><a href="class_details.php?grade=6">Grade 6 ICT</a></li>
                    <li><a href="class_details.php?grade=7">Grade 7 ICT</a></li>
                    <li><a href="class_details.php?grade=8">Grade 8 ICT</a></li>
                    <li><a href="class_details.php?grade=9">Grade 9 ICT</a></li>
                    <li><a href="class_details.php?grade=10">Grade 10 ICT</a></li>
                    <li><a href="class_details.php?grade=11">Grade 11 ICT</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul class="footer-links">
                    <li>📍 Embilipitiya</li>
                    <li>📞 0777 695 130</li>
                    <li>💬 0777 695 130 (WhatsApp)</li>
                    <li>📧 <a href="mailto:sdilhara544@gmail.com">sdilhara544@gmail.com</a></li>
                    <li>⏰ Mon-Sat: 8AM - 8PM</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 ICT with Dilhara. All rights reserved. | Designed with passion for education</p>
        </div>
        
        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top">↑</button>

        <style>


            .back-to-top {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 50px;
                height: 50px;
                background: var(--primary);
                color: var(--light);
                border: none;
                border-radius: 50%;
                font-size: 1.5rem;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                opacity: 0;
                visibility: hidden;
                transform: translateY(20px);
                transition: all 0.3s ease;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .back-to-top.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .back-to-top:hover {
                background: var(--primary-hover);
                transform: translateY(-5px);
            }
        </style>

        <script>
            const backToTopBtn = document.getElementById('backToTop');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });

            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        </script>
    </footer>
