<?php

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

$filter = isset($_GET['status']) ? $_GET['status'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FarmToHome Reports</title>

<link rel="stylesheet" href="admindashboard.css">

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
            <a href="products.php">📦 Products</a>
            <a href="reports.php" class="active">📊 Reports</a>

        </div>

    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">

            <h1>Reports & Analytics</h1>

            <div class="admin-user">
                Admin User | Logout
            </div>

        </div>

        <!-- REPORT CARDS -->
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

                    <a href="?status=Completed" class="green">
                        <?php echo $completedOrders; ?>
                    </a>

                </div>

            </div>

            <div class="card">

                <h3>⏳ Pending Orders</h3>

                <div class="number orange">

                    <a href="?status=Pending" class="orange">
                        <?php echo $pendingOrders; ?>
                    </a>

                </div>

            </div>

        </div>

        <!-- TABLE -->
        <div class="table-container">

            <a href="reports.php" class="reset-btn">
                Show All Orders
            </a>

            <h2 style="margin-bottom:20px;">
                Sales Summary
            </h2>

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

<!-- FLOATING CHAT BUTTON -->
<button class="chat-btn">
    💬
</button>

</body>
</html>