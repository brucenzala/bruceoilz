<?php
// Include the central TiDB database connection configuration
require_once 'db.php';

$db_products = [];

// Query database if connection is established
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    $result = $conn->query("SELECT * FROM products");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Convert 'benefits' string/JSON from DB into a clean PHP array
            if (!empty($row['benefits'])) {
                if (is_string($row['benefits'])) {
                    $decoded = json_decode($row['benefits'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $row['benefits'] = $decoded;
                    } else {
                        $row['benefits'] = array_map('trim', explode(',', $row['benefits']));
                    }
                }
            } else {
                $row['benefits'] = [];
            }

            $db_products[] = $row;
        }
    }
}

// Fallback hardcoded list matching your schema IDs
$default_products = [
    [
        'id' => 1,
        'name' => 'Comfrey Oil',
        'tag' => 'Skin & Hair Care',
        'price' => 120.00,
        'image' => 'image/comfrey oil.jpg',
        'icon' => '🌱',
        'description' => 'Comfrey Oil is one of the best oils for wounds and inflammations. It contains allantoin, a compound that speeds up cell regeneration and promotes healing of damaged skin and tissue.',
        'benefits' => [
            'Antibacterial and anti-inflammatory',
            'Speeds up wound healing',
            'Ideal for skin and hair care',
            'Reduces joint and muscle pain'
        ]
    ],
    [
        'id' => 2,
        'name' => 'Neem Oil',
        'tag' => 'Growth & Focus',
        'price' => 100.00,
        'image' => 'image/neem oil.jpg',
        'icon' => '🍃',
        'description' => 'Neem Oil is a powerful natural remedy derived from the seeds of the neem tree. It has been used for centuries in traditional medicine for its wide range of health and beauty benefits.',
        'benefits' => [
            'Stimulates hair growth',
            'Enhances memory and focus',
            'Natural antifungal properties',
            'Improves scalp health'
        ]
    ],
    [
        'id' => 3,
        'name' => 'Clove Oil',
        'tag' => 'Relief & Dental Care',
        'price' => 100.00,
        'image' => 'image/clove oil.jpg',
        'icon' => '🌸',
        'description' => 'Clove Oil is extracted from organic clove flower buds rich in eugenol. Renowned for its natural anesthetic and antibacterial properties, it provides fast relief for toothaches, gum discomfort, and oral hygiene.',
        'benefits' => [
            'Provides fast relief for toothaches & gum soreness',
            'Natural antibacterial for oral health',
            'Calming and relaxing aroma',
            'Reduces pain and physical inflammation'
        ]
    ]
];

