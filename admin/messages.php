<?php
session_start();
include '../config.php';
$conn->query("UPDATE contact_messages SET status = 'read' WHERE status = 'unread'");

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle message status update
if (isset($_POST['mark_as_read'])) {
    $message_id = $_POST['message_id'];
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

if (isset($_POST['mark_as_replied'])) {
    $message_id = $_POST['message_id'];
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

if (isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

// Get all messages
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - LuxFurn Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-messages {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .messages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .message-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            position: relative;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .message-info h3 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
        }

        .message-info p {
            margin: 0.25rem 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .message-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .message-status.new {
            background: #e74c3c;
            color: #fff;
        }

        .message-status.read {
            background: #f39c12;
            color: #fff;
        }

        .message-status.replied {
            background: #27ae60;
            color: #fff;
        }

        .message-content {
            margin: 1rem 0;
            color: #333;
            line-height: 1.5;
        }

        .message-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .read-btn {
            background: #f39c12;
            color: #fff;
        }

        .read-btn:hover {
            background: #d68910;
        }

        .reply-btn {
            background: #27ae60;
            color: #fff;
        }

        .reply-btn:hover {
            background: #219a52;
        }

        .delete-btn {
            background: #e74c3c;
            color: #fff;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .message-date {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            color: #999;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="admin-messages">
    <?php include '_Nav.php'; ?>

        <div class="messages-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($message = $result->fetch_assoc()): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <div class="message-info">
                                <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                                <p><?php echo htmlspecialchars($message['email']); ?></p>
                            </div>
                            <span class="message-status <?php echo $message['status']; ?>">
                                <?php echo ucfirst($message['status']); ?>
                            </span>
                        </div>
                        
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                        
                        <div class="message-date">
                            <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                        </div>
                        
                        <div class="message-actions">
                            <?php if ($message['status'] === 'new'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" name="mark_as_read" class="action-btn read-btn">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($message['status'] === 'read'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" name="mark_as_replied" class="action-btn reply-btn">
                                        <i class="fas fa-reply"></i> Mark as Replied
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" name="delete_message" class="action-btn delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                    <p>No messages found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 