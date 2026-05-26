<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Allow customers, but also students/admins to access their dashboard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Handle Profile/Address Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_profile') {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $phone, $user_id);
        if ($stmt->execute()) {
            $msg = "Profile updated successfully.";
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update_address') {
        $billing = mysqli_real_escape_string($conn, $_POST['billing_address']);
        $shipping = mysqli_real_escape_string($conn, $_POST['shipping_address']);
        $stmt = $conn->prepare("UPDATE users SET billing_address = ?, shipping_address = ? WHERE id = ?");
        $stmt->bind_param("ssi", $billing, $shipping, $user_id);
        if ($stmt->execute()) {
            $msg = "Addresses updated successfully.";
        }
        $stmt->close();
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch orders
$orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// Default tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'history';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | ICT with Dilhara Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --border: #E2E8F0;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .dashboard-container { max-width: 1200px; margin: 8rem auto 4rem; padding: 0 2rem; flex: 1; width: 100%; box-sizing: border-box; display: grid; grid-template-columns: 280px 1fr; gap: 3rem; align-items: start; }
        
        @media (max-width: 900px) {
            .dashboard-container { grid-template-columns: 1fr; }
        }

        /* Sidebar Navigation */
        .dash-sidebar { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border); overflow: hidden; }
        .dash-profile-card { padding: 2rem; text-align: center; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, rgba(0, 102, 255, 0.05) 0%, rgba(124, 58, 237, 0.05) 100%); }
        .avatar { width: 80px; height: 80px; background: var(--dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; margin: 0 auto 1rem; }
        .dash-name { font-size: 1.2rem; font-weight: 700; color: var(--dark); margin: 0; }
        .dash-email { color: var(--gray); font-size: 0.9rem; margin-top: 0.2rem; }
        
        .dash-nav { list-style: none; padding: 0; margin: 0; }
        .dash-nav li a { display: block; padding: 1.2rem 2rem; color: var(--dark); text-decoration: none; font-weight: 500; border-bottom: 1px solid var(--border); transition: all 0.2s; }
        .dash-nav li:last-child a { border-bottom: none; }
        .dash-nav li a:hover, .dash-nav li a.active { background: #f8fafc; color: var(--primary); padding-left: 2.5rem; border-left: 4px solid var(--primary); }
        
        /* Main Content Area */
        .dash-content { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border); padding: 2.5rem; }
        .content-header { border-bottom: 2px solid var(--border); padding-bottom: 1rem; margin-bottom: 2rem; }
        .content-header h2 { font-size: 1.8rem; color: var(--dark); margin: 0; }
        
        /* Forms */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; box-sizing: border-box; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
        
        .btn-submit { background: var(--primary); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #0052cc; }
        
        /* Orders Table */
        .orders-table { width: 100%; border-collapse: collapse; }
        .orders-table th, .orders-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .orders-table th { font-weight: 600; color: var(--gray); font-size: 0.9rem; text-transform: uppercase; }
        .orders-table td { color: var(--dark); }
        .status-badge { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; }
        .status-awaiting { background: #fef3c7; color: #d97706; }
        .status-processing { background: #e0e7ff; color: #4338ca; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #ef4444; }
        
        .alert-success { background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #bbf7d0; }
        
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dash-sidebar">
            <div class="dash-profile-card">
                <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                <h3 class="dash-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <div class="dash-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <ul class="dash-nav">
                <li><a href="customer_dashboard.php?tab=history" class="<?php echo $active_tab == 'history' ? 'active' : ''; ?>">Purchase History & Progress</a></li>
                <li><a href="customer_dashboard.php?tab=profile" class="<?php echo $active_tab == 'profile' ? 'active' : ''; ?>">Profile Details</a></li>
                <li><a href="customer_dashboard.php?tab=address" class="<?php echo $active_tab == 'address' ? 'active' : ''; ?>">Billing & Shipping Address</a></li>
                <li><a href="cart.php">Shopping Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a></li>
                <li><a href="logout.php" style="color: #ef4444;">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dash-content">
            <?php if($msg): ?>
                <div class="alert-success">✅ <?php echo $msg; ?></div>
            <?php endif; ?>

            <?php if ($active_tab == 'history'): ?>
                <div class="content-header">
                    <h2>Purchase History & Progress</h2>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                        <h3>No orders yet</h3>
                        <p>When you purchase an item, it will appear here.</p>
                        <a href="store.php" class="btn-submit" style="display:inline-block; margin-top:1rem; text-decoration:none;">Go to Store</a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $o): ?>
                                    <tr>
                                        <td><strong>#<?php echo str_pad($o['id'], 6, "0", STR_PAD_LEFT); ?></strong></td>
                                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                        <td>Rs. <?php echo number_format($o['total_amount'], 0); ?></td>
                                        <td>
                                            <?php
                                                $s_class = 'status-awaiting';
                                                if ($o['status'] == 'Processing') $s_class = 'status-processing';
                                                if ($o['status'] == 'Completed') $s_class = 'status-completed';
                                                if ($o['status'] == 'Cancelled') $s_class = 'status-cancelled';
                                            ?>
                                            <span class="status-badge <?php echo $s_class; ?>"><?php echo $o['status']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($active_tab == 'profile'): ?>
                <div class="content-header">
                    <h2>Profile Details</h2>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f8fafc; color:#64748b;">
                        <small style="color:var(--gray); display:block; margin-top:0.3rem;">Email cannot be changed.</small>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </form>

            <?php elseif ($active_tab == 'address'): ?>
                <div class="content-header">
                    <h2>Billing & Shipping Address</h2>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_address">
                    <div class="form-group">
                        <label>Billing Address</label>
                        <textarea name="billing_address" rows="3" placeholder="Enter full billing address..."><?php echo htmlspecialchars($user['billing_address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea name="shipping_address" rows="3" placeholder="Enter full shipping address..."><?php echo htmlspecialchars($user['shipping_address'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Save Addresses</button>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
