<?php
include('assets/config/db.php');

$user_res = $conn->query("SELECT user_id FROM users WHERE email = 'user@gmail.com'");
if (!$user_res || $user_res->num_rows == 0) die("User not found.");
$user_id = $user_res->fetch_assoc()['user_id'];

// Create 10 Frontend Jobs by different random employers
$companies = ['MetaTech', 'Skyline Soft', 'Global IT', 'DevCorp', 'NextGen Solutions', 'WebWizards', 'Pixel Perfect', 'CodeCrafters', 'Innovatech', 'RemoteDevs'];

foreach ($companies as $idx => $company) {
    // Check or create employer
    $emp_email = "emp{$idx}@test.com";
    $eres = $conn->query("SELECT user_id FROM users WHERE email = '$emp_email'");
    if ($eres && $eres->num_rows > 0) {
        $emp_id = $eres->fetch_assoc()['user_id'];
    } else {
        $conn->query("INSERT INTO users (full_name, email, role) VALUES ('$company', '$emp_email', 'employer')");
        $emp_id = $conn->insert_id;
        $conn->query("INSERT INTO employer_profiles (user_id, company_name) VALUES ($emp_id, '$company')");
    }

    // Determine Job Type
    $types = ['Full-time', 'Part-time', 'Remote', 'Contract'];
    $job_type = $types[$idx % 4];

    // Insert Job
    $conn->query("INSERT INTO jobs (employer_id, title, job_category, job_type, vacancy, salary, experience_required, status, created_at, application_deadline, district_id) 
                  VALUES ($emp_id, 'Frontend Developer', 'IT & Computer', '$job_type', 1, '50000', '2 Years', 'active', DATE_SUB(NOW(), INTERVAL $idx DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 1)");
    $job_id = $conn->insert_id;

    // Apply for the user
    $statuses = ['Pending', 'Under Review', 'Interview Proposed', 'Interview Scheduled', 'Rejected', 'Selected'];
    $status = $statuses[$idx % 6];
    
    $conn->query("INSERT INTO applications (job_id, user_id, status, applied_at) VALUES ($job_id, $user_id, '$status', DATE_SUB(NOW(), INTERVAL $idx DAY))");
    $app_id = $conn->insert_id;

    // If Interview Scheduled or Proposed
    if ($status == 'Interview Scheduled' || $status == 'Interview Proposed') {
        $conn->query("INSERT INTO interviews (application_id, employer_id, interview_datetime, status) 
                      VALUES ($app_id, $emp_id, DATE_ADD(NOW(), INTERVAL 2 DAY), 'scheduled')");
    }
}

// Generate Varied Notifications
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, '🚀 A new Remote Frontend Developer job was just posted in your preferred category!', 0, NOW())");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, '📅 MetaTech has proposed an interview schedule for you.', 0, DATE_SUB(NOW(), INTERVAL 1 HOUR))");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, '❌ Your application for Frontend Developer at Skyline Soft was unfortunately rejected.', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR))");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, '🎉 Congratulations! You have been Selected for the Frontend Developer position at WebWizards.', 0, DATE_SUB(NOW(), INTERVAL 3 HOUR))");
$conn->query("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($user_id, '📍 3 new IT & Computer jobs have been posted in Dhaka (Your preferred district).', 0, DATE_SUB(NOW(), INTERVAL 5 HOUR))");

echo "10 Frontend Jobs, Applications, and various Notifications successfully injected!";
?>
