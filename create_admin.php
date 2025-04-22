<?php
include 'config.php';

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin'");
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo "Admin user already exists!";
    exit();
}

// Create new admin user
$username = 'admin';
$email = 'admin@luxfurn.com';
$password = 'admin123';
$full_name = 'Admin User';
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert admin user
$stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error creating admin user: " . $conn->error;
}
?> 