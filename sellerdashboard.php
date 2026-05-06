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

if (!isset($_SESSION["user_id"])) {
    header("Location: registration.php?mode=login");
    exit();
}

$userName = $_SESSION["user_name"] ?? "User";
$userRole = strtolower(trim($_SESSION["user_role"] ?? "buyer"));
$isFarmer = ($userRole === "farmer" || $userRole === "seller");

if (!$isFarmer) {
    header("Location: dashboard.php");
    exit();
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
                <div class="overview-card-value">3</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Active Orders</div>
                <div class="overview-card-value">2</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Low Stock Alerts</div>
                <div class="overview-card-value">0</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Total Sales</div>
                <div class="overview-card-value">₱25.00</div>
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
