<?php
include('assets/config/db.php');

// 1. Add "Laravel", "React", "Vue", "Node.js", "Python" to dic_skills
$new_skills = ['Laravel', 'React', 'Vue', 'Node.js', 'Python', 'AWS', 'Docker', 'UI/UX', 'Figma', 'SEO'];
foreach ($new_skills as $sk) {
    $conn->query("INSERT IGNORE INTO dic_skills (skill_name) VALUES ('$sk')");
}

// 2. Randomize job seeker skills with the new skills
$seekers_res = $conn->query("SELECT user_id FROM job_seeker_profiles");
$all_skill_ids = [];
$res = $conn->query("SELECT skill_id FROM dic_skills");
while($r = $res->fetch_assoc()) { $all_skill_ids[] = $r['skill_id']; }

if ($seekers_res) {
    while($row = $seekers_res->fetch_assoc()) {
        $uid = $row['user_id'];
        
        // Randomly assign 2-4 new skills
        $num_skills = rand(2, 4);
        $assigned = array_rand(array_flip($all_skill_ids), $num_skills);
        if (!is_array($assigned)) $assigned = [$assigned];
        
        foreach($assigned as $sk_id) {
            $conn->query("INSERT IGNORE INTO job_seeker_skills (user_id, skill_id, proficiency) VALUES ($uid, $sk_id, 'Expert')");
        }
        
        // 3. Randomize availability_status, partner_type, and is_remote
        $avail = ['Available Now', 'Available This Week', 'Busy'];
        $ptype = ['Job Candidate', 'Business Partner', 'Freelancer', 'Intern'];
        $is_remote = rand(0, 1);
        $av = $avail[array_rand($avail)];
        $pt = $ptype[array_rand($ptype)];
        
        $conn->query("UPDATE job_seeker_profiles SET is_remote = $is_remote, availability_status = '$av', partner_type = '$pt' WHERE user_id = $uid");
    }
}

// 4. Ensure Test Users exist and have data
// Let's create an Admin if it doesn't exist
$admin_email = 'admin@example.com';
$conn->query("INSERT IGNORE INTO users (full_name, email, password, role) VALUES ('System Admin', '$admin_email', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin')");

// 5. Generate Notifications to show the teacher
// We will target the active test users: 
$target_users = [];
$tu_res = $conn->query("SELECT user_id, email, role FROM users WHERE email IN ('testemployer@example.com', 'testseeker@example.com', 'ratul003ghosh@gmail.com', 'testuser@gmail.com') OR role='admin' LIMIT 10");
while($r = $tu_res->fetch_assoc()) {
    $target_users[] = $r['user_id'];
}

foreach($target_users as $uid) {
    // Generate a few notifications for each
    $msgs = [
        "Your profile has been viewed by a top employer.",
        "System maintenance is scheduled for tomorrow at 12 AM.",
        "You have a new message regarding your application.",
        "Your resume matched with 5 new job postings!",
        "Interview successfully scheduled."
    ];
    
    foreach($msgs as $msg) {
        $conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($uid, '$msg', 0, NOW() - INTERVAL " . rand(1, 48) . " HOUR)");
    }
}

// 6. Ensure some specific test cases for the filters
// Let's force one candidate to be a 'Freelancer', 'Remote', 'React'/'Laravel' expert named "Demo Freelancer"
$conn->query("INSERT IGNORE INTO users (full_name, email, password, role) VALUES ('Demo Freelancer', 'freelance@example.com', '123', 'job_seeker')");
$res = $conn->query("SELECT user_id FROM users WHERE email='freelance@example.com'");
if($res && $res->num_rows > 0) {
    $uid = $res->fetch_assoc()['user_id'];
    $conn->query("INSERT IGNORE INTO job_seeker_profiles (user_id, is_remote, availability_status, partner_type, experience_years) VALUES ($uid, 1, 'Available Now', 'Freelancer', 5)");
    
    $sk_res = $conn->query("SELECT skill_id FROM dic_skills WHERE skill_name IN ('React', 'Laravel', 'AWS')");
    while($sk = $sk_res->fetch_assoc()) {
        $conn->query("INSERT IGNORE INTO job_seeker_skills (user_id, skill_id, proficiency) VALUES ($uid, {$sk['skill_id']}, 'Expert')");
    }
}

echo "Database successfully enriched for Viva Presentation.";
?>
