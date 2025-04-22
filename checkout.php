<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch cart items and calculate total
$sql = "SELECT c.*, p.name, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_address = $_POST['shipping_address'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $_SESSION['user_id'], $total, $shipping_address);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        
        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = "An error occurred while processing your order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - LuxFurn</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            grid-column: 1 / -1;
        }
        .checkout-nav {
            display: flex;
            gap: 1rem;
        }
        .checkout-nav a {
            padding: 0.5rem 1rem;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .checkout-nav a:hover {
            background: #34495e;
        }
        .checkout-form {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        .order-summary {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .submit-btn {
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
        .submit-btn:hover {
            background: #27ae60;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <div class="checkout-nav">
                <a href="cart.php">Back to Cart</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="checkout-form">
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <h2>Shipping Information</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="shipping_address">Shipping Address</label>
                    <textarea id="shipping_address" name="shipping_address" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Place Order</button>
            </form>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <div>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="item-price">
                    ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <span>Total:</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </div>
</body>
</html> 