<?php
$cookieLifetime = 60 * 60 * 24 * 30;
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: registration.php?mode=login');
    exit();
}

require_once 'database.php';

$currentUserId = (int) $_SESSION['user_id'];
$currentUserName = $_SESSION['user_name'] ?? 'User';
$currentUserRole = strtolower(trim($_SESSION['user_role'] ?? 'buyer'));

if ($currentUserRole !== 'farmer' && $currentUserRole !== 'seller') {
    header('Location: dashboard.php');
    exit();
}

$orders = [];
$orderMessage = '';

$orderQuery = "SELECT o.id AS order_id, oi.quantity, o.order_status AS status, o.created_at AS order_date, p.product_name, p.category, oi.price, u.full_name AS buyer_name
               FROM orders o
               JOIN order_items oi ON o.id = oi.order_id
               JOIN products p ON oi.product_id = p.id
               JOIN users u ON o.user_id = u.id
               WHERE p.farmer_id = $currentUserId
               ORDER BY o.created_at DESC";

$orderResult = @mysqli_query($conn, $orderQuery);
if ($orderResult) {
    while ($row = mysqli_fetch_assoc($orderResult)) {
        $orders[] = $row;
    }
} else {
    $orderMessage = 'No orders found yet. When buyers place orders, they will appear here.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="products.css">
</head>
<body class="dashboard-page">

<header class="dashboard-topbar">
    <div class="dashboard-brand">
        <img src="images/logo.png" alt="FarmToHome Logo">
        FarmToHome
    </div>
    <div class="dashboard-topbar-right">
        <span class="dashboard-role-label">Farmer Role</span>
        <div class="dashboard-user">Hi, <?php echo htmlspecialchars($currentUserName); ?></div>
        <a href="logout.php" class="dashboard-logout">Logout</a>
    </div>
</header>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-title">Farmer Menu</div>
        <div class="sidebar-description">Manage products, orders, inventory, and customer messages.</div>
        <div class="sidebar-menu">
            <a class="sidebar-item" href="dashboard.php">
                <span>Dashboard</span>
            </a>
            <a class="sidebar-item" href="products.php">
                <span>Products</span>
            </a>
            <a class="sidebar-item" href="inventory.php">
                <span>Inventory</span>
            </a>
            <a class="sidebar-item active" href="orders.php">
                <span>Orders</span>
            </a>
            <a class="sidebar-item" href="Message.php">
                <span>Messages</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <section class="products-header">
            <div class="products-title">
                <h1>Orders</h1>
                <p>Track your customer orders and review the latest order activity.</p>
            </div>
        </section>

        <?php if ($orderMessage && count($orders) === 0): ?>
            <div class="no-orders">
                <h2>No orders yet</h2>
                <p><?php echo htmlspecialchars($orderMessage); ?></p>
                <p>Once a customer places an order, you will see details here, including order status and quantity.</p>
            </div>
        <?php endif; ?>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <?php
                    $statusClass = 'pending';
                    $statusLabel = 'Pending';
                    if (strtolower($order['status']) === 'confirmed') {
                        $statusClass = 'confirmed';
                        $statusLabel = 'Confirmed';
                    } elseif (strtolower($order['status']) === 'completed') {
                        $statusClass = 'completed';
                        $statusLabel = 'Completed';
                    }
                ?>
                <div class="order-card">
                    <div>
                        <div class="order-card-title"><?php echo htmlspecialchars($order['product_name']); ?></div>
                        <div class="order-card-meta">
                            Buyer: <?php echo htmlspecialchars($order['buyer_name']); ?><br>
                            Qty: <?php echo (int) $order['quantity']; ?> kg · ₱<?php echo number_format((float) $order['price'] * (int) $order['quantity'], 2); ?><br>
                            Category: <?php echo htmlspecialchars($order['category']); ?><br>
                            Ordered on: <?php echo htmlspecialchars($order['order_date']); ?>
                        </div>
                    </div>
                    <div>
                        <div class="order-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
