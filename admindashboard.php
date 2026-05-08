<?php
require_once 'database.php';

function dbCount($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return (int) $row[0];
    }
    return 0;
}

$totalUsers = dbCount("SELECT COUNT(*) FROM users");
$totalFarmers = dbCount("SELECT COUNT(*) FROM users WHERE role = 'farmer'");
$totalBuyers = dbCount("SELECT COUNT(*) FROM users WHERE role = 'buyer'");
$totalProducts = dbCount("SELECT COUNT(*) FROM products");
$totalOrders = dbCount("SELECT COUNT(*) FROM orders");
$activeOrders = dbCount("SELECT COUNT(*) FROM orders WHERE order_status NOT IN ('completed', 'cancelled') OR order_status IS NULL");

if ($totalUsers === 0 || $totalProducts === 0) {
    $platformHealth = 'Starting';
    $platformClass = 'orange';
} elseif ($activeOrders > 0) {
    $platformHealth = 'Good';
    $platformClass = 'green';
} elseif ($totalOrders > 0) {
    $platformHealth = 'Fair';
    $platformClass = 'orange';
} else {
    $platformHealth = 'Idle';
    $platformClass = 'orange';
}

$recentActivities = [];

$userResult = mysqli_query($conn, "SELECT full_name, role FROM users ORDER BY id DESC LIMIT 2");
if ($userResult) {
    while ($user = mysqli_fetch_assoc($userResult)) {
        $roleLabel = strtolower(trim($user['role'])) === 'farmer' ? 'New farmer registered' : 'New buyer registered';
        $recentActivities[] = [
            'title' => $roleLabel,
            'details' => $user['full_name']
        ];
    }
}

$productResult = mysqli_query($conn, "SELECT p.product_name, u.full_name AS seller_name FROM products p LEFT JOIN users u ON p.farmer_id = u.id ORDER BY p.id DESC LIMIT 1");
if ($productResult && mysqli_num_rows($productResult) > 0) {
    $productRow = mysqli_fetch_assoc($productResult);
    $recentActivities[] = [
        'title' => 'New product listed',
        'details' => $productRow['product_name'] . ' added by ' . ($productRow['seller_name'] ?? 'a seller')
    ];
}

$orderResult = mysqli_query($conn, "SELECT o.order_status, p.product_name, u.full_name AS buyer_name FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id JOIN users u ON o.user_id = u.id WHERE LOWER(o.order_status) IN ('completed', 'confirmed') ORDER BY o.created_at DESC LIMIT 1");
if ($orderResult && mysqli_num_rows($orderResult) > 0) {
    $orderRow = mysqli_fetch_assoc($orderResult);
    $orderTitle = strtolower($orderRow['order_status']) === 'completed' ? 'Order completed' : ucfirst($orderRow['order_status']) . ' order';
    $recentActivities[] = [
        'title' => $orderTitle,
        'details' => $orderRow['buyer_name'] . ' received ' . $orderRow['product_name']
    ];
}

if (count($recentActivities) === 0) {
    $recentActivities[] = [
        'title' => 'No activity yet',
        'details' => 'New buyers, sellers, products, and orders will appear here once they happen.'
    ];
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
                <h3>🚚 Active Orders</h3>
                <div class="number">
                    <?php echo $activeOrders; ?>
                </div>
            </div>

            <div class="card">
                <h3>📑 Total Orders</h3>
                <div class="number">
                    <?php echo $totalOrders; ?>
                </div>
            </div>

            <div class="card">
                <h3>✅ Platform Health</h3>
                <div class="number <?php echo $platformClass; ?>">
                    <?php echo htmlspecialchars($platformHealth); ?>
                </div>
            </div>

        </div>

        <!-- RECENT ACTIVITY -->
        <div class="activity">

            <h2>Recent Activity</h2>

            <?php foreach ($recentActivities as $activity): ?>
                <div class="activity-item">
                    <?php echo htmlspecialchars($activity['title']); ?>
                    <p><?php echo htmlspecialchars($activity['details']); ?></p>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

</div>

<!-- FLOATING CHAT BUTTON -->
<button class="chat-btn">
    💬
</button>

</body>
</html>