<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="assest/logo/logo1.png" alt="ICT with Dilhara Logo" style="height: 50px;">
            </div>
            <ul class="nav-links">
                <li><a href="index.php#home">Home</a></li>
                <li><a href="index.php#classes">Classes</a></li>
                <li><a href="index.php#online">Online Classes</a></li>
                <li><a href="index.php#store">Store</a></li>
                <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary" style="text-decoration:none;">Register</a>
            </div>
        </div>
    </nav>
