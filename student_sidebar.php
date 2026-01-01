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

<!-- Shared Logout Modal -->
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
        z-index: 9999;
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
        text-decoration: none;
        font-family: inherit;
        font-size: 1rem;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #64748B;
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
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
