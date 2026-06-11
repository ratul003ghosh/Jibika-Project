<?php
include('assets/config/db.php');

// Fetch IDs
$emp_res = $conn->query("SELECT user_id FROM users WHERE email = 'employer@gmail.com'");
$user_res = $conn->query("SELECT user_id FROM users WHERE email = 'user@gmail.com'");

if (!$emp_res || !$user_res || $emp_res->num_rows == 0 || $user_res->num_rows == 0) {
    die("Error: Employer or User account not found. Please run setup_credentials.php first.");
}

$emp_id = $emp_res->fetch_assoc()['user_id'];
$user_id = $user_res->fetch_assoc()['user_id'];

// --- 1. SETUP EMPLOYER DATA ---

// Update Employer Profile
$conn->query("UPDATE employer_profiles SET 
    company_name = 'TechNova Solutions', 
    company_description = 'A leading tech agency specializing in modern web and AI solutions.', 
    industry_type = 'IT & Computer', 
    website_link = 'https://technova.example.com',
    district_id = 1,
    upazila_id = 1
    WHERE user_id = $emp_id");

// Create 3 Jobs for the Employer
$jobs = [
    ['title' => 'Senior Frontend Developer', 'category' => 'Engineering', 'skills' => ['React', 'HTML', 'Tailwind']],
    ['title' => 'Backend PHP Engineer', 'category' => 'Engineering', 'skills' => ['PHP', 'Laravel', 'MySQL']],
    ['title' => 'Product Manager', 'category' => 'Management', 'skills' => ['Agile', 'Jira', 'Communication']]
];

$job_ids = [];
foreach ($jobs as $j) {
    $conn->query("INSERT INTO jobs (employer_id, title, job_category, job_type, vacancy, salary_type, salary, experience_required, status, created_at, application_deadline, district_id) 
                  VALUES ($emp_id, '{$j['title']}', '{$j['category']}', 'Full-Time', 2, 'Monthly', '60000 - 80000 BDT', '3', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1)");
    $job_ids[] = $conn->insert_id;
}

// --- 2. SETUP USER (CANDIDATE) DATA ---

// Update User Profile
$conn->query("UPDATE job_seeker_profiles SET 
    gender = 'Male',
    experience_years = 4,
    preferred_job_category = 'Engineering',
    expected_salary = '70000 BDT',
    is_remote = 1,
    availability_status = 'Available Now',
    partner_type = 'Job Candidate',
    about = 'A passionate software engineer specializing in backend development and scalable architectures.',
    district_id = 1
    WHERE user_id = $user_id");

// Ensure User has Education
$conn->query("DELETE FROM seeker_education WHERE user_id = $user_id");
$conn->query("INSERT INTO seeker_education (user_id, level_id) VALUES ($user_id, 3)"); // 3 = Bachelor

// Ensure User has specific Skills (PHP, Laravel, MySQL) to match the Backend job
$conn->query("DELETE FROM job_seeker_skills WHERE user_id = $user_id");
$target_skills = ['PHP', 'Laravel', 'React', 'MySQL'];
foreach ($target_skills as $ts) {
    $sk_res = $conn->query("SELECT skill_id FROM dic_skills WHERE skill_name = '$ts'");
    if ($sk_res && $sk_res->num_rows > 0) {
        $sk_id = $sk_res->fetch_assoc()['skill_id'];
        $conn->query("INSERT INTO job_seeker_skills (user_id, skill_id, proficiency) VALUES ($user_id, $sk_id, 'Expert')");
    }
}

// --- 3. CREATE APPLICATIONS & PIPELINE ---

// User applies to the Backend Job
$backend_job_id = $job_ids[1];
$conn->query("INSERT INTO applications (job_id, user_id, status, applied_at) VALUES ($backend_job_id, $user_id, 'Interview Scheduled', NOW())");
$app_id_1 = $conn->insert_id;

// Add Interview Schedule (Tomorrow)
$conn->query("INSERT INTO interviews (application_id, employer_id, interview_datetime, status) 
              VALUES ($app_id_1, $emp_id, DATE_ADD(NOW(), INTERVAL 1 DAY), 'scheduled')");

// User applies to Frontend Job (Pending)
$frontend_job_id = $job_ids[0];
$conn->query("INSERT INTO applications (job_id, user_id, status, applied_at) VALUES ($frontend_job_id, $user_id, 'Pending', NOW())");
$app_id_2 = $conn->insert_id;

// --- 4. CREATE MESSAGES (CHAT) ---
$conn->query("INSERT INTO messages (application_id, sender_id, receiver_id, message, created_at) 
              VALUES ($app_id_1, $emp_id, $user_id, 'Hello! We reviewed your profile and would love to interview you for the Backend position tomorrow.', DATE_SUB(NOW(), INTERVAL 2 HOUR))");

$conn->query("INSERT INTO messages (application_id, sender_id, receiver_id, message, created_at) 
              VALUES ($app_id_1, $user_id, $emp_id, 'Thank you! I am looking forward to the interview.', DATE_SUB(NOW(), INTERVAL 1 HOUR))");

// --- 5. CREATE NOTIFICATIONS ---
// For User
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, 'TechNova Solutions has scheduled an interview with you!', 0, NOW())");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, 'You have a new message from TechNova Solutions.', 0, NOW())");

// For Employer
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($emp_id, 'A new candidate has applied to your Backend PHP Engineer position.', 0, NOW())");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($emp_id, 'Demo User replied to your message.', 0, NOW())");

// Generate some random applicants for the Employer's jobs to fill the dashboard
$random_users = [];
$ru_res = $conn->query("SELECT user_id FROM users WHERE role = 'job_seeker' AND user_id != $user_id LIMIT 5");
while($r = $ru_res->fetch_assoc()) { $random_users[] = $r['user_id']; }

foreach ($random_users as $ru) {
    $conn->query("INSERT IGNORE INTO applications (job_id, user_id, status, applied_at) VALUES ($frontend_job_id, $ru, 'Pending', NOW())");
}

echo "Rich demo data successfully generated for employer@gmail.com and user@gmail.com!";
?>
