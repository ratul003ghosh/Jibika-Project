<?php
include('assets/config/db.php');
$res=$conn->query("SELECT * FROM interviews WHERE application_id IN (174,175,180,181)");
while($row=$res->fetch_assoc()) {
    print_r($row);
}
if ($conn->error) echo "Error: " . $conn->error;
?>
