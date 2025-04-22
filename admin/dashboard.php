<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LuxFurn</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 2rem;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .admin-nav {
            display: flex;
            gap: 1rem;
        }
        .admin-nav a {
            padding: 0.5rem 1rem;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .admin-nav a:hover {
            background: #34495e;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .product-table th,
        .product-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .product-table th {
            background: #f5f5f5;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .action-buttons button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-btn {
            background: #3498db;
            color: #fff;
        }
        .delete-btn {
            background: #e74c3c;
            color: #fff;
        }
        .add-product-btn {
            padding: 0.8rem 1.5rem;
            background: #2ecc71;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .add-product-btn:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-nav">
                <a href="add_product.php">Add New Product</a>
                <a href="manage_orders.php">Manage Orders</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <h2>Products</h2>
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td><?php echo substr($row['description'], 0, 100) . '...'; ?></td>
                    <td><img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" style="width: 100px; height: 100px; object-fit: cover;"></td>
                    <td class="action-buttons">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_product" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 