<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

if (isset($_POST['mark_delivered']) && !empty($_POST['order_id'])) {
    // Get the order ID from the submitted form
    $order_id_to_update = $_POST['order_id'];

    // Prepare and execute the SQL UPDATE statement
    try {
        $sql = "UPDATE purchase_order SET order_state = 'Delivered' WHERE purchase_order_ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id_to_update]);

        // Redirect to the same page to refresh the order list
        // and prevent form resubmission on page reload.
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        // Handle potential database errors
        die("Error updating record: " . $e->getMessage());
    }
}

// Fetch Vendors
$stmt = $pdo->query("SELECT vendor_ID, vendor_name, contact_information, email FROM vendor ORDER BY vendor_ID");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Products
$stmt = $pdo->query("
    SELECT p.product_ID, p.product_name, p.price, p.product_count, v.vendor_name
    FROM product p
    JOIN vendor v ON p.vendor_ID = v.vendor_ID
    ORDER BY p.product_ID
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Purchase Ordars
$stmt = $pdo->query("
    SELECT po.purchase_order_ID, po.product_ID, po.vendor_ID, po.price, po.order_quantity, po.total_amount, po.order_state,
           p.product_name, v.vendor_name
    FROM purchase_order po
    JOIN product p ON po.product_ID = p.product_ID
    JOIN vendor v ON po.vendor_ID = v.vendor_ID
    WHERE po.order_state != 'Delivered'
    ORDER BY po.purchase_order_ID DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lanka Vehicle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Headar -->
    <header class="header">
        <h1><i class="fas fa-car"></i> Lanka Vehicle</h1>
        <p>Vendor & Supply Chain Management Dashboard — Manage vendors, products, and purchase orders in one place.</p>
    </header>

    <div class="container">

        <!-- VENDOR MANAGE-->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-users"></i> Vendor Management</h2>
                <a href="add_vendor.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Vendor</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Vendor ID</th>
                            <th>Vendor Name</th>
                            <th>Contact Info</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendors as $v): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v['vendor_ID']); ?></td>
                                <td><?php echo htmlspecialchars($v['vendor_name']); ?></td>
                                <td><?php echo htmlspecialchars($v['contact_information']); ?></td>
                                <td><?php echo htmlspecialchars($v['email']); ?></td>
                                <td>
                                    <a href="edit_vendor.php?id=<?php echo $v['vendor_ID']; ?>"
                                        class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>
                                    <a href="delete_vendor.php?id=<?php echo $v['vendor_ID']; ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i
                                            class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!--PRODUCT MANAGE-->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-box"></i> Product Management</h2>
                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Vendor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['product_ID']); ?></td>
                                <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                                <td>$<?php echo number_format($p['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($p['product_count']); ?></td>
                                <td><?php echo htmlspecialchars($p['vendor_name']); ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $p['product_ID']; ?>"
                                        class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>
                                    <a href="delete_product.php?id=<?php echo $p['product_ID']; ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i
                                            class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- PURCHASE ORDER -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-shopping-cart"></i> Create Purchase Order</h2>
            </div>
            <div class="form-container">
                <form id="orderForm" action="add_order.php" method="POST">
                    <div class="form-row">
                        <label>Product ID:</label>
                        <select name="product_ID" id="productID" required>
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['product_ID']; ?>" data-price="<?php echo $p['price']; ?>"
                                    data-name="<?php echo htmlspecialchars($p['product_name']); ?>">
                                    #<?php echo $p['product_ID']; ?> - <?php echo htmlspecialchars($p['product_name']); ?>
                                    ($<?php echo number_format($p['price'], 2); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Product Name:</label>
                        <input type="text" id="productName" readonly>
                    </div>
                    <div class="form-row">
                        <label>Vendor ID:</label>
                        <select name="vendor_ID" id="vendorID" required>
                            <option value="">-- Select Vendor --</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?php echo $v['vendor_ID']; ?>">
                                    <?php echo htmlspecialchars($v['vendor_name']); ?> (ID: <?php echo $v['vendor_ID']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Price per Unit:</label>
                        <input type="number" id="price" step="0.01" readonly>
                    </div>
                    <div class="form-row">
                        <label>Quantity:</label>
                        <input type="number" name="order_quantity" id="quantity" min="1" required
                            onchange="calculateTotal()">
                    </div>
                    <div class="form-row">
                        <label>Total Amount:</label>
                        <input type="number" id="totalAmount" step="0.01" readonly>
                    </div>
                    <!-- <div class="form-row">
                        <label>Order State:</label>
                        <select name="order_state" required>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div> -->
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Order</button>
                </form>
            </div>
        </section>

        <!--ORDER TRACKING -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-clipboard-list"></i> Order Tracking</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($o['purchase_order_ID']); ?></td>
                                <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($o['vendor_name']); ?></td>
                                <td>$<?php echo number_format($o['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($o['order_quantity']); ?></td>
                                <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($o['order_state']); ?>">
                                        <?php echo htmlspecialchars($o['order_state']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (strtolower($o['order_state']) !== 'delivered'): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($o['purchase_order_ID']); ?>">
                                            <button type="submit" name="mark_delivered" class="delivered-btn">
                                                Delivered
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Lanka Vehicle — Vendor Management System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>