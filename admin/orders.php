<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

// Get all orders with user information
$result = $conn->query("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - LuxFurn Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-orders {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .orders-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .order-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .order-header {
            padding: 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-info h3 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
        }

        .order-info p {
            margin: 0.25rem 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .order-status.pending {
            background: #f39c12;
            color: #fff;
        }

        .order-status.processing {
            background: #3498db;
            color: #fff;
        }

        .order-status.shipped {
            background: #27ae60;
            color: #fff;
        }

        .order-status.delivered {
            background: #2ecc71;
            color: #fff;
        }

        .order-status.cancelled {
            background: #e74c3c;
            color: #fff;
        }

        .order-details {
            padding: 1.5rem;
        }

        .order-items {
            margin-bottom: 1.5rem;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .item-info {
            flex-grow: 1;
        }

        .item-info h4 {
            margin: 0;
            color: #333;
        }

        .item-info p {
            margin: 0.25rem 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .order-total {
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        .order-actions {
            padding: 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .update-btn {
            padding: 0.5rem 1rem;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .update-btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="admin-orders">
        <header class="admin-header">
            <h1>Manage Orders</h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/32" alt="Admin">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" style="margin-left: 1rem;">Logout</a>
            </div>
        </header>

       <?php include '_Nav.php'; ?>

        <div class="orders-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($order = $result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p>
                                    By <?php echo htmlspecialchars($order['username']); ?> 
                                    (<?php echo htmlspecialchars($order['email']); ?>)
                                </p>
                                <p>
                                    Date: <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            <span class="order-status <?php echo strtolower($order['status']); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>

                        <div class="order-details">
                            <div class="order-items">
                                <?php
                                $stmt = $conn->prepare("
                                    SELECT oi.*, p.name, p.image_url, p.price 
                                    FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = ?
                                ");
                                $stmt->bind_param("i", $order['id']);
                                $stmt->execute();
                                $items = $stmt->get_result();
                                ?>

                                <?php while($item = $items->fetch_assoc()): ?>
                                    <div class="order-item">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="item-image">
                                        <div class="item-info">
                                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p>
                                                Quantity: <?php echo $item['quantity']; ?> × 
                                                ₹<?php echo number_format($item['price'], 2); ?>
                                            </p>
                                        </div>
                                        <div class="item-total">
                                        ₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="order-total">
                                Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?>
                            </div>
                        </div>

                        <div class="order-actions">
                            <form method="POST" style="display: flex; gap: 1rem;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>
                                        Pending
                                    </option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>
                                        Processing
                                    </option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>
                                        Shipped
                                    </option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>
                                        Delivered
                                    </option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>
                                        Cancelled
                                    </option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>No orders found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 