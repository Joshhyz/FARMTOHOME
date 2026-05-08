<?php
require_once 'database.php';

$totalUsers = 0;
$totalFarmers = 0;
$totalBuyers = 0;
$totalProducts = 0;
$totalOrders = 0;
$activeOrders = 0;
$platformHealth = 'Unknown';
$recentActivities = [];

if ($conn) {
    $platformHealth = mysqli_ping($conn) ? 'Good' : 'Issue';

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $totalUsers = (int) $row['cnt'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE role IN ('farmer', 'seller')");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $totalFarmers = (int) $row['cnt'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE role = 'buyer'");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $totalBuyers = (int) $row['cnt'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $totalProducts = (int) $row['cnt'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $totalOrders = (int) $row['cnt'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE order_status NOT IN ('Completed', 'Cancelled')");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $activeOrders = (int) $row['cnt'];
    }

    $activityUsers = [];
    $result = mysqli_query($conn, "SELECT id, full_name, role FROM users ORDER BY id DESC LIMIT 3");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $role = strtolower($row['role']);
            $activityUsers[] = [
                'id' => (int)$row['id'],
                'title' => $role === 'farmer' ? '🌱 New farmer registered' : '🛍️ New buyer registered',
                'subtitle' => $row['full_name'] . ' joined the platform',
                'date' => (int)$row['id'],
            ];
        }
    }

    $activityProducts = [];
    $result = mysqli_query($conn, "SELECT p.id, p.product_name, u.full_name FROM products p JOIN users u ON p.farmer_id = u.id ORDER BY p.id DESC LIMIT 3");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activityProducts[] = [
                'id' => (int)$row['id'],
                'title' => '📦 New product listed',
                'subtitle' => $row['product_name'] . ' added by ' . $row['full_name'],
                'date' => (int)$row['id'],
            ];
        }
    }

    $activityOrders = [];
    $result = mysqli_query($conn, "SELECT o.id, o.order_status, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 3");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $status = ucfirst(strtolower($row['order_status']));
            $activityOrders[] = [
                'id' => (int)$row['id'],
                'title' => '✅ Order ' . $status,
                'subtitle' => $row['full_name'] . ' placed an order',
                'date' => (int)$row['id'],
            ];
        }
    }

    $recentActivities = array_merge($activityProducts, $activityUsers, $activityOrders);
    usort($recentActivities, function ($a, $b) {
        return $b['date'] <=> $a['date'];
    });
    $recentActivities = array_slice($recentActivities, 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmToHome Admin Dashboard</title>

    <link rel="stylesheet" href="admindashboard.css">
</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
            🌱 FarmToHome
            <span>Admin Panel</span>
        </div>

        <div class="menu">
            <a href="index.php" class="active">🏠 Dashboard</a>
            <a href="users.php">👥 Users</a>
            <a href="products.php">📦 Products</a>
            <a href="reports.php">📊 Reports</a>
        </div>

    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <h1>System Overview</h1>

            <div class="admin-user">
                Admin User | Logout
            </div>
        </div>

        <!-- DASHBOARD CARDS -->
        <div class="cards">

            <div class="card">
                <h3>👥 Active Users</h3>
                <div class="number">
                    <?php echo $totalUsers; ?>
                </div>
            </div>

            <div class="card">
                <h3>🚜 Farmers</h3>
                <div class="number">
                    <?php echo $totalFarmers; ?>
                </div>
            </div>

            <div class="card">
                <h3>🛒 Buyers</h3>
                <div class="number">
                    <?php echo $totalBuyers; ?>
                </div>
            </div>

            <div class="card">
                <h3>📦 Total Products</h3>
                <div class="number">
                    <?php echo $totalProducts; ?>
                </div>
            </div>

            <div class="card">
                <h3>📑 Total Orders</h3>
                <div class="number">
                    <?php echo $totalOrders; ?>
                </div>
            </div>

            <div class="card">
                <h3>🚦 Active Orders</h3>
                <div class="number">
                    <?php echo $activeOrders; ?>
                </div>
            </div>

            <div class="card">
                <h3>✅ Platform Health</h3>
                <div class="number green">
                    <?php echo htmlspecialchars($platformHealth); ?>
                </div>
            </div>

        </div>

        <!-- RECENT ACTIVITY -->
        <div class="activity">

            <h2>Recent Activity</h2>

            <?php if (!empty($recentActivities)): ?>
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <?php echo htmlspecialchars($activity['title']); ?>
                        <p><?php echo htmlspecialchars($activity['subtitle']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-item">
                    No recent activity available yet.
                    <p>Once farmers, buyers, and orders are created, activity will appear here.</p>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<!-- FLOATING CHAT BUTTON -->
<button class="chat-btn">
    💬
</button>

</body>
</html>