<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = trim($_POST['vendor_name']);
    $contact_information = trim($_POST['contact_information']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $admin = isset($_POST['admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO vendor (vendor_name, contact_information, email, password, admin) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$vendor_name, $contact_information, $email, $password, $admin]);
        header('Location: dashboard.php?msg=Vendor added successfully');
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Vendor</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Add New Vendor</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label>Vendor Name:</label>
                <input type="text" name="vendor_name" required>
            </div>
            <div class="form-row">
                <label>Contact Info:</label>
                <input type="text" name="contact_information" required>
            </div>
            <div class="form-row">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-row">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-row">
                <label>Is Admin?</label>
                <input type="checkbox" name="admin" value="1">
            </div>
            <button type="submit" class="btn btn-primary">Add Vendor</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>