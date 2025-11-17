<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

// Get vendors for dropdown
$stmt = $pdo->query("SELECT vendor_ID, vendor_name FROM vendor ORDER BY vendor_name");
$vendors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_ID = $_POST['vendor_ID'];
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $product_count = intval($_POST['product_count']);

    try {
        $stmt = $pdo->prepare("INSERT INTO product (vendor_ID, product_name, price, product_count) VALUES (?, ?, ?, ?)");
        $stmt->execute([$vendor_ID, $product_name, $price, $product_count]);
        header('Location: dashboard.php?msg=Product added successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Product</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label>Vendor:</label>
                <select name="vendor_ID" required>
                    <option value="">-- Select Vendor --</option>
                    <?php foreach ($vendors as $v): ?>
                    <option value="<?php echo $v['vendor_ID']; ?>"><?php echo htmlspecialchars($v['vendor_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <label>Product Name:</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-row">
                <label>Price:</label>
                <input type="number" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-row">
                <label>Stock Quantity:</label>
                <input type="number" name="product_count" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>