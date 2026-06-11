<?php
include('assets/config/db.php');

$res = $conn->query("SELECT a.application_id, a.job_id, a.user_id, j.employer_id, a.status 
                     FROM applications a 
                     JOIN jobs j ON a.job_id = j.job_id 
                     WHERE a.user_id = 91 AND a.status IN ('Interview Scheduled', 'Interview Proposed')");

while($row = $res->fetch_assoc()) {
    $app_id = $row['application_id'];
    $job_id = $row['job_id'];
    $emp_id = $row['employer_id'];
    $cand_id = $row['user_id'];
    $status = 'scheduled';
    
    // check if exists
    $ch = $conn->query("SELECT * FROM interviews WHERE application_id=$app_id");
    if ($ch->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO interviews (job_id, application_id, employer_id, candidate_id, interview_type, interview_title, interview_datetime, status) VALUES (?, ?, ?, ?, 'online', 'Technical Interview', DATE_ADD(NOW(), INTERVAL 2 DAY), ?)");
        $stmt->bind_param("iiiis", $job_id, $app_id, $emp_id, $cand_id, $status);
        if (!$stmt->execute()) {
            echo "Error inserting interview: " . $stmt->error . "\n";
        } else {
            echo "Inserted interview for app $app_id\n";
        }
    } else {
        echo "Interview already exists for app $app_id\n";
    }
}
?>
