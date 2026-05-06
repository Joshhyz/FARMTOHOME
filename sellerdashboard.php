<<?php
session_start();
include "database.php";

// SQA SECURITY CHECK: Only let Farmers in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "farmer") {
    header("Location: registration.php?mode=login");
    exit();
}

$userName = $_SESSION["user_name"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Dashboard - FarmToHome</title>
    <link rel="stylesheet" href="marketplace.css"> </head>
<body>

<header class="market-navbar">
    <div class="market-logo"><span>FarmToHome Farmer</span></div>
    <div class="market-nav-right">
        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($userName); ?>!</span>
        <a href="logout.php" class="dashboard-btn">Logout</a>
    </div>
</header>

<section class="role-dashboard">
    <div class="role-dashboard-inner">
        <div class="dashboard-header">
            <h1>Farmer Control Center</h1>
            <p>Verification Status: <span style="color: green;">Verified ✅</span></p>
        </div>

        <div class="dashboard-actions" style="margin-top: 30px;">
            <a href="add_product.php" class="action-btn">➕ Add New Product</a>
            <a href="marketplace.php" class="action-btn">🛒 View Marketplace</a>
        </div>
    </div>
</section>

</body>
</html>

