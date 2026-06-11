<?php
session_start();
include('../assets/config/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'send') {
    $receiver_id = intval($_POST['receiver_id']);
    $message = $conn->real_escape_string($_POST['message']);
    
    if ($message != '' && $receiver_id > 0) {
        $conn->query("INSERT INTO messages (sender_id, receiver_id, message_text, message) VALUES ($user_id, $receiver_id, '$message', '$message')");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid data']);
    }
} 
elseif ($action == 'fetch') {
    $partner_id = intval($_GET['partner_id']);
    
    // Mark as read
    $conn->query("UPDATE messages SET is_read=1 WHERE sender_id=$partner_id AND receiver_id=$user_id");
    
    // Fetch last 50 messages
    $q = $conn->query("SELECT * FROM messages 
                       WHERE (sender_id=$user_id AND receiver_id=$partner_id) 
                          OR (sender_id=$partner_id AND receiver_id=$user_id)
                       ORDER BY created_at ASC LIMIT 50");
                       
    $msgs = [];
    while($row = $q->fetch_assoc()) {
        $row['is_mine'] = ($row['sender_id'] == $user_id);
        $msgs[] = $row;
    }
    echo json_encode(['messages' => $msgs]);
}
?>
