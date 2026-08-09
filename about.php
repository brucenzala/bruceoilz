<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <script src="js/javascript.js" defer></script>
  <style>
    .page-banner {
      background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                  url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') center/cover no-repeat;
      color: #fff;
      text-align: center;
      padding: 80px 20px;
    }
    .page-banner h1 {
      font-size: clamp(28px, 5vw, 48px);
      font-weight: 700;
      margin-bottom: 10px;
    }
    .page-banner p {
      font-size: 16px;
      color: #ccc;
    }
    .about-container {
      max-width: 900px;
      margin: 60px auto;
      padding: 0 20px;
    }
    .about-story {
      text-align: center;
      margin-bottom: 50px;
    }
    .about-story h2 {
      color: #2c5e1a; 
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .about-text {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #444;
      margin-bottom: 30px;
    }
    .values-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 40px;
      text-align: center;
    }
    .value-card {
      background: #f9fbf8;
      border: 1px solid #e0e8dc;
      border-radius: 10px;
      padding: 24px;
    }
    .value-card h3 {
      color: #2c5e1a;
      margin-bottom: 10px;
    }
    .value-card p {
      font-size: 0.95rem;
      color: #555;
      line-height: 1.6;
    }
    .actions-wrapper {
      text-align: center;
      margin-top: 50px;
    }
    .back-home-btn {
      display: inline-block;
      background-color: #2c5e1a;
      color: white;
      padding: 12px 28px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    .back-home-btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <a href="index.php" class="logo">
        <img src="image/logo.jpg" alt="BruceOilz Logo" style="height: 40px; vertical-align: middle; margin-right: 8px; border-radius: 4px;" onerror="this.style.display='none'">
        BruceOilz
      </a>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php" class="active">About</a>
        <a href="product.php">Products</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php">Cart</a>
        <a href="login.php">Login</a>
      </div>
      <div class="hamburger" id="hamburger">☰</div>
    </nav>
    <div class="mobile-menu" id="mobile-menu">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="product.php">Products</a>
      <a href="contact.php">Contact</a>
      <a href="cart.php">Cart</a>
      <a href="login.php">Login / Account</a>
    </div>
  </header>

  <!-- PAGE BANNER -->
  <div class="page-banner">
    <p class="section-label">OUR IDENTITY</p>
    <h1>About BruceOilz</h1>
    <p>Crafting natural wellness through pure herbal extracts.</p>
  </div>

  <!-- ABOUT CONTENT -->
  <main class="about-container">
    <section class="about-story">
      <h2>Our Story</h2>
      <p class="about-text">
        Welcome to <strong>BruceOilz</strong>! We are a small-scale, artisanal venture dedicated to crafting premium herbal oils. 
        Unlike massive industrial production facilities, we focus entirely on small batches, mindful preparation, and high-quality ingredients. 
        Every bottle of our Comfrey, Neem, and Clove oil is handled with care to preserve its natural, powerful benefits for your hair, body, and overall wellness.
      </p>
    </section>

    <div class="values-grid">
      <div class="value-card">
        <h3>🌿 Small Batch</h3>
        <p>Crafted in limited quantities to guarantee maximum freshness, potency, and quality control in every single bottle.</p>
      </div>
      <div class="value-card">
        <h3>🍃 100% Natural</h3>
        <p>Pure herbal infusions free from harmful additives, harsh chemicals, or synthetic fillers.</p>
      </div>
      <div class="value-card">
        <h3>🇿🇲 Locally Handcrafted</h3>
        <p>Proudly formulated and prepared in Zambia, bringing natural traditional wellness to your daily routine.</p>
      </div>
    </div>

    <div class="actions-wrapper">
      <a href="product.php" class="back-home-btn">Explore Our Collection →</a>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="footer-logo">BruceOilz</div>
        <p>Premium herbal oils crafted with care in Zambia.</p>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="product.php">Products</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php">Cart</a>
      </div>
      <div class="footer-links">
        <h4>Products</h4>
        <a href="product.php">Comfrey Oil</a>
        <a href="product.php">Neem Oil</a>
        <a href="product.php">Clove Oil</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 BruceOilz. All rights reserved. | Made with 💚 in Zambia</p>
    </div>
  </footer>

</body>
</html>
