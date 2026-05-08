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

$products = [];
$productMessage = "";
require_once 'database.php';

if (isset($conn)) {
    $query = "SELECT p.id, p.product_name as name, p.description, p.price, p.stock as quantity, p.farmer_id, u.full_name as seller_name FROM products p JOIN users u ON p.farmer_id = u.id WHERE p.stock > 0 ORDER BY p.id DESC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    } else {
        $productMessage = "Unable to load marketplace products right now.";
    }
} else {
    $productMessage = "Product database unavailable.";
}

$productCount = count($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - FarmToHome</title>
    <link rel="stylesheet" href="marketplace.css">
</head>
<body>

<header class="market-navbar">
    <div class="market-logo">
        <img src="images/logo.png" alt="FarmToHome Logo">
        <span>FarmToHome</span>
    </div>

    <div class="market-nav-right">
        <span class="welcome-text">Hi, <?php echo htmlspecialchars($userName); ?></span>
        <a href="dashboard.php" class="dashboard-btn">Dashboard</a>
        <a href="Mainpage.php" class="dashboard-btn">Home</a>
        <a href="logout.php" class="dashboard-btn">Logout</a>
    </div>
</header>

<!-- MARKETPLACE HERO -->
<section class="market-hero">
    <h1>Fresh Produce Marketplace</h1>
    <p>Discover farm-fresh products delivered straight from local farmers</p>

    <div class="search-box">
        <input type="text" placeholder="Search for fresh tomatoes, carrots, lettuce..." disabled>
    </div>
</section>

<!-- MARKETPLACE PRODUCTS SECTION -->
<section class="market-content" id="marketplace-products">
    <div class="filter-bar">
        <div class="filter-left">
            <h3>Filters</h3>

            <div class="filter-grid">
                <div class="filter-group">
                    <label>Category</label>
                    <input type="text" placeholder="Coming soon" disabled>
                </div>

                <div class="filter-group">
                    <label>Price Range</label>
                    <input type="text" placeholder="Coming soon" disabled>
                </div>

                <div class="filter-group results-box">
                    <label>Showing Results</label>
                    <div class="result-number"><?php echo $productCount; ?></div>
                </div>
            </div>
        </div>

        <div class="view-icons">
            <button type="button" aria-label="Grid View">▦</button>
            <button type="button" aria-label="List View">☰</button>
        </div>
    </div>

    <?php if ($productCount > 0): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-desc"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="price-row">
                        <p class="price">₱<?php echo number_format($product['price'], 2); ?> <span>/kg</span></p>
                        <p class="stock"><?php echo (int)$product['quantity']; ?> kg left</p>
                    </div>
                    <p class="seller-name">Farmer: <?php echo htmlspecialchars($product['seller_name'] ?? 'Local Farmer'); ?></p>
                    <div class="product-actions">
                        <span class="product-badge">Accessible to buyers and sellers</span>
                        <?php if ($product['farmer_id'] !== $_SESSION['user_id']): ?>
                            <a href="Message.php?user_id=<?php echo (int)$product['farmer_id']; ?>" class="product-contact">Message Seller</a>
                        <?php else: ?>
                            <span class="product-contact product-owner">Your Listing</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-products">
            <div class="empty-card">
                <h2>No products available yet</h2>
                <p>
                    Farmers will post their fresh produce here soon.
                    Once sellers add products, they will appear in this marketplace.
                </p>

                <?php if ($productMessage): ?>
                    <p class="error-message"><?php echo htmlspecialchars($productMessage); ?></p>
                <?php endif; ?>

                <?php if ($isFarmer): ?>
                    <a href="products.php" class="back-market-btn">Add Your First Product</a>
                <?php else: ?>
                    <a href="Mainpage.php" class="back-market-btn">Back to Home</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

</body>
</html>