<?php
session_start();
include 'config.php';

// Handle add to cart
if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1;
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->bind_param("i", $cart_item['id']);
    } else {
        // Add new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $_SESSION['user_id'], $product_id, $quantity);
    }
    $stmt->execute();
}

// Handle add to wishlist
if (isset($_POST['add_to_wishlist']) && isset($_SESSION['user_id'])) {
    $product_id = $_POST['product_id'];
    
    // Check if already in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Furniture Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">LuxFurn</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section id="hero">
        <div class="hero-content">
            <h1>Welcome to LuxFurn</h1>
            <p>Discover our exclusive collection of premium furniture</p>
            <a href="#products" class="cta-button">Shop Now</a>
        </div>
    </section>

    <section id="products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php
            $sql = "SELECT * FROM products ";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img style="object-fit:contain;" src="' . $row['image_url'] . '" alt="' . $row['name'] . '">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p class="price">₹' . $row['price'] . '</p>';
                    echo '<p class="description">' . $row['description'] . '</p>';
                    if (isset($_SESSION['user_id'])) {
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>';
                        echo '</form>';
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" name="add_to_wishlist" class="add-to-wishlist"><i class="fas fa-heart"></i></button>';
                        echo '</form>';
                    } else {
                        echo '<a href="login.php" class="add-to-cart">Login to Purchase</a>';
                    }
                    echo '</div>';
                }
            } else {
                echo "No products found";
            }
            ?>
        </div>
    </section>

    <section id="about">
        <h2>About Us</h2>
        <div class="about-content">
            <p>LuxFurn is your premier destination for high-quality furniture. We offer a carefully curated selection of pieces that combine style, comfort, and durability.</p>
        </div>
    </section>

    <section id="contact">
        <h2>Contact Us</h2>
        <div class="contact-form">
            <form action="contact.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>LuxFurn</h3>
                <p>Your trusted source for premium furniture.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <p>Email: info@luxfurn.com</p>
                <p>Phone: (555) 123-4567</p>
                <p>Address: 123 Furniture Street, Design City</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 LuxFurn. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 