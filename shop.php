<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "bruceoilz");

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    header("Location: shop.php?added=1");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products");
?>

<h2>Our Artisanal Oils</h2>
<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div style="border: 1px solid #ddd; padding: 15px; width: 200px;">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p>Price: K<?php echo number_format($row['price'], 2); ?></p>
            <form method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<p><a href="cart.php">View Cart (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a></p>