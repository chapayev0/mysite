<?php
// Function to check active link
function isActive($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page) ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="logo">Admin Dashboard</div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link <?php echo isActive('admin_dashboard.php'); ?>">Dashboard</a>
        <a href="admin_students.php" class="nav-link <?php echo isActive('admin_students.php'); echo isActive('admin_edit_student.php'); ?>">Students</a>
        <a href="admin_classes.php" class="nav-link <?php echo isActive('admin_classes.php'); echo isActive('admin_edit_class.php'); ?>">Classes</a>
        <a href="admin_products.php" class="nav-link <?php echo isActive('admin_products.php'); echo isActive('admin_edit_product.php'); ?>">Store</a>
        <a href="admin_resources.php" class="nav-link <?php echo isActive('admin_resources.php'); echo isActive('admin_edit_resource.php'); ?>">Resources</a>
        <a href="admin_requests.php" class="nav-link <?php echo isActive('admin_requests.php'); ?>">Requests</a>
        <a href="admin_inbox.php" class="nav-link <?php echo isActive('admin_inbox.php'); ?>">Inbox</a>
        <a href="logout.php" class="logout-link nav-link">Logout</a>
    </nav>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-content">
        <h3>Confirm Logout</h3>
        <p>Are you sure you want to log out?</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
            <a href="logout.php" class="btn-confirm">Logout</a>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        z-index: 1000;
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease-out;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transform: translateY(20px);
        animation: slideUp 0.3s ease-out forwards;
    }
    
    .modal-content h3 {
        margin-top: 0;
        color: #0F172A;
        font-size: 1.5rem;
    }
    
    .modal-content p {
        color: #64748B;
        margin-bottom: 2rem;
    }
    
    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    
    .btn-cancel, .btn-confirm {
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 1rem;
    }
    
    .btn-cancel {
        background: #F1F5F9;
        color: #64748B;
    }
    
    .btn-cancel:hover {
        background: #E2E8F0;
        color: #475569;
    }
    
    .btn-confirm {
        background: #EF4444;
        color: white;
    }
    
    .btn-confirm:hover {
        background: #DC2626;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLinks = document.querySelectorAll('a[href="logout.php"]:not(.btn-confirm)');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('logoutModal').style.display = 'flex';
            });
        });
    });

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    
    // Close on outside click
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLogoutModal();
        }
    });
</script>
