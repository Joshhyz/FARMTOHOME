<?php
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
        <span>FarmToHome</span>
    </div>

    <nav>
        <a href="products.php">Browse</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#benefits">Benefits</a>

        <?php if (isset($_SESSION["user_id"])) { ?>
            <span class="user-name">
                Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </span>

            <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "farmer") { ?>
                <a href="dashboard.php">Dashboard</a>
            <?php } else { ?>
                <a href="orderhistory.php">My Orders</a>
            <?php } ?>

            <a class="logout-btn" href="Mainpage.php">Logout</a>
        <?php } else { ?>
            <a href="registration.php?mode=login">Login</a>
            <a class="btn-start" href="registration.php?mode=signup">Get Started</a>
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
            <a class="btn-primary" href="products.php">Browse Fresh Produce →</a>

            <?php if (isset($_SESSION["user_id"])) { ?>
                <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "farmer") { ?>
                    <a class="btn-outline" href="dashboard.php">Go to Farmer Dashboard</a>
                <?php } else { ?>
                    <a class="btn-outline" href="products.php">Start Shopping</a>
                <?php } ?>
            <?php } else { ?>
                <a class="btn-outline" href="registration.php?mode=signup">Sell as a Farmer</a>
            <?php } ?>
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

<!-- PRODUCE SECTION -->
<section class="produce-section">
    <div class="produce-header">
        <h2>Fresh Produce Available Now</h2>
        <p>Handpicked from local farms, delivered fresh to your doorstep</p>
    </div>

    <div class="produce-container">
        <div class="product-card">
            <div class="product-image">
                <img src="images/potatoes.jpg" alt="Fresh Potatoes">
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

        <div class="product-card">
            <div class="product-image">
                <img src="images/strawberries.jpg" alt="Organic Strawberries">
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

        <div class="product-card">
            <div class="product-image">
                <img src="images/mixedvegetables.jpg" alt="Mixed Vegetables">
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

<!-- HOW IT WORKS -->
<section class="how-it-works" id="how-it-works">
    <div class="how-header">
        <h2>How It Works</h2>
        <p>Three simple steps to fresh farm produce</p>
    </div>

    <div class="steps-container">
        <div class="step-card">
            <div class="step-icon">🌱</div>
            <h3>Farmers List Products</h3>
            <p>
                Local farmers showcase their fresh produce with photos,
                prices, and availability
            </p>
        </div>

        <div class="step-card">
            <div class="step-icon">🛒</div>
            <h3>Browse & Order</h3>
            <p>
                Buyers browse products, message farmers,
                and place orders directly
            </p>
        </div>

        <div class="step-card">
            <div class="step-icon">📈</div>
            <h3>Direct Delivery</h3>
            <p>
                Arrange pickup or delivery directly with the farmer.
                Simple and transparent.
            </p>
        </div>
    </div>
</section>

<!-- BENEFITS -->
<section class="benefits-section" id="benefits">
    <div class="benefits-header">
        <h2>Benefits for Everyone</h2>
        <p>Empowering farmers and delighting customers</p>
    </div>

    <div class="benefit-row">
        <div class="benefit-image-card">
            <img src="images/farmer-benefit.jpg" alt="Farmer">
            <div class="benefit-badge top-badge">
                <h3>85%</h3>
                <span>Higher Earnings</span>
            </div>
        </div>

        <div class="benefit-content">
            <h3 class="benefit-title">🌱 For Farmers</h3>

            <div class="benefit-list">
                <div class="benefit-item">✔ Sell directly without middlemen and earn more</div>
                <div class="benefit-item">✔ Easy inventory management and order tracking</div>
                <div class="benefit-item">✔ Direct communication with customers</div>
                <div class="benefit-item">✔ Build your reputation and customer base</div>
            </div>
        </div>
    </div>

    <div class="benefit-row reverse-row">
        <div class="benefit-content">
            <h3 class="benefit-title">🛍️ For Buyers</h3>

            <div class="benefit-list">
                <div class="benefit-item">✔ Fresh produce directly from local farms</div>
                <div class="benefit-item">✔ Fair prices without middleman markup</div>
                <div class="benefit-item">✔ Know exactly where your food comes from</div>
                <div class="benefit-item">✔ Support local agriculture and farmers</div>
            </div>
        </div>

        <div class="benefit-image-card">
            <img src="images/buyer-benefit.jpg" alt="Buyer">
            <div class="benefit-badge bottom-badge">
                <h3>100%</h3>
                <span>Farm Fresh</span>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section">
    <div class="testimonials-header">
        <h2>What Our Community Says</h2>
        <p>Real stories from real people</p>
    </div>

    <div class="testimonials-container">
        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">
                “The produce is so fresh! I love knowing exactly where my vegetables come from.”
            </p>

            <div class="testimonial-user">
                <img src="images/user1.jpg" alt="Sarah Johnson">
                <div>
                    <h4>Sarah Johnson</h4>
                    <span>Home Cook</span>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">
                “Direct connection with farmers means better quality and better prices for my restaurant.”
            </p>

            <div class="testimonial-user">
                <img src="images/user2.jpg" alt="Mike Chen">
                <div>
                    <h4>Mike Chen</h4>
                    <span>Restaurant Owner</span>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">★★★★★</div>
            <p class="testimonial-text">
                “Supporting local farmers while eating organic has never been easier!”
            </p>

            <div class="testimonial-user">
                <img src="images/user3.jpg" alt="Emily Davis">
                <div>
                    <h4>Emily Davis</h4>
                    <span>Health Enthusiast</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-full">
    <div class="cta-section">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of farmers and customers already using FarmToHome</p>

        <div class="cta-buttons">
            <?php if (isset($_SESSION["user_id"])) { ?>
                <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "farmer") { ?>
                    <a href="dashboard.php" class="cta-btn-primary">Go to Dashboard</a>
                <?php } else { ?>
                    <a href="products.php" class="cta-btn-primary">Explore Marketplace</a>
                <?php } ?>
                <a href="logout.php" class="cta-btn-outline">Logout</a>
            <?php } else { ?>
                <a href="registration.php?mode=signup" class="cta-btn-primary">Create Free Account</a>
                <a href="products.php" class="cta-btn-outline">Explore Marketplace</a>
            <?php } ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h3>🌱 FarmToHome</h3>
            <p>Connecting farmers and consumers for a sustainable future.</p>
        </div>

        <div class="footer-column">
            <h4>Quick Links</h4>
            <a href="products.php">Browse</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#benefits">Benefits</a>
        </div>

        <div class="footer-column">
            <h4>For Farmers</h4>
            <?php if (isset($_SESSION["user_id"]) && isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "farmer") { ?>
                <a href="dashboard.php">Dashboard</a>
            <?php } else { ?>
                <a href="registration.php?mode=signup">Sign Up</a>
            <?php } ?>
            <a href="registration.php?mode=login">Login</a>
        </div>

        <div class="footer-column">
            <h4>Legal</h4>
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Contact Us</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2026 FarmToHome. All rights reserved.</p>
    </div>
</footer>

</body>
</html>