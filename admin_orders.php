<?php
// Connect to the bruceoilz database
$conn = new mysqli("localhost", "root", "", "bruceoilz");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Order Status Update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['order_status']);
    
    $conn->query("UPDATE orders SET order_status = '$new_status' WHERE id = $order_id");
    header("Location: admin_orders.php");
    exit();
}

// Fetch all orders
$orders_result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders Admin — BruceOilz</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/javascript.js" defer></script>
    <style>
        .page-banner {
            background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
                        url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') center/cover no-repeat;
            color: #fff;
            text-align: center;
            padding: 50px 20px;
        }
        .page-banner h1 {
            font-size: clamp(26px, 5vw, 38px);
            font-weight: 700;
            margin-bottom: 8px;
        }
        .admin-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 50vh;
        }
        .order-card { 
            background: #fff; 
            border-radius: 12px; 
            padding: 25px; 
            margin-bottom: 25px; 
            border: 1px solid #e5e5e5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .order-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 12px; 
            margin-bottom: 15px; 
            flex-wrap: wrap;
            gap: 10px;
        }
        .order-header h3 { margin: 0; color: #2c5e1a; font-size: 1.25rem; }
        
        .badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.85rem; 
            font-weight: bold;
            display: inline-block;
        }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-processing { background-color: #cce5ff; color: #004085; }
        .badge-completed { background-color: #d4edda; color: #155724; }
        .badge-cancelled { background-color: #f8d7da; color: #721c24; }

        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px 20px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            color: #444;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.95rem; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #333; font-weight: bold; }
        
        .status-form { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
            flex-wrap: wrap;
        }
        select, button { 
            padding: 8px 14px; 
            border-radius: 6px; 
            border: 1px solid #ccc; 
            font-size: 0.9rem;
        }
        button { 
            background-color: #2c5e1a; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            transition: background 0.2s;
        }
        button:hover { opacity: 0.9; }

        @media (max-width: 600px) {
            .order-card { padding: 15px; }
            th, td { padding: 8px; }
        }
    </style>
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <a href="index.php" class="logo">
        <img src="image/logo.jpg" alt="BruceOilz Logo" style="height: 40px; vertical-align: middle; margin-right: 8px; border-radius: 4px;">
        BruceOilz Admin
      </a>
      <div class="nav-links">
        <a href="index.php">View Site</a>
        <a href="admin_orders.php" class="active">Orders</a>
        <a href="product.php">Products</a>
        <a href="login.php">Account</a>
      </div>
      <div class="hamburger" id="hamburger" onclick="toggleMenu()">☰</div>
    </nav>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php" onclick="toggleMenu()">View Site</a>
      <a href="admin_orders.php" onclick="toggleMenu()">Orders Dashboard</a>
      <a href="product.php" onclick="toggleMenu()">Products</a>
      <a href="login.php" onclick="toggleMenu()">Account</a>
    </div>
  </header>

  <!-- PAGE BANNER -->
  <div class="page-banner">
    <h1>📦 Customer Orders Dashboard</h1>
    <p>Manage and track incoming store orders</p>
  </div>

  <!-- MAIN ADMIN CONTENT -->
  <div class="admin-container">
    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <?php 
                $status = !empty($order['order_status']) ? $order['order_status'] : 'Pending';
                $badge_class = 'badge-pending';
                if ($status === 'Processing') $badge_class = 'badge-processing';
                if ($status === 'Completed') $badge_class = 'badge-completed';
                if ($status === 'Cancelled') $badge_class = 'badge-cancelled';
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h3>Order #<?php echo $order['id']; ?> — <?php echo htmlspecialchars($order['customer_name']); ?></h3>
                        <small style="color: #666;">Date Placed: <?php echo $order['created_at']; ?></small>
                    </div>
                    <div>
                        <span class="badge <?php echo $badge_class; ?>">Status: <?php echo htmlspecialchars($status); ?></span>
                    </div>
                </div>

                <div class="order-details-grid">
                    <div><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div><strong>Payment Method:</strong> <?php echo strtoupper(htmlspecialchars($order['payment_method'])); ?></div>
                </div>

                <h4>Items Ordered</h4>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $order_id = $order['id'];
                            $items_result = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
                            if ($items_result && $items_result->num_rows > 0):
                                while ($item = $items_result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>K<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>K<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php 
                                endwhile; 
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>

                <h3 style="text-align: right; color: #2c5e1a; margin-top: 15px;">Total Amount: K<?php echo number_format($order['total_amount'], 2); ?></h3>

                <!-- Update Order Status Form -->
                <form method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <label for="order_status"><strong>Update Order Status:</strong></label>
                    <select name="order_status" id="order_status">
                        <option value="Pending" <?php echo ($status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Processing" <?php echo ($status === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                        <option value="Completed" <?php echo ($status === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status">Save Status</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; background: white; padding: 40px; border-radius: 12px; border: 1px solid #eee;">
            <h2>No orders found yet!</h2>
            <p style="color: #666;">When customers submit orders through checkout, they will show up here.</p>
        </div>
    <?php endif; ?>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="footer-logo">BruceOilz Admin</div>
        <p>Internal order management and administration dashboard.</p>
      </div>
      <div class="footer-links">
        <h4>Navigation</h4>
        <a href="index.php">Main Website</a>
        <a href="admin_orders.php">Customer Orders</a>
        <a href="product.php">Product Catalog</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 BruceOilz. All rights reserved. | Admin Dashboard</p>
    </div>
  </footer>

  <script>
    function toggleMenu() {
      document.getElementById('mobileMenu').classList.toggle('open');
    }
  </script>

</body>
</html>