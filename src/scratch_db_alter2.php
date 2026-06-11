<?php
include('d:/261/dbms project/src/assets/config/db.php');

$sql_alter = "ALTER TABLE job_seeker_profiles 
    ADD COLUMN gender VARCHAR(20) DEFAULT NULL,
    ADD COLUMN dob DATE DEFAULT NULL,
    ADD COLUMN address TEXT DEFAULT NULL,
    ADD COLUMN expected_salary VARCHAR(50) DEFAULT NULL,
    ADD COLUMN experience_years INT DEFAULT 0,
    ADD COLUMN certifications TEXT DEFAULT NULL,
    ADD COLUMN languages TEXT DEFAULT NULL,
    ADD COLUMN degree VARCHAR(100) DEFAULT NULL,
    ADD COLUMN institution VARCHAR(150) DEFAULT NULL,
    ADD COLUMN gpa VARCHAR(20) DEFAULT NULL,
    ADD COLUMN passing_year INT DEFAULT NULL,
    ADD COLUMN company_name VARCHAR(150) DEFAULT NULL,
    ADD COLUMN job_position VARCHAR(100) DEFAULT NULL,
    ADD COLUMN work_duration VARCHAR(50) DEFAULT NULL,
    ADD COLUMN responsibilities TEXT DEFAULT NULL,
    ADD COLUMN cv_file VARCHAR(255) DEFAULT NULL,
    ADD COLUMN portfolio_link VARCHAR(255) DEFAULT NULL,
    ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL";

if ($conn->query($sql_alter) === TRUE) {
    echo "job_seeker_profiles altered successfully again\n";
} else {
    echo "Error altering table: " . $conn->error . "\n";
}
?>
