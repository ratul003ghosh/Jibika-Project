<?php
include('assets/config/db.php');

$res = $conn->query("SELECT user_id, email FROM users WHERE email='user@gmail.com'");
if ($res && $res->num_rows > 0) {
    $uid = $res->fetch_assoc()['user_id'];
    echo "User ID for user@gmail.com is $uid\n";
    
    $notif_res = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE user_id = $uid");
    echo "Notification Count for this user: " . $notif_res->fetch_assoc()['c'] . "\n";
} else {
    echo "User not found.\n";
}
?>
