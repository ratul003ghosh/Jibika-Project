<?php
include('assets/config/db.php');
$res = $conn->query("SHOW COLUMNS FROM interviews");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
