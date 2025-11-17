<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Fetch user regardless of admin status first
        $stmt = $pdo->prepare("SELECT * FROM vendor WHERE vendor_name = :vendor_name");
        $stmt->execute(['vendor_name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($password === $user['password']) {
                // Store session data (same for both admin and user)
                $_SESSION['admin_id'] = $user['vendor_ID'];
                $_SESSION['admin_name'] = $user['vendor_name'];
                $_SESSION['logged_in'] = true;

                //  admin status
                if ($user['admin'] == 1) {
                   
                    header('Location: dashboard.php');
                    exit();
                } else {
                    // user redirect
                    header('Location: dashboard_user.php');
                    exit();
                }
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanka Vehicle - Vendor Management System</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="company-logo">
            <h1>LANKA VEHICLE</h1>
        </div>
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password">
            </div>
            <?php if ($error): ?>
                <div id="errorMessage" class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>

</html>