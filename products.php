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

$isAdmin = $currentUserRole !== 'farmer' && $currentUserRole !== 'seller';
$adminSearch = trim($_GET['search'] ?? '');
$message = '';
$error = '';

// Handle add/edit product
if (!$isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $productName = trim($_POST['product_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $productId = (int) ($_POST['product_id'] ?? 0);

    if (!$productName) {
        $error = 'Product name is required.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0.';
    } elseif ($stock < 0) {
        $error = 'Stock cannot be negative.';
    } elseif (!$category) {
        $error = 'Category is required.';
    } else {
        if ($action === 'add') {
            $safeName = mysqli_real_escape_string($conn, $productName);
            $safeCategory = mysqli_real_escape_string($conn, $category);
            $safeDesc = mysqli_real_escape_string($conn, $description);

            $insertSql = "INSERT INTO products (farmer_id, product_name, category, price, stock, description)
                         VALUES ($currentUserId, '$safeName', '$safeCategory', $price, $stock, '$safeDesc')";

            if (mysqli_query($conn, $insertSql)) {
                $message = 'Product added successfully! It will appear on the marketplace.';
            } else {
                $error = 'Failed to add product: ' . mysqli_error($conn);
            }
        } elseif ($action === 'edit' && $productId > 0) {
            $checkOwner = mysqli_query($conn, "SELECT farmer_id FROM products WHERE id = $productId");
            if ($checkOwner && mysqli_num_rows($checkOwner) > 0) {
                $row = mysqli_fetch_assoc($checkOwner);
                if ((int) $row['farmer_id'] === $currentUserId) {
                    $safeName = mysqli_real_escape_string($conn, $productName);
                    $safeCategory = mysqli_real_escape_string($conn, $category);
                    $safeDesc = mysqli_real_escape_string($conn, $description);

                    $updateSql = "UPDATE products
                                 SET product_name = '$safeName', category = '$safeCategory',
                                     price = $price, stock = $stock, description = '$safeDesc'
                                 WHERE id = $productId AND farmer_id = $currentUserId";

                    if (mysqli_query($conn, $updateSql)) {
                        $message = 'Product updated successfully!';
                    } else {
                        $error = 'Failed to update product: ' . mysqli_error($conn);
                    }
                } else {
                    $error = 'Unauthorized: You cannot edit this product.';
                }
            } else {
                $error = 'Product not found.';
            }
        } elseif ($action === 'adjust_stock' && $productId > 0) {
            $adjustAmount = 0;
            if (isset($_POST['quick_adjust'])) {
                $adjustAmount = (int) $_POST['quick_adjust'];
            } elseif (isset($_POST['adjust_quantity'])) {
                $adjustAmount = (int) $_POST['adjust_quantity'];
            }

            if ($adjustAmount === 0) {
                $error = 'Enter a stock amount or use the quick stock buttons.';
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
                            $message = 'Stock updated successfully!';
                        } else {
                            $error = 'Failed to update stock: ' . mysqli_error($conn);
                        }
                    } else {
                        $error = 'Unauthorized: You cannot edit this product.';
                    }
                } else {
                    $error = 'Product not found.';
                }
            }
        }
    }
}

// Handle delete
if (!$isAdmin && isset($_GET['delete_id'])) {
    $deleteId = (int) $_GET['delete_id'];
    $checkOwner = mysqli_query($conn, "SELECT farmer_id FROM products WHERE id = $deleteId");
    if ($checkOwner && mysqli_num_rows($checkOwner) > 0) {
        $row = mysqli_fetch_assoc($checkOwner);
        if ((int) $row['farmer_id'] === $currentUserId) {
            if (mysqli_query($conn, "DELETE FROM products WHERE id = $deleteId AND farmer_id = $currentUserId")) {
                $message = 'Product deleted successfully.';
            } else {
                $error = 'Failed to delete product.';
            }
        } else {
            $error = 'Unauthorized: You cannot delete this product.';
        }
    }
}

// Get products
$products = [];
if ($isAdmin) {
    $searchFilter = '';
    if ($adminSearch !== '') {
        $safeSearch = mysqli_real_escape_string($conn, $adminSearch);
        $searchFilter = "WHERE p.product_name LIKE '%$safeSearch%' OR p.category LIKE '%$safeSearch%' OR u.full_name LIKE '%$safeSearch%'";
    }

    $productResult = mysqli_query($conn, "SELECT p.id, p.product_name, p.category, p.price, p.stock, p.description, u.full_name AS farmer_name FROM products p LEFT JOIN users u ON p.farmer_id = u.id $searchFilter ORDER BY p.id DESC");
    if ($productResult) {
        while ($row = mysqli_fetch_assoc($productResult)) {
            $products[] = $row;
        }
    }
} else {
    $productResult = mysqli_query($conn, "SELECT id, product_name, category, price, stock, description FROM products WHERE farmer_id = $currentUserId ORDER BY id DESC");
    if ($productResult) {
        while ($row = mysqli_fetch_assoc($productResult)) {
            $products[] = $row;
        }
    }
}

