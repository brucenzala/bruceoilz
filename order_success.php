<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed - BruceOilz</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        h2 { color: #2c5e1a; }
        .btn { display: inline-block; background: #2c5e1a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Placed Successfully!</h2>
    <p>Thank you for choosing BruceOilz. Your order has been received and is being processed.</p>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>You can view the status of your order in your <a href="profile.php">account profile</a>.</p>
    <?php endif; ?>
    
    <a href="shop.php" class="btn">Return to Shop</a>
</div>

</body>
</html>