<?php
session_start();
include 'db_connect.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id === 0) {
    header("Location: index.php");
    exit;
}

// Ensure the user actually owns this order
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        header("Location: index.php");
        exit;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | ICT with Dilhara Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .success-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
        .success-card { background: white; padding: 4rem 3rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; max-width: 600px; width: 100%; border: 1px solid #e2e8f0; }
        .success-icon { font-size: 5rem; margin-bottom: 1.5rem; display: inline-block; background: #dcfce7; border-radius: 50%; width: 120px; height: 120px; line-height: 120px; }
        h1 { color: var(--dark); margin-top: 0; margin-bottom: 1rem; font-weight: 800; font-size: 2.5rem; }
        .order-id { font-size: 1.2rem; color: var(--gray); font-weight: 600; margin-bottom: 2rem; background: #f1f5f9; display: inline-block; padding: 0.5rem 1rem; border-radius: 8px; }
        .info-box { background: rgba(0, 102, 255, 0.05); border-left: 4px solid var(--primary); padding: 1.5rem; border-radius: 0 8px 8px 0; text-align: left; margin-bottom: 2rem; }
        .info-box p { margin: 0; color: var(--dark); font-size: 1.1rem; line-height: 1.6; }
        .btn-home { display: inline-block; background: var(--dark); color: white; padding: 1rem 2.5rem; border-radius: 10px; font-weight: 600; text-decoration: none; transition: background 0.3s; font-size: 1.1rem; }
        .btn-home:hover { background: #1e293b; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1>Order Placed!</h1>
            <div class="order-id">Order Reference: #<?php echo str_pad($order_id, 6, "0", STR_PAD_LEFT); ?></div>
            
            <div class="info-box">
                <p><strong>Thank you for your purchase!</strong></p>
                <p style="margin-top: 0.5rem;">Our team will contact you shortly. From that point, our team will proceed to payments and upload the receipt via email out of the system.</p>
            </div>
            
            <a href="store.php" class="btn-home">Continue Shopping</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
