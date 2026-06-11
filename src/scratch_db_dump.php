<?php
include('d:/261/dbms project/src/assets/config/db.php');

$tables = ['users', 'job_seeker_profiles', 'jobs', 'applications', 'skills'];
foreach ($tables as $t) {
    echo "TABLE: $t\n";
    $res = $conn->query("DESCRIBE $t");
    while ($row = $res->fetch_assoc()) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
?>
