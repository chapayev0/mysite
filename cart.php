<?php
session_start();
include 'db_connect.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$total_price = 0;

if (!empty($cart_items)) {
    // Get product IDs
    $ids = array_keys($cart_items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    // Fetch products
    $sql = "SELECT p.*, 
            (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image 
            FROM store_products p WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // Create types string 'i' for each ID
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $pid = $row['id'];
        $row['qty'] = $cart_items[$pid];
        $row['subtotal'] = $row['price'] * $row['qty'];
        $total_price += $row['subtotal'];
        
        if (empty($row['primary_image'])) {
            $img_res = $conn->query("SELECT image_url FROM product_images WHERE product_id = " . $pid . " LIMIT 1");
            if ($img_row = $img_res->fetch_assoc()) {
                $row['primary_image'] = $img_row['image_url'];
            } else {
                 $row['primary_image'] = '📦';
            }
        }
        $products[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | ICT with Dilhara Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assest/css/style.css">
    <style>
        /* Cart Specific CSS */
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --border: #E2E8F0;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .cart-container { max-width: 1200px; margin: 8rem auto 4rem; padding: 0 2rem; flex: 1; width: 100%; box-sizing: border-box; }
        .cart-header { margin-bottom: 2rem; border-bottom: 2px solid var(--border); padding-bottom: 1rem; }
        .cart-header h1 { font-size: 2.5rem; color: var(--dark); margin: 0; }
        
        .cart-grid { display: grid; grid-template-columns: 1fr 350px; gap: 3rem; align-items: start; }
        @media (max-width: 900px) { .cart-grid { grid-template-columns: 1fr; } }
        
        .cart-items { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid var(--border); }
        .cart-item { display: flex; padding: 1.5rem; border-bottom: 1px solid var(--border); align-items: center; gap: 1.5rem; }
        .cart-item:last-child { border-bottom: none; }
        
        .item-image { width: 100px; height: 100px; background: #f8fafc; border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .item-image img { width: 100%; height: 100%; object-fit: contain; }
        
        .item-details { flex: 1; }
        .item-category { font-size: 0.8rem; color: var(--primary); font-weight: 600; text-transform: uppercase; }
        .item-title { font-size: 1.2rem; font-weight: 700; color: var(--dark); margin: 0.5rem 0; }
        .item-price { font-weight: 600; color: var(--gray); }
        
        .qty-controls { display: flex; align-items: center; gap: 0.5rem; }
        .qty-btn { width: 30px; height: 30px; border-radius: 5px; border: 1px solid var(--border); background: white; cursor: pointer; font-weight: bold; }
        .qty-input { width: 50px; text-align: center; border: 1px solid var(--border); padding: 0.3rem; border-radius: 5px; font-family: inherit; }
        /* Hide arrows from number input */
        .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .qty-input[type=number] { -moz-appearance: textfield; }
        
        .item-total { font-size: 1.2rem; font-weight: 700; color: var(--dark); min-width: 120px; text-align: right; }
        
        .btn-remove { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.9rem; text-decoration: underline; padding: 0; margin-top: 0.5rem; }
        
        /* Summary Box */
        .cart-summary { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid var(--border); position: sticky; top: 100px; }
        .summary-title { font-size: 1.5rem; font-weight: 700; margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--gray); font-size: 1.1rem; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--border); font-size: 1.5rem; font-weight: 800; color: var(--dark); }
        
        .btn-checkout { width: 100%; background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 10px; font-size: 1.1rem; font-weight: 700; cursor: pointer; margin-top: 2rem; transition: background 0.3s; text-decoration: none; display: inline-block; text-align: center; box-sizing: border-box; }
        .btn-checkout:hover { background: #0052cc; }
        
        .empty-cart { text-align: center; padding: 4rem 2rem; background: white; border-radius: 15px; border: 1px solid var(--border); }
        .empty-cart-icon { font-size: 4rem; margin-bottom: 1rem; }
        .empty-cart h2 { color: var(--dark); margin-bottom: 0.5rem; }
        .btn-continue { display: inline-block; margin-top: 1.5rem; padding: 0.8rem 2rem; background: var(--secondary); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Your Shopping Cart</h1>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your cart is currently empty!</h2>
                <p style="color: var(--gray);">Browse our store and find something you like.</p>
                <a href="store.php" class="btn-continue">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <?php foreach ($products as $p): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php if (filter_var($p['primary_image'], FILTER_VALIDATE_URL) || file_exists($p['primary_image']) || strpos($p['primary_image'], 'assest/') === 0): ?>
                                    <img src="<?php echo htmlspecialchars($p['primary_image']); ?>" alt="">
                                <?php else: ?>
                                    <span style="font-size: 3rem;"><?php echo htmlspecialchars($p['primary_image']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <div class="item-category"><?php echo htmlspecialchars($p['category']); ?></div>
                                <h3 class="item-title"><?php echo htmlspecialchars($p['name']); ?></h3>
                                <div class="item-price">Rs. <?php echo number_format($p['price'], 0); ?> each</div>
                                
                                <form action="cart_action.php" method="POST" style="margin-top: 0.5rem; display: inline-block;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn-remove">Remove</button>
                                </form>
                            </div>
                            <div>
                                <form action="cart_action.php" method="POST" class="qty-controls">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="button" class="qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepDown(); this.parentNode.submit();">-</button>
                                    <input type="number" name="qty" class="qty-input" value="<?php echo $p['qty']; ?>" min="1" onchange="this.form.submit()">
                                    <button type="button" class="qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepUp(); this.parentNode.submit();">+</button>
                                </form>
                            </div>
                            <div class="item-total">
                                Rs. <?php echo number_format($p['subtotal'], 0); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3 class="summary-title">Order Summary</h3>
                    <div class="summary-row">
                        <span>Items (<?php echo array_sum($cart_items); ?>)</span>
                        <span>Rs. <?php echo number_format($total_price, 0); ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>Rs. <?php echo number_format($total_price, 0); ?></span>
                    </div>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
