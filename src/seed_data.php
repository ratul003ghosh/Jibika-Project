<?php
// Seed Data Script
include('assets/config/db.php');

$password_hash = '$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK'; // password123

echo "Starting data seeding...\n";

// Array of common districts from districts table (1=Dhaka, 2=Chattogram, 5=Barishal, 3=Khulna, 6=Sylhet, 4=Rajshahi)
$district_ids = [1, 2, 3, 4, 5, 6];

// 1. Insert Employers
$employers = [
    ['Walton BD', 'walton@example.com', '01711000001', 'IT & Electronics'],
    ['Pran-RFL', 'pran@example.com', '01711000002', 'Manufacturing'],
    ['Pathao', 'hr@pathao.com', '01711000003', 'IT Company'],
    ['BrainStation-23', 'careers@bs23.com', '01711000004', 'IT Company'],
    ['Beximco', 'beximco@example.com', '01711000005', 'Garments'],
    ['Square Group', 'square@example.com', '01711000006', 'Manufacturing'],
    ['Grameenphone', 'gp@example.com', '01711000007', 'IT & Telecomm'],
    ['Aarong', 'aarong@example.com', '01711000008', 'Shop/Small Business'],
    ['BRAC', 'brac@example.com', '01711000009', 'NGO'],
    ['Daraz BD', 'daraz@example.com', '01711000010', 'IT Company']
];

$employer_user_ids = [];
foreach ($employers as $emp) {
    $name = $emp[0];
    $email = $emp[1];
    $phone = $emp[2];
    $type = $emp[3];
    
    // Check if exists
    $check = $conn->query("SELECT user_id FROM users WHERE email='$email'");
    if($check->num_rows == 0) {
        $conn->query("INSERT INTO users (full_name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$password_hash', 'employer')");
        $uid = $conn->insert_id;
        $employer_user_ids[] = $uid;
        $d_id = $district_ids[array_rand($district_ids)];
        $conn->query("INSERT INTO employer_profiles (user_id, company_name, company_type, company_email, company_phone, district_id) VALUES ($uid, '$name', '$type', '$email', '$phone', $d_id)");
    } else {
        $employer_user_ids[] = $check->fetch_assoc()['user_id'];
    }
}
echo "Employers seeded.\n";

// 2. Insert Job Seekers
$names = ['Rahim', 'Karim', 'Nusrat', 'Sadia', 'Hasan', 'Mehedi', 'Rina', 'Tariq', 'Sumi', 'Kamrul', 'Faisal', 'Nadia', 'Jamil', 'Farhana', 'Sohel', 'Arif', 'Tahmina', 'Imran', 'Liza', 'Rubel'];
$seeker_user_ids = [];
$i = 1;
foreach ($names as $name) {
    $email = strtolower($name) . $i . '@example.com';
    $phone = '01811' . str_pad($i, 6, '0', STR_PAD_LEFT);
    
    $check = $conn->query("SELECT user_id FROM users WHERE email='$email'");
    if($check->num_rows == 0) {
        $conn->query("INSERT INTO users (full_name, email, phone, password, role) VALUES ('$name Uddin', '$email', '$phone', '$password_hash', 'job_seeker')");
        $uid = $conn->insert_id;
        $seeker_user_ids[] = $uid;
        $d_id = $district_ids[array_rand($district_ids)];
        
        $skills = ['PHP, MySQL', 'Data Entry, Excel', 'Java, Spring', 'Customer Service', 'Sewing, Tailoring', 'Graphic Design'];
        $skill = $skills[array_rand($skills)];
        
        $conn->query("INSERT INTO job_seeker_profiles (user_id, district_id, skills, education) VALUES ($uid, $d_id, '$skill', 'Bachelor Degree')");
    } else {
        $seeker_user_ids[] = $check->fetch_assoc()['user_id'];
    }
    $i++;
}
echo "Job Seekers seeded.\n";

// 3. Insert Jobs
$job_titles = ['Software Engineer', 'Data Entry Operator', 'Sales Executive', 'Accountant', 'Graphic Designer', 'Customer Support', 'Production Manager', 'Delivery Rider', 'HR Manager', 'System Admin'];
$categories = ['IT & Computer', 'Office Support', 'Sales & Marketing', 'Office Support', 'IT & Computer', 'Office Support', 'Garments', 'Driving', 'Office Support', 'IT & Computer'];
$job_ids = [];

if (count($employer_user_ids) > 0) {
    for ($j = 0; $j < 30; $j++) {
        $emp_id = $employer_user_ids[array_rand($employer_user_ids)];
        $idx = array_rand($job_titles);
        $title = $job_titles[$idx];
        $cat = $categories[$idx];
        $d_id = $district_ids[array_rand($district_ids)];
        
        $salaries = ['15000-20000', '25000-40000', 'Negotiable', '50000+'];
        $sal = $salaries[array_rand($salaries)];
        
        $conn->query("INSERT INTO jobs (employer_id, title, description, job_category, job_type, vacancy, district_id, salary, experience_required, status) 
        VALUES ($emp_id, '$title', 'We are looking for a reliable $title.', '$cat', 'Full-time', 2, $d_id, '$sal', '1-2 Years', 'active')");
        $job_ids[] = $conn->insert_id;
    }
}
echo "Jobs seeded.\n";

// 4. Insert Applications
if (count($seeker_user_ids) > 0 && count($job_ids) > 0) {
    $statuses = ['Pending', 'Accepted', 'Rejected', 'Pending'];
    for ($k = 0; $k < 60; $k++) {
        $uid = $seeker_user_ids[array_rand($seeker_user_ids)];
        $jid = $job_ids[array_rand($job_ids)];
        $stat = $statuses[array_rand($statuses)];
        
        $check = $conn->query("SELECT application_id FROM applications WHERE user_id=$uid AND job_id=$jid");
        if($check->num_rows == 0) {
            $conn->query("INSERT INTO applications (job_id, user_id, status) VALUES ($jid, $uid, '$stat')");
        }
    }
}
echo "Applications seeded.\n";

echo "Database seeding completed successfully!";
?>