$products_to_display = !empty($db_products) ? $db_products : $default_products;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products — BruceOilz</title>
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
    .products-page {
      padding: 60px 40px;
      max-width: 1100px;
      margin: 0 auto;
    }
    .products-page h2 {
      text-align: center;
      font-size: clamp(22px, 4vw, 32px);
      font-weight: 700;
      color: #111;
      margin-bottom: 8px;
    }
    .products-page .section-sub {
      text-align: center;
      color: #777;
      margin-bottom: 40px;
    }
    .product-detail-card {
      display: flex;
      gap: 40px;
      align-items: center;
      background: #fff;
      border: 1px solid #e5e5e5;
      border-radius: 12px;
      padding: 32px;
      margin-bottom: 28px;
      flex-wrap: wrap;
      transition: box-shadow 0.2s;
    }
    .product-detail-card:hover {
      box-shadow: 0 6px 24px rgba(0,0,0,0.1);
    }
    .product-detail-image {
      flex: 0 0 200px;
      height: 200px;
      background: #f0f7ee;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 72px;
      position: relative;
      overflow: hidden;
    }
    .product-detail-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0; left: 0;
    }
    .product-detail-info {
      flex: 1 1 280px;
    }
    .product-detail-info .product-tag {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      color: #2c5e1a;
      text-transform: uppercase;
    }
    .product-detail-info h3 {
      font-size: 24px;
      font-weight: 700;
      color: #111;
      margin: 8px 0 12px 0;
    }
    .product-detail-info p {
      font-size: 14px;
      color: #555;
      line-height: 1.7;
      margin-bottom: 12px;
    }
    .benefits-list {
      list-style: none;
      padding: 0;
      margin-bottom: 20px;
    }
    .benefits-list li {
      font-size: 14px;
      color: #444;
      padding: 4px 0;
    }
    .benefits-list li::before {
      content: "✓ ";
      color: #2c5e1a;
      font-weight: 700;
    }
    .product-detail-footer {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    .price-large {
      font-size: 24px;
      font-weight: 700;
      color: #2c5e1a;
    }
    .add-btn {
      background-color: #2c5e1a;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 700;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .add-btn:hover {
      background-color: #1e4212;
    }
    @media (max-width: 768px) {
      .products-page { padding: 40px 20px; }
      .product-detail-card { padding: 20px; }
      .product-detail-image { flex: 0 0 100%; height: 180px; }
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
        <a href="about.php">About</a>
        <a href="product.php" class="active">Products</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php">Cart</a>
        <a href="login.php">Login</a>
      </div>
      <div class="hamburger" id="hamburger" onclick="toggleMenu()">☰</div>
    </nav>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php" onclick="toggleMenu()">Home</a>
      <a href="about.php" onclick="toggleMenu()">About</a>
      <a href="product.php" onclick="toggleMenu()">Products</a>
      <a href="contact.php" onclick="toggleMenu()">Contact</a>
      <a href="cart.php" onclick="toggleMenu()">Cart</a>
      <a href="login.php" onclick="toggleMenu()">Login / Account</a>
    </div>
  </header>

  <!-- PAGE BANNER -->
  <div class="page-banner">
    <p class="section-label">OUR RANGE</p>
    <h1>Our Products</h1>
    <p>We are the leading oil extract from herbal infusions treated with the best herbs that deal with most diseases.</p>
  </div>

  <!-- PRODUCTS -->
  <div class="products-page">
    <p class="section-label" style="text-align:center;">HANDCRAFTED COLLECTION</p>
    <h2>All Products</h2>
    <p class="section-sub">100% natural herbal oils for your hair, skin, and wellbeing</p>

    <?php foreach ($products_to_display as $product): ?>
      <div class="product-detail-card product-card">
        <div class="product-detail-image product-image">
          <img src="<?php echo htmlspecialchars(!empty($product['image']) ? $product['image'] : 'image/logo.jpg'); ?>" 
               alt="<?php echo htmlspecialchars($product['name']); ?>" 
               onerror="this.style.display='none'">
          <?php echo htmlspecialchars(!empty($product['icon']) ? $product['icon'] : '🌿'); ?>
        </div>
        <div class="product-detail-info">
          <span class="product-tag"><?php echo htmlspecialchars(!empty($product['tag']) ? $product['tag'] : 'Herbal Care'); ?></span>
          <h3><?php echo htmlspecialchars($product['name']); ?></h3>
          <p><?php echo htmlspecialchars($product['description']); ?></p>
          
          <?php if (!empty($product['benefits']) && is_array($product['benefits'])): ?>
            <ul class="benefits-list">
              <?php foreach ($product['benefits'] as $benefit): ?>
                <li><?php echo htmlspecialchars($benefit); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="product-detail-footer">
            <span class="price-large price">K<?php echo number_format((float)$product['price'], 2); ?></span>
            
            <!-- Submit form via POST directly to cart.php -->
            <form action="cart.php" method="POST" style="margin: 0;">
              <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" class="add-btn">Add to Cart</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>

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
        <a href="login.php">Login</a>
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

  <script>
    function toggleMenu() {
      document.getElementById('mobileMenu').classList.toggle('open');
    }
  </script>

</body>
</html>