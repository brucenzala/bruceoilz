<?php
session_start();

// Display errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to products page if cart is empty and no order post request
if (empty($_SESSION['cart']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: product.php");
    exit();
}

$conn = @mysqli_connect("localhost", "root", "", "bruceoilz");

// Compute totals from session cart
$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;

foreach ($cart_items as $item) {
    $qty = is_array($item) ? ($item['quantity'] ?? 1) : 1;
    $price = is_array($item) ? ($item['price'] ?? 0) : 0;
    $total_price += ($price * $qty);
}

$order_success = false;
$order_id = null;
$error_msg = "";
$whatsapp_url = "";

// Your verified WhatsApp business number
$whatsapp_business_number = "260777392580";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name  = trim($_POST['full_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $city           = trim($_POST['city'] ?? 'Lusaka');
    $payment_method = trim($_POST['payment_method'] ?? 'WhatsApp / Mobile Money');

    if (empty($customer_name) || empty($phone) || empty($address)) {
        $error_msg = "Please fill in all required fields (Name, Phone, and Address).";
    } else {
        if ($conn) {
            // 1. Insert into orders table (including payment_method)
            $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_name, email, phone, address, city, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssd", $customer_name, $email, $phone, $address, $city, $payment_method, $total_price);
                if (mysqli_stmt_execute($stmt)) {
                    $order_id = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt);

                    // Build item summary text for WhatsApp
                    $wa_items_text = "";

                    // 2. Insert order items
                    $item_stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($item_stmt) {
                        foreach ($cart_items as $pid => $item) {
                            $p_id    = (int)($item['id'] ?? $pid);
                            $p_name  = $item['name'] ?? 'Herbal Oil';
                            $p_price = (float)($item['price'] ?? 0);
                            $p_qty   = (int)($item['quantity'] ?? 1);
                            $sub     = $p_price * $p_qty;

                            $wa_items_text .= "• " . $p_name . " x" . $p_qty . " (K" . number_format($sub, 2) . ")\n";

                            mysqli_stmt_bind_param($item_stmt, "iisdid", $order_id, $p_id, $p_name, $p_price, $p_qty, $sub);
                            mysqli_stmt_execute($item_stmt);
                        }
                        mysqli_stmt_close($item_stmt);
                    }

                    // 3. Build pre-formatted WhatsApp Message
                    $wa_message  = "🌿 *NEW ORDER #{$order_id} - BruceOilz*\n\n";
                    $wa_message .= "*Customer:* {$customer_name}\n";
                    $wa_message .= "*Phone:* {$phone}\n";
                    $wa_message .= "*Delivery Location:* {$address}, {$city}\n";
                    $wa_message .= "*Payment Option:* {$payment_method}\n\n";
                    $wa_message .= "*Ordered Items:*\n" . $wa_items_text . "\n";
                    $wa_message .= "*Total Amount:* K" . number_format($total_price, 2) . "\n\n";
                    $wa_message .= "Please process my order. Thank you!";

                    $whatsapp_url = "https://wa.me/" . $whatsapp_business_number . "?text=" . urlencode($wa_message);

                    // 4. Clear cart and set success flag
                    unset($_SESSION['cart']);
                    $order_success = true;
                } else {
                    $error_msg = "Database error: Could not save order. Ensure your 'orders' table has all required columns.";
                }
            } else {
                $error_msg = "Failed to prepare order query.";
            }
        } else {
            $error_msg = "Database connection failed.";
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <script src="js/javascript.js" defer></script>
  <style>
    .page-banner {
      background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                  url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') center/cover no-repeat;
      color: #fff;
      text-align: center;
      padding: 50px 20px;
    }
    .page-banner h1 { font-size: clamp(26px, 4vw, 40px); font-weight: 700; margin-bottom: 8px; }
    .checkout-wrapper { max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 50vh; }
    .grid-layout { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
    .checkout-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2c5e1a; outline: none; }
    .order-summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .order-summary-table td { padding: 10px 0; border-bottom: 1px solid #eee; font-size: 0.95rem; }
    .summary-total { font-weight: bold; font-size: 1.2rem; color: #2c5e1a; display: flex; justify-content: space-between; padding-top: 15px; border-top: 2px solid #2c5e1a; }
    
    .btn-place-order {
      width: 100%; background-color: #2c5e1a; color: white; padding: 14px; border: none; border-radius: 6px;
      font-weight: bold; font-size: 1rem; cursor: pointer; transition: background 0.2s;
    }
    .btn-place-order:hover { background-color: #224a14; }
    
    .btn-whatsapp {
      display: inline-flex; align-items: center; justify-content: center; gap: 10px;
      background-color: #25D366; color: white; padding: 14px 28px; border-radius: 6px;
      font-weight: bold; font-size: 1.05rem; text-decoration: none; margin-top: 15px;
      transition: background 0.2s; box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
    }
    .btn-whatsapp:hover { background-color: #1ebc57; }

    .error-box { background: #fce4e4; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    .success-card { text-align: center; padding: 40px 20px; }
    .success-icon { font-size: 50px; color: #2c5e1a; margin-bottom: 15px; }

    @media (max-width: 800px) { .grid-layout { grid-template-columns: 1fr; } }
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

  <!-- BANNER -->
  <div class="page-banner">
    <h1>Checkout</h1>
    <p>Provide your delivery and payment details</p>
  </div>

  <div class="checkout-wrapper">
    <?php if ($order_success): ?>
      <div class="checkout-card success-card">
        <div class="success-icon">🌿</div>
        <h2 style="color: #2c5e1a;">Thank You for Your Order!</h2>
        <p style="margin: 15px 0; font-size: 1.1rem;">
          Your order <strong>#<?php echo $order_id; ?></strong> has been saved successfully.
        </p>
        <p style="color: #555; margin-bottom: 20px;">
          Click the button below to send your order summary directly to our WhatsApp for quick processing!
        </p>

        <!-- WHATSAPP DIRECT ACTION BUTTON -->
        <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn-whatsapp">
          📲 Send Order to WhatsApp
        </a>

        <div style="margin-top: 30px;">
          <a href="product.php" style="color: #2c5e1a; font-weight: bold; text-decoration: underline;">Return to Products</a>
        </div>
      </div>
    <?php else: ?>

      <?php if (!empty($error_msg)): ?>
        <div class="error-box"><?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="checkout.php" method="POST" class="grid-layout">
        <!-- Delivery Details -->
        <div class="checkout-card">
          <h2 style="margin-bottom: 20px; color: #2c5e1a;">Shipping & Contact Information</h2>
          
          <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" required placeholder="e.g., Bruce Nzala">
          </div>

          <div class="form-group">
            <label for="phone">Phone Number (WhatsApp or Call) *</label>
            <input type="tel" id="phone" name="phone" required placeholder="e.g., 0777392580">
          </div>

          <div class="form-group">
            <label for="email">Email Address (Optional)</label>
            <input type="email" id="email" name="email" placeholder="e.g., example@gmail.com">
          </div>

          <div class="form-group">
            <label for="address">Delivery Address / Location *</label>
            <textarea id="address" name="address" rows="3" required placeholder="Enter street address or area details"></textarea>
          </div>

          <div class="form-group">
            <label for="city">City / Area</label>
            <input type="text" id="city" name="city" value="Lusaka">
          </div>

          <div class="form-group">
            <label for="payment_method">Preferred Payment / Order Method *</label>
            <select id="payment_method" name="payment_method" required>
              <option value="WhatsApp / Direct Chat">WhatsApp Order Confirmation</option>
              <option value="Airtel Money">Airtel Money</option>
              <option value="MTN Mobile Money">MTN Mobile Money</option>
              <option value="Zamtel Kwacha">Zamtel Kwacha</option>
              <option value="Cash on Delivery">Cash on Delivery</option>
            </select>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="checkout-card" style="height: fit-content;">
          <h3 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Order Summary</h3>
          <table class="order-summary-table">
            <tbody>
              <?php foreach ($cart_items as $item): 
                $name  = $item['name'] ?? 'Herbal Oil';
                $price = (float)($item['price'] ?? 0);
                $qty   = (int)($item['quantity'] ?? 1);
                $sub   = $price * $qty;
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($name); ?> × <?php echo $qty; ?></td>
                  <td style="text-align: right;">K<?php echo number_format($sub, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="summary-total">
            <span>Total Payable:</span>
            <span>K<?php echo number_format($total_price, 2); ?></span>
          </div>

          <button type="submit" name="place_order" class="btn-place-order" style="margin-top: 25px;">Place Order</button>
        </div>
      </form>
    <?php endif; ?>
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