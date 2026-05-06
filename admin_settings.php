<?php
session_start();
include 'db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';

function execute_sql($conn, $sql, $success_msg) {
    if (trim($sql) === '') {
        return "<div class='error-msg'>SQL query is empty.</div>";
    }
    
    if ($conn->multi_query($sql)) {
        $success = true;
        $error_msg = "";
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
            if (!$conn->more_results()) {
                break;
            }
            if (!$conn->next_result()) {
                $success = false;
                $error_msg = $conn->error;
                break;
            }
        } while (true);
        
        if ($success) {
            return "<div class='success-msg'>" . htmlspecialchars($success_msg) . "</div>";
        } else {
            return "<div class='error-msg'>Error executing query: " . htmlspecialchars($error_msg) . "</div>";
        }
    } else {
        return "<div class='error-msg'>Error executing SQL: " . htmlspecialchars($conn->error) . "</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_db'])) {
        $sql_file = __DIR__ . '/db_updates.sql';
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            $sql = str_replace("\0", "", $sql);
            $message = execute_sql($conn, $sql, "Database updated successfully from db_updates.sql!");
        } else {
            $message = "<div class='error-msg'>db_updates.sql file not found.</div>";
        }
    } elseif (isset($_POST['execute_custom_sql'])) {
        $custom_sql = $_POST['custom_sql'] ?? '';
        $message = execute_sql($conn, $custom_sql, "Custom SQL executed successfully!");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Base Admin Styles - Copied/adapted for consistency */
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; margin: 0; color: #1E293B; }
        .dashboard-container { display: flex; height: 100vh; }
        .main-content { flex: 1; padding: 2rem; overflow-y: auto; margin-left: 250px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.8rem; font-weight: 700; color: #0F172A; margin: 0; }
        
        .card { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); max-width: 800px; margin-bottom: 2rem; }
        .card h2 { margin-top: 0; font-size: 1.2rem; color: #334155; border-bottom: 1px solid #E2E8F0; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .card p { color: #64748B; line-height: 1.6; margin-bottom: 1.5rem; }
        
        .btn-primary { background: #0066FF; color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; font-size: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary:hover { background: #0052CC; }
        
        .success-msg { background: #DCFCE7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; border: 1px solid #BBF7D0; }
        .error-msg { background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; border: 1px solid #FECACA; }
        
        .sql-preview { background: #1E293B; color: #E2E8F0; padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.9rem; overflow-x: auto; max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem; white-space: pre-wrap; }
        
        .sql-textarea { width: 100%; min-height: 200px; background: #1E293B; color: #E2E8F0; padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.9rem; border: 1px solid #334155; margin-bottom: 1.5rem; box-sizing: border-box; resize: vertical; }
        .sql-textarea:focus { outline: none; border-color: #0066FF; }

        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h1>Settings</h1>
            </div>

            <?php echo $message; ?>

            <div class="card">
                <h2>Database Backup</h2>
                <p>
                    Download a complete backup of your current database, including all tables and data. This generates a safe, downloadable <code>.sql</code> file.
                </p>
                <form method="GET" action="admin_backup_db.php">
                    <button type="submit" class="btn-primary" style="background: #10B981;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Download SQL Backup
                    </button>
                </form>
            </div>

            <div class="card">
                <h2>Sync from db_updates.sql</h2>
                <p>
                    Execute all SQL statements currently saved in your <strong>db_updates.sql</strong> file in the root directory.
                </p>

                <?php 
                $sql_file = __DIR__ . '/db_updates.sql';
                if (file_exists($sql_file)): 
                    $content = file_get_contents($sql_file);
                    $content = str_replace("\0", "", $content); // Handle potential null bytes
                ?>
                    <div class="sql-preview"><?php echo htmlspecialchars($content ?: 'File is empty.'); ?></div>
                    
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to execute db_updates.sql? This action cannot be undone.');">
                        <button type="submit" name="update_db" class="btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            Execute File
                        </button>
                    </form>
                <?php else: ?>
                    <div class="error-msg">The file <strong>db_updates.sql</strong> was not found in the root directory.</div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Execute Custom SQL</h2>
                <p>
                    Paste your custom SQL queries below and execute them directly without modifying the file.
                </p>
                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to execute this custom SQL? This action cannot be undone.');">
                    <textarea name="custom_sql" class="sql-textarea" placeholder="CREATE TABLE IF NOT EXISTS new_table (...);"></textarea>
                    
                    <button type="submit" name="execute_custom_sql" class="btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Run Custom SQL
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
