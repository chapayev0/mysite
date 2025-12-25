<?php
// Function to check active link
function isActive($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page) ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="logo">TechLearn Admin</div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link <?php echo isActive('admin_dashboard.php'); ?>">Dashboard</a>
        <a href="admin_students.php" class="nav-link <?php echo isActive('admin_students.php'); echo isActive('admin_edit_student.php'); ?>">Students</a>
        <a href="admin_classes.php" class="nav-link <?php echo isActive('admin_classes.php'); echo isActive('admin_edit_class.php'); ?>">Classes</a>
        <a href="admin_products.php" class="nav-link <?php echo isActive('admin_products.php'); echo isActive('admin_edit_product.php'); ?>">Store</a>
        <a href="admin_resources.php" class="nav-link <?php echo isActive('admin_resources.php'); echo isActive('admin_edit_resource.php'); ?>">Resources</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </nav>
</div>
