<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order details
$sql = "SELECT o.*, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();

// Fetch order items
$sql = "SELECT oi.*, p.name, p.image_url 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - LuxFurn</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 80px auto 0;
            padding: 2rem;
        }
        .confirmation-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .confirmation-header h1 {
            color: #2ecc71;
            margin-bottom: 1rem;
        }
        .confirmation-content {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .order-info {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        .order-info p {
            margin: 0.5rem 0;
        }
        .order-items {
            margin-bottom: 2rem;
        }
        .order-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .order-item img {
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
        .order-total {
            text-align: right;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .continue-shopping {
            display: block;
            text-align: center;
            padding: 1rem;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 2rem;
        }
        .continue-shopping:hover {
            background: #34495e;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1>Thank You for Your Order!</h1>
            <p>Order #<?php echo $order_id; ?></p>
        </div>

        <div class="confirmation-content">
            <div class="order-info">
                <h2>Order Information</h2>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                <p><strong>Order Status:</strong> <span style="color: #2ecc71;"><?php echo ucfirst($order['status']); ?></span></p>
                <p><strong>Shipping Address:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>

            <div class="order-items">
                <h2>Order Items</h2>
                <?php while($item = $items->fetch_assoc()): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="item-price">
                        ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="order-total">
                <span>Total Amount:</span>
                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>

            <a href="index.php" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
</body>
</html> 