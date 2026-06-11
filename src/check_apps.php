<?php
include('assets/config/db.php');
$res=$conn->query("SELECT a.application_id, a.status as app_status, i.status as int_status, i.interview_datetime FROM applications a LEFT JOIN interviews i ON a.application_id=i.application_id WHERE a.user_id=91");
while($row=$res->fetch_assoc()) {
    print_r($row);
}
?>
