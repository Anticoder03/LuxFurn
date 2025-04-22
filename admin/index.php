<?php
session_start();
include '../config.php';
include '_Nav.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get statistics
$stats = [
    'products' => 0,
    'orders' => 0,
    'users' => 0,
    'messages' => 0
];

// Get total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// Get total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Get total users (excluding admins)
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
$stats['users'] = $result->fetch_assoc()['count'];

// Get total unread messages
$result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
$stats['messages'] = $result->fetch_assoc()['count'];

// Get recent orders
$recent_orders = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");

// Get recent messages
$recent_messages = $conn->query("
    SELECT * FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LuxFurn</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-dashboard {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .admin-header h1 {
            margin: 0;
            color: #333;
        }

        .admin-nav {
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .admin-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 1rem;
        }

        .admin-nav a {
            color: #666;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .admin-nav a:hover {
            background: #f8f9fa;
            color: #e67e22;
        }

        .admin-nav a.active {
            background: #e67e22;
            color: #fff;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-card i {
            font-size: 2rem;
            color: #e67e22;
        }

        .stat-info h3 {
            margin: 0;
            color: #666;
            font-size: 1rem;
        }

        .stat-info p {
            margin: 0.5rem 0 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin: 2rem;
        }

        .content-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .content-card h2 {
            margin: 0 0 1.5rem;
            color: #2c3e50;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .content-card h2 i {
            color: #e67e22;
        }

        .recent-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .recent-item h4 {
            margin: 0;
            color: #2c3e50;
        }

        .recent-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #ffeaa7;
            color: #d35400;
        }

        .status-processing {
            background: #81ecec;
            color: #00b894;
        }

        .status-completed {
            background: #a8e6cf;
            color: #27ae60;
        }

        .status-cancelled {
            background: #fab1a0;
            color: #d63031;
        }

        .view-all {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #e67e22;
            text-decoration: none;
            font-weight: bold;
        }

        .view-all:hover {
            color: #d35400;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-info img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eee;
        }
        .mt-16{
            margin-top: 16px;
        }
    </style>
</head>
<body>
   

       

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-info">
                    <h3>Total Products</h3>
                    <p><?php echo $stats['products']; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?php echo $stats['orders']; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p><?php echo $stats['users']; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <div class="stat-info">
                    <h3>New Messages</h3>
                    <p><?php echo $stats['messages']; ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-card">
                <h2><i class="fas fa-shopping-cart"></i> Recent Orders</h2>
                <?php while($order = $recent_orders->fetch_assoc()): ?>
                    <div class="recent-item">
                        <h4>Order #<?php echo $order['id']; ?> - <?php echo htmlspecialchars($order['username']); ?></h4>
                        <p>
                            Amount: $<?php echo number_format($order['total_amount'], 2); ?>
                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </p>
                        <p>Date: <?php echo date('M j, Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
                <a href="orders.php" class="view-all">View All Orders →</a>
            </div>

            <div class="content-card">
                <h2><i class="fas fa-envelope"></i> Recent Messages</h2>
                <?php while($message = $recent_messages->fetch_assoc()): ?>
                    <div class="recent-item">
                        <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                        <p><?php echo htmlspecialchars(substr($message['message'], 0, 100)) . '...'; ?></p>
                        <p>Date: <?php echo date('M j, Y H:i', strtotime($message['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
                <a href="messages.php" class="view-all">View All Messages →</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html> 