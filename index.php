<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BruceOilz | Artisanal Herbal Oils</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/javascript.js" defer></script>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <div class="navbar">
            <a href="index.php" class="logo">
                <img src="image/logo.jpg" alt="BruceOilz Logo" style="height: 40px; vertical-align: middle; margin-right: 8px; border-radius: 4px;">
                BruceOilz
            </a>
            <nav class="nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="about.php">About</a>
                <a href="product.php">Products</a>
                <a href="contact.php">Contact</a>
                <a href="cart.php">Cart</a>
                <a href="login.php" class="login-link">Login</a>
            </nav>
            <div class="hamburger" id="hamburger">
                &#9776;
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobile-menu">
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="product.php">Products</a>
            <a href="contact.php">Contact</a>
            <a href="cart.php">Cart</a>
            <a href="login.php">Login / Account</a>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-tagline">100% NATURAL & HANDCRAFTED</div>
            <h1>OUR RANGE OF HERBAL OILS</h1>
            <p class="hero-sub">We are the leading provider of artisanal oil extracts from organic herbal plants.</p>
            <a href="product.php" class="btn">Explore Collection</a>
        </div>
    </section>

    <!-- About Strip -->
    <section class="about-strip">
        <div class="about-strip-inner">
            <div class="strip-item">
                🌿
                <strong>100% Organic</strong>
                <span>Pure Botanical Extracts</span>
            </div>
            <div class="strip-item">
                💧
                <strong>Cold Pressed</strong>
                <span>Maximum Potency</span>
            </div>
            <div class="strip-item">
                ✨
                <strong>Handcrafted</strong>
                <span>Made in Small Batches</span>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <div class="about-image">
            🌿
        </div>
        <div class="about-text">
            <div class="section-label">OUR MISSION</div>
            <h2>Nurturing Health Through Natural Extracts</h2>
            <p>At BruceOilz, we focus on carefully extracting the rich wellness properties of medicinal plants to support skin care, joint health, and overall daily vitality.</p>
            <a href="about.php" class="btn-outline">Learn More About Us</a>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products" id="products">
        <div class="section-label">HANDCRAFTED COLLECTION</div>
        <h2>Our Featured Oils</h2>
        <p class="section-sub">Pure botanical formulations crafted for your daily wellness</p>

        <div class="product-container">
            <!-- Product 1: Comfrey Oil -->
            <div class="product-card">
                <div class="product-image">
                    <img src="image/comfrey oil.jpg" alt="Comfrey Oil" onerror="this.style.display='none'">
                </div>
                <div class="product-info">
                    <span class="product-tag">Skin & Joint Care</span>
                    <h3>Comfrey Oil</h3>
                    <p>Natural extract supporting tissue repair, skin soothe, and joint comfort.</p>
                    <div class="product-footer">
                        <a href="product.php" class="btn-outline" style="width: 100%; text-align: center;">View Product Details</a>
                    </div>
                </div>
            </div>

            <!-- Product 2: Clove Oil -->
            <div class="product-card">
                <div class="product-image">
                    <img src="image/clove oil.jpg" alt="Clove Oil" onerror="this.style.display='none'">
                </div>
                <div class="product-info">
                    <span class="product-tag">Relief & Oral Care</span>
                    <h3>Clove Oil</h3>
                    <p>Powerful natural remedy for toothaches, oral discomfort, relaxation, and targeted pain relief.</p>
                    <div class="product-footer">
                        <a href="product.php" class="btn-outline" style="width: 100%; text-align: center;">View Product Details</a>
                    </div>
                </div>
            </div>

            <!-- Product 3: Neem Oil -->
            <div class="product-card">
                <div class="product-image">
                    <img src="image/neem oil.jpg" alt="Neem Oil" onerror="this.style.display='none'">
                </div>
                <div class="product-info">
                    <span class="product-tag">Skin & Scalp</span>
                    <h3>Neem Oil</h3>
                    <p>Potent restorative botanical oil ideal for clear skin and healthy scalp treatment.</p>
                    <div class="product-footer">
                        <a href="product.php" class="btn-outline" style="width: 100%; text-align: center;">View Product Details</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>"The Comfrey Oil has made a noticeable difference for joint comfort after long workdays!"</p>
                <strong>— Satisfied Client</strong>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>"Pure quality products. You can immediately feel the difference of authentic artisanal oils."</p>
                <strong>— Verified Buyer</strong>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <h2>Join Our Community</h2>
        <p>Subscribe to receive tips on natural remedies and special product announcements.</p>
        <form class="newsletter-form" onsubmit="event.preventDefault();">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">BruceOilz</div>
                <p>Artisanal herbal oils extracted from premium natural plants.</p>
            </div>
            <div class="footer-links">
                <h4>Navigation</h4>
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="product.php">Products</a>
                <a href="contact.php">Contact</a>
                <a href="cart.php">Cart</a>
            </div>
            <div class="footer-links">
                <h4>Products</h4>
                <a href="product.php">Comfrey Oil</a>
                <a href="product.php">Clove Oil</a>
                <a href="product.php">Neem Oil</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BruceOilz. All rights reserved.</p>
        </div>
    </footer>

    <!-- Toast Notification Holder -->
    <div class="toast" id="toast">Added to cart!</div>

</body>
</html>