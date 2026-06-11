<?php
include('d:/261/dbms project/src/assets/config/db.php');

$sql_create = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    title_bn VARCHAR(255) NOT NULL,
    message_en TEXT NOT NULL,
    message_bn TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql_create) === TRUE) {
    echo "Table notifications created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$sql_alter = "ALTER TABLE job_seeker_profiles 
    ADD COLUMN preferred_district VARCHAR(100) DEFAULT NULL,
    ADD COLUMN preferred_upazila VARCHAR(100) DEFAULT NULL,
    ADD COLUMN preferred_job_category VARCHAR(100) DEFAULT NULL";

if ($conn->query($sql_alter) === TRUE) {
    echo "job_seeker_profiles altered successfully\n";
} else {
    echo "Error altering table: " . $conn->error . "\n";
}
?>
