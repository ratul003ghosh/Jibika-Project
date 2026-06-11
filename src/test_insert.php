<?php
include('assets/config/db.php');
$res = $conn->query("INSERT INTO interviews (application_id, employer_id, interview_datetime, status) VALUES (174, 88, DATE_ADD(NOW(), INTERVAL 2 DAY), 'scheduled')");
if (!$res) {
    echo "Insert error: " . $conn->error;
} else {
    echo "Insert success.";
}
?>
