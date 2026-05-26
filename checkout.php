<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: store.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user addresses
$stmt = $conn->prepare("SELECT full_name, email, phone, billing_address, shipping_address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

$cart_items = $_SESSION['cart'];
$products = [];
$total_price = 0;

// Fetch product details for summary
$ids = array_keys($cart_items);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, name, price FROM store_products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pid = $row['id'];
    $row['qty'] = $cart_items[$pid];
    $row['subtotal'] = $row['price'] * $row['qty'];
    $total_price += $row['subtotal'];
    $products[] = $row;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $billing_address = mysqli_real_escape_string($conn, $_POST['billing_address']);
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $same_as_billing = isset($_POST['same_as_billing']) ? true : false;
    
    if ($same_as_billing) {
        $shipping_address = $billing_address;
    }

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Update user addresses for future use
        $update_stmt = $conn->prepare("UPDATE users SET billing_address = ?, shipping_address = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $billing_address, $shipping_address, $user_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Create Order
        $order_status = 'Awaiting Payment';
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, billing_address, shipping_address) VALUES (?, ?, ?, ?, ?)");
        $order_stmt->bind_param("idsss", $user_id, $total_price, $order_status, $billing_address, $shipping_address);
        $order_stmt->execute();
        $order_id = $conn->insert_id;
        $order_stmt->close();

        // Create Order Items
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($products as $p) {
            $item_stmt->bind_param("iiid", $order_id, $p['id'], $p['qty'], $p['price']);
            $item_stmt->execute();
        }
        $item_stmt->close();

        $conn->commit();
        
        // Clear Cart
        unset($_SESSION['cart']);
        
        header("Location: order_success.php?id=" . $order_id);
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error placing order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ICT with Dilhara Academy</title>
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
        .checkout-container { max-width: 1200px; margin: 8rem auto 4rem; padding: 0 2rem; flex: 1; width: 100%; box-sizing: border-box; }
        .checkout-header { margin-bottom: 2rem; border-bottom: 2px solid var(--border); padding-bottom: 1rem; }
        .checkout-header h1 { font-size: 2.5rem; color: var(--dark); margin: 0; }
        
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 3rem; align-items: start; }
        @media (max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } }
        
        .form-section { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 2rem; border: 1px solid var(--border); margin-bottom: 2rem; }
        .form-section h2 { margin-top: 0; color: var(--dark); font-size: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem; }
        .form-group textarea, .form-group input { width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box; }
        .form-group textarea:focus, .form-group input:focus { outline: none; border-color: var(--primary); }
        
        .summary-box { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border); position: sticky; top: 100px; }
        .summary-title { font-size: 1.5rem; font-weight: 700; margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; }
        
        .item-list { margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 1rem; }
        .item-name { color: var(--dark); font-weight: 500; }
        .item-price { color: var(--gray); }
        
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--gray); font-size: 1.1rem; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--dark); font-size: 1.5rem; font-weight: 800; color: var(--dark); }
        
        .payment-method { background: #f8fafc; border: 1px solid var(--border); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .payment-method input[type="radio"] { width: 20px; height: 20px; accent-color: var(--primary); }
        .payment-method label { font-weight: 600; cursor: pointer; color: var(--dark); flex: 1; margin: 0; }
        .payment-info { font-size: 0.9rem; color: var(--gray); margin-top: 0.5rem; margin-left: 2.2rem; }
        
        .btn-submit { width: 100%; background: var(--primary); color: white; border: none; padding: 1.2rem; border-radius: 10px; font-size: 1.2rem; font-weight: 700; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #0052cc; }
    </style>
    <script>
        function toggleShipping() {
            var checkbox = document.getElementById('same_as_billing');
            var shippingGroup = document.getElementById('shipping_group');
            if (checkbox.checked) {
                shippingGroup.style.display = 'none';
                document.getElementById('shipping_address').removeAttribute('required');
            } else {
                shippingGroup.style.display = 'block';
                document.getElementById('shipping_address').setAttribute('required', 'required');
            }
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
        </div>
        
        <?php if(isset($error)): ?>
            <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #fecaca; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="checkout.php">
            <div class="checkout-grid">
                <div>
                    <div class="form-section">
                        <h2>Contact Details</h2>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_info['full_name']); ?>" disabled style="background:#f1f5f9;">
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" disabled style="background:#f1f5f9;">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" value="<?php echo htmlspecialchars($user_info['phone']); ?>" disabled style="background:#f1f5f9;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2>Billing Address</h2>
                        <div class="form-group">
                            <label for="billing_address">Full Address</label>
                            <textarea id="billing_address" name="billing_address" rows="3" required placeholder="Enter your full billing address..."><?php echo htmlspecialchars($user_info['billing_address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" id="same_as_billing" name="same_as_billing" onclick="toggleShipping()" checked style="width:18px; height:18px;">
                            <label for="same_as_billing" style="cursor:pointer; font-weight:500;">Shipping address is the same as billing address</label>
                        </div>
                        
                        <div class="form-group" id="shipping_group" style="display: none;">
                            <label for="shipping_address">Shipping Address</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3" placeholder="Enter your full shipping address..."><?php echo htmlspecialchars($user_info['shipping_address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="summary-box">
                        <h3 class="summary-title">Your Order</h3>
                        
                        <div class="item-list">
                            <?php foreach($products as $p): ?>
                                <div class="item-row">
                                    <span class="item-name"><?php echo htmlspecialchars($p['name']); ?> x <?php echo $p['qty']; ?></span>
                                    <span class="item-price">Rs. <?php echo number_format($p['subtotal'], 0); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rs. <?php echo number_format($total_price, 0); ?></span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>Rs. <?php echo number_format($total_price, 0); ?></span>
                        </div>
                        
                        <div style="margin-top: 2rem; margin-bottom: 1rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--dark);">Payment Method</h4>
                            <div class="payment-method">
                                <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" checked>
                                <div>
                                    <label for="bank_transfer">Bank Transfer</label>
                                    <div class="payment-info">Make your payment directly into our bank account. Your order will not be processed until the funds have cleared in our account.</div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-submit">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
