<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Cart — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .cart-section {
      max-width: 800px;
      margin: 60px auto;
      padding: 20px;
    }
    .cart-section h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 16px 0;
      border-bottom: 1px solid #e0e0e0;
    }
    .cart-item-info h3 {
      margin: 0 0 4px;
    }
    .cart-item-info span {
      color: #666;
      font-size: 0.95rem;
    }
    .cart-item-controls {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .qty-btn {
      background: #2c5e1a;
      color: white;
      border: none;
      width: 28px;
      height: 28px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .qty-btn:hover {
      opacity: 0.85;
    }
    .qty-value {
      min-width: 24px;
      text-align: center;
      font-weight: bold;
    }
    .remove-btn {
      background: none;
      border: none;
      color: #b33;
      cursor: pointer;
      font-size: 0.9rem;
      text-decoration: underline;
    }
    .cart-summary {
      margin-top: 30px;
      text-align: right;
      font-size: 1.2rem;
    }
    .cart-empty {
      text-align: center;
      color: #666;
      padding: 60px 0;
    }
    .cart-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    .back-home-btn, .checkout-btn {
      display: inline-block;
      background-color: #2c5e1a;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    .back-home-btn:hover, .checkout-btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <div class="logo">🌿BruceOilz</div>
      <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="about.html">About</a>
        <a href="product.html">Products</a>
        <a href="contact.html">Contact</a>
        <a href="cart.html" class="active">Cart</a>
      </div>
      <div class="hamburger" onclick="toggleMenu()">☰</div>
    </nav>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.html" onclick="toggleMenu()">Home</a>
      <a href="about.html" onclick="toggleMenu()">About</a>
      <a href="product.html" onclick="toggleMenu()">Products</a>
      <a href="contact.html" onclick="toggleMenu()">Contact</a>
      <a href="cart.html" onclick="toggleMenu()">Cart</a>
    </div>
  </header>

  <!-- CART -->
  <section class="cart-section">
    <h1>Your Cart</h1>
    <div id="cartItems"></div>
    <div id="cartSummary" class="cart-summary"></div>
    <div class="cart-actions">
      <a href="index.html" class="back-home-btn">← Continue Shopping</a>
      <button class="checkout-btn" onclick="checkout()">Checkout</button>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="footer-logo">🌿 BruceOilz</div>
        <p>Premium herbal oils crafted with care in Zambia.</p>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <a href="index.html">Home</a>
        <a href="about.html">About</a>
        <a href="product.html">Products</a>
        <a href="contact.html">Contact</a>
      </div>
      <div class="footer-links">
        <h4>Products</h4>
        <a href="product.html">Comfrey Oil</a>
        <a href="product.html">Neem Oil</a>
        <a href="product.html">Clove Oil</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 BruceOilz. All rights reserved. | Made with 💚 in Zambia</p>
    </div>
  </footer>

  <div class="toast" id="toast"></div>

  <script>
    const CART_KEY = 'bruceoilzCart';

    function getCart() {
      try {
        return JSON.parse(localStorage.getItem(CART_KEY)) || [];
      } catch (e) {
        return [];
      }
    }

    function saveCart(cart) {
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
    }

    function renderCart() {
      const cart = getCart();
      const itemsEl = document.getElementById('cartItems');
      const summaryEl = document.getElementById('cartSummary');

      if (cart.length === 0) {
        itemsEl.innerHTML = '<p class="cart-empty">Your cart is empty. Go add some oils! 🌿</p>';
        summaryEl.innerHTML = '';
        return;
      }

      itemsEl.innerHTML = cart.map((item, i) => `
        <div class="cart-item">
          <div class="cart-item-info">
            <h3>${item.name}</h3>
            <span>K${item.price.toFixed(2)} each</span>
          </div>
          <div class="cart-item-controls">
            <button class="qty-btn" onclick="changeQty(${i}, -1)">−</button>
            <span class="qty-value">${item.qty}</span>
            <button class="qty-btn" onclick="changeQty(${i}, 1)">+</button>
            <button class="remove-btn" onclick="removeItem(${i})">Remove</button>
          </div>
        </div>
      `).join('');

      const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
      summaryEl.innerHTML = `<strong>Total: K${total.toFixed(2)}</strong>`;
    }

    function changeQty(index, delta) {
      const cart = getCart();
      cart[index].qty += delta;
      if (cart[index].qty <= 0) {
        cart.splice(index, 1);
      }
      saveCart(cart);
      renderCart();
    }

    function removeItem(index) {
      const cart = getCart();
      cart.splice(index, 1);
      saveCart(cart);
      renderCart();
    }

    function checkout() {
      const cart = getCart();
      if (cart.length === 0) {
        alert('Your cart is empty.');
        return;
      }
      const toast = document.getElementById('toast');
      toast.textContent = '✅ Checkout coming soon!';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function toggleMenu() {
      document.getElementById('mobileMenu').classList.toggle('open');
    }

    renderCart();
  </script>

</body>
</html>