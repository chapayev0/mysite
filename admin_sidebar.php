<?php
// Function to check active link
function isActive($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page) ? 'active' : '';
}
?>
<div class="mobile-toggle" onclick="toggleSidebar()">
    <span></span>
    <span></span>
    <span></span>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">Admin Dashboard</div>
        <button class="close-sidebar" onclick="toggleSidebar()">×</button>
    </div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link <?php echo isActive('admin_dashboard.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg></span> 
            Dashboard
        </a>
        <a href="admin_students.php" class="nav-link <?php echo isActive('admin_students.php'); echo isActive('admin_edit_student.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span> 
            Students
        </a>
        <a href="admin_classes.php" class="nav-link <?php echo isActive('admin_classes.php'); echo isActive('admin_edit_class.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></span> 
            Classes
        </a>
        <a href="admin_products.php" class="nav-link <?php echo isActive('admin_products.php'); echo isActive('admin_edit_product.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></span> 
            Store
        </a>
        <a href="admin_resources.php" class="nav-link <?php echo isActive('admin_resources.php'); echo isActive('admin_edit_resource.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg></span> 
            Resources
        </a>
        <a href="admin_papers.php" class="nav-link <?php echo isActive('admin_papers.php'); echo isActive('admin_edit_paper.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></span> 
            Paper Archive
        </a>
        <a href="admin_requests.php" class="nav-link <?php echo isActive('admin_requests.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg></span> 
            Requests
        </a>
        <a href="admin_wall.php" class="nav-link <?php echo isActive('admin_wall.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span> 
            Wall Requests
        </a>
        <a href="admin_playground_categories.php" class="nav-link <?php echo isActive('admin_playground_categories.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 12 12 17 22 12"></polyline><polyline points="2 17 12 22 22 17"></polyline></svg></span> 
            Playground Categories
        </a>
        <a href="admin_playground_games.php" class="nav-link <?php echo isActive('admin_playground_games.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="15" y1="13" x2="15.01" y2="13"></line><line x1="18" y1="11" x2="18.01" y2="11"></line><rect x="2" y="6" width="20" height="12" rx="2"></rect></svg></span> 
            Playground Games
        </a>
        <a href="admin_tool_categories.php" class="nav-link <?php echo isActive('admin_tool_categories.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg></span> 
            Tool Categories
        </a>
        <a href="admin_tools.php" class="nav-link <?php echo isActive('admin_tools.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 12 12 17 22 12"></polyline><polyline points="2 17 12 22 22 17"></polyline></svg></span> 
            Tools
        </a>
        <a href="admin_menus.php" class="nav-link <?php echo isActive('admin_menus.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></span> 
            Menu Settings
        </a>
        <a href="admin_inbox.php" class="nav-link <?php echo isActive('admin_inbox.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span> 
            Inbox
        </a>
        <a href="admin_settings.php" class="nav-link <?php echo isActive('admin_settings.php'); ?>">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg></span> 
            Settings
        </a>
        <a href="#" class="nav-link" onclick="openLogoutModal()">
            <span class="icon-frame"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></span> 
            Logout
        </a>
    </nav>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

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

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    
    // Close on outside click for modal
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLogoutModal();
        }
    });
</script>

<style>
    /* Global Reset for Sidebar Context */
    * {
        box-sizing: border-box;
    }

    /* Mobile Toggle */
    .mobile-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: #0066FF;
        color: white;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        flex-direction: column;
        gap: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .mobile-toggle span {
        width: 25px;
        height: 3px;
        background: white;
        border-radius: 2px;
    }

    /* Sidebar Header & Close Button */
    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .logo {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0066FF;
    }

    .close-sidebar {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    /* Global Body and Main Content Scroll Reset for Admin Panel */
    body {
        height: 100vh;
        overflow: hidden;
    }
    
    .main-content {
        height: 100vh;
        overflow-y: auto;
    }

    /* Sidebar Base Styles */
    .sidebar {
        width: 250px;
        background: #0F172A;
        color: white;
        height: 100vh;
        overflow-y: auto;
        padding: 2rem;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        transition: transform 0.3s ease;
    }
    
    /* Custom Scrollbar for Sidebar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        padding: 0.6rem 1rem;
        margin-bottom: 0.3rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .icon-frame {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .nav-link:hover .icon-frame, .nav-link.active .icon-frame {
        border-color: rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-link:hover, .nav-link.active {
        color: white;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        backdrop-filter: blur(2px);
    }

    /* Responsive Media Query */
    @media (max-width: 768px) {
        .mobile-toggle {
            display: flex;
        }

        .sidebar {
            transform: translateX(-100%);
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-overlay.active {
            display: block;
        }

        .close-sidebar {
            display: block;
        }
        
        .sidebar-header .logo {
            margin-bottom: 0;
        }
        
        /* Force main content adjustment globally */
        body .main-content {
            margin-left: 0 !important;
            padding: 1.5rem !important;
            padding-top: 5rem !important; /* Space for toggle */
        }
    }
    
    /* Modal Styles */
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
