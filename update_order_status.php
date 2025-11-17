<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['order_state'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_state'];
    $vendor_id = $_SESSION['admin_id'];
    
    // Only allow specific status transitions
    $allowed_statuses = ['Pending', 'Processing', 'Shipped'];
    
    if (in_array($new_status, $allowed_statuses)) {
        // Verify this order belongs to the logged-in vendor
        $stmt = $pdo->prepare("SELECT vendor_ID FROM purchase_order WHERE purchase_order_ID = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        if ($order && $order['vendor_ID'] == $vendor_id) {
            $stmt = $pdo->prepare("UPDATE purchase_order SET order_state = ? WHERE purchase_order_ID = ?");
            $stmt->execute([$new_status, $order_id]);
        }
    }
}

header('Location: dashboard_user.php');
exit();
?>