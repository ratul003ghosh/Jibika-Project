<?php
include('assets/config/db.php');

// 1. Alter Jobs Table to support proposed dates
$conn->query("ALTER TABLE jobs 
    ADD COLUMN proposed_date_1 DATE NULL,
    ADD COLUMN proposed_time_1 VARCHAR(50) NULL,
    ADD COLUMN proposed_date_2 DATE NULL,
    ADD COLUMN proposed_time_2 VARCHAR(50) NULL");

// 2. Alter Applications Table to support rejection counter
$conn->query("ALTER TABLE applications 
    ADD COLUMN rejection_count INT DEFAULT 0,
    ADD COLUMN suggested_datetime DATETIME NULL,
    MODIFY COLUMN status ENUM('Pending', 'Under Review', 'Interview Proposed', 'Interview Scheduled', 'Interview Cancelled', 'Selected', 'Rejected', 'Accepted') DEFAULT 'Pending'");

// 3. Alter Interviews Table to support complex scheduling
$conn->query("ALTER TABLE interviews 
    MODIFY COLUMN status ENUM('proposed', 'scheduled', 'completed', 'cancelled', 'selected', 'rejected', 'reschedule_requested') DEFAULT 'proposed'");

echo "Database updated for advanced interview workflow.\n";
?>
