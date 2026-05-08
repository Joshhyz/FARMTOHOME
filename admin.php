<?php
// 1. DATABASE CONNECTION
// Replace 'farmtohome_db' with your actual database name
$conn = new mysqli("localhost", "root", "", "farmtohome_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

/* ====================================
   LIVE DASHBOARD DATA (SQL)
   ==================================== */
// These queries replace your hardcoded numbers
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalFarmers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'Farmer'")->fetch_assoc()['count'];
$totalBuyers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'Buyer'")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalRevenueResult = $conn->query("SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE LOWER(o.order_status) = 'completed'");
$totalRevenue = $totalRevenueResult ? $totalRevenueResult->fetch_assoc()['total'] : 0;

/* ====================================
   LIVE DATA FETCHING
   ==================================== */

// Fetch Users
$user_result = $conn->query("SELECT * FROM users");
$users = [];
while($row = $user_result->fetch_assoc()) {
    $users[] = $row;
}

// Fetch Products
$product_result = $conn->query("SELECT * FROM products");
$products = [];
while($row = $product_result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch Orders
$order_result = $conn->query("SELECT * FROM orders");
$orders = [];
$completedOrders = 0;
$pendingOrders = 0;

while($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
    if ($row['status'] == 'Completed') $completedOrders++;
    if ($row['status'] == 'Pending') $pendingOrders++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmToHome Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; margin-bottom:24px; }
        .stat-card { background:white; padding:20px; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { margin:0 0 12px 0; color:#6b7280; font-size:13px; font-weight:600; }
        .stat-card .value { font-size:28px; font-weight:700; color:#1f2937; }
        .table-container { overflow-x:auto; background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; }
        table th { background:#f3f4f6; padding:16px; text-align:left; font-weight:600; color:#374151; border-bottom:1px solid #e5e7eb; }
        table td { padding:16px; border-bottom:1px solid #e5e7eb; }
        table tr:hover { background:#f9fafb; }
        .badge { display:inline-block; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:600; }
        .badge.available { background:#dcfce7; color:#166534; }
        .badge.outofstock { background:#fee2e2; color:#991b1b; }
        .actions { display:flex; gap:10px; color:#6b7280; cursor:pointer; }
        .actions i { transition:all 0.2s; }
        .actions i:hover { color:#16a34a; }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="logo">🌱 FarmToHome <span>Admin Panel</span></div>
        <div class="menu">
            <a href="admin.php?page=dashboard" class="<?= ($page == 'dashboard') ? 'active' : ''; ?>">🏠 Dashboard</a>
            <a href="admin.php?page=users" class="<?= ($page == 'users') ? 'active' : ''; ?>">👥 Users</a>
            <a href="admin.php?page=products" class="<?= ($page == 'products') ? 'active' : ''; ?>">📦 Products</a>
            <a href="admin.php?page=reports" class="<?= ($page == 'reports') ? 'active' : ''; ?>">📊 Reports</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>
                <?php
                if ($page == 'dashboard') echo "System Overview";
                elseif ($page == 'users') echo "User Management";
                elseif ($page == 'products') echo "Product Oversight";
                elseif ($page == 'reports') echo "Reports & Analytics";
                ?>
            </h1>
            <div class="admin-user">Admin User | <a href="logout.php">Logout</a></div>
        </div>

        <?php if ($page == 'dashboard'): ?>
        <div class="cards">
            <div class="card"><h3>👥 Active Users</h3><div class="number"><?= $totalUsers; ?></div></div>
            <div class="card"><h3>🚜 Farmers</h3><div class="number"><?= $totalFarmers; ?></div></div>
            <div class="card"><h3>🛒 Buyers</h3><div class="number"><?= $totalBuyers; ?></div></div>
            <div class="card"><h3>📦 Products</h3><div class="number"><?= $totalProducts; ?></div></div>
            <div class="card"><h3>📑 Orders</h3><div class="number"><?= $totalOrders; ?></div></div>
            <div class="card"><h3>✅ Health</h3><div class="number green">Live</div></div>
        </div>
        <?php endif; ?>

        <?php if ($page == 'users'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Profile</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($user['image'] ?? 'default.png'); ?>" width="40"></td>
                        <td><?= htmlspecialchars($user['name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td><span class="badge <?= strtolower($user['status']); ?>"><?= $user['status']; ?></span></td>
                        <td>
                            <div class="actions">
                                <i class="fa-solid fa-eye"></i> <i class="fa-solid fa-pen"></i> <i class="fa-solid fa-trash"></i>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($page == 'products'): ?>
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Products</h3><div class="value"><?= $totalProducts; ?></div></div>
            <div class="stat-card"><h3>In Stock</h3><div class="value"><?= count(array_filter($products, fn($p) => $p['stock'] > 0)); ?></div></div>
            <div class="stat-card"><h3>Out of Stock</h3><div class="value"><?= count(array_filter($products, fn($p) => $p['stock'] <= 0)); ?></div></div>
            <div class="stat-card"><h3>Categories</h3><div class="value"><?= count(array_unique(array_column($products, 'category'))); ?></div></div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Farmer</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                        <td>$<?= number_format((float)($product['price'] ?? 0), 2); ?></td>
                        <td><?= (int)($product['stock'] ?? 0); ?> kg</td>
                        <td><?= htmlspecialchars($product['farmer_id'] ?? 'N/A'); ?></td>
                        <td><span class="badge <?= ($product['stock'] > 0 ? 'available' : 'outofstock'); ?>"><?= ($product['stock'] > 0 ? 'Available' : 'Out of Stock'); ?></span></td>
                        <td>
                            <div class="actions">
                                <i class="fa-solid fa-eye"></i> <i class="fa-solid fa-pen"></i> <i class="fa-solid fa-trash"></i>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
