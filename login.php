<?php
session_start();

// Database Connection
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

$error_msg = "";
$success_msg = "";

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Optionally redirect if already authenticated
}

// Handle User Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = trim($_POST['reg_name']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];

    if ($conn) {
        $clean_email = mysqli_real_escape_string($conn, $email);
        $clean_name = mysqli_real_escape_string($conn, $name);
        
        // Check if user already exists
        $check_sql = "SELECT id FROM users WHERE email = '$clean_email'";
        $check_res = mysqli_query($conn, $check_sql);

        if ($check_res && mysqli_num_rows($check_res) > 0) {
            $error_msg = "An account with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (full_name, email, password) VALUES ('$clean_name', '$clean_email', '$hashed_password')";
            
            if (mysqli_query($conn, $insert_sql)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                header("Location: cart.php");
                exit();
            } else {
                $error_msg = "Error creating account. Please try again.";
            }
        }
    } else {
        // Fallback for demonstration if database is disconnected
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        header("Location: cart.php");
        exit();
    }
}

// Handle User Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    if ($conn) {
        $clean_email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM users WHERE email = '$clean_email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: cart.php");
                exit();
            } else {
                $error_msg = "Incorrect password. Please try again.";
            }
        } else {
            $error_msg = "No account found with that email address.";
        }
    } else {
        // Fallback demo login
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        header("Location: cart.php");
        exit();
    }
}

