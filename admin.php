<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

/* ====================================
DASHBOARD DATA
==================================== */
$totalUsers = 5;
$totalFarmers = 3;
$totalBuyers = 2;
$totalProducts = 3;
$totalOrders = 3;

/* ====================================
PRODUCTS DATA
==================================== */
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

/* ====================================
USERS DATA
==================================== */
$users = [
    [
        "image" => "https://i.pinimg.com/736x/83/bc/8b/83bc8b88cf6bc4b4e04d153a418cde62.jpg",
        "name" => "Lizbeth Canela",
        "email" => "lizbeth@gmail.com",
        "role" => "Farmer",
        "status" => "Active"
    ],
    [
        "image" => "https://preview.redd.it/random-question-but-does-anyone-have-versions-of-this-cat-v0-ya8qikz9kn0f1.png?auto=webp&s=c2fdba9a3904ab3bec9e7367e380f66343c2929a",
        "name" => "Abigael Pallugna",
        "email" => "abi@gmail.com",
        "role" => "Buyer",
        "status" => "Active"
    ],
    [
        "image" => "https://i.pinimg.com/736x/83/bc/8b/83bc8b88cf6bc4b4e04d153a418cde62.jpg",
        "name" => "Joshua Perez",
        "email" => "josh@outlook.com",
        "role" => "Admin",
        "status" => "Suspended"
    ]
];

/* ====================================
REPORTS DATA
==================================== */
$totalRevenue = 500.00;

$orders = [
    [
        "id" => "ORD001",
        "product" => "Eggplant",
        "farmer" => "Lizbeth Canela",
        "buyer" => "Joshua Perez",
        "amount" => 300.00,
        "status" => "Pending"
    ],
    [
        "id" => "ORD002",
        "product" => "Okra",
        "farmer" => "Ryza Basas",
        "buyer" => "Joshua Perez",
        "amount" => 300.00,
        "status" => "Confirmed"
    ],
    [
        "id" => "ORD003",
        "product" => "String Beans",
        "farmer" => "Ryvyne Santos",
        "buyer" => "Abigael Pallugna",
        "amount" => 200.00,
        "status" => "Completed"
    ]
];

$completedOrders = 0;
$pendingOrders = 0;

foreach ($orders as $order) {

    if ($order['status'] == 'Completed') {
        $completedOrders++;
    }

    if ($order['status'] == 'Pending') {
        $pendingOrders++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FarmToHome Admin Panel</title>

<link rel="stylesheet" href="style.css">

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

            <a href="admin.php?page=dashboard"
            class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                🏠 Dashboard
            </a>

            <a href="admin.php?page=users"
            class="<?php echo ($page == 'users') ? 'active' : ''; ?>">
                👥 Users
            </a>

            <a href="admin.php?page=products"
            class="<?php echo ($page == 'products') ? 'active' : ''; ?>">
                📦 Products
            </a>

            <a href="admin.php?page=reports"
            class="<?php echo ($page == 'reports') ? 'active' : ''; ?>">
                📊 Reports
            </a>

        </div>

    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">

            <h1>

                <?php

                if ($page == 'dashboard') {
                    echo "System Overview";
                }

                elseif ($page == 'users') {
                    echo "User Management";
                }

                elseif ($page == 'products') {
                    echo "Product Oversight";
                }

                elseif ($page == 'reports') {
                    echo "Reports & Analytics";
                }

                ?>

            </h1>

            <div class="admin-user">
                Admin User | Logout
            </div>

        </div>

        <!-- DASHBOARD -->
        <?php if ($page == 'dashboard'): ?>

        <div class="cards">

            <div class="card">
                <h3>👥 Active Users</h3>
                <div class="number"><?php echo $totalUsers; ?></div>
            </div>

            <div class="card">
                <h3>🚜 Farmers</h3>
                <div class="number"><?php echo $totalFarmers; ?></div>
            </div>

            <div class="card">
                <h3>🛒 Buyers</h3>
                <div class="number"><?php echo $totalBuyers; ?></div>
            </div>

            <div class="card">
                <h3>📦 Products</h3>
                <div class="number"><?php echo $totalProducts; ?></div>
            </div>

            <div class="card">
                <h3>📑 Orders</h3>
                <div class="number"><?php echo $totalOrders; ?></div>
            </div>

            <div class="card">
                <h3>✅ Platform Health</h3>
                <div class="number green">Good</div>
            </div>

        </div>

        <div class="activity">

            <h2>Recent Activity</h2>

            <div class="activity-item">
                📦 New product listed
                <p>Eggplants added by Lizbeth Canela</p>
            </div>

            <div class="activity-item">
                🌱 New farmer registered
                <p>Lizbeth Canela joined the platform</p>
            </div>

            <div class="activity-item">
                ✅ Order completed
                <p>Ryvyne Santos received Fresh Okras</p>
            </div>

        </div>

        <?php endif; ?>

        <!-- USERS -->
        <?php if ($page == 'users'): ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach($users as $user): ?>

                <tr>

                    <td>
                        <div class="user-info">
                            <img src="<?= $user['image']; ?>">
                        </div>
                    </td>

                    <td><?= $user['name']; ?></td>
                    <td><?= $user['email']; ?></td>
                    <td><?= $user['role']; ?></td>

                    <td>
                        <span class="badge <?= strtolower($user['status']); ?>">
                            <?= $user['status']; ?>
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

                </tbody>

            </table>

        </div>

        <?php endif; ?>

        <!-- PRODUCTS -->
        <?php if ($page == 'products'): ?>

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

                </tbody>

            </table>

        </div>

        <?php endif; ?>

        <!-- REPORTS -->
        <?php if ($page == 'reports'): ?>

        <div class="cards">

            <div class="card">

                <h3>💰 Total Revenue</h3>

                <div class="number green">
                    ₱<?php echo number_format($totalRevenue, 2); ?>
                </div>

            </div>

            <div class="card">

                <h3>✅ Completed Orders</h3>

                <div class="number green">
                    <?php echo $completedOrders; ?>
                </div>

            </div>

            <div class="card">

                <h3>⏳ Pending Orders</h3>

                <div class="number orange">
                    <?php echo $pendingOrders; ?>
                </div>

            </div>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($orders as $order): ?>

                <tr>

                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['product']; ?></td>
                    <td><?php echo $order['farmer']; ?></td>
                    <td><?php echo $order['buyer']; ?></td>

                    <td class="green">
                        ₱<?php echo number_format($order['amount'], 2); ?>
                    </td>

                    <td>

                        <span class="status <?php echo strtolower($order['status']); ?>">

                            <?php echo $order['status']; ?>

                        </span>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php endif; ?>

    </div>

</div>

<button class="chat-btn">
    💬
</button>

</body>
</html>