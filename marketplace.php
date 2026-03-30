<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: registration.php?mode=login");
    exit();
}

$userName = $_SESSION["user_name"] ?? "User";
$userRole = strtolower(trim($_SESSION["user_role"] ?? "buyer")); // buyer or farmer
$isFarmer = ($userRole === "farmer" || $userRole === "seller");
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
        <a href="Mainpage.php" class="dashboard-btn">Home</a>
        <a href="logout.php" class="dashboard-btn">Logout</a>
    </div>
</header>

<!-- ROLE-BASED DASHBOARD SECTION -->
<section class="role-dashboard">
    <div class="role-dashboard-inner">

        <?php if ($isFarmer): ?>
            <div class="dashboard-header">
                <h1>Farmer Dashboard</h1>
                <p>Manage your products, orders, inventory, and sales in one place.</p>
            </div>

            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h3>Total Products</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Active Orders</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Low Stock Alerts</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Total Sales</h3>
                    <p>₱0.00</p>
                </div>
            </div>

            <div class="dashboard-actions">
                <a href="add_product.php" class="action-btn">Add Product</a>
                <a href="products.php" class="action-btn">My Products</a>
                <a href="orders.php" class="action-btn">View Orders</a>
                <a href="inventory.php" class="action-btn">Inventory</a>
                <a href="#marketplace-products" class="action-btn">Browse Marketplace</a>
            </div>

        <?php else: ?>
            <div class="dashboard-header">
                <h1>Buyer Dashboard</h1>
                <p>Browse fresh produce, track your orders, and connect with local farmers.</p>
            </div>

            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h3>Total Orders</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Active Orders</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Completed Orders</h3>
                    <p>0</p>
                </div>

                <div class="dashboard-card">
                    <h3>Saved Items</h3>
                    <p>0</p>
                </div>
            </div>

            <div class="dashboard-actions">
                <a href="#marketplace-products" class="action-btn">Browse Products</a>
                <a href="orders.php" class="action-btn">My Orders</a>
                <a href="messages.php" class="action-btn">Messages</a>
                <a href="profile.php" class="action-btn">My Profile</a>
            </div>
        <?php endif; ?>

    </div>
</section>

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
                    <div class="result-number">0</div>
                </div>
            </div>
        </div>

        <div class="view-icons">
            <button type="button" aria-label="Grid View">▦</button>
            <button type="button" aria-label="List View">☰</button>
        </div>
    </div>

    <div class="empty-products">
        <div class="empty-card">
            <h2>No products available yet</h2>
            <p>
                Farmers will post their fresh produce here soon.
                Once sellers add products, they will appear in this marketplace.
            </p>

            <?php if ($isFarmer): ?>
                <a href="add_product.php" class="back-market-btn">Add Your First Product</a>
            <?php else: ?>
                <a href="Mainpage.php" class="back-market-btn">Back to Home</a>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>