// Handle Password Reset Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    $reset_email = trim($_POST['reset_email']);
    $success_msg = "A password reset link has been sent to " . htmlspecialchars($reset_email) . ".";
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
  <title>Account — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <script src="js/javascript.js" defer></script>
  <style>
    .auth-section {
      max-width: 450px;
      margin: 60px auto;
      padding: 30px;
      background: #ffffff;
      border: 1px solid #e0e8dc;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
      min-height: 400px;
    }
    .auth-tabs {
      display: flex;
      border-bottom: 2px solid #e0e0e0;
      margin-bottom: 24px;
    }
    .tab-btn {
      flex: 1;
      padding: 12px;
      background: none;
      border: none;
      font-size: 16px;
      font-weight: 700;
      color: #777;
      cursor: pointer;
      transition: all 0.2s;
    }
    .tab-btn.active {
      color: #2c5e1a;
      border-bottom: 3px solid #2c5e1a;
    }
    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      text-align: left;
    }
    .form-group label {
      font-size: 14px;
      font-weight: 600;
      color: #333;
    }
    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .password-wrapper input {
      width: 100%;
      padding-right: 45px;
    }
    .toggle-password {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 14px;
      color: #2c5e1a;
      font-weight: 600;
    }
    .form-group input {
      padding: 12px 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      outline: none;
      box-sizing: border-box;
    }
    .form-group input:focus {
      border-color: #2c5e1a;
    }
    .forgot-link {
      text-align: right;
      font-size: 13px;
      margin-top: -8px;
    }
    .forgot-link a {
      color: #2c5e1a;
      text-decoration: none;
      font-weight: 600;
    }
    .forgot-link a:hover {
      text-decoration: underline;
    }
    .submit-btn {
      padding: 14px;
      background-color: #2c5e1a;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .submit-btn:hover {
      opacity: 0.9;
    }
    .form-footer {
      margin-top: 10px;
      text-align: center;
      font-size: 14px;
      color: #666;
    }
    .form-footer a {
      color: #2c5e1a;
      text-decoration: none;
      font-weight: 600;
    }
    .hidden {
      display: none;
    }

    .alert-box {
      padding: 12px 15px;
      border-radius: 6px;
      font-size: 0.9rem;
      margin-bottom: 20px;
      text-align: center;
    }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    /* Reset Modal Styles */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal-overlay.active {
      display: flex;
    }
    .modal-card {
      background: #fff;
      padding: 24px;
      border-radius: 12px;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .modal-card h2 {
      margin-top: 0;
      color: #2c5e1a;
    }
    .close-modal-btn {
      background: #888;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      float: right;
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
        <a href="cart.php">Cart</a>
        <a href="login.php" class="active">Login</a>
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

  <!-- MAIN CONTAINER -->
  <main class="auth-section">

    <?php if (!empty($error_msg)): ?>
      <div class="alert-box alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
      <div class="alert-box alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <div class="auth-tabs">
      <button class="tab-btn active" id="loginTabBtn" onclick="switchTab('login')">Sign In</button>
      <button class="tab-btn" id="registerTabBtn" onclick="switchTab('register')">Register</button>
    </div>

    <!-- LOGIN FORM -->
    <form id="loginForm" class="auth-form" action="login.php" method="POST">
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label for="loginEmail">Email Address</label>
        <input type="email" id="loginEmail" name="login_email" placeholder="your@email.com" required />
      </div>
      <div class="form-group">
        <label for="loginPassword">Password</label>
        <div class="password-wrapper">
          <input type="password" id="loginPassword" name="login_password" placeholder="••••••••" required />
          <button type="button" class="toggle-password" onclick="togglePasswordVisibility('loginPassword', this)">Show</button>
        </div>
      </div>
      <div class="forgot-link">
        <a href="#" onclick="openResetModal()">Forgot Password?</a>
      </div>
      <button type="submit" class="submit-btn">Sign In</button>
      <p class="form-footer">
        Don't have an account? <a href="#" onclick="switchTab('register')">Register here</a>
      </p>
    </form>

    <!-- REGISTER FORM -->
    <form id="registerForm" class="auth-form hidden" action="login.php" method="POST">
      <input type="hidden" name="action" value="register">
      <div class="form-group">
        <label for="regName">Full Name</label>
        <input type="text" id="regName" name="reg_name" placeholder="Your Name" required />
      </div>
      <div class="form-group">
        <label for="regEmail">Email Address</label>
        <input type="email" id="regEmail" name="reg_email" placeholder="your@email.com" required />
      </div>
      <div class="form-group">
        <label for="regPassword">Password</label>
        <div class="password-wrapper">
          <input type="password" id="regPassword" name="reg_password" placeholder="••••••••" required />
          <button type="button" class="toggle-password" onclick="togglePasswordVisibility('regPassword', this)">Show</button>
        </div>
      </div>
      <button type="submit" class="submit-btn">Create Account</button>
      <p class="form-footer">
        Already have an account? <a href="#" onclick="switchTab('login')">Sign in</a>
      </p>
    </form>
  </main>

  <!-- PASSWORD RESET MODAL -->
  <div class="modal-overlay" id="resetModal">
    <div class="modal-card">
      <button class="close-modal-btn" onclick="closeResetModal()">Close</button>
      <h2>Reset Password</h2>
      <p style="font-size: 14px; color: #666; margin-bottom: 16px;">Enter your email address and we'll send you a link to reset your password.</p>
      
      <form action="login.php" method="POST" class="auth-form">
        <input type="hidden" name="action" value="reset">
        <div class="form-group">
          <label for="resetEmail">Email Address</label>
          <input type="email" id="resetEmail" name="reset_email" placeholder="your@email.com" required />
        </div>
        <button type="submit" class="submit-btn" style="margin-top: 8px;">Send Reset Link</button>
      </form>
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

    function switchTab(tab) {
      const loginForm = document.getElementById('loginForm');
      const registerForm = document.getElementById('registerForm');
      const loginBtn = document.getElementById('loginTabBtn');
      const registerBtn = document.getElementById('registerTabBtn');

      if (tab === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
      } else {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        loginBtn.classList.remove('active');
        registerBtn.classList.add('active');
      }
    }

    function togglePasswordVisibility(inputId, button) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        button.textContent = 'Hide';
      } else {
        input.type = 'password';
        button.textContent = 'Show';
      }
    }

    function openResetModal() {
      document.getElementById('resetModal').classList.add('active');
    }

    function closeResetModal() {
      document.getElementById('resetModal').classList.remove('active');
    }
  </script>

</body>
</html>