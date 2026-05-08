<?php

// =====================================
// DASHBOARD DATA
// =====================================

$totalUsers = 5;
$totalFarmers = 3;
$totalBuyers = 2;
$totalProducts = 3;
$totalOrders = 3;
$totalRevenue = 800;

// =====================================
// PRODUCTS
// =====================================

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

// =====================================
// ORDERS
// =====================================

$orders = [
    [
        "id" => "ORD001",
        "product" => "Eggplant",
        "farmer" => "Lizbeth Canela",
        "buyer" => "Joshua Perez",
        "amount" => 300,
        "status" => "Pending"
    ],
    [
        "id" => "ORD002",
        "product" => "Okra",
        "farmer" => "Ryza Basas",
        "buyer" => "Joshua Perez",
        "amount" => 300,
        "status" => "Confirmed"
    ],
    [
        "id" => "ORD003",
        "product" => "String Beans",
        "farmer" => "Ryvyne Santos",
        "buyer" => "Abigael Pallugna",
        "amount" => 200,
        "status" => "Completed"
    ]
];

// =====================================
// COUNT ORDERS
// =====================================

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

// =====================================
// SEARCH
// =====================================

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

// =====================================
// REPORT FILTER
// =====================================

$filter = isset($_GET['status']) ? $_GET['status'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FarmToHome Admin</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* ====================================
GLOBAL STYLES
==================================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body{
    background:#f4f7fb;
    color:#1f2937;
}

/* ====================================
LAYOUT
==================================== */

.container{
    display:flex;
    min-height:100vh;
}

/* ====================================
SIDEBAR
==================================== */

.sidebar{
    width:260px;
    background:#ffffff;
    border-right:1px solid #e5e7eb;
    padding:25px 20px;
    position:sticky;
    top:0;
    height:100vh;
}

.logo{
    font-size:28px;
    font-weight:700;
    color:#16a34a;
    margin-bottom:40px;
}

.logo span{
    display:block;
    font-size:14px;
    color:#6b7280;
    margin-top:6px;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    border-radius:14px;
    text-decoration:none;
    color:#374151;
    font-weight:500;
    transition:0.3s;
}

.menu a:hover{
    background:#dcfce7;
    color:#16a34a;
}

.menu .active{
    background:#16a34a;
    color:white;
}

/* ====================================
MAIN
==================================== */

.main{
    flex:1;
    padding:35px;
}

/* ====================================
TOPBAR
==================================== */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    background:white;
    padding:20px 25px;
    border-radius:18px;
    box-shadow:0 4px 10px rgba(0,0,0,0.04);
}

.topbar h1{
    font-size:30px;
}

.admin-user{
    color:#6b7280;
}

/* ====================================
CARDS
==================================== */

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.card h3{
    margin-bottom:15px;
    color:#6b7280;
}

.number{
    font-size:38px;
    font-weight:700;
}

.number a{
    text-decoration:none;
}

.green{
    color:#16a34a;
}

.orange{
    color:#f97316;
}

/* ====================================
ACTIVITY
==================================== */

.activity{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    margin-bottom:30px;
}

.activity h2{
    margin-bottom:20px;
}

.activity-item{
    padding:18px 0;
    border-bottom:1px solid #f1f5f9;
}

.activity-item:last-child{
    border-bottom:none;
}

.activity-item p{
    color:#6b7280;
    margin-top:6px;
}

/* ====================================
SEARCH
==================================== */

.search{
    margin-bottom:20px;
    background:white;
    padding:14px 18px;
    border-radius:14px;
    display:flex;
    align-items:center;
    gap:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.04);
}

.search input{
    width:100%;
    border:none;
    outline:none;
    background:transparent;
}

.clear-btn{
    color:#6b7280;
    text-decoration:none;
}

/* ====================================
TABLE
==================================== */

.table-container{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    margin-bottom:30px;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th{
    text-align:left;
    padding:16px;
    background:#f9fafb;
}

table td{
    padding:16px;
    border-bottom:1px solid #f3f4f6;
}

.product-info{
    display:flex;
    align-items:center;
    gap:12px;
}

.product-info img{
    width:55px;
    height:55px;
    border-radius:12px;
    object-fit:cover;
}

.product-details span{
    color:#6b7280;
    font-size:13px;
}

/* ====================================
STATUS
==================================== */

.status,
.badge{
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.completed,
.available{
    background:#dcfce7;
    color:#16a34a;
}

.pending{
    background:#fef3c7;
    color:#d97706;
}

.confirmed{
    background:#dbeafe;
    color:#2563eb;
}

.out-of-stock{
    background:#fee2e2;
    color:#dc2626;
}

/* ====================================
ACTIONS
==================================== */

.actions{
    display:flex;
    gap:10px;
}

.actions a{
    width:35px;
    height:35px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f3f4f6;
    text-decoration:none;
}

.view{
    color:#2563eb;
}

.edit{
    color:#f59e0b;
}

.delete{
    color:#dc2626;
}

/* ====================================
RESET BUTTON
==================================== */

.reset-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 18px;
    background:#16a34a;
    color:white;
    border-radius:12px;
    text-decoration:none;
}

/* ====================================
CHAT BUTTON
==================================== */

.chat-btn{
    position:fixed;
    right:25px;
    bottom:25px;
    width:65px;
    height:65px;
    border:none;
    border-radius:50%;
    background:#16a34a;
    color:white;
    font-size:26px;
    cursor:pointer;
}

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
        <a href="#" class="active">🏠 Dashboard</a>
        <a href="#products">📦 Products</a>
        <a href="#reports">📊 Reports</a>
    </div>

</div>

<!-- MAIN -->
<div class="main">

    <!-- DASHBOARD -->
    <div class="topbar">

        <h1>System Overview</h1>

        <div class="admin-user">
            Admin User | Logout
        </div>

    </div>

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
            <h3>📦 Total Products</h3>
            <div class="number"><?php echo $totalProducts; ?></div>
        </div>

        <div class="card">
            <h3>📑 Total Orders</h3>
            <div class="number"><?php echo $totalOrders; ?></div>
        </div>

        <div class="card">
            <h3>💰 Revenue</h3>
            <div class="number green">₱<?php echo $totalRevenue; ?></div>
        </div>

    </div>

    <!-- ACTIVITY -->
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

    <!-- PRODUCTS -->
    <div id="products" class="topbar">
        <h1>Product Oversight</h1>
    </div>

    <form method="GET" class="search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input 
            type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
        >

        <?php if (!empty($_GET['search'])): ?>
            <a href="admin.php" class="clear-btn">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>

    </form>

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

    <!-- REPORTS -->
    <div id="reports" class="topbar">
        <h1>Reports & Analytics</h1>
    </div>

    <div class="cards">

        <div class="card">
            <h3>✅ Completed Orders</h3>

            <div class="number green">
                <a href="?status=Completed#reports" class="green">
                    <?php echo $completedOrders; ?>
                </a>
            </div>
        </div>

        <div class="card">
            <h3>⏳ Pending Orders</h3>

            <div class="number orange">
                <a href="?status=Pending#reports" class="orange">
                    <?php echo $pendingOrders; ?>
                </a>
            </div>
        </div>

    </div>

    <div class="table-container">

        <a href="admin.php#reports" class="reset-btn">
            Show All Orders
        </a>

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

                <?php
                if ($filter && $order['status'] != $filter) {
                    continue;
                }
                ?>

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

</div>

</div>

<button class="chat-btn">
    💬
</button>

</body>
</html>