<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($product_id > 0) {
    if ($action === 'add') {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
    } elseif ($action === 'update') {
        if ($qty > 0) {
            $_SESSION['cart'][$product_id] = $qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Redirect back to referring page or cart page
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'cart.php';
header("Location: " . $redirect);
exit();
?>
