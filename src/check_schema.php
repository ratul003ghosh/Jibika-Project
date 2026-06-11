<?php
include('assets/config/db.php');
$res = $conn->query("SHOW COLUMNS FROM notifications");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
