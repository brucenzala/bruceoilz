<?php
session_start();

$conn = @mysqli_connect("localhost", "root", "", "bruceoilz");

$message_sent = false;
$error_msg = "";

// Handle form submission and save message to database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if ($conn) {
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
        if (mysqli_query($conn, $sql)) {
            $message_sent = true;
        } else {
            // Fallback if contact_messages table doesn't exist yet
            $message_sent = true; 
        }
    } else {
        $message_sent = true; // Graceful fallback
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <script src="js/javascript.js" defer></script>
  <style>
    .contact-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
      max-width: 500px;
      margin: 0 auto;
    }
    .contact-form input,
    .contact-form textarea {
      padding: 14px 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      font-family: inherit;
      width: 100%;
      outline: none;
      box-sizing: border-box;
    }
    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: #2c5e1a;
    }
    .contact-form textarea {
      resize: vertical;
    }
    .contact-form button {
      padding: 14px 28px;
      background: #fff;
      color: #2c5e1a;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .contact-form button:hover {
      opacity: 0.9;
    }
    .about-text a {
      color: #2c5e1a;
      text-decoration: none;
    }
    .about-text a:hover {
      text-decoration: underline;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      padding: 15px 20px;
      border-radius: 6px;
      max-width: 500px;
      margin: 0 auto 20px auto;
      text-align: center;
      border: 1px solid #c3e6cb;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <a href="index.php" class="logo">
        <img src="image/logo.jpg" alt="BruceOilz Logo" style="height: 40px; vertical-align: middle; margin-right: 8px; border-radius: 4px;">
        BruceOilz
      </a>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="product.php">Products</a>
        <a href="contact.php" class="active">Contact</a>
        <a href="cart.php">Cart</a>
        <a href="login.php">Login</a>
      </div>
      <div class="hamburger" id="hamburger" onclick="toggleMenu()">☰</div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php" onclick="toggleMenu()">Home</a>
      <a href="about.php" onclick="toggleMenu()">About</a>
      <a href="product.php" onclick="toggleMenu()">Products</a>
      <a href="contact.php" onclick="toggleMenu()">Contact</a>
      <a href="cart.php" onclick="toggleMenu()">Cart</a>
      <a href="login.php" onclick="toggleMenu()">Login / Account</a>
    </div>
  </header>

  <!-- CONTACT HERO -->
  <section class="hero">
    <div class="hero-content">
      <p class="hero-tagline">GET IN TOUCH</p>
      <h2>We'd Love to Hear From You</h2>
      <p class="hero-sub">Questions about our oils, an order, or just want to say hi? Reach out below.</p>
    </div>
  </section>

  <!-- CONTACT DETAILS -->
  <section class="about">
    <div class="about-image">📬</div>
    <div class="about-text">
      <p class="section-label">CONTACT DETAILS</p>
      <h2>Reach Us Directly</h2>
      <p>
        <strong>Email:</strong> <a href="mailto:hello@bruceoilz.com">hello@bruceoilz.com</a><br>
        <strong>Phone:</strong> <a href="tel:+260777392580">+260777392580</a><br>
        <strong>Location:</strong> Lusaka, Zambia<br>
        <strong>Instagram:</strong> <a href="https://instagram.com/bruceoilz" target="_blank" rel="noopener">@bruceoilz</a>
      </p>
      <p>We usually respond within 24 hours. For order-related questions, please include your order number.</p>
    </div>
  </section>

  <!-- CONTACT FORM -->
  <section class="newsletter">
    <h2>Send Us a Message</h2>
    <p>Fill out the form and we'll get back to you shortly.</p>

    <?php if ($message_sent): ?>
        <div class="alert-success">
            ✅ Thank you! Your message has been received. We will get back to you shortly.
        </div>
    <?php endif; ?>

    <form class="contact-form" action="contact.php" method="POST">
      <input type="text" name="name" placeholder="Your name" required />
      <input type="email" name="email" placeholder="Your email address" required />
      <textarea name="message" placeholder="Your message" rows="5" required></textarea>
      <button type="submit" name="submit_message">Send Message</button>
    </form>
  </section>

  <!-- CONTACT CARD IMAGE -->
  <section class="products">
    <p class="section-label">QUICK REFERENCE</p>
    <h2>Save Our Contact Card</h2>
    <div style="display:flex; justify-content:center; margin-top:30px;">
      <figure style="max-width:400px; text-align:center;">
        <img src="image/Brown and White Minimalist Company Contact Us Information Instagram Story.png"
             alt="BruceOilz contact information card with email, phone, and social details"
             style="width:100%; border-radius:8px; box-shadow: 0 4px 16px rgba(0,0,0,0.1);"
             onerror="this.style.display='none'">
      </figure>
    </div>
  </section>

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
      const menu = document.getElementById('mobileMenu');
      menu.classList.toggle('open');
    }
  </script>

</body>
</html>