// Get product for editing
$editProduct = null;
if (!$isAdmin && isset($_GET['edit_id'])) {
    $editId = (int) $_GET['edit_id'];
    $editResult = mysqli_query($conn, "SELECT * FROM products WHERE id = $editId AND farmer_id = $currentUserId");
    if ($editResult && mysqli_num_rows($editResult) > 0) {
        $editProduct = mysqli_fetch_assoc($editResult);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - FarmToHome</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="products.css">
    <style>
        .search-bar { display:flex; gap:10px; margin-bottom:20px; }
        .search-bar input { flex:1; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; font-size:14px; }
        .search-bar button { padding:12px 18px; border:none; border-radius:12px; background:#16a34a; color:#fff; cursor:pointer; font-weight:600; }
        
        .admin-products-grid { display:flex; flex-direction:column; gap:14px; }
        .admin-product-row { display:grid; grid-template-columns:80px 1fr 180px 100px 100px 80px; gap:20px; align-items:center; background:white; padding:16px 20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.04); border:1px solid #f3f4f6; }
        
        .product-image-cell img { width:80px; height:80px; border-radius:8px; object-fit:cover; }
        
        .product-details-cell h3 { font-size:16px; font-weight:600; color:#1f2937; margin:0 0 6px 0; }
        .product-category { font-size:13px; color:#6b7280; margin:0; }
        
        .seller-info-cell { position:relative; }
        .farmer-name { font-size:14px; color:#374151; margin:0; font-weight:500; }
        .verified-badge { color:#16a34a; font-size:16px; margin-left:6px; }
        
        .price-cell { text-align:center; }
        .price { font-size:18px; font-weight:700; color:#1f2937; }
        .unit { font-size:12px; color:#6b7280; margin-left:4px; }
        
        .stock-cell { text-align:center; }
        .stock-value { font-size:18px; font-weight:700; color:#1f2937; display:block; }
        .stock-unit { font-size:12px; color:#6b7280; }
        
        .actions-cell { display:flex; gap:8px; justify-content:center; }
        .action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:6px; text-decoration:none; font-size:16px; cursor:pointer; border:none; transition:0.3s; }
        .view-btn { background:#f0f9ff; color:#0284c7; }
        .view-btn:hover { background:#bfdbfe; }
        .delete-btn { background:#fee2e2; color:#dc2626; }
        .delete-btn:hover { background:#fecaca; }
        
        .no-products-message { grid-column:1/-1; padding:40px; text-align:center; color:#6b7280; }
        
        @media (max-width:1200px) {
            .admin-product-row { grid-template-columns:70px 1fr 130px 80px 80px 70px; gap:12px; }
            .product-image-cell img { width:70px; height:70px; }
        }
        @media (max-width:768px) {
            .admin-product-row { grid-template-columns:60px 1fr 60px; gap:12px; }
            .seller-info-cell, .price-cell, .stock-cell { display:none; }
            .actions-cell { grid-column:3; }
        }
    </style>
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
            <a class="sidebar-item active" href="products.php">
                <span>Products</span>
            </a>
            <a class="sidebar-item" href="inventory.php">
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
        <?php if ($isAdmin): ?>
            <section class="products-header">
                <div class="products-title">
                    <h1>Product Oversight</h1>
                    <p>Review all products listed by sellers and search inventory across the platform.</p>
                </div>
                <form class="search-bar" method="GET" action="products.php">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($adminSearch); ?>">
                    <button type="submit">Search</button>
                </form>
            </section>

            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="admin-products-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="admin-product-row">
                            <div class="product-image-cell">
                                <img src="https://via.placeholder.com/80x80?text=<?php echo urlencode($product['product_name']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </div>
                            <div class="product-details-cell">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                            </div>
                            <div class="seller-info-cell">
                                <p class="farmer-name"><?php echo htmlspecialchars($product['farmer_name'] ?: 'Unknown Seller'); ?></p>
                                <span class="verified-badge">✓</span>
                            </div>
                            <div class="price-cell">
                                <span class="price">₱<?php echo number_format($product['price'], 2); ?></span>
                                <span class="unit">/kg</span>
                            </div>
                            <div class="stock-cell">
                                <span class="stock-value"><?php echo (int) $product['stock']; ?></span>
                                <span class="stock-unit">kg</span>
                            </div>
                            <div class="actions-cell">
                                <a href="#" class="action-btn view-btn" title="View">👁</a>
                                <a href="#" class="action-btn delete-btn" title="Delete">✕</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products-message">
                        <p>No products found.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <section class="products-header">
                <div class="products-title">
                    <h1>Product Management</h1>
                    <p>View, add, and manage your farm products on the marketplace.</p>
                </div>
                <button class="add-product-btn" onclick="openAddModal()">+ Add Product</button>
            </section>

            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <section class="products-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="https://via.placeholder.com/300x200?text=<?php echo urlencode($product['product_name']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </div>
                            <div class="product-info">
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
                                <div class="product-status <?php echo $statusClass; ?>">
                                    <?php echo $statusLabel; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <p class="product-desc"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?><?php echo strlen($product['description']) > 100 ? '...' : ''; ?></p>
                                <div class="product-meta">
                                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?>/kg</div>
                                    <div class="product-stock <?php echo $statusClass; ?>">
                                        <?php echo $stockValue; ?> kg
                                    </div>
                                </div>
                                <div class="product-actions">
                                    <a href="products.php?edit_id=<?php echo (int) $product['id']; ?>" class="btn-edit">✎ Edit</a>
                                    <a href="products.php?delete_id=<?php echo (int) $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">🗑 Delete</a>
                                </div>
                                <form method="POST" class="stock-adjust-form">
                                    <input type="hidden" name="action" value="adjust_stock">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <div class="stock-adjust-actions">
                                        <button type="submit" name="quick_adjust" value="-10" class="btn-stock-quick btn-stock-minus">-10</button>
                                        <button type="submit" name="quick_adjust" value="10" class="btn-stock-quick btn-stock-plus">+10</button>
                                    </div>
                                    <div class="stock-custom-group">
                                        <input type="number" name="adjust_quantity" min="1" placeholder="Add amount" class="stock-amount-input">
                                        <button type="submit" class="btn-add-stock">Update Stock</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <h2>No products yet</h2>
                        <p>Add your first product to start selling on the marketplace.</p>
                        <button class="add-product-btn" onclick="openAddModal()">+ Add Your First Product</button>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Product</h2>
            <button class="modal-close" onclick="closeAddModal()">&times;</button>
        </div>

        <?php if ($editProduct): ?>
            <form method="POST" class="product-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="product_id" value="<?php echo (int) $editProduct['id']; ?>">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($editProduct['product_name']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Vegetables" <?php echo $editProduct['category'] === 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                            <option value="Fruits" <?php echo $editProduct['category'] === 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                            <option value="Grains" <?php echo $editProduct['category'] === 'Grains' ? 'selected' : ''; ?>>Grains</option>
                            <option value="Dairy" <?php echo $editProduct['category'] === 'Dairy' ? 'selected' : ''; ?>>Dairy</option>
                            <option value="Herbs" <?php echo $editProduct['category'] === 'Herbs' ? 'selected' : ''; ?>>Herbs</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit" disabled>
                            <option selected>kg</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (per unit)</label>
                        <input type="number" name="price" step="0.01" min="0" value="<?php echo (float) $editProduct['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" min="0" value="<?php echo (int) $editProduct['stock']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($editProduct['description']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.location.href='products.php'">Cancel</button>
                    <button type="submit" class="btn-update">Update Product</button>
                </div>
            </form>
        <?php else: ?>
            <form method="POST" class="product-form">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" placeholder="Organic Tomatoes" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="">Select a category</option>
                            <option value="Vegetables">Vegetables</option>
                            <option value="Fruits">Fruits</option>
                            <option value="Grains">Grains</option>
                            <option value="Dairy">Dairy</option>
                            <option value="Herbs">Herbs</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit" disabled>
                            <option selected>kg</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (per unit)</label>
                        <input type="number" name="price" step="0.01" min="0" placeholder="3.99" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" min="0" placeholder="50" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe your product..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-save">Add Product</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('productModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Add Product';
}

function closeAddModal() {
    document.getElementById('productModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Auto-open edit modal if edit_id is present
<?php if ($editProduct): ?>
    document.getElementById('productModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Edit Product';
<?php endif; ?>
</script>

</body>
</html>
