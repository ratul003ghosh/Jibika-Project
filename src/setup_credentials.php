<?php
include('assets/config/db.php');

$accounts = [
    [
        'name' => 'System Admin',
        'email' => 'admin@gmail.com',
        'password' => '12345',
        'role' => 'admin'
    ],
    [
        'name' => 'Demo User',
        'email' => 'user@gmail.com',
        'password' => '1234',
        'role' => 'job_seeker'
    ],
    [
        'name' => 'Demo Employer',
        'email' => 'employer@gmail.com',
        'password' => '1234',
        'role' => 'employer'
    ]
];

foreach ($accounts as $acc) {
    $email = $conn->real_escape_string($acc['email']);
    $hash = password_hash($acc['password'], PASSWORD_DEFAULT);
    
    // Check if user exists
    $res = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
    if ($res && $res->num_rows > 0) {
        $uid = $res->fetch_assoc()['user_id'];
        $conn->query("UPDATE users SET password = '$hash', role = '{$acc['role']}' WHERE user_id = $uid");
    } else {
        $conn->query("INSERT INTO users (full_name, email, password, role) VALUES ('{$acc['name']}', '$email', '$hash', '{$acc['role']}')");
        $uid = $conn->insert_id;
    }
    
    // Ensure profile tables are populated to prevent crashes
    if ($acc['role'] == 'employer') {
        $conn->query("INSERT IGNORE INTO employer_profiles (user_id, company_name, company_description) VALUES ($uid, 'Demo Corporation', 'We are a top company.')");
    } elseif ($acc['role'] == 'job_seeker') {
        $conn->query("INSERT IGNORE INTO job_seeker_profiles (user_id, experience_years) VALUES ($uid, 2)");
    }
}

echo "Accounts successfully created/updated!";
?>
