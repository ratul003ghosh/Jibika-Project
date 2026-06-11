<?php
include('assets/config/db.php');

$q = $conn->query("SELECT user_id FROM users WHERE role='employer'");
$employers = [];
while($r = $q->fetch_assoc()){
    $employers[] = $r['user_id'];
}

// Ensure all employers have jobs and applicants
foreach($employers as $emp_id) {
    // Assign 3 seeded jobs to each employer
    $conn->query("UPDATE jobs SET employer_id = $emp_id WHERE employer_id NOT IN (" . implode(',', $employers) . ") LIMIT 3");
    
    // Now make sure they have applicants
    $jobs = $conn->query("SELECT job_id FROM jobs WHERE employer_id = $emp_id LIMIT 3");
    $job_ids = [];
    while($r = $jobs->fetch_assoc()) $job_ids[] = $r['job_id'];
    
    $seekers = $conn->query("SELECT user_id FROM users WHERE role='job_seeker' LIMIT 15");
    $seeker_ids = [];
    while($r = $seekers->fetch_assoc()) $seeker_ids[] = $r['user_id'];
    
    if (count($job_ids) > 0 && count($seeker_ids) > 0) {
        $statuses = ['Pending', 'Accepted', 'Rejected', 'Pending'];
        for ($i=0; $i<10; $i++) {
            $j = $job_ids[array_rand($job_ids)];
            $s = $seeker_ids[array_rand($seeker_ids)];
            $stat = $statuses[array_rand($statuses)];
            $conn->query("INSERT IGNORE INTO applications (job_id, user_id, status) VALUES ($j, $s, '$stat')");
        }
    }
}
echo "Assigned data to ALL employers.";
?>
