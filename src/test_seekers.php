<?php
include('assets/config/db.php');
$q=$conn->query("SELECT u.full_name, jsp.education, jsp.experience_years FROM users u LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id WHERE u.role='job_seeker'");
while($r=$q->fetch_assoc()) echo $r['full_name'] . ' : ' . $r['education'] . ' : ' . $r['experience_years'] . "\n";
?>
