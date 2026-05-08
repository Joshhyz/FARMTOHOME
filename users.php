<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: registration.php?mode=login');
    exit();
}

$currentUserRole = strtolower(trim($_SESSION['user_role'] ?? 'buyer'));
$search = trim($_GET['search'] ?? '');
$searchSql = '';
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $searchSql = "WHERE full_name LIKE '%$safeSearch%' OR email LIKE '%$safeSearch%' OR role LIKE '%$safeSearch%'";
}

$users = [];
$userResult = mysqli_query($conn, "SELECT id, full_name, email, role, created_at FROM users $searchSql ORDER BY id DESC");
if ($userResult) {
    while ($row = mysqli_fetch_assoc($userResult)) {
        $users[] = $row;
    }
}

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
$totalFarmers = dbCount("SELECT COUNT(*) FROM users WHERE LOWER(role) = 'farmer'");
$totalBuyers = dbCount("SELECT COUNT(*) FROM users WHERE LOWER(role) = 'buyer'");
$totalAdmins = dbCount("SELECT COUNT(*) FROM users WHERE LOWER(role) = 'admin'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - FarmToHome Admin</title>
    <link rel="stylesheet" href="admindashboard.css">
    <style>
        .search-bar { display:flex; gap:10px; margin-bottom:20px; }
        .search-bar input { flex:1; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; }
        .search-bar button { padding:12px 18px; border:none; border-radius:12px; background:#16a34a; color:#fff; cursor:pointer; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:18px; margin-bottom:24px; }
        .stat-card { background:white; padding:20px; border-radius:18px; box-shadow:0 10px 30px rgba(15,23,42,0.06); }
        .stat-card h3 { margin-bottom:12px; color:#6b7280; font-size:14px; }
        .stat-card .value { font-size:32px; font-weight:700; }
        .table-container { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        table th, table td { padding:16px; border-bottom:1px solid #e5e7eb; text-align:left; }
        table th { background:#f8fafc; color:#374151; }
        .badge { display:inline-block; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:700; }
        .badge.farmer { background:#dcfce7; color:#166534; }
        .badge.buyer { background:#dbeafe; color:#1e3a8a; }
        .badge.admin { background:#fde68a; color:#92400e; }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="logo">🌱 FarmToHome<span>Admin Panel</span></div>
        <div class="menu">
            <a href="admindashboard.php">🏠 Dashboard</a>
            <a href="users.php" class="active">👥 Users</a>
            <a href="admin_products.php">📦 Products</a>
            <a href="reports.php">📊 Reports</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>User Management</h1>
            <div class="admin-user">Admin User | <a href="logout.php">Logout</a></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3>Total Users</h3><div class="value"><?php echo $totalUsers; ?></div></div>
            <div class="stat-card"><h3>Farmers</h3><div class="value"><?php echo $totalFarmers; ?></div></div>
            <div class="stat-card"><h3>Buyers</h3><div class="value"><?php echo $totalBuyers; ?></div></div>
            <div class="stat-card"><h3>Admins</h3><div class="value"><?php echo $totalAdmins; ?></div></div>
        </div>

        <form class="search-bar" method="GET" action="users.php">
            <input type="search" name="search" placeholder="Search by name, email, or role..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) === 0): ?>
                        <tr><td colspan="4">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge <?php echo strtolower(htmlspecialchars($user['role'])); ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                                <td><?php echo htmlspecialchars($user['created_at'] ?? 'N/A'); ?></td>
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
