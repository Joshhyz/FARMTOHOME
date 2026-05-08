<?php

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

// SEARCH
$search = isset($_GET['search']) ? strtolower($_GET['search']) : "";

if ($search != "") {
    $users = array_filter($users, function ($user) use ($search) {
        return (
            strpos(strtolower($user['name']), $search) !== false ||
            strpos(strtolower($user['email']), $search) !== false ||
            strpos(strtolower($user['role']), $search) !== false ||
            strpos(strtolower($user['status']), $search) !== false
        );
    });
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>

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
        <a href="users.php" class="active">👥 Users</a>
        <a href="products.php">📦 Products</a>
        <a href="reports.php">📊 Reports</a>
    </div>

</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <h1>User Management</h1>
        <div class="admin-user">Admin User | Logout</div>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input 
            type="text"
            name="search"
            placeholder="Search users..."
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
        >

        <?php if (!empty($_GET['search'])): ?>
            <a href="users.php" class="clear-btn">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>

    </form>

    <!-- TABLE -->
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

            <?php if (count($users) > 0): ?>

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
                            <a href="#" class="view"><i class="fa-solid fa-eye"></i></a>
                            <a href="#" class="edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="#" class="delete"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">
                        No users found
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