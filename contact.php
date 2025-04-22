<?php
// Include database connection
require 'config.php'; // Ensure this file contains the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"]));
    
    // Validate required fields
    if (!empty($name) && !empty($email) && !empty($message)) {
        $created_at = date("Y-m-d H:i:s"); // Current timestamp
        $status = "unread"; // Default status

        // Prepare the SQL statement
        $sql = "INSERT INTO contact_messages (name, email, message, created_at, status) 
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $name, $email, $message, $created_at, $status);
            if ($stmt->execute()) {
                // Redirect after successful submission
                header("Location: index.php?success=1");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "Please fill in all fields.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
