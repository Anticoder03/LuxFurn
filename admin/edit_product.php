<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];
    
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $image_url, $product_id);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to update product. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - LuxFurn Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 800px;
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
        .product-form {
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
        .submit-btn {
            padding: 1rem 2rem;
            background: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .submit-btn:hover {
            background: #27ae60;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        .preview-image {
            max-width: 200px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Edit Product</h1>
            <div class="admin-nav">
                <a href="index.php">Back to Dashboard</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <div class="product-form">
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current product image" class="preview-image">
                </div>
                
                <button type="submit" class="submit-btn">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html> 