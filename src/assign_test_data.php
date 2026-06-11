<?php
include('assets/config/db.php');

// Find the 'test' employer
$q = $conn->query("SELECT user_id FROM users WHERE full_name LIKE '%test%' AND role='employer' LIMIT 1");
if ($q->num_rows > 0) {
    $test_id = $q->fetch_assoc()['user_id'];
    echo "Found test employer ID: " . $test_id . "\n";
    
    // Assign 5 random seeded jobs to 'test'
    $conn->query("UPDATE jobs SET employer_id = $test_id WHERE employer_id != $test_id LIMIT 5");
    
    // Assign 10 dummy applications to the jobs owned by 'test'
    $jobs = $conn->query("SELECT job_id FROM jobs WHERE employer_id = $test_id LIMIT 5");
    $job_ids = [];
    while($r = $jobs->fetch_assoc()) {
        $job_ids[] = $r['job_id'];
    }
    
    $seekers = $conn->query("SELECT user_id FROM users WHERE role='job_seeker' LIMIT 15");
    $seeker_ids = [];
    while($r = $seekers->fetch_assoc()) {
        $seeker_ids[] = $r['user_id'];
    }
    
    if (count($job_ids) > 0 && count($seeker_ids) > 0) {
        $statuses = ['Pending', 'Pending', 'Pending', 'shortlisted'];
        for ($i=0; $i<15; $i++) {
            $j = $job_ids[array_rand($job_ids)];
            $s = $seeker_ids[array_rand($seeker_ids)];
            $stat = $statuses[array_rand($statuses)];
            $conn->query("INSERT IGNORE INTO applications (job_id, user_id, status) VALUES ($j, $s, '$stat')");
        }
    }
    echo "Successfully assigned seeded jobs and applicants to 'test' account!";
} else {
    echo "Could not find 'test' employer.";
}
?>
