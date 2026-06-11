<?php
include('assets/config/db.php');

$q = $conn->query("SELECT user_id, full_name FROM users WHERE role='job_seeker'");
while ($u = $q->fetch_assoc()) {
    $uid = $u['user_id'];
    
    $check = $conn->query("SELECT * FROM job_seeker_profiles WHERE user_id=$uid");
    if($check->num_rows == 0) {
        $conn->query("INSERT INTO job_seeker_profiles (user_id) VALUES ($uid)");
    }

    // Check what jobs they applied to
    $j_res = $conn->query("SELECT jobs.job_category, jobs.title FROM applications JOIN jobs ON applications.job_id = jobs.job_id WHERE applications.user_id = $uid LIMIT 1");
    
    $cat = 'IT & Computer';
    $title = 'Software Engineer';
    if ($j_res && $j_res->num_rows > 0) {
        $j = $j_res->fetch_assoc();
        $cat = $j['job_category'];
        $title = $j['title'];
    }
    
    // Generate realistic data based on category
    $edu = 'Bachelor';
    $degree = 'B.Sc in Computer Science';
    $inst = 'Dhaka University';
    $skills = 'HTML, CSS, PHP';
    $pos = 'Junior Developer';
    $resp = 'Developed web applications and maintained databases.';
    
    if (stripos($title, 'Data Entry') !== false) {
        $edu = 'HSC';
        $degree = 'HSC (Commerce)';
        $inst = 'City College';
        $skills = 'Microsoft Word, Excel, Typing';
        $pos = 'Data Operator';
        $resp = 'Entered 500+ records daily into the system.';
    } elseif (stripos($title, 'Plumb') !== false) {
        $edu = 'SSC';
        $degree = 'SSC (Vocational)';
        $inst = 'Technical Training Institute';
        $skills = 'Pipe fitting, Welding, Maintenance';
        $pos = 'Assistant Plumber';
        $resp = 'Repaired residential plumbing systems.';
    } elseif (stripos($title, 'Sales') !== false || stripos($cat, 'Sales') !== false) {
        $edu = 'Bachelor';
        $degree = 'BBA in Marketing';
        $inst = 'North South University';
        $skills = 'Communication, Sales, CRM';
        $pos = 'Sales Executive';
        $resp = 'Increased sales by 15% in the first quarter.';
    } elseif (stripos($title, 'Graphic') !== false) {
        $edu = 'Diploma';
        $degree = 'Diploma in Fine Arts';
        $inst = 'Graphic Arts Institute';
        $skills = 'Photoshop, Illustrator, UI/UX';
        $pos = 'Junior Designer';
        $resp = 'Created social media graphics and banners.';
    } elseif (stripos($title, 'Driver') !== false || stripos($title, 'Rider') !== false || stripos($cat, 'Driving') !== false) {
        $edu = 'SSC';
        $degree = 'SSC';
        $inst = 'Local High School';
        $skills = 'Valid Driving License, Traffic Rules, Maintenance';
        $pos = 'Driver';
        $resp = 'Delivered goods safely across districts.';
    } elseif (stripos($title, 'Garments') !== false || stripos($cat, 'Garments') !== false) {
        $edu = 'SSC';
        $degree = 'SSC';
        $inst = 'Vocational School';
        $skills = 'Sewing, Quality Checking, Tailoring';
        $pos = 'Quality Inspector';
        $resp = 'Ensured product quality met export standards.';
    }
    
    $gender = (rand(0, 1) == 0) ? 'Male' : 'Female';
    $dob = date('Y-m-d', strtotime('-' . rand(22, 35) . ' years -' . rand(1, 12) . ' months'));
    $address = 'House ' . rand(1, 100) . ', Road ' . rand(1, 15) . ', Block ' . chr(rand(65, 70));
    $sal = rand(15, 40) . '000';
    $exp = rand(1, 5);
    $company = 'Previous Company BD Ltd.';
    
    // Update profile
    $conn->query("UPDATE job_seeker_profiles SET 
        education = '$edu',
        skills = '$skills',
        dob = '$dob',
        gender = '$gender',
        address = '$address',
        expected_salary = '$sal',
        experience_years = '$exp',
        preferred_job_category = '$cat',
        degree = '$degree',
        institution = '$inst',
        gpa = '3." . rand(2, 9) . "',
        passing_year = '20" . rand(15, 22) . "',
        company_name = '$company',
        job_position = '$pos',
        work_duration = '$exp',
        responsibilities = '$resp'
        WHERE user_id = $uid
    ");
    
    // Update Skills Table properly (delete old, insert new)
    $conn->query("DELETE FROM skills WHERE user_id = $uid");
    $skill_array = explode(',', $skills);
    foreach($skill_array as $s) {
        $s = trim($s);
        if(!empty($s)) {
            $conn->query("INSERT IGNORE INTO skills (user_id, skill_name) VALUES ($uid, '$s')");
        }
    }
}
echo "Realistic data generated and inserted!";
?>
