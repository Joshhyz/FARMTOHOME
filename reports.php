<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: registration.php?mode=login');
    exit();
}

$currentUserRole = strtolower(trim($_SESSION['user_role'] ?? 'buyer'));

function dbCount($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return (int) $row[0];
    }
    return 0;
}

function dbValue($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row[0];
    }
    return 0;
}

$totalRevenue = dbValue("SELECT SUM(oi.quantity * oi.price) FROM order_items oi");
$completedOrders = dbCount("SELECT COUNT(*) FROM orders WHERE LOWER(order_status) = 'completed'");
$pendingOrders = dbCount("SELECT COUNT(*) FROM orders WHERE LOWER(order_status) IN ('pending','confirmed')");
$totalOrders = dbCount("SELECT COUNT(*) FROM orders");

$salesSummary = [];
$salesSql = "SELECT o.id AS order_id, p.product_name, u_farmer.full_name AS farmer_name, u_buyer.full_name AS buyer_name, oi.quantity * oi.price AS amount, o.order_status AS status, o.created_at AS order_date
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             JOIN products p ON oi.product_id = p.id
             LEFT JOIN users u_farmer ON p.farmer_id = u_farmer.id
             LEFT JOIN users u_buyer ON o.user_id = u_buyer.id
             ORDER BY o.created_at DESC
             LIMIT 20";

$result = mysqli_query($conn, $salesSql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $salesSummary[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - FarmToHome Admin</title>
    <link rel="stylesheet" href="admindashboard.css">
    <style>
        .cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:26px; }
        .report-card { background:#fff; border-radius:18px; padding:24px; box-shadow:0 15px 40px rgba(15,23,42,0.08); }
        .report-card h3 { margin-bottom:14px; color:#6b7280; font-size:14px; }
        .report-card .value { font-size:32px; font-weight:700; }
        .reports-table { background:white; border-radius:18px; padding:20px; box-shadow:0 15px 40px rgba(15,23,42,0.08); overflow-x:auto; }
        .reports-table table { width:100%; border-collapse:collapse; }
        .reports-table th, .reports-table td { padding:16px; border-bottom:1px solid #e5e7eb; text-align:left; }
        .reports-table th { background:#f9fafb; color:#374151; }
        .status-badge { font-size:12px; font-weight:700; padding:8px 12px; border-radius:999px; display:inline-block; }
        .status-pending { background:#ffedd5; color:#c2410c; }
        .status-confirmed { background:#dbeafe; color:#1e40af; }
        .status-completed { background:#dcfce7; color:#166534; }
        .search-bar { margin-bottom:20px; display:flex; gap:10px; }
        .search-bar input { flex:1; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; }
        .search-bar button { padding:12px 18px; border:none; border-radius:12px; background:#16a34a; color:#fff; cursor:pointer; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <div class="logo">🌱 FarmToHome<span>Admin Panel</span></div>
        <div class="menu">
            <a href="admindashboard.php">🏠 Dashboard</a>
            <a href="users.php">👥 Users</a>
            <a href="products.php">📦 Products</a>
            <a href="reports.php" class="active">📊 Reports</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Reports & Analytics</h1>
            <div class="admin-user">Admin User | <a href="logout.php">Logout</a></div>
        </div>

        <div class="cards">
            <div class="report-card"><h3>Total Revenue</h3><div class="value">₱<?php echo number_format((float) $totalRevenue, 2); ?></div></div>
            <div class="report-card"><h3>Completed Orders</h3><div class="value"><?php echo $completedOrders; ?></div></div>
            <div class="report-card"><h3>Pending Orders</h3><div class="value"><?php echo $pendingOrders; ?></div></div>
            <div class="report-card"><h3>Total Orders</h3><div class="value"><?php echo $totalOrders; ?></div></div>
        </div>

        <div class="reports-table">
            <h2>Sales Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($salesSummary) === 0): ?>
                        <tr><td colspan="6">No sales recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($salesSummary as $sale): ?>
                            <?php $statusClass = 'status-pending';
                                  $statusText = htmlspecialchars($sale['status'] ?: 'pending');
                                  $statusLower = strtolower($sale['status'] ?? 'pending');
                                  if ($statusLower === 'completed') { $statusClass = 'status-completed'; }
                                  elseif ($statusLower === 'confirmed') { $statusClass = 'status-confirmed'; }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['farmer_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                                <td>₱<?php echo number_format((float) $sale['amount'], 2); ?></td>
                                <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($statusText); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
