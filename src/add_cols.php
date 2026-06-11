<?php
include('assets/config/db.php');
$conn->query("ALTER TABLE job_seeker_profiles ADD COLUMN availability_status VARCHAR(50) DEFAULT 'Available Now'");
$conn->query("ALTER TABLE job_seeker_profiles ADD COLUMN partner_type VARCHAR(50) DEFAULT 'Job Candidate'");
$conn->query("ALTER TABLE job_seeker_profiles ADD COLUMN is_remote BOOLEAN DEFAULT FALSE");
echo "Done";
?>
