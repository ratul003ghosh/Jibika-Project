<?php
include('assets/config/db.php');

$first_names_male = ['Rahim', 'Karim', 'Tariq', 'Faisal', 'Jamil', 'Hasan', 'Mahmud', 'Arif', 'Kamrul', 'Shafiq', 'Zahir', 'Tanvir', 'Imran', 'Rubel', 'Nazmul', 'Sajid', 'Farhan', 'Riyad', 'Ashiq', 'Mehedi', 'Anis', 'Rakib', 'Sohel', 'Liton', 'Babu', 'Monir'];
$first_names_female = ['Sumi', 'Rina', 'Farhana', 'Nusrat', 'Sadia', 'Tania', 'Mitu', 'Shila', 'Jahanara', 'Ayesha', 'Fatema', 'Salma', 'Shabnam', 'Mimi', 'Liza', 'Sharmin', 'Nadia'];
$last_names = ['Rahman', 'Ahmed', 'Islam', 'Hossain', 'Chowdhury', 'Haque', 'Khan', 'Sikder', 'Miah', 'Talukder', 'Das', 'Roy', 'Saha', 'Majumder', 'Kazi', 'Mirza', 'Bhuiyan', 'Hasan', 'Ali'];

// Update all job_seekers
$q = $conn->query("SELECT user_id, full_name, email FROM users WHERE role = 'job_seeker'");
while($row = $q->fetch_assoc()) {
    $is_female = in_array(explode(' ', $row['full_name'])[0], $first_names_female) || (rand(0,1) == 1 && rand(0,3) == 0); // Keep original gender bias or add randomness
    
    if ($is_female) {
        $first = $first_names_female[array_rand($first_names_female)];
    } else {
        $first = $first_names_male[array_rand($first_names_male)];
    }
    
    $last = $last_names[array_rand($last_names)];
    $new_name = $first . ' ' . $last;
    
    // Check if testseeker
    if ($row['email'] == 'testseeker@example.com') {
        $new_name = 'Test Seeker';
    }
    
    $uid = $row['user_id'];
    $conn->query("UPDATE users SET full_name = '$new_name' WHERE user_id = $uid");
}

// Update employers
$q = $conn->query("SELECT user_id, full_name, email FROM users WHERE role = 'employer'");
while($row = $q->fetch_assoc()) {
    $first = $first_names_male[array_rand($first_names_male)];
    $last = $last_names[array_rand($last_names)];
    $new_name = $first . ' ' . $last;
    
    if ($row['email'] == 'testemployer@example.com') {
        $new_name = 'Test Employer';
    } elseif ($row['email'] == 'ratu22l@gmail.com') {
        $new_name = 'Ratul Ghosh';
    }
    
    $uid = $row['user_id'];
    $conn->query("UPDATE users SET full_name = '$new_name' WHERE user_id = $uid");
}

echo "Names updated successfully with diverse Bangladeshi names.";
?>
