<?php

$products = [
    [
        "image" => "https://images.squarespace-cdn.com/content/v1/57d1b689e6f2e1faa4ced747/1684335564505-9DR6NIAOUE04W55LKDZJ/IMG_3073.JPG",
        "name" => "Eggplant",
        "category" => "Vegetables",
        "farmer" => "Lizbeth Canela",
        "price" => "₱100 / kg",
        "stock" => "50 kg",
        "status" => "Available"
    ],
    [
        "image" => "https://www.cookedandloved.com/wp-content/uploads/2020/02/what-is-okra-s.jpg",
        "name" => "Okra",
        "category" => "Vegetables",
        "farmer" => "Ryza Basas",
        "price" => "₱100 / kg",
        "stock" => "30 kg",
        "status" => "Available"
    ],
    [
        "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRZRaWyTGXiFdSl050XDiZl796MQ9Tph8Df0Q&s",
        "name" => "String Beans",
        "category" => "Vegetables",
        "farmer" => "Ryvyne Santos",
        "price" => "₱70 / kg",
        "stock" => "0 kg",
        "status" => "Out of Stock"
    ]
];

// ============================
// SEARCH FUNCTION
// ============================
$search = isset($_GET['search']) ? strtolower($_GET['search']) : "";

if ($search != "") {
    $products = array_filter($products, function ($product) use ($search) {
        return (
            strpos(strtolower($product['name']), $search) !== false ||
            strpos(strtolower($product['category']), $search) !== false ||
            strpos(strtolower($product['farmer']), $search) !== false ||
            strpos(strtolower($product['status']), $search) !== false
        );
    });
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Oversight</title>

<link rel="stylesheet" href="admindashboard.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

        <a href="index.php">🏠 Dashboard</a>
        <a href="users.php">👥 Users</a>
        <a href="products.php" class="active">📦 Products</a>
        <a href="reports.php">📊 Reports</a>

    </div>

</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">

        <h1>Product Oversight</h1>

        <div class="admin-user">
            Admin User | Logout
        </div>

    </div>

    <!-- SEARCH BAR (UPDATED + X BUTTON) -->

    <form method="GET" class="search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input 
            type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
        >

        <?php if (!empty($_GET['search'])): ?>
            <a href="products.php" class="clear-btn">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>

    </form>

    <!-- TABLE -->

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Farmer</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (count($products) > 0): ?>

                <?php foreach($products as $product): ?>

                <tr>

                    <td>
                        <div class="product-info">

                            <img src="<?= $product['image']; ?>">

                            <div class="product-details">
                                <h4><?= $product['name']; ?></h4>
                                <span><?= $product['category']; ?></span>
                            </div>

                        </div>
                    </td>

                    <td><?= $product['farmer']; ?></td>
                    <td><?= $product['price']; ?></td>
                    <td><?= $product['stock']; ?></td>

                    <td>
                        <span class="badge <?= strtolower(str_replace(' ', '-', $product['status'])); ?>">
                            <?= $product['status']; ?>
                        </span>
                    </td>

                    <td>
                        <div class="actions">

                            <a href="#" class="view">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="#" class="edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="#" class="delete">
                                <i class="fa-solid fa-trash"></i>
                            </a>

                        </div>
                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">
                        No products found
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</div>

</body>
</html>