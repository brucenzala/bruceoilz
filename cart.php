<?php
session_start();

// Display errors during development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. INITIALIZE CART SESSION
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// 2. DATABASE CONNECTION
// Prefer centralized db.php if available; otherwise fallback to environment/local connection
if (file_exists('db.php')) {
    require_once 'db.php';
} else {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $db   = getenv('DB_NAME') ?: 'bruceoilz';
    $port = getenv('DB_PORT') ?: 3306;

    $conn = @mysqli_connect($host, $user, $pass, $db, (int)$port);
}

// Fallback products array if database lookup fails
$default_products = [
    1 => ['id' => 1, 'name' => 'Comfrey Oil', 'price' => 120.00, 'image' => 'image/comfrey oil.jpg'],
    2 => ['id' => 2, 'name' => 'Neem Oil',    'price' => 100.00, 'image' => 'image/neem oil.jpg'],
    3 => ['id' => 3, 'name' => 'Clove Oil',   'price' => 100.00, 'image' => 'image/clove oil.jpg']
];

// 3. HANDLE ADD TO CART VIA POST FORM (from product.php / product details)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity   = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    $product_info = null;

    if (isset($conn) && $conn) {
        $stmt = mysqli_prepare($conn, "SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $product_info = $row;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Fallback if DB fetch fails or returns no rows
    if (!$product_info && isset($default_products[$product_id])) {
        $product_info = $default_products[$product_id];
    }

    if ($product_info) {
        if (isset($_SESSION['cart'][$product_id]) && is_array($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id'       => (int)$product_info['id'],
                'name'     => $product_info['name'],
                'price'    => (float)$product_info['price'],
                'image'    => !empty($product_info['image']) ? $product_info['image'] : 'image/logo.jpg',
                'quantity' => $quantity
            ];
        }
    }

    header("Location: cart.php");
    exit();
}

// 4. HANDLE CART ACTIONS VIA GET (+, -, remove, clear)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($action === 'clear') {
        $_SESSION['cart'] = array();
    } elseif ($action === 'add' && $id > 0 && isset($_SESSION['cart'][$id])) {
        if (is_array($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = ['id' => $id, 'quantity' => (int)$_SESSION['cart'][$id] + 1];
        }
    } elseif ($action === 'decrease' && $id > 0 && isset($_SESSION['cart'][$id])) {
        if (is_array($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']--;
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        } else {
            $new_qty = (int)$_SESSION['cart'][$id] - 1;
            if ($new_qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $new_qty;
            }
        }
    } elseif ($action === 'remove' && $id > 0) {
        unset($_SESSION['cart'][$id]);
    }

    header("Location: cart.php");
    exit();
}

// 5. PREPARE DISPLAY DATA AND SYNC SESSION FOR CHECKOUT
$cart_items = array();
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $item_data) {
        $pid = (int)$pid;
        $qty = is_array($item_data) ? (isset($item_data['quantity']) ? (int)$item_data['quantity'] : 1) : (int)$item_data;

        $product_info = null;

        if (isset($conn) && $conn) {
            $res = mysqli_query($conn, "SELECT id, name, price, image FROM products WHERE id = $pid LIMIT 1");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $product_info = $row;
            }
        }

        if (!$product_info && isset($default_products[$pid])) {
            $product_info = $default_products[$pid];
        }

        if ($product_info) {
            $subtotal = (float)$product_info['price'] * $qty;
            $total_price += $subtotal;

            $item_struct = [
                'id'       => (int)$product_info['id'],
                'name'     => $product_info['name'],
                'price'    => (float)$product_info['price'],
                'image'    => !empty($product_info['image']) ? $product_info['image'] : 'image/logo.jpg',
                'quantity' => $qty,
                'subtotal' => $subtotal
            ];

            // Normalize session structure
            $_SESSION['cart'][$pid] = [
                'id'       => $item_struct['id'],
                'name'     => $item_struct['name'],
                'price'    => $item_struct['price'],
                'image'    => $item_struct['image'],
                'quantity' => $qty
            ];

            $cart_items[] = $item_struct;
        }
    }
}

