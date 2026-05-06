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

// Handle add/edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
        }
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
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

// Get seller's products
$products = [];
$productResult = mysqli_query($conn, "SELECT id, product_name, category, price, stock, description FROM products WHERE farmer_id = $currentUserId ORDER BY id DESC");
if ($productResult) {
    while ($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit_id'])) {
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
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="product-desc"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?><?php echo strlen($product['description']) > 100 ? '...' : ''; ?></p>
                            <div class="product-meta">
                                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?>/kg</div>
                                <div class="product-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                    <?php echo (int) $product['stock']; ?> kg
                                </div>
                            </div>
                            <div class="product-actions">
                                <a href="products.php?edit_id=<?php echo (int) $product['id']; ?>" class="btn-edit">✎ Edit</a>
                                <a href="products.php?delete_id=<?php echo (int) $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">🗑 Delete</a>
                            </div>
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
