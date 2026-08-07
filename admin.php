<?php
session_start();

// Simple admin authentication (Change password as needed)
$admin_password = "adminpassword123"; 

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Incorrect password.";
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin.php");
    exit();
}

$conn = @mysqli_connect("localhost", "root", "", "bruceoilz");

$success_msg = "";
$error_msg = "";

// Handle Delete Product
if (isset($_GET['delete_product']) && isset($_SESSION['admin_logged_in']) && $conn) {
    $del_id = (int)$_GET['delete_product'];
    $del_stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    if ($del_stmt) {
        mysqli_stmt_bind_param($del_stmt, "i", $del_id);
        mysqli_stmt_execute($del_stmt);
        mysqli_stmt_close($del_stmt);
    }
    header("Location: admin.php");
    exit();
}

// Handle Add or Edit Product
if (isset($_POST['save_product']) && isset($_SESSION['admin_logged_in']) && $conn) {
    $product_id  = (int)($_POST['product_id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $tag         = trim($_POST['tag'] ?? 'Herbal Care');
    $price       = (float)($_POST['price'] ?? 0);
    $icon        = trim($_POST['icon'] ?? '🌿');
    $description = trim($_POST['description'] ?? '');
    $benefits    = trim($_POST['benefits'] ?? '');
    
    // Keep existing image by default
    $image_path = trim($_POST['current_image'] ?? 'image/logo.jpg');

    // Handle New Image Upload if provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['product_image']['tmp_name'];
        $file_name = basename($_FILES['product_image']['name']);
        $target_dir = "image/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name);
        
        if (move_uploaded_file($file_tmp, $target_file)) {
            $image_path = $target_file;
        }
    }

    if (!empty($name) && $price > 0) {
        if ($product_id > 0) {
            // Update Existing Product
            $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, tag = ?, price = ?, image = ?, icon = ?, description = ?, benefits = ? WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssdssssi", $name, $tag, $price, $image_path, $icon, $description, $benefits, $product_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Product successfully updated!";
                } else {
                    $error_msg = "Failed to update product.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Insert New Product
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, tag, price, image, icon, description, benefits) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssdssss", $name, $tag, $price, $image_path, $icon, $description, $benefits);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Product successfully added!";
                } else {
                    $error_msg = "Failed to add product to database.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $error_msg = "Please provide a valid product name and price.";
    }
}

// Handle Status Update for Orders
if (isset($_POST['update_status']) && isset($_SESSION['admin_logged_in']) && $conn) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    if ($update_stmt) {
        mysqli_stmt_bind_param($update_stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
    }
    header("Location: admin.php");
    exit();
}

// Fetch Orders & Products if logged in
$orders = [];
$products = [];
$stats = [
    'total_revenue' => 0,
    'total_orders' => 0,
    'completed_orders' => 0,
    'pending_orders' => 0
];

if (isset($_SESSION['admin_logged_in']) && $conn) {
    // Fetch Orders
    $result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $oid = $row['id'];
            $items_result = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $oid");
            $row['items'] = [];
            if ($items_result) {
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $row['items'][] = $item;
                }
            }
            $orders[] = $row;

            // Compute Stats
            $stats['total_orders']++;
            if ($row['status'] === 'Completed' || $row['status'] === 'Processing') {
                $stats['total_revenue'] += (float)$row['total_amount'];
            }
            if ($row['status'] === 'Completed') {
                $stats['completed_orders']++;
            }
            if ($row['status'] === 'Pending') {
                $stats['pending_orders']++;
            }
        }
    }

    // Fetch Products
    $prod_result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
    if ($prod_result) {
        while ($p = mysqli_fetch_assoc($prod_result)) {
            $products[] = $p;
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
  <title>Admin Dashboard — BruceOilz</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { background-color: #f8f9fa; font-family: Arial, sans-serif; margin: 0; padding: 0; }
    .admin-header { background: #2c5e1a; color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
    .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
    .login-card { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
    .login-card input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    .login-card button { width: 100%; background: #2c5e1a; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
    
    /* Analytics Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .stats-grid { grid-template-columns: 1fr; } }
    .stat-card { background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .stat-card h3 { margin: 0 0 5px 0; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-card .stat-value { font-size: 1.6rem; font-weight: bold; color: #2c5e1a; margin: 0; }

    .section-box { background: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; font-size: 0.9rem; }
    .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 0.9rem; }
    
    .btn-primary { background: #2c5e1a; color: white; padding: 12px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
    .btn-primary:hover { background: #224a14; }
    .btn-secondary { background: #6c757d; color: white; padding: 12px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-danger { background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: bold; }
    .btn-edit { background: #ffc107; color: #333; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: bold; display: inline-block; cursor: pointer; border: none;}

    .order-card { background: white; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); overflow: hidden; }
    .order-header-bar { background: #f1f3f5; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; flex-wrap: wrap; gap: 10px; }
    .order-body { padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .order-body { grid-template-columns: 1fr; } }
    
    .badge { padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; }
    .badge-Pending { background: #fff3cd; color: #856404; }
    .badge-Processing { background: #cce5ff; color: #004085; }
    .badge-Completed { background: #d4edda; color: #155724; }
    .badge-Cancelled { background: #f8d7da; color: #721c24; }

    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th, .items-table td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9rem; }
    .items-table th { background: #fafafa; color: #555; }
    
    .btn-logout { background: #dc3545; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.9rem; font-weight: bold; }
    .status-form select { padding: 6px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9rem; }
    .status-form button { padding: 6px 12px; background: #2c5e1a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    
    .alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    .alert-error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
  </style>
  <script>
    function editProduct(prod) {
      document.getElementById('product_id').value = prod.id;
      document.getElementById('name').value = prod.name;
      document.getElementById('tag').value = prod.tag;
      document.getElementById('price').value = prod.price;
      document.getElementById('icon').value = prod.icon;
      document.getElementById('description').value = prod.description;
      document.getElementById('benefits').value = prod.benefits;
      document.getElementById('current_image').value = prod.image;
      document.getElementById('form-title').innerText = "✏️ Edit Product #" + prod.id;
      document.getElementById('submit-btn').innerText = "Update Product";
      document.getElementById('cancel-edit-btn').style.display = "inline-block";
      window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function resetForm() {
      document.getElementById('product_id').value = "";
      document.getElementById('productForm').reset();
      document.getElementById('current_image').value = "image/logo.jpg";
      document.getElementById('form-title').innerText = "🌿 Add New Product";
      document.getElementById('submit-btn').innerText = "Upload Product";
      document.getElementById('cancel-edit-btn').style.display = "none";
    }
  </script>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
  <!-- LOGIN SCREEN -->
  <div class="login-card">
    <h2 style="color: #2c5e1a; margin-bottom: 10px;">BruceOilz Admin</h2>
    <p style="color: #666; font-size: 0.9rem;">Enter your management password</p>
    <?php if (isset($login_error)): ?>
      <p style="color: #dc3545; font-size: 0.85rem; margin-top: 10px;"><?php echo $login_error; ?></p>
    <?php endif; ?>
    <form action="admin.php" method="POST">
      <input type="password" name="password" placeholder="Password" required autofocus>
      <button type="submit" name="login">Log In</button>
    </form>
    <div style="margin-top: 20px;">
      <a href="index.php" style="color: #666; font-size: 0.85rem; text-decoration: underline;">← Back to Storefront</a>
    </div>
  </div>

<?php else: ?>
  <!-- DASHBOARD -->
  <div class="admin-header">
    <div>
      <h1 style="margin: 0; font-size: 1.5rem;">BruceOilz Management Console</h1>
      <span style="font-size: 0.85rem; opacity: 0.8;">Signed in as Administrator</span>
    </div>
    <div>
      <a href="index.php" target="_blank" style="color: white; margin-right: 20px; text-decoration: underline; font-size: 0.9rem;">View Website</a>
      <a href="admin.php?logout=true" class="btn-logout">Log Out</a>
    </div>
  </div>

  <div class="admin-container">

    <!-- SALES ANALYTICS BANNER -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Revenue</h3>
        <p class="stat-value">K<?php echo number_format($stats['total_revenue'], 2); ?></p>
      </div>
      <div class="stat-card">
        <h3>Total Orders</h3>
        <p class="stat-value"><?php echo $stats['total_orders']; ?></p>
      </div>
      <div class="stat-card">
        <h3>Completed Orders</h3>
        <p class="stat-value"><?php echo $stats['completed_orders']; ?></p>
      </div>
      <div class="stat-card">
        <h3>Pending Orders</h3>
        <p class="stat-value" style="color: #d97706;"><?php echo $stats['pending_orders']; ?></p>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <!-- ADD / EDIT PRODUCT SECTION -->
    <div class="section-box">
      <h2 id="form-title" style="margin-top: 0; color: #2c5e1a; margin-bottom: 20px;">🌿 Add New Product</h2>
      <form id="productForm" action="admin.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="product_id" name="product_id" value="">
        <input type="hidden" id="current_image" name="current_image" value="image/logo.jpg">
        
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" required placeholder="e.g., Moringa Oil">
          </div>
          <div class="form-group">
            <label for="tag">Category / Tag *</label>
            <input type="text" id="tag" name="tag" required value="Herbal Care" placeholder="e.g., Skin & Hair Care">
          </div>
          <div class="form-group">
            <label for="price">Price (K) *</label>
            <input type="number" step="0.01" id="price" name="price" required placeholder="120.00">
          </div>
          <div class="form-group">
            <label for="icon">Emoji Icon</label>
            <input type="text" id="icon" name="icon" value="🌿" placeholder="🌿">
          </div>
          <div class="form-group" style="grid-column: span 2;">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="2" placeholder="Brief description of the product and its origin..."></textarea>
          </div>
          <div class="form-group" style="grid-column: span 2;">
            <label for="benefits">Benefits (Separate with commas)</label>
            <input type="text" id="benefits" name="benefits" placeholder="Natural anti-inflammatory, Promotes hair growth, Skin healing">
          </div>
          <div class="form-group" style="grid-column: span 2;">
            <label for="product_image">Product Image File (Leave blank to keep current image)</label>
            <input type="file" id="product_image" name="product_image" accept="image/*">
          </div>
        </div>
        <div style="display: flex; gap: 10px;">
          <button type="submit" name="save_product" id="submit-btn" class="btn-primary">Upload Product</button>
          <button type="button" id="cancel-edit-btn" class="btn-secondary" style="display: none;" onclick="resetForm()">Cancel Edit</button>
        </div>
      </form>
    </div>

    <!-- EXISTING PRODUCTS LIST -->
    <div class="section-box">
      <h2 style="margin-top: 0; color: #2c5e1a; margin-bottom: 15px;">📦 Store Catalog (<?php echo count($products); ?> Products)</h2>
      <div style="display: flex; flex-wrap: wrap; gap: 15px;">
        <?php foreach ($products as $prod): ?>
          <div style="background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 12px; width: 220px; display: flex; flex-direction: column; align-items: center; text-align: center; justify-content: space-between;">
            <div>
              <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;" onerror="this.src='image/logo.jpg'">
              <strong style="font-size: 0.95rem; display: block;"><?php echo htmlspecialchars($prod['name']); ?></strong>
              <span style="color: #2c5e1a; font-weight: bold; margin: 4px 0; display: block;">K<?php echo number_format($prod['price'], 2); ?></span>
              <span style="font-size: 0.8rem; color: #666; display: block; margin-bottom: 10px;"><?php echo htmlspecialchars($prod['tag']); ?></span>
            </div>
            <div style="display: flex; gap: 8px; width: 100%; justify-content: center;">
              <button type="button" class="btn-edit" onclick='editProduct(<?php echo json_encode($prod); ?>)'>Edit</button>
              <a href="admin.php?delete_product=<?php echo $prod['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- CUSTOMER ORDERS SECTION -->
    <h2 style="margin-bottom: 20px; color: #333;">Customer Orders (<?php echo count($orders); ?>)</h2>

    <?php if (empty($orders)): ?>
      <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; border: 1px solid #e0e0e0;">
        <p style="color: #666; font-size: 1.1rem;">No orders have been placed yet.</p>
      </div>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <div class="order-header-bar">
            <div>
              <strong>Order #<?php echo $order['id']; ?></strong> &nbsp;|&nbsp; 
              <span style="color: #555;"><?php echo date('M d, Y - H:i', strtotime($order['created_at'])); ?></span>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
              <span class="badge badge-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
              <form action="admin.php" method="POST" class="status-form" style="display: flex; gap: 5px;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="status">
                  <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                  <option value="Processing" <?php if($order['status']=='Processing') echo 'selected'; ?>>Processing</option>
                  <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
                  <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status">Update</button>
              </form>
            </div>
          </div>

          <div class="order-body">
            <div>
              <h4 style="margin-top: 0; color: #2c5e1a; margin-bottom: 10px;">Customer Details</h4>
              <p style="margin: 4px 0;"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
              <p style="margin: 4px 0;"><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>"><?php echo htmlspecialchars($order['phone']); ?></a></p>
              <p style="margin: 4px 0;"><strong>Email:</strong> <?php echo !empty($order['email']) ? htmlspecialchars($order['email']) : 'N/A'; ?></p>
              <p style="margin: 4px 0;"><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?></p>
              <p style="margin: 4px 0;"><strong>Payment Option:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'WhatsApp'); ?></p>
            </div>

            <div>
              <h4 style="margin-top: 0; color: #2c5e1a; margin-bottom: 10px;">Ordered Items</h4>
              <table class="items-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($order['items'] as $item): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                      <td><?php echo $item['quantity']; ?></td>
                      <td>K<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <div style="text-align: right; margin-top: 10px; font-weight: bold; font-size: 1.05rem; color: #2c5e1a;">
                Total: K<?php echo number_format($order['total_amount'], 2); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

</body>
</html>