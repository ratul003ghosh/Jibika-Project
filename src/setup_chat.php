<?php
include('assets/config/db.php');

$sql = "
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE
);
";

if ($conn->query($sql)) {
    echo "Messages table created successfully.\n";
} else {
    echo "Error creating messages table: " . $conn->error . "\n";
}

$conn->query("CREATE INDEX idx_messages_sender ON messages(sender_id)");
$conn->query("CREATE INDEX idx_messages_receiver ON messages(receiver_id)");
?>
