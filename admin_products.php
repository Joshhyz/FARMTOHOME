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

$totalProducts = dbCount("SELECT COUNT(*) FROM products");
$totalFarmers = dbCount("SELECT COUNT(*) FROM users WHERE role = 'farmer'");
$inStock = dbCount("SELECT COUNT(*) FROM products WHERE stock > 0");
$outOfStock = dbCount("SELECT COUNT(*) FROM products WHERE stock <= 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - FarmToHome Admin</title>

    <link rel="stylesheet" href="admindashboard.css">
    <style>
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; }
        .page-header h1 { margin:0; font-size:28px; font-weight:700; color:#1f2937; }
        
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; margin-bottom:32px; }
        .stat-card { background:white; padding:20px; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { margin:0 0 12px 0; color:#6b7280; font-size:13px; font-weight:600; }
        .stat-card .value { font-size:28px; font-weight:700; color:#1f2937; }
        
        .products-section { margin-top:32px; }
        
        .farmer-card { margin-bottom:32px; background:white; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
        
        .farmer-header { background:linear-gradient(135deg, #16a34a 0%, #15803d 100%); color:white; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; }
        .farmer-details h3 { margin:0 0 4px 0; font-size:18px; font-weight:700; }
        .farmer-details p { margin:0; font-size:13px; opacity:0.9; }
        .product-count { display:inline-block; margin-top:8px; background:rgba(255,255,255,0.2); padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600; }
        .verified-badge { background:rgba(255,255,255,0.3); padding:8px 14px; border-radius:999px; font-size:12px; font-weight:600; }
        
        .products-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px; padding:20px; background:#f9fafb; }
        
        .product-card-item { background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; transition:all 0.3s; }
        .product-card-item:hover { transform:translateY(-4px); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
        
        .product-image { width:100%; height:160px; background:#f0f0f0; overflow:hidden; }
        .product-image img { width:100%; height:100%; object-fit:cover; }
        
        .product-card-info { padding:12px; }
        .product-card-info h4 { margin:0 0 4px 0; font-size:13px; font-weight:600; color:#1f2937; }
        .category { margin:4px 0; font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:500; }
        
        .product-meta { display:flex; justify-content:space-between; align-items:center; margin:8px 0; font-size:12px; }
        .price { color:#16a34a; font-weight:700; }
        .stock { color:#6b7280; }
        
        .product-actions-bar { display:flex; gap:6px; margin-top:8px; }
        .action-btn { flex:1; border:none; padding:6px; border-radius:6px; background:#f0f0f0; cursor:pointer; font-size:14px; transition:all 0.2s; }
        .view-btn:hover { background:#dbeafe; }
        .edit-btn:hover { background:#fef3c7; }
        .delete-btn:hover { background:#fee2e2; }
        
        .no-products, .no-farmers { padding:40px 20px; text-align:center; color:#6b7280; font-size:14px; }
    </style>
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
            <a href="admindashboard.php">🏠 Dashboard</a>
            <a href="users.php">👥 Users</a>
            <a href="admin_products.php" class="active">📦 Products</a>
            <a href="reports.php">📊 Reports</a>
        </div>

    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <h1>Products Oversight</h1>

            <div class="admin-user">
                Admin User | Logout
            </div>
        </div>

        <!-- PAGE HEADER -->
        <div class="page-header">
            <h1>Registered Sellers & Their Products</h1>
        </div>

        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="value"><?php echo $totalProducts; ?></div>
            </div>
            <div class="stat-card">
                <h3>In Stock</h3>
                <div class="value"><?php echo $inStock; ?></div>
            </div>
            <div class="stat-card">
                <h3>Out of Stock</h3>
                <div class="value"><?php echo $outOfStock; ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Farmers</h3>
                <div class="value"><?php echo $totalFarmers; ?></div>
            </div>
        </div>

        <!-- SELLERS & PRODUCTS SECTION -->
        <div class="products-section">
            <?php
            $farmersResult = mysqli_query($conn, "SELECT id, full_name, email FROM users WHERE LOWER(role) = 'farmer' ORDER BY id DESC");
            $farmers = [];
            if ($farmersResult) {
                while ($farmer = mysqli_fetch_assoc($farmersResult)) {
                    $farmers[] = $farmer;
                }
            }

            if (count($farmers) > 0): ?>
                <?php foreach ($farmers as $farmer): ?>
                    <?php
                    $farmerId = $farmer['id'];
                    $productsResult = mysqli_query($conn, "SELECT id, product_name, category, price, stock FROM products WHERE farmer_id = $farmerId ORDER BY id DESC");
                    $farmerProducts = [];
                    if ($productsResult) {
                        while ($product = mysqli_fetch_assoc($productsResult)) {
                            $farmerProducts[] = $product;
                        }
                    }
                    $productCount = count($farmerProducts);
                    ?>
                    <div class="farmer-card">
                        <div class="farmer-header">
                            <div class="farmer-details">
                                <h3><?php echo htmlspecialchars($farmer['full_name']); ?></h3>
                                <p><?php echo htmlspecialchars($farmer['email']); ?></p>
                                <span class="product-count"><?php echo $productCount; ?> products</span>
                            </div>
                            <span class="verified-badge">✓ Verified Farmer</span>
                        </div>

                        <?php if ($productCount > 0): ?>
                            <div class="products-grid">
                                <?php foreach ($farmerProducts as $product): ?>
                                    <?php
                                    $categoryKey = strtolower(trim($product['category'] ?? ''));
                                    $colors = [
                                        'vegetables' => 'A5D6A7',
                                        'fruits' => 'F48FB1',
                                        'mixed vegetables' => 'FFE082',
                                        'herbs' => 'AED581',
                                        'dairy' => '90CAF9'
                                    ];
                                    $color = $colors[$categoryKey] ?? '9E9E9E';
                                    ?>
                                    <div class="product-card-item">
                                        <div class="product-image">
                                            <img src="https://via.placeholder.com/250x200/<?php echo $color; ?>/ffffff?text=<?php echo urlencode($product['product_name']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                        </div>
                                        <div class="product-card-info">
                                            <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                            <p class="category"><?php echo strtoupper($product['category'] ?? 'N/A'); ?></p>
                                            <div class="product-meta">
                                                <span class="price">₱<?php echo number_format((float)($product['price'] ?? 0), 2); ?>/kg</span>
                                                <span class="stock">Stock: <?php echo (int)($product['stock'] ?? 0); ?> kg</span>
                                            </div>
                                            <div class="product-actions-bar">
                                                <button class="action-btn view-btn" title="View">👁</button>
                                                <button class="action-btn edit-btn" title="Edit">✎</button>
                                                <button class="action-btn delete-btn" title="Delete">🗑</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-products">
                                <p>No products listed yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-farmers">
                    <p>No farmers registered yet</p>
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
