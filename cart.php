<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle cart updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        $cart_id = $_POST['cart_id'];
        $quantity = $_POST['quantity'];
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $_SESSION['user_id']);
        $stmt->execute();
    }
    
    if (isset($_POST['remove_item'])) {
        $cart_id = $_POST['cart_id'];
        
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
        $stmt->execute();
    }
    
    if (isset($_POST['checkout'])) {
        header("Location: checkout.php");
        exit();
    }
}

// Fetch cart items
$sql = "SELECT c.*, p.name, p.price, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - LuxFurn</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 2rem;
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .cart-nav {
            display: flex;
            gap: 1rem;
        }
        .cart-nav a {
            padding: 0.5rem 1rem;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .cart-nav a:hover {
            background: #34495e;
        }
        .cart-items {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto auto;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .item-details h3 {
            margin: 0;
            color: #2c3e50;
        }
        .item-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .remove-btn {
            padding: 0.5rem 1rem;
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .cart-summary {
            margin-top: 2rem;
            padding: 1rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            margin-top: 1rem;
        }
        .checkout-btn:hover {
            background: #27ae60;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <div class="cart-nav">
                <a href="index.php">Continue Shopping</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="cart-items">
                <?php while($row = $result->fetch_assoc()): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="item-price">₹<?php echo number_format($row['price'], 2); ?></p>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" class="quantity-input">
                            <button type="submit" name="update_cart">Update</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="cart-summary">
                <h2>Order Summary</h2>
                <p>Total: ₹<?php echo number_format($total, 2); ?></p>
                <form method="POST">
                    <button type="submit" name="checkout" class="checkout-btn">Proceed to Checkout</button>
                </form>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart to see them here.</p>
                <a href="index.php" class="cart-nav a">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 