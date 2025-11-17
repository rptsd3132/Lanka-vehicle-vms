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

$stmt = $pdo->prepare("SELECT * FROM vendor WHERE vendor_ID = ?");
$stmt->execute([$id]);
$vendor = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = trim($_POST['vendor_name']);
    $contact_information = trim($_POST['contact_information']);
    $email = trim($_POST['email']);
    $admin = isset($_POST['admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE vendor SET vendor_name = ?, contact_information = ?, email = ?, admin = ? WHERE vendor_ID = ?");
        $stmt->execute([$vendor_name, contact_information, email, $admin, $id]);
        header('Location: dashboard.php?msg=Vendor updated successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Vendor</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Edit Vendor</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label>Vendor Name:</label>
                <input type="text" name="vendor_name" value="<?php echo htmlspecialchars($vendor['vendor_name']); ?>" required>
            </div>
            <div class="form-row">
                <label>Contact Info:</label>
                <input type="text" name="contact_information" value="<?php echo htmlspecialchars($vendor['contact_information']); ?>" required>
            </div>
            <div class="form-row">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($vendor['email']); ?>" required>
            </div>
            <div class="form-row">
                <label>Is Admin?</label>
                <input type="checkbox" name="admin" value="1" <?php if ($vendor['admin']) echo 'checked'; ?>>
            </div>
            <button type="submit" class="btn btn-primary">Update Vendor</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>