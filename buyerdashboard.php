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
$userRole = strtolower(trim($_SESSION["user_role"] ?? "buyer")); // buyer or farmer
$isFarmer = ($userRole === "farmer" || $userRole === "seller");

if ($isFarmer) {
    header("Location: sellerdashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page">

<header class="dashboard-topbar">
    <div class="dashboard-brand">
        <img src="images/logo.png" alt="FarmToHome Logo">
        FarmToHome
    </div>
    <div class="dashboard-topbar-right">
        <span class="dashboard-role-label">Buyer Role</span>
        <div class="dashboard-user">Hi, <?php echo htmlspecialchars($userName); ?></div>
        <a href="logout.php" class="dashboard-logout">Logout</a>
    </div>
</header>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-title">Buyer Menu</div>
        <div class="sidebar-description">Access your orders, messages, and marketplace quickly.</div>
        <div class="sidebar-menu">
            <a class="sidebar-item active" href="dashboard.php">
                <span>Dashboard</span>
            </a>
            <a class="sidebar-item" href="orders.php">
                <span>My Orders</span>
            </a>
            <a class="sidebar-item" href="Message.php">
                <span>Messages</span>
            </a>
            <a class="sidebar-item" href="marketplace.php">
                <span>Browse Products</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-hero">
            <h1>Buyer Dashboard</h1>
            <p>Browse fresh products, manage your orders, and stay connected with local farmers.</p>
        </section>

        <section class="overview-cards">
            <div class="overview-card">
                <div class="overview-card-label">Total Orders</div>
                <div class="overview-card-value">2</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Active Orders</div>
                <div class="overview-card-value">2</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Completed Orders</div>
                <div class="overview-card-value">0</div>
            </div>
            <div class="overview-card">
                <div class="overview-card-label">Saved Items</div>
                <div class="overview-card-value">0</div>
            </div>
        </section>

        <section class="quick-actions">
            <div class="quick-action">
                <h3>Browse Products</h3>
                <p>Discover fresh produce and place orders from trusted farmers.</p>
                <a href="marketplace.php">Shop Now</a>
            </div>
            <div class="quick-action">
                <h3>Message Farmers</h3>
                <p>Ask questions, get updates, and coordinate deliveries directly.</p>
                <a href="Message.php">Open Messages</a>
            </div>
        </section>

        <section class="recent-panel">
            <div class="panel-title">Recent Orders</div>
            <div class="recent-list">
                <div class="recent-item">
                    <div class="recent-item-details">
                        <div class="recent-item-title">Organic Tomatoes</div>
                        <div class="recent-item-meta">John Farmer • 5 units</div>
                        <div class="recent-item-note">₱19.95</div>
                    </div>
                    <div class="recent-item-status status-pending">Pending</div>
                </div>
                <div class="recent-item">
                    <div class="recent-item-details">
                        <div class="recent-item-title">Farm Fresh Carrots</div>
                        <div class="recent-item-meta">John Farmer • 3 units</div>
                        <div class="recent-item-note">₱8.97</div>
                    </div>
                    <div class="recent-item-status status-confirmed">Confirmed</div>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>