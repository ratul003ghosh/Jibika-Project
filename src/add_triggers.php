<?php
include('assets/config/db.php');

$sql_trigger = "
CREATE TRIGGER after_application_update 
AFTER UPDATE ON applications
FOR EACH ROW 
BEGIN
    IF NEW.status = 'Accepted' AND OLD.status != 'Accepted' THEN
        INSERT INTO notifications (user_id, message, type, link)
        VALUES (NEW.user_id, 'Congratulations! Your job application has been Accepted.', 'success', 'jobseeker/dashboard.php');
    ELSEIF NEW.status = 'Rejected' AND OLD.status != 'Rejected' THEN
        INSERT INTO notifications (user_id, message, type, link)
        VALUES (NEW.user_id, 'We regret to inform you that your application was Rejected.', 'danger', 'jobseeker/dashboard.php');
    END IF;
END;
";

$conn->query("DROP TRIGGER IF EXISTS after_application_update");
if ($conn->query($sql_trigger)) {
    echo "Trigger 'after_application_update' created successfully.\n";
} else {
    echo "Error creating trigger: " . $conn->error . "\n";
}

$sql_procedure = "
CREATE PROCEDURE GetJobStatistics(IN emp_id INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM jobs WHERE employer_id = emp_id) AS total_jobs,
        (SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.job_id WHERE j.employer_id = emp_id) AS total_applications,
        (SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.job_id WHERE j.employer_id = emp_id AND a.status = 'Accepted') AS hired_candidates;
END;
";

$conn->query("DROP PROCEDURE IF EXISTS GetJobStatistics");
if ($conn->query($sql_procedure)) {
    echo "Stored Procedure 'GetJobStatistics' created successfully.\n";
} else {
    echo "Error creating procedure: " . $conn->error . "\n";
}
?>
