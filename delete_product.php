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

try {
    $stmt = $pdo->prepare("DELETE FROM product WHERE product_ID = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php?msg=Product deleted successfully');
} catch (PDOException $e) {
    header('Location: dashboard.php?error=' . urlencode("Error deleting product: " . $e->getMessage()));
}
exit();