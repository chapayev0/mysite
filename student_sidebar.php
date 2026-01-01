<div class="sidebar">
    <div class="logo">Student Dashboard</div>
    <nav>
        <a href="student_dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'student_dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="link_library.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'link_library.php' ? 'active' : ''; ?>">Link Library</a>
        <a href="student_profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'student_profile.php' ? 'active' : ''; ?>">My Profile</a>
        <a href="#" onclick="openLogoutModal()" class="nav-link">Logout</a>
    </nav>
</div>

<script>
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
</script>
