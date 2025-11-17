<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM purchase_order WHERE purchase_order_ID = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_state = $_POST['order_state'];

    try {
        $stmt = $pdo->prepare("UPDATE purchase_order SET order_state = ? WHERE purchase_order_ID = ?");
        $stmt->execute([$order_state, $id]);
        header('Location: dashboard.php?msg=Order status updated successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Update Order</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Update Order Status</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label>Order ID:</label>
                <input type="text" value="<?php echo htmlspecialchars($order['purchase_order_ID']); ?>" readonly>
            </div>
            <div class="form-row">
                <label>Current Status:</label>
                <input type="text" value="<?php echo htmlspecialchars($order['order_state']); ?>" readonly>
            </div>
            <div class="form-row">
                <label>New Status:</label>
                <select name="order_state" required>
                    <option value="Pending" <?php if ($order['order_state'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Processing" <?php if ($order['order_state'] == 'Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if ($order['order_state'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="Delivered" <?php if ($order['order_state'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if ($order['order_state'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>