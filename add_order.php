<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ID = intval($_POST['product_ID']);
    $vendor_ID = intval($_POST['vendor_ID']);
    $price = floatval($_POST['price']);
    $order_quantity = intval($_POST['order_quantity']);
    $order_state = 'Pending';

    $total_amount = $price * $order_quantity;

    try {
        $stmt = $pdo->prepare("INSERT INTO purchase_order (vendor_ID, product_ID, price, order_quantity, order_state, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$vendor_ID, $product_ID, $price, $order_quantity, $order_state, $total_amount]);
        header('Location: dashboard.php?msg=Order created successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
header('Location: dashboard.php?error=' . urlencode($error ?? 'Unknown error'));
exit();