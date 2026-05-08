<?php
$cookieLifetime = 60 * 60 * 24 * 30; // 30 days
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
require_once 'database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: registration.php?mode=login");
    exit();
}

$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION["user_name"] ?? "User";
$userRole = strtolower(trim($_SESSION["user_role"] ?? "buyer"));
$isFarmer = ($userRole === "farmer" || $userRole === "seller");

$productCount = 0;
$lowStockCount = 0;
$activeOrders = 0;
$totalInventory = 0;
$totalRevenue = 0.0;

if ($isFarmer && $currentUserId > 0) {
    $productResult = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products WHERE farmer_id = $currentUserId");
    if ($productResult && mysqli_num_rows($productResult) > 0) {
        $row = mysqli_fetch_assoc($productResult);
        $productCount = (int) $row['cnt'];
    }

    $lowResult = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products WHERE farmer_id = $currentUserId AND stock > 0 AND stock <= 10");
    if ($lowResult && mysqli_num_rows($lowResult) > 0) {
        $row = mysqli_fetch_assoc($lowResult);
        $lowStockCount = (int) $row['cnt'];
    }

    $orderResult = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.farmer_id = $currentUserId");
    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
        $row = mysqli_fetch_assoc($orderResult);
        $activeOrders = (int) $row['cnt'];
    }

    $inventoryResult = mysqli_query($conn, "SELECT SUM(stock) AS total_stock FROM products WHERE farmer_id = $currentUserId");
    if ($inventoryResult && mysqli_num_rows($inventoryResult) > 0) {
        $row = mysqli_fetch_assoc($inventoryResult);
        $totalInventory = (int) ($row['total_stock'] ?? 0);
    }

    $revenueResult = @mysqli_query($conn, "SELECT SUM(oi.quantity * oi.price) AS total_revenue FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.farmer_id = $currentUserId");
    if ($revenueResult && mysqli_num_rows($revenueResult) > 0) {
        $row = mysqli_fetch_assoc($revenueResult);
        $totalRevenue = (float) ($row['total_revenue'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page">

<header class="dashboard-topbar">
    <div class="dashboard-brand">
        <img src="images/logo.png" alt="FarmToHome Logo">
        FarmToHome
    </div>
    <div class="dashboard-topbar-right">
        <span class="dashboard-role-label">Farmer Role</span>
        <div class="dashboard-user">Hi, <?php echo htmlspecialchars($userName); ?></div>
        <a href="logout.php" class="dashboard-logout">Logout</a>
    </div>
</header>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-title">Farmer Menu</div>
        <div class="sidebar-description">Manage products, orders, inventory, and customer messages.</div>
        <div class="sidebar-menu">
            <a class="sidebar-item active" href="dashboard.php">
                <span>Dashboard</span>
            </a>
            <a class="sidebar-item" href="products.php">
                <span>Products</span>
            </a>
            <a class="sidebar-item" href="inventory.php">
                <span>Inventory</span>
            </a>
            <a class="sidebar-item" href="orders.php">
                <span>Orders</span>
            </a>
            <a class="sidebar-item" href="Message.php">
                <span>Messages</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-hero">
            <h1>Farmer Dashboard</h1>
            <p>View your latest product sales, manage inventory, and keep track of customer orders.</p>
        </section>

        <section class="overview-cards">
            <div class="overview-card">
                <div class="overview-card-label">Total Products</div>
                <div class="overview-card-value"><?php echo $productCount; ?></div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">On-Hand Inventory</div>
                <div class="overview-card-value"><?php echo $totalInventory; ?> kg</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Low Stock Alerts</div>
                <div class="overview-card-value"><?php echo $lowStockCount; ?></div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Total Revenue</div>
                <div class="overview-card-value">₱<?php echo number_format($totalRevenue, 2); ?></div>
            </div>
        </section>

        <section class="quick-actions">
            <div class="quick-action">
                <h3>Add Product</h3>
                <p>Create a new listing so buyers can discover your fresh produce quickly.</p>
                <a href="products.php">Add Product</a>
            </div>
            <div class="quick-action">
                <h3>View Orders</h3>
                <p>Monitor active orders and prepare shipments for your customers.</p>
                <a href="orders.php">View Orders</a>
            </div>
        </section>

        <section class="recent-panel">
            <div class="panel-title">Recent Orders</div>
            <div class="recent-list">
                <div class="recent-item">
                    <div class="recent-item-details">
                        <div class="recent-item-title">Organic Tomatoes</div>
                        <div class="recent-item-meta">Jane Buyer • 5 units</div>
                        <div class="recent-item-note">₱19.95</div>
                    </div>
                    <div class="recent-item-status status-sending">Sending</div>
                </div>
                <div class="recent-item">
                    <div class="recent-item-details">
                        <div class="recent-item-title">Farm Fresh Carrots</div>
                        <div class="recent-item-meta">Jane Buyer • 3 units</div>
                        <div class="recent-item-note">₱8.97</div>
                    </div>
                    <div class="recent-item-status status-confirmed">Confirmed</div>
                </div>
                <div class="recent-item">
                    <div class="recent-item-details">
                        <div class="recent-item-title">Fresh Lettuce</div>
                        <div class="recent-item-meta">Tom Wilson • 10 units</div>
                        <div class="recent-item-note">₱25.00</div>
                    </div>
                    <div class="recent-item-status status-completed">Completed</div>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>
