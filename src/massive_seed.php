<?php
include('assets/config/db.php');

// Helper to execute and ignore errors for bulk seed
function q($conn, $sql) { $conn->query($sql); }

// 1. Clear existing demo data to ensure a clean massive seed (optional, but let's just insert ignores or new)
// To prevent destroying everything, we will just add onto the DB.

$companies = [
    ['Walton', 'Manufacturing', 'Dhaka'],
    ['Pathao', 'IT & Computer', 'Dhaka'],
    ['PRAN-RFL', 'Manufacturing', 'Gazipur'],
    ['Brain Station 23', 'IT & Computer', 'Dhaka'],
    ['bKash', 'Banking', 'Dhaka'],
    ['Nagad', 'Banking', 'Dhaka'],
    ['SSL Wireless', 'IT & Computer', 'Dhaka'],
    ['ACI', 'Manufacturing', 'Gazipur'],
    ['Daraz Bangladesh', 'Marketing', 'Dhaka'],
    ['Evaly', 'Sales', 'Dhaka']
];

// Create Employers
$employer_ids = [];
foreach ($companies as $i => $c) {
    $email = strtolower(str_replace(' ', '', $c[0])) . "@example.com";
    $conn->query("INSERT IGNORE INTO users (full_name, email, phone, password, role) VALUES ('{$c[0]} Admin', '$email', '018000000" . sprintf("%02d", $i) . "', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'employer')");
    
    $eid_res = $conn->query("SELECT user_id FROM users WHERE email='$email'");
    if($eid_res && $eid_res->num_rows > 0) {
        $eid = $eid_res->fetch_assoc()['user_id'];
        $employer_ids[] = $eid;
        
        $conn->query("INSERT IGNORE INTO employer_profiles (user_id, company_name, company_type, company_address) VALUES ($eid, '{$c[0]}', '{$c[1]}', '{$c[2]} Corporate Office')");
    }
}

// Create Seekers
$seeker_names = ["Arif Hossain", "Nusrat Jahan", "Kamrul Hasan", "Farhana Akter", "Tariqul Islam", "Sumi Begum", "Mehedi Hasan", "Sadia Islam", "Rahim Uddin", "Karim Ali", "Rina Akter", "Sabbir Rahman", "Nazmul Huda", "Shamim Osman", "Tania Sultana", "Hasib Mahmud", "Rakib Hasan", "Sharmin Akter", "Imran Nazir", "Liza Begum", "Sohel Rana", "Mominul Islam", "Tamim Iqbal", "Shakib Al Hasan", "Mushfiqur Rahim", "Mahmudullah Riyad", "Mustafizur Rahman", "Liton Das", "Soumya Sarkar", "Taskin Ahmed", "Rubel Hossain"];

$seeker_ids = [];
foreach ($seeker_names as $i => $name) {
    $email = "seeker" . $i . "@example.com";
    $conn->query("INSERT IGNORE INTO users (full_name, email, phone, password, role) VALUES ('$name', '$email', '017000000" . sprintf("%02d", $i) . "', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'job_seeker')");
    
    $sid_res = $conn->query("SELECT user_id FROM users WHERE email='$email'");
    if($sid_res && $sid_res->num_rows > 0) {
        $sid = $sid_res->fetch_assoc()['user_id'];
        $seeker_ids[] = $sid;
        
        $conn->query("INSERT IGNORE INTO job_seeker_profiles (user_id, degree, expected_salary, experience_years) VALUES ($sid, 'Bachelor', '25000', " . rand(1,5) . ")");
    }
}

// Create Jobs
$job_titles = ['Software Engineer', 'Senior Developer', 'Sales Executive', 'Marketing Manager', 'Customer Support', 'Delivery Rider', 'Accounting Officer', 'HR Manager', 'Quality Controller', 'Machine Operator'];
$categories = ['IT & Computer', 'Sales', 'Marketing', 'Customer Service', 'Delivery', 'Banking', 'Manufacturing'];

$job_ids = [];
for ($i=0; $i<60; $i++) {
    $eid = $employer_ids[array_rand($employer_ids)];
    $title = $job_titles[array_rand($job_titles)];
    $cat = $categories[array_rand($categories)];
    $deadline = date('Y-m-d', strtotime('+' . rand(10, 30) . ' days'));
    
    $conn->query("INSERT INTO jobs (employer_id, title, description, job_category, job_type, vacancy, location, application_deadline, status, education_required, experience_required) VALUES ($eid, '$title', 'We are looking for a dedicated $title.', '$cat', 'Full-time', " . rand(1, 5) . ", 'Dhaka', '$deadline', 'active', 'Bachelor', '" . rand(1, 3) . " Years')");
    $job_ids[] = $conn->insert_id;
}

// Create Applications
$statuses = ['Pending', 'Under Review', 'Interview Scheduled', 'Selected', 'Rejected', 'Accepted'];
$application_ids = [];

for ($i=0; $i<180; $i++) {
    $sid = $seeker_ids[array_rand($seeker_ids)];
    $jid = $job_ids[array_rand($job_ids)];
    $status = $statuses[array_rand($statuses)];
    $score = rand(65, 95);
    
    $conn->query("INSERT IGNORE INTO applications (job_id, user_id, status, score) VALUES ($jid, $sid, '$status', $score)");
    if ($conn->insert_id) {
        $application_ids[] = $conn->insert_id;
    }
}

// Ensure at least 40 Interviews
$interview_statuses = ['scheduled', 'completed', 'cancelled', 'selected', 'rejected'];
$int_count = 0;
$app_res = $conn->query("SELECT application_id, job_id, user_id FROM applications LIMIT 50");
while($app = $app_res->fetch_assoc()) {
    $appid = $app['application_id'];
    $jid = $app['job_id'];
    $sid = $app['user_id'];
    
    $emp_res = $conn->query("SELECT employer_id, title FROM jobs WHERE job_id=$jid");
    $emp = $emp_res->fetch_assoc();
    $eid = $emp['employer_id'];
    $jtitle = $emp['title'];
    
    $type = (rand(0,1)==0) ? 'online' : 'offline';
    $istatus = $interview_statuses[array_rand($interview_statuses)];
    $date = date('Y-m-d H:i:s', strtotime('+' . rand(1, 7) . ' days'));
    
    $conn->query("INSERT INTO interviews (job_id, application_id, employer_id, candidate_id, interview_type, interview_title, interview_datetime, interview_location, meeting_link, status) VALUES ($jid, $appid, $eid, $sid, '$type', 'Interview for $jtitle', '$date', 'Head Office', 'https://zoom.us/j/123456', '$istatus')");
    $int_count++;
}

echo "Massive database population complete!\n";
echo "Employers: " . count($employer_ids) . "\n";
echo "Seekers: " . count($seeker_ids) . "\n";
echo "Jobs: " . count($job_ids) . "\n";
echo "Applications: 180 (Attempted)\n";
echo "Interviews: $int_count\n";

?>
