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
$totalRevenue = $conn->query("SELECT SUM(amount) as total FROM orders WHERE status = 'Completed'")->fetch_assoc()['total'] ?? 0;

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

        </div>
</div>
</body>
</html>