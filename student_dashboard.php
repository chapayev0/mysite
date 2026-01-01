<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // Should ideally not happen if data integrity is maintained
    $error = "Student profile not found. Please contact admin.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | ICT with Dilhara</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--light);
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: var(--dark);
            color: white;
            min-height: 100vh;
            padding: 2rem;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            color: var(--primary);
        }
        .nav-link {
            display: block;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 1rem 0;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            font-weight: 600;
        }
        .main-content {
            flex: 1;
            padding: 3rem;
        }
        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 800px;
        }
        h2 {
            margin-top: 0;
            color: var(--dark);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .profile-item {
            margin-bottom: 1rem;
        }
        .label {
            display: block;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .value {
            color: var(--dark);
            font-weight: 600;
            font-size: 1.1rem;
        }
        .full-width {
            grid-column: span 2;
        }
        .section-header {
            grid-column: span 2;
            color: var(--primary);
            font-weight: 700;
            margin-top: 1rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Student Dashboard</div>
        <nav>
            <a href="student_dashboard.php" class="nav-link active">Dashboard</a>
            <a href="student_profile.php" class="nav-link">Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </nav>
    </div>
    <div class="main-content">
        <h1 style="color: var(--dark); margin-bottom: 2rem;">Welcome, <?php echo htmlspecialchars($student['first_name']); ?>!</h1>

        <?php
        // Fetch resources for the student's grade
        $student_grade = $student['grade'] ?? '';
        $resources = [];
        if ($student_grade) {
            // New system: Grade is just a number (e.g. '6')
            // We search for this specific grade in the resources table.
            // If the database still has old 'g6' entries, we might miss them unless we include them.
            // But user said "renamed g6 to 6", implying the structure is updated.
            // Let's stick to the student's grade value, but ensure we strip 'g' if it exists in the student's profile just in case.
            
            $clean_grade = str_replace('g', '', strtolower($student_grade));
            
            // We search for the clean grade (e.g. '6')
            // Note: If you have old resources stored as 'g6', they won't show unless we search for 'g'.$clean_grade too.
            // I will keep both for safety: '6' and 'g6' to ensure nothing breaks during transition.
            
            $grade_variants = [$clean_grade, 'g' . $clean_grade];
            
            // Create a comma-separated string of placeholders for the IN clause
            $placeholders = implode(',', array_fill(0, count($grade_variants), '?'));
            $types = str_repeat('s', count($grade_variants));
            
            $stmt = $conn->prepare("SELECT * FROM resources WHERE grade IN ($placeholders) ORDER BY created_at DESC");
            $stmt->bind_param($types, ...$grade_variants);
            
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $resources[$row['category']][] = $row;
            }
            $stmt->close();
        }
        
        $categories = [
            'theory' => 'Theory',
            'tute' => 'Tutes',
            'paper' => 'Papers',
            'video' => 'Videos'
        ];
        
        $hasResources = false;
        foreach($categories as $catKey => $catName) {
            if(!empty($resources[$catKey])) {
                $hasResources = true;
                break;
            }
        }
        ?>

        <?php if(!$hasResources): ?>
             <div class="card">
                <p style="color: var(--gray); font-size: 1.1rem;">No resources available for your grade yet.</p>
             </div>
        <?php endif; ?>

        <?php foreach ($categories as $catKey => $catName): ?>
            <?php if (!empty($resources[$catKey])): ?>
                <!-- Section Container for <?php echo $catName; ?> -->
                <div style="margin-bottom: 4rem; display: flow-root; border-bottom: 1px solid transparent;">
                    <h2 style="color: var(--dark); border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 2rem;"><?php echo $catName; ?></h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                        <?php foreach ($resources[$catKey] as $resource): ?>
                            <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; height: 100%;">
                                <div style="flex: 1;">
                                    <span style="background: var(--secondary); color: white; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        Grade <?php echo htmlspecialchars($resource['grade']); ?>
                                    </span>
                                    <h3 style="margin: 1rem 0 0.5rem 0; color: var(--dark); font-size: 1.3rem; word-wrap: break-word; overflow-wrap: break-word;">
                                        <?php if(!empty($resource['lesson_number'])): ?>
                                            <span style="font-size: 0.9rem; color: var(--primary); display: block; margin-bottom: 0.2rem;">
                                                <?php 
                                                    $ln = $resource['lesson_number'];
                                                    // Add 'Lesson ' prefix for theory cards if not already present
                                                    if ($catKey === 'theory' && stripos($ln, 'Lesson') === false) {
                                                        $ln = 'Lesson ' . $ln;
                                                    }
                                                    echo htmlspecialchars($ln); 
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($resource['title']); ?>
                                    </h3>

                                </div>
                                
                                <?php if ($catKey === 'video'): ?>
                                    <div style="margin-top:auto; display:flex; gap:10px;">
                                        <a href="video_player.php?id=<?php echo $resource['id']; ?>" target="_blank" 
                                           style="flex:1; text-align: center; background: var(--primary); color: white; text-decoration: none; padding: 0.8rem; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                                            Play
                                        </a>
                                        <?php if ($resource['allow_download'] && $resource['video_type'] === 'file'): ?>
                                            <a href="<?php echo htmlspecialchars($resource['filepath']); ?>" download
                                               style="flex:1; text-align: center; background: #ea580c; color: white; text-decoration: none; padding: 0.8rem; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                                                Download
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($resource['filepath']); ?>" target="_blank" 
                                       style="display: inline-block; text-align: center; background: var(--primary); color: white; text-decoration: none; padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; margin-top: auto; transition: background 0.2s;">
                                        Open
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
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
</body>
</html>
