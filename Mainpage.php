<?php
// Start session (for login later)
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>FarmToHome</title>
<link rel="stylesheet" href="mainpage.css">
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">

<div class="logo">
<img src="images/logo.png" alt="FarmToHome Logo" style="height: 40px; width: auto;">
FarmToHome
</div>

<nav>
<a href="products.php">Browse</a>
<a href="#">How It Works</a>
<a href="#">Benefits</a>

<?php if(isset($_SESSION['user'])){ ?>

<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>

<?php } else { ?>

<a href="login.php">Login</a>
<a class="btn-start" href="register.php">Get Started</a>

<?php } ?>

</nav>

</header>


<!-- HERO SECTION -->
<section class="hero">

<div class="hero-content">

<span class="badge">🌱 100% Farm Fresh & Organic</span>

<h1>
Fresh From Farm <br>
<span>to Your Home</span>
</h1>

<p>
Connect directly with local farmers. Buy fresh produce.
No middlemen. Fair prices for everyone.
</p>

<div class="hero-buttons">

<a class="btn-primary" href="products.php">
Browse Fresh Produce →
</a>

<a class="btn-outline" href="register.php">
Sell as a Farmer
</a>

</div>

<div class="stats">

<div>
<h3>500+</h3>
<p>Verified Farmers</p>
</div>

<div>
<h3>10K+</h3>
<p>Happy Customers</p>
</div>

<div>
<h3>50+</h3>
<p>Cities</p>
</div>

</div>

</div>

</section>
<section class="produce-section">

<div class="produce-header">
<h2>Fresh Produce Available Now</h2>
<p>Handpicked from local farms, delivered fresh to your doorstep</p>
</div>

<div class="produce-container">

<!-- Product 1 -->
<div class="product-card">

<div class="product-image">
<img src="images/potatoes.jpg">
<span class="verified">✔ Verified</span>
</div>

<div class="product-info">
<span class="category">Vegetables</span>

<h3>Fresh Potatoes</h3>

<div class="price-row">
<p class="price">$1.99 <span>/kg</span></p>
<p class="stock">100 kg left</p>
</div>

<p class="farmer">👨‍🌾 Sarah Green</p>

</div>
</div>


<!-- Product 2 -->
<div class="product-card">

<div class="product-image">
<img src="images/strawberries.jpg">
<span class="verified">✔ Verified</span>
</div>

<div class="product-info">

<span class="category">Fruits</span>

<h3>Organic Strawberries</h3>

<div class="price-row">
<p class="price">$5.99 <span>/kg</span></p>
<p class="stock">20 kg left</p>
</div>

<p class="farmer">👨‍🌾 Sarah Green</p>

</div>
</div>


<!-- Product 3 -->
<div class="product-card">

<div class="product-image">
<img src="images/mixedvegetables.jpg">
</div>

<div class="product-info">

<span class="category">Vegetables</span>

<h3>Mixed Vegetables</h3>

<div class="price-row">
<p class="price">$4.5 <span>/kg</span></p>
<p class="stock">40 kg left</p>
</div>

<p class="farmer">👨‍🌾 Mike Harvest</p>

</div>
</div>

</div>


<div class="view-products">
<a href="products.php">View All Products →</a>
</div>

</section>

<section class="how-it-works">

<div class="how-header">
<h2>How It Works</h2>
<p>Three simple steps to fresh farm produce</p>
</div>

<div class="steps-container">

<!-- Step 1 -->
<div class="step-card">

<div class="step-icon">
🌱
</div>

<h3>Farmers List Products</h3>

<p>
Local farmers showcase their fresh produce with photos,
prices, and availability
</p>

</div>

<!-- Step 2 -->
<div class="step-card">

<div class="step-icon">
🛒
</div>

<h3>Browse & Order</h3>

<p>
Buyers browse products, message farmers,
and place orders directly
</p>

</div>

<!-- Step 3 -->
<div class="step-card">

<div class="step-icon">
📈
</div>

<h3>Direct Delivery</h3>

<p>
Arrange pickup or delivery directly with the farmer.
Simple and transparent.
</p>

</div>

</div>

</section>




















<!-- FOOTER -->
<footer class="footer">
<div class="footer-content">
<p>&copy; 2026 FarmToHome. All rights reserved.</p>
<div class="footer-links">
<a href="#">Privacy Policy</a>
<a href="#">Terms of Service</a>
<a href="#">Contact Us</a>
</div>
</div>
</footer>

</body>
</html>