if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <script src="js/javascript.js" defer></script>
  <style>
    .page-banner {
      background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                  url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') center/cover no-repeat;
      color: #fff;
      text-align: center;
      padding: 60px 20px;
    }
    .page-banner h1 {
      font-size: clamp(28px, 5vw, 44px);
      font-weight: 700;
      margin-bottom: 8px;
    }
    .cart-wrapper {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
      min-height: 50vh;
    }
    .cart-card {
      background: #fff;
      border: 1px solid #e5e5e5;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .cart-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    .cart-table th, .cart-table td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #eee;
      vertical-align: middle;
    }
    .cart-table th {
      background-color: #f8f9fa;
      color: #333;
      font-weight: 700;
    }
    .product-info-cell {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .product-thumb {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #eee;
    }
    .total-row {
      font-weight: bold;
      font-size: 1.15rem;
      color: #2c5e1a;
    }
    .actions {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }
    .btn {
      padding: 12px 22px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      display: inline-block;
      cursor: pointer;
      border: none;
      font-size: 0.95rem;
      transition: background 0.2s, opacity 0.2s;
    }
    .btn-continue { background-color: #7f8c8d; color: white; }
    .btn-clear { background-color: #e74c3c; color: white; margin-right: 8px; }
    .btn-checkout { background-color: #2c5e1a; color: white; }
    .btn-continue:hover, .btn-clear:hover, .btn-checkout:hover { opacity: 0.9; }

    .qty-controls { display: flex; align-items: center; gap: 8px; }
    .qty-btn { 
      background: #2c5e1a; 
      color: white; 
      text-decoration: none; 
      width: 28px; 
      height: 28px; 
      border-radius: 4px; 
      display: inline-flex; 
      align-items: center; 
      justify-content: center; 
      font-weight: bold; 
    }
    .remove-link { color: #e74c3c; text-decoration: underline; font-size: 0.85rem; margin-left: 10px; }
    
    @media (max-width: 600px) {
      .cart-card { padding: 15px; }
      .cart-table th, .cart-table td { padding: 8px; font-size: 0.9rem; }
      .actions { flex-direction: column; align-items: stretch; }
      .actions div { display: flex; flex-direction: column; gap: 10px; }
      .btn-clear { margin-right: 0; }
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
        <a href="product.php">Products</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php" class="active">Cart</a>
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

  <!-- BANNER -->
  <div class="page-banner">
    <h1>Your Shopping Cart</h1>
    <p>Review your selected herbal products before checkout</p>
  </div>

  <!-- MAIN CART CONTAINER -->
  <div class="cart-wrapper">
    <div class="cart-card">
      <?php if (empty($cart_items)): ?>
          <p style="text-align: center; font-size: 1.1rem; padding: 20px 0;">Your cart is empty. Go add some oils! 🌿</p>
          <div class="actions" style="justify-content: center;">
              <a href="product.php" class="btn btn-continue">← Browse Products</a>
          </div>
      <?php else: ?>
          <table class="cart-table">
              <thead>
                  <tr>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($cart_items as $item): ?>
                      <tr>
                          <td>
                              <div class="product-info-cell">
                                  <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-thumb" onerror="this.src='image/logo.jpg'">
                                  <span><?php echo htmlspecialchars($item['name']); ?></span>
                              </div>
                          </td>
                          <td>K<?php echo number_format($item['price'], 2); ?></td>
                          <td>
                              <div class="qty-controls">
                                  <a href="cart.php?action=decrease&id=<?php echo $item['id']; ?>" class="qty-btn">−</a>
                                  <span><?php echo $item['quantity']; ?></span>
                                  <a href="cart.php?action=add&id=<?php echo $item['id']; ?>" class="qty-btn">+</a>
                                  <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="remove-link">Remove</a>
                              </div>
                          </td>
                          <td>K<?php echo number_format($item['subtotal'], 2); ?></td>
                      </tr>
                  <?php endforeach; ?>
                  <tr class="total-row">
                      <td colspan="3" style="text-align: right;">Total:</td>
                      <td>K<?php echo number_format($total_price, 2); ?></td>
                  </tr>
              </tbody>
          </table>

          <div class="actions">
              <a href="product.php" class="btn btn-continue">← Continue Shopping</a>
              <div>
                  <a href="cart.php?action=clear" class="btn btn-clear">Clear Cart</a>
                  <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
              </div>
          </div>
      <?php endif; ?>
    </div>
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

</body>
</html>