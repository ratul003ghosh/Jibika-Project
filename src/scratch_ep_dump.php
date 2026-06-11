<?php
include('d:/261/dbms project/src/assets/config/db.php');

$res = $conn->query("DESCRIBE employer_profiles");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
