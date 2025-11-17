<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

$vendor_id = $_SESSION['admin_id'];

// Fetch current vendor's details
$stmt = $pdo->prepare("SELECT vendor_ID, vendor_name, contact_information, email FROM vendor WHERE vendor_ID = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    die("Vendor not found.");
}

// Fetch products belonging to this vendor
$stmt = $pdo->prepare("
    SELECT product_ID, product_name, price, product_count 
    FROM product 
    WHERE vendor_ID = ? 
    ORDER BY product_ID
");
$stmt->execute([$vendor_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch delivered orders for this vendor
$stmt = $pdo->prepare("
    SELECT po.purchase_order_ID, po.product_ID, po.order_quantity, po.total_amount, po.order_state,
           p.product_name
    FROM purchase_order po
    JOIN product p ON po.product_ID = p.product_ID
    WHERE po.vendor_ID = ? AND po.order_state = 'Delivered'
    ORDER BY po.purchase_order_ID DESC
");
$stmt->execute([$vendor_id]);
$delivered_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch ALL orders for this vendor (for tracking section)
$stmt = $pdo->prepare("
    SELECT po.purchase_order_ID, po.product_ID, po.order_quantity, po.total_amount, po.order_state,
           p.product_name
    FROM purchase_order po
    JOIN product p ON po.product_ID = p.product_ID
    WHERE po.vendor_ID = ?
    ORDER BY po.purchase_order_ID DESC
");
$stmt->execute([$vendor_id]);
$all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Lanka Vehicle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <h1><i class="fas fa-car"></i> Lanka Vehicle</h1>
        <p>Vendor & Supply Chain Management Dashboard — Manage your products and orders.</p>
    </header>

    <div class="container">

        <!-- SECTION 1: VENDOR PROFILE -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-user"></i> My Profile</h2>
            </div>
            <div class="profile-container">
                <div class="profile-item">
                    <strong>Vendor Name:</strong>
                    <span><?php echo htmlspecialchars($vendor['vendor_name']); ?></span>
                </div>
                <div class="profile-item">
                    <strong>Vendor ID:</strong>
                    <span><?php echo htmlspecialchars($vendor['vendor_ID']); ?></span>
                </div>
                <div class="profile-item">
                    <strong>Contact Information:</strong>
                    <span><?php echo htmlspecialchars($vendor['contact_information']); ?></span>
                </div>
                <div class="profile-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($vendor['email']); ?></span>
                </div>
            </div>
        </section>

        <!-- SECTION 2: MY PRODUCTS -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-box"></i> My Products</h2>
            </div>
            <?php if (!empty($products)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['product_ID']); ?></td>
                            <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td>$<?php echo number_format($p['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($p['product_count']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>No products found for your vendor account.</p>
            <?php endif; ?>
        </section>

        <!-- SECTION 3: DELIVERED ORDER HISTORY -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-truck"></i> Successfully Delivered Orders</h2>
            </div>
            <?php if (!empty($delivered_orders)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($delivered_orders as $o): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($o['purchase_order_ID']); ?></td>
                            <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($o['order_quantity']); ?></td>
                            <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-delivered">
                                    <?php echo htmlspecialchars($o['order_state']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>No delivered orders found.</p>
            <?php endif; ?>
        </section>

        <!-- SECTION 4: NEW PURCHASE ORDERS (ASSIGNED TO THIS VENDOR) -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-shopping-cart"></i> New Purchase Orders</h2>
            </div>

            <?php
            // Filter out delivered orders
            $new_orders = array_filter($all_orders, function ($o) {
                return strtolower($o['order_state']) !== 'delivered';
            });
            ?>
        
            <?php if (!empty($new_orders)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($new_orders as $o): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['purchase_order_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($o['order_quantity']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($o['order_state']); ?>">
                                            <?php echo htmlspecialchars($o['order_state']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No purchase orders assigned to you.</p>
            <?php endif; ?>
        </section>

        <!-- SECTION 5: ORDER TRACKING (UPDATE STATUS) -->
        <section class="section">
            <div class="section-header">
                <h2><i class="fas fa-clipboard-list"></i> Update Order Status</h2>
            </div>

            <?php
            // Filter out delivered orders
            $pending_orders = array_filter($all_orders, function ($o) {
                return strtolower($o['order_state']) !== 'delivered';
            });
            ?>
        
            <?php if (!empty($pending_orders)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Current Status</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_orders as $o):
                                // Allow updates only for non-cancelled orders
                                $can_update = !in_array($o['order_state'], ['Cancelled']);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['purchase_order_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($o['order_state']); ?>">
                                            <?php echo htmlspecialchars($o['order_state']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($can_update): ?>
                                            <form method="POST" action="update_order_status.php" style="margin:0;">
                                                <input type="hidden" name="order_id" value="<?php echo $o['purchase_order_ID']; ?>">
                                                <select name="order_state" style="margin-right: 5px;">
                                                    <option value="Pending" <?php echo $o['order_state'] === 'Pending' ? 'selected' : ''; ?>>
                                                        Pending</option>
                                                    <option value="Processing" <?php echo $o['order_state'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="Shipped" <?php echo $o['order_state'] === 'Shipped' ? 'selected' : ''; ?>>
                                                        Shipped</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success">Update</button>
                                            </form>
                                        <?php else: ?>
                                            <span>Final Status</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No orders available for status updates.</p>
            <?php endif; ?>
        </section>

    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Lanka Vehicle — Vendor Management System. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>