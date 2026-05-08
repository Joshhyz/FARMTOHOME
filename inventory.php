<?php
$cookieLifetime = 60 * 60 * 24 * 30;
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: registration.php?mode=login');
    exit();
}

require_once 'database.php';

$currentUserId = (int) $_SESSION['user_id'];
$currentUserName = $_SESSION['user_name'] ?? 'User';
$currentUserRole = strtolower(trim($_SESSION['user_role'] ?? 'buyer'));

if ($currentUserRole !== 'farmer' && $currentUserRole !== 'seller') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'adjust_stock') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $adjustAmount = 0;

    if (isset($_POST['quick_adjust'])) {
        $adjustAmount = (int) $_POST['quick_adjust'];
    } elseif (isset($_POST['adjust_quantity'])) {
        $adjustAmount = (int) $_POST['adjust_quantity'];
    }

    if ($productId <= 0 || $adjustAmount === 0) {
        $error = 'Provide a valid amount to update stock.';
    } else {
        $checkOwner = mysqli_query($conn, "SELECT farmer_id, stock FROM products WHERE id = $productId");
        if ($checkOwner && mysqli_num_rows($checkOwner) > 0) {
            $row = mysqli_fetch_assoc($checkOwner);
            if ((int) $row['farmer_id'] === $currentUserId) {
                $currentStock = (int) $row['stock'];
                $newStock = $currentStock + $adjustAmount;
                if ($newStock < 0) {
                    $newStock = 0;
                }

                if (mysqli_query($conn, "UPDATE products SET stock = $newStock WHERE id = $productId AND farmer_id = $currentUserId")) {
                    $message = 'Stock updated successfully.';
                } else {
                    $error = 'Could not update stock: ' . mysqli_error($conn);
                }
            } else {
                $error = 'Unauthorized: You cannot update this product.';
            }
        } else {
            $error = 'Product not found.';
        }
    }
}

$products = [];
$productResult = mysqli_query($conn, "SELECT id, product_name, category, stock, description FROM products WHERE farmer_id = $currentUserId ORDER BY id DESC");
if ($productResult) {
    while ($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Monitoring - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="products.css">
</head>
<body class="dashboard-page">

<header class="dashboard-topbar">
    <div class="dashboard-brand">
        <img src="images/logo.png" alt="FarmToHome Logo">
        FarmToHome
    </div>
    <div class="dashboard-topbar-right">
        <span class="dashboard-role-label">Farmer Role</span>
        <div class="dashboard-user">Hi, <?php echo htmlspecialchars($currentUserName); ?></div>
        <a href="logout.php" class="dashboard-logout">Logout</a>
    </div>
</header>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-title">Farmer Menu</div>
        <div class="sidebar-description">Manage products, orders, inventory, and customer messages.</div>
        <div class="sidebar-menu">
            <a class="sidebar-item" href="dashboard.php">
                <span>Dashboard</span>
            </a>
            <a class="sidebar-item" href="products.php">
                <span>Products</span>
            </a>
            <a class="sidebar-item active" href="inventory.php">
                <span>Inventory</span>
            </a>
            <a class="sidebar-item" href="orders.php">
                <span>Orders</span>
            </a>
            <a class="sidebar-item" href="Message.php">
                <span>Messages</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <section class="products-header">
            <div class="products-title">
                <h1>Inventory Monitoring</h1>
                <p>Review your listings and update available stock from one place.</p>
            </div>
        </section>

        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (count($products) === 0): ?>
            <div class="no-products">
                <h2>No products found</h2>
                <p>Add products first from the Products page, then manage your inventory here.</p>
                <button class="add-product-btn" onclick="window.location.href='products.php'">Go to Products</button>
            </div>
        <?php else: ?>
            <section class="inventory-panel">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Stock Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                                $stockValue = (int) $product['stock'];
                                if ($stockValue === 0) {
                                    $statusClass = 'out-of-stock';
                                    $statusLabel = 'Out of Stock';
                                } elseif ($stockValue <= 10) {
                                    $statusClass = 'low-stock';
                                    $statusLabel = 'Low Stock';
                                } else {
                                    $statusClass = 'in-stock';
                                    $statusLabel = 'In Stock';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="inventory-product-info">
                                        <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                        <span><?php echo htmlspecialchars($product['category']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo $stockValue; ?> kg</td>
                                <td><div class="product-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></div></td>
                                <td class="inventory-actions">
                                    <form method="POST" class="inventory-action-form">
                                        <input type="hidden" name="action" value="adjust_stock">
                                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                        <button type="submit" name="quick_adjust" value="-10" class="btn-stock-quick btn-stock-minus">-10</button>
                                        <button type="submit" name="quick_adjust" value="10" class="btn-stock-quick btn-stock-plus">+10</button>
                                    </form>
                                    <form method="POST" class="inventory-action-form">
                                        <input type="hidden" name="action" value="adjust_stock">
                                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                        <input type="number" name="adjust_quantity" min="1" placeholder="Add amount" class="stock-amount-input">
                                        <button type="submit" class="btn-add-stock">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
