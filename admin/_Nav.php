<?php
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="admin-navbar">
    <h2>Admin Panel</h2>
    <div class="admin-navbar-links">
        <a href="index.php" <?php echo $current_page === 'index.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="products.php" <?php echo $current_page === 'products.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-box"></i> Products
        </a>
        <a href="orders.php" <?php echo $current_page === 'orders.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="users.php" <?php echo $current_page === 'users.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-users"></i> Users
        </a>
        <a href="messages.php" <?php echo $current_page === 'messages.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-envelope"></i> Messages
        </a>
    </div>
    <div class="admin-navbar-user">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<style>
.admin-navbar {
    background: #2c3e50;
    padding: 1rem 2rem;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-navbar h2 {
    margin: 0;
    color: white;
    font-size: 1.5rem;
}

.admin-navbar-links {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.admin-navbar-links a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-navbar-links a:hover {
    background: #34495e;
}

.admin-navbar-links a.active {
    background: #e67e22;
}

.admin-navbar-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.logout-btn {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logout-btn:hover {
    background: #c0392b;
}

/* Adjust main content for navbar */
body {
    padding-top: 70px;
    background: #f5f6fa;
}
</style> 