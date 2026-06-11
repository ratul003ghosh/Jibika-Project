<?php
session_start();
require_once("../assets/config/db.php");

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';

if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return str_replace($eng_nums, $bng_nums, (string)$num);
        }
        return $num;
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Job Seeker';

// Process Wizard Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wizard_submit'])) {
    // Create profile if not exists
    $conn->query("INSERT IGNORE INTO job_seeker_profiles (user_id) VALUES ('$user_id')");
    
    if (!empty($_POST['district_id'])) {
        $dist = intval($_POST['district_id']);
        $addr = $conn->real_escape_string($_POST['address'] ?? '');
        $conn->query("UPDATE job_seeker_profiles SET district_id=$dist, address='$addr' WHERE user_id='$user_id'");
    }
    
    if (!empty($_POST['skills'])) {
        $skills_raw = $_POST['skills'];
        $skill_arr = explode(',', $skills_raw);
        foreach ($skill_arr as $sk) {
            $sk = trim($sk);
            if (!empty($sk)) {
                $conn->query("INSERT IGNORE INTO skills (user_id, skill_name) VALUES ('$user_id', '$sk')");
            }
        }
    }
    
    if (!empty($_POST['degree'])) {
        $deg = $conn->real_escape_string($_POST['degree']);
        $inst = $conn->real_escape_string($_POST['institution'] ?? '');
        $conn->query("UPDATE job_seeker_profiles SET degree='$deg', institution='$inst' WHERE user_id='$user_id'");
    }
    
    header("Location: dashboard.php");
    exit();
}

$profile_check = $conn->query("SELECT profile_id FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");
if (!$profile_check || $profile_check->num_rows == 0) {
    $conn->query("INSERT INTO job_seeker_profiles (user_id) VALUES ('$user_id')");
}

// Fetch all districts for the wizard
$all_districts = [];
$d_res = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
if ($d_res) while ($dr = $d_res->fetch_assoc()) $all_districts[] = $dr;


$total_jobs = 0; $matched_jobs = 0; $applied_jobs = 0; $saved_jobs = 0;
$employment_status = 'unemployed';

$status_query = $conn->query("SELECT current_status FROM employment_status WHERE user_id='$user_id' LIMIT 1");
if ($status_query && $status_query->num_rows > 0) {
    $employment_status = $status_query->fetch_assoc()['current_status'] ?? 'unemployed';
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE status='active'");
if ($q) $total_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE user_id='$user_id'");
if ($q) $applied_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM saved_jobs WHERE user_id='$user_id'");
if ($q) $saved_jobs = $q->fetch_assoc()['total'] ?? 0;

$profile_result = $conn->query("SELECT * FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");
$profile = ($profile_result && $profile_result->num_rows > 0) ? $profile_result->fetch_assoc() : null;
$user_district_id = $profile['district_id'] ?? 0;

$user_skills_result = $conn->query("SELECT skill_name FROM skills WHERE user_id='$user_id'");
$skill_conditions = [];
$has_skills = false;
if ($user_skills_result && $user_skills_result->num_rows > 0) {
    $has_skills = true;
    while ($skill_row = $user_skills_result->fetch_assoc()) {
        $safe_skill = $conn->real_escape_string($skill_row['skill_name']);
        $skill_conditions[] = "(jobs.title LIKE '%$safe_skill%' OR jobs.description LIKE '%$safe_skill%' OR jobs.job_category LIKE '%$safe_skill%')";
    }
}

// Calculate Profile Completion
$completion = 0;
$missing_steps = [];

// 1. Basic Info
if (!empty($profile['district_id']) && !empty($profile['address'])) {
    $completion += 20;
} else {
    $missing_steps[] = ['name' => 'Add Address & District', 'link' => 'profile.php#basic-info'];
}

// 2. Skills
if ($has_skills) {
    $completion += 20;
} else {
    $missing_steps[] = ['name' => 'Add Professional Skills', 'link' => 'profile.php#skills'];
}

// 3. Education
if (!empty($profile['degree']) && !empty($profile['institution'])) {
    $completion += 20;
} else {
    $missing_steps[] = ['name' => 'Add Education Details', 'link' => 'profile.php#education'];
}

// 4. Experience
if (!empty($profile['experience_years']) || !empty($profile['company_name'])) {
    $completion += 20;
} else {
    $missing_steps[] = ['name' => 'Add Work Experience', 'link' => 'profile.php#experience'];
}

// 5. Resume & Photo
if (!empty($profile['cv_file']) && !empty($profile['profile_photo'])) {
    $completion += 20;
} elseif (empty($profile['cv_file'])) {
    $missing_steps[] = ['name' => 'Upload CV/Resume', 'link' => 'profile.php#documents'];
} else {
    $missing_steps[] = ['name' => 'Upload Profile Photo', 'link' => 'profile.php#documents'];
}

// Matched = jobs that match user's skills OR are in user's district
$skill_match = "";
if (!empty($skill_conditions)) {
    $skill_match = "(jobs.status='active' AND (" . implode(" OR ", $skill_conditions) . "))";
}
$district_match = "";
if (!empty($user_district_id)) {
    $district_match = "(jobs.status='active' AND jobs.district_id='$user_district_id')";
}

// Combine with OR, wrapped properly
$has_profile_data = false;
if ($skill_match && $district_match) {
    $matched_condition = "($skill_match OR $district_match)";
    $has_profile_data = true;
} elseif ($skill_match) {
    $matched_condition = $skill_match;
    $has_profile_data = true;
} elseif ($district_match) {
    $matched_condition = $district_match;
    $has_profile_data = true;
} else {
    $matched_condition = "1=0"; // fallback: don't show random jobs if no profile data
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE $matched_condition");
if ($q) $matched_jobs = $q->fetch_assoc()['total'] ?? 0;

$recommended_jobs = $conn->query("
    SELECT jobs.*, users.full_name AS company_name, d.district_name
    FROM jobs
    LEFT JOIN users ON jobs.employer_id = users.user_id
    LEFT JOIN districts d ON jobs.district_id = d.district_id
    WHERE ($matched_condition)
    AND jobs.job_id NOT IN (SELECT job_id FROM applications WHERE user_id='$user_id')
    AND (jobs.application_deadline IS NULL OR jobs.application_deadline >= CURDATE())
    ORDER BY jobs.job_id DESC LIMIT 3
");

$recent_applications = $conn->query("
    SELECT applications.status, applications.applied_at, jobs.title, users.full_name AS company_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    LEFT JOIN users ON jobs.employer_id = users.user_id
    WHERE applications.user_id='$user_id'
    ORDER BY applications.application_id DESC LIMIT 5
");

// Notifications removed from dashboard

$dash_t = [
    'bn' => [
        'panel' => 'চাকরিপ্রার্থী প্যানেল',
        'welcome' => 'স্বাগতম, ',
        'welcome_sub' => 'আপনার দক্ষতা ও অবস্থানের ভিত্তিতে চাকরি খুঁজুন',
        'total' => 'মোট চাকরি', 'matched' => 'মেলা চাকরি', 'applied' => 'আবেদনকৃত', 'saved' => 'সেভ করা',
        'status' => 'অবস্থা', 's_emp' => 'কর্মরত', 's_train' => 'প্রশিক্ষণ', 's_self' => 'স্ব-কর্ম', 's_unemp' => 'কাজ খুঁজছি',
        'search_ph' => 'চাকরি খুঁজুন...', 'select_dist' => 'জেলা', 'any_type' => 'যেকোনো',
        'search_btn' => 'খুঁজুন', 'recom' => 'সুপারিশকৃত চাকরি', 'view_all' => 'সব দেখুন →',
        'apply' => 'আবেদন', 'recent' => 'সাম্প্রতিক আবেদন',
        'th_job' => 'চাকরি', 'th_comp' => 'কোম্পানি', 'th_date' => 'তারিখ', 'th_status' => 'অবস্থা',
        'no_jobs' => 'কোনো চাকরি পাওয়া যায়নি',
        'no_jobs_missing' => 'আপনার প্রোফাইলের তথ্যের ওপর ভিত্তি করে চাকরি দেখতে অনুগ্রহ করে আপনার প্রোফাইল সম্পূর্ণ করুন।',
        'prof_comp' => 'প্রোফাইল সম্পন্নকরণ',
        'prof_sub' => 'আরও ভালো চাকরির সুপারিশ পেতে এই বাধ্যতামূলক ধাপগুলি সম্পূর্ণ করুন।',
        'btn_complete' => 'প্রোফাইল সম্পূর্ণ করুন',
        'next_step' => 'পরবর্তী ধাপ:',
        'complete_now' => 'এখনই সম্পূর্ণ করুন'
    ],
    'en' => [
        'panel' => 'Job Seeker Dashboard',
        'welcome' => 'Welcome back, ',
        'welcome_sub' => 'Find jobs based on your skills and location',
        'total' => 'Available Jobs', 'matched' => 'Matched Jobs', 'applied' => 'Applied', 'saved' => 'Saved',
        'status' => 'Status', 's_emp' => 'Employed', 's_train' => 'Training', 's_self' => 'Self Employed', 's_unemp' => 'Seeking Opportunities',
        'search_ph' => 'Search jobs...', 'select_dist' => 'District', 'any_type' => 'Any Type',
        'search_btn' => 'Search', 'recom' => 'Recommended Jobs', 'view_all' => 'View All →',
        'apply' => 'Apply', 'recent' => 'Recent Applications', 'notifs' => 'Notifications',
        'th_job' => 'Job', 'th_comp' => 'Company', 'th_date' => 'Date', 'th_status' => 'Status',
        'no_jobs' => 'No recommended jobs found',
        'no_jobs_missing' => 'Please complete your profile with mandatory data to see recommended jobs based on your profile.',
        'prof_comp' => 'Profile Completion',
        'prof_sub' => 'Complete these mandatory steps to get better job recommendations.',
        'btn_complete' => 'Complete Profile',
        'next_step' => 'Next Step to Complete:',
        'complete_now' => 'Complete Now'
    ]
][$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jibika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    /* ═══ ALL CSS INLINE TO GUARANTEE IT LOADS ═══ */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body { font-family: 'Inter', sans-serif !important; background: #F3F4F6 !important; }

    /* HERO */
    .dash-hero { background: #006A4E !important; color: #fff; padding: 44px 0 80px 0; }
    .dash-hero .badge-label {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
        color: #fff; padding: 5px 14px; border-radius: 999px;
        font-size: 12px; font-weight: 600; letter-spacing: 0.5px;
        text-transform: uppercase; margin-bottom: 14px;
    }
    .dash-hero h1 { font-size: 28px; font-weight: 700; color: #fff; margin-bottom: 6px; }
    .dash-hero p { color: rgba(255,255,255,0.8); font-size: 15px; margin: 0; }

    /* KPI CARDS */
    .kpi-row { margin-top: -40px; position: relative; z-index: 10; }
    .kpi-card {
        background: #fff !important; border: 1px solid #E5E7EB !important;
        border-radius: 12px !important; padding: 18px 20px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04) !important;
        display: flex !important; align-items: center !important; gap: 14px !important;
        position: relative; overflow: hidden; height: 100%;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .kpi-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; transform: translateY(-2px); }
    .kpi-card::before {
        content: ""; position: absolute; left: 0; top: 0; bottom: 0;
        width: 4px; border-radius: 12px 0 0 12px;
    }
    .kpi-card.kpi-green::before  { background: #10B981; }
    .kpi-card.kpi-blue::before   { background: #3B82F6; }
    .kpi-card.kpi-purple::before { background: #8B5CF6; }
    .kpi-card.kpi-amber::before  { background: #F59E0B; }
    .kpi-card.kpi-red::before    { background: #EF4444; }

    .kpi-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .kpi-green  .kpi-icon { background: #D1FAE5; color: #059669; }
    .kpi-blue   .kpi-icon { background: #DBEAFE; color: #2563EB; }
    .kpi-purple .kpi-icon { background: #EDE9FE; color: #7C3AED; }
    .kpi-amber  .kpi-icon { background: #FEF3C7; color: #D97706; }
    .kpi-red    .kpi-icon { background: #FEE2E2; color: #EF4444; }

    .kpi-body h3 { font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 2px 0; line-height: 1.1; }
    .kpi-body p { font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.3px; margin: 0; }

    .kpi-badge-status {
        background: #FFF7ED; color: #C2410C; padding: 4px 10px; border-radius: 6px;
        font-size: 13px; font-weight: 600; display: inline-block; border: 1px solid #FFEDD5;
    }

    /* SEARCH */
    .search-bar {
        background: #fff; border: 1px solid #E5E7EB; border-radius: 12px;
        padding: 14px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-top: 20px; margin-bottom: 24px;
    }
    .search-bar .form-control, .search-bar .form-select {
        height: 42px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; background: #F9FAFB;
    }
    .search-bar .form-control:focus, .search-bar .form-select:focus {
        border-color: #006A4E; box-shadow: 0 0 0 3px rgba(0,106,78,0.12); background: #fff;
    }
    .search-bar .btn-search {
        height: 42px; background: #006A4E; color: #fff; border: none; border-radius: 8px;
        font-weight: 600; font-size: 14px; padding: 0 20px; cursor: pointer;
    }
    .search-bar .btn-search:hover { background: #00563F; }

    /* PANELS */
    .dash-panel {
        background: #fff !important; border: 1px solid #E5E7EB !important;
        border-radius: 12px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        overflow: hidden; margin-bottom: 24px;
    }
    .dash-panel-header {
        padding: 18px 24px; border-bottom: 1px solid #E5E7EB;
        display: flex; justify-content: space-between; align-items: center; background: #F9FAFB;
    }
    .dash-panel-header h2 { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
    .dash-panel-header a { font-size: 14px; font-weight: 500; color: #006A4E; text-decoration: none; }
    .dash-panel-header a:hover { text-decoration: underline; }

    /* JOB ITEMS */
    .job-item {
        display: flex; align-items: center; padding: 20px 24px;
        border-bottom: 1px solid #F3F4F6; transition: background 0.15s;
    }
    .job-item:last-child { border-bottom: none; }
    .job-item:hover { background: #F9FAFB; }
    .job-img {
        width: 52px; height: 52px; border-radius: 8px; background-size: cover;
        background-position: center; border: 1px solid #E5E7EB; flex-shrink: 0; margin-right: 16px;
    }
    .job-text { flex: 1; min-width: 0; }
    .job-text h4 { font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 3px 0; }
    .job-text .company { font-size: 14px; color: #6B7280; margin-bottom: 6px; }
    .job-meta-row { display: flex; flex-wrap: wrap; gap: 14px; font-size: 13px; color: #6B7280; }
    .job-meta-row i { color: #9CA3AF; margin-right: 4px; }
    .job-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; margin-left: 16px; }
    .badge-match {
        background: #FEFCE8; color: #A16207; padding: 3px 8px; border-radius: 4px;
        font-size: 11px; font-weight: 600; border: 1px solid #FEF08A;
    }
    .btn-apply {
        background: #006A4E; color: #fff !important; border: none; border-radius: 6px;
        font-size: 13px; font-weight: 600; padding: 7px 16px; text-decoration: none !important;
    }
    .btn-apply:hover { background: #00563F; color: #fff !important; }

    /* TABLE */
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table th {
        text-align: left; padding: 12px 24px; font-size: 12px; font-weight: 600;
        color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;
        background: #F9FAFB; border-bottom: 1px solid #E5E7EB;
    }
    .dash-table td { padding: 14px 24px; font-size: 14px; color: #374151; border-bottom: 1px solid #F3F4F6; }
    .dash-table tr:last-child td { border-bottom: none; }
    .dash-table tr:hover td { background: #F9FAFB; }

    .pill { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-block; }
    .pill-pending   { background: #FEF3C7; color: #B45309; }
    .pill-accepted  { background: #D1FAE5; color: #15803D; }
    .pill-rejected  { background: #FEE2E2; color: #B91C1C; }
    .pill-review    { background: #DBEAFE; color: #1E40AF; }
    .pill-interview { background: #EDE9FE; color: #6D28D9; }

    @media (max-width: 768px) {
        .job-item { flex-direction: column; align-items: flex-start; }
        .job-actions { flex-direction: row; width: 100%; margin-left: 0; margin-top: 12px; }
    }
    </style>
</head>
<body>

<?php include('../includes/navbar.php'); ?>

<!-- HERO -->
<div class="dash-hero">
    <div class="container">
        <div class="badge-label"><i class="fa-solid fa-briefcase"></i> <?php echo $dash_t['panel']; ?></div>
        <h1><?php echo $dash_t['welcome']; ?><?php echo htmlspecialchars($full_name); ?></h1>
        <p><?php echo $dash_t['welcome_sub']; ?></p>
    </div>
</div>

<!-- KPI CARDS -->
<div class="container kpi-row">
    <div class="row g-3">
        <div class="col"><div class="kpi-card kpi-green">
            <div class="kpi-icon"><i class="fa-solid fa-suitcase"></i></div>
            <div class="kpi-body"><h3><?php echo translateNumber($total_jobs, $lang); ?></h3><p><?php echo $dash_t['total']; ?></p></div>
        </div></div>
        <div class="col"><div class="kpi-card kpi-blue">
            <div class="kpi-icon"><i class="fa-solid fa-bullseye"></i></div>
            <div class="kpi-body"><h3><?php echo translateNumber($matched_jobs, $lang); ?></h3><p><?php echo $dash_t['matched']; ?></p></div>
        </div></div>
        <div class="col"><div class="kpi-card kpi-purple">
            <div class="kpi-icon"><i class="fa-solid fa-paper-plane"></i></div>
            <div class="kpi-body"><h3><?php echo translateNumber($applied_jobs, $lang); ?></h3><p><?php echo $dash_t['applied']; ?></p></div>
        </div></div>
        <div class="col"><div class="kpi-card kpi-amber">
            <div class="kpi-icon"><i class="fa-solid fa-bookmark"></i></div>
            <div class="kpi-body"><h3><?php echo translateNumber($saved_jobs, $lang); ?></h3><p><?php echo $dash_t['saved']; ?></p></div>
        </div></div>
        <div class="col"><div class="kpi-card kpi-red">
            <div class="kpi-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="kpi-body">
                <?php
                    if ($employment_status == 'employed') echo "<span class='kpi-badge-status' style='background:#D1FAE5;color:#15803D;border-color:#A7F3D0;'>{$dash_t['s_emp']}</span>";
                    elseif ($employment_status == 'training') echo "<span class='kpi-badge-status' style='background:#DBEAFE;color:#1E40AF;border-color:#BFDBFE;'>{$dash_t['s_train']}</span>";
                    elseif ($employment_status == 'self_employed') echo "<span class='kpi-badge-status' style='background:#FEF3C7;color:#B45309;border-color:#FDE68A;'>{$dash_t['s_self']}</span>";
                    else echo "<span class='kpi-badge-status'>{$dash_t['s_unemp']}</span>";
                ?>
                <p style="margin-top:6px;"><?php echo $dash_t['status']; ?></p>
            </div>
        </div></div>
    </div>
</div>

<!-- SEARCH -->
<div class="container">
    <div class="search-bar">
        <form class="d-flex gap-2" method="GET" action="jobs.php">
            <input type="text" class="form-control" name="search" placeholder="<?php echo $dash_t['search_ph']; ?>">
            <select class="form-select" name="district_id" style="max-width:200px;">
                <option value=""><?php echo $dash_t['select_dist']; ?></option>
                <?php
                $districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
                if ($districts && $districts->num_rows > 0) {
                    while ($dist = $districts->fetch_assoc()) {
                        echo "<option value='{$dist['district_id']}'>".htmlspecialchars($dist['district_name'])."</option>";
                    }
                }
                ?>
            </select>
            <select class="form-select" name="job_type" style="max-width:160px;">
                <option value=""><?php echo $dash_t['any_type']; ?></option>
                <option value="Part-time">Part-time</option>
                <option value="Full-time">Full-time</option>
                <option value="Contract">Contract</option>
            </select>
            <button type="submit" class="btn-search"><?php echo $dash_t['search_btn']; ?></button>
        </form>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-12">
            <!-- PROFILE COMPLETION WIDGET -->
            <?php if ($completion < 100): ?>
            <div class="dash-panel" style="border-left: 4px solid #F59E0B;">
                <div style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 4px;"><i class="fa-solid fa-bullseye text-warning me-2"></i> <?php echo $dash_t['prof_comp'] ?? 'Profile Completion'; ?> (<?php echo $completion; ?>%)</h3>
                            <p style="font-size: 13px; color: #6B7280; margin: 0;"><?php echo $dash_t['prof_sub'] ?? 'Complete these mandatory steps to get better job recommendations.'; ?></p>
                        </div>
                        <a href="profile.php" class="btn btn-primary btn-sm" style="background:#006A4E;border:none;border-radius:6px;font-weight:600;"><?php echo $dash_t['btn_complete'] ?? 'Complete Profile'; ?></a>
                    </div>
                    
                    <div class="progress mb-4" style="height: 8px; border-radius: 4px; background: #E5E7EB;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $completion; ?>%;" aria-valuenow="<?php echo $completion; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <?php if (count($missing_steps) > 0): ?>
                    <!-- WIZARD UI: Google Form Style -->
                    <div style="background: white; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <div style="background: #006A4E; color: white; padding: 16px 24px;">
                            <h4 style="margin: 0; font-size: 16px; font-weight: 600;">Complete Your Profile Setup</h4>
                        </div>
                        
                        <form method="POST" action="dashboard.php" id="wizardForm" style="padding: 24px;">
                            <input type="hidden" name="wizard_submit" value="1">
                            
                            <!-- Step 1: Basic Info -->
                            <div class="wizard-step" id="step-1" <?php if(!empty($profile['district_id'])) echo 'style="display:none;"'; ?>>
                                <h5 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 16px;">Step 1: Where are you located?</h5>
                                <div class="mb-3">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">District *</label>
                                    <select name="district_id" class="form-select" required>
                                        <option value="">Select District</option>
                                        <?php foreach ($all_districts as $d): ?>
                                            <option value="<?php echo $d['district_id']; ?>"><?php echo $d['district_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Full Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="e.g. House 12, Road 5, Block B">
                                </div>
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)" style="background: #006A4E; border: none; padding: 10px 24px; font-weight: 600;">Next Step <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                            
                            <!-- Step 2: Skills -->
                            <div class="wizard-step" id="step-2" <?php if(empty($profile['district_id']) || $has_skills) echo 'style="display:none;"'; ?>>
                                <h5 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 16px;">Step 2: Add Professional Skills</h5>
                                <div class="mb-4">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Skills (Comma Separated) *</label>
                                    <input type="text" name="skills" class="form-control" placeholder="e.g. PHP, HTML, Graphic Design, Driving" required>
                                    <small style="color: #6B7280; margin-top: 6px; display: block;">Adding relevant skills unlocks personalized job recommendations.</small>
                                </div>
                                <button type="button" class="btn btn-secondary me-2" onclick="prevStep(1)" style="font-weight: 600;">Back</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)" style="background: #006A4E; border: none; padding: 10px 24px; font-weight: 600;">Next Step <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                            
                            <!-- Step 3: Education -->
                            <div class="wizard-step" id="step-3" <?php if(!$has_skills || !empty($profile['degree'])) echo 'style="display:none;"'; ?>>
                                <h5 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 16px;">Step 3: Education Details</h5>
                                <div class="mb-3">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Highest Degree *</label>
                                    <input type="text" name="degree" class="form-control" placeholder="e.g. BSc in Computer Science, SSC" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Institution</label>
                                    <input type="text" name="institution" class="form-control" placeholder="e.g. Dhaka University">
                                </div>
                                <button type="button" class="btn btn-secondary me-2" onclick="prevStep(2)" style="font-weight: 600;">Back</button>
                                <button type="submit" class="btn btn-primary" style="background: #006A4E; border: none; padding: 10px 24px; font-weight: 600;">Submit Profile <i class="fa-solid fa-check ms-2"></i></button>
                            </div>
                            
                        </form>
                        
                        <script>
                            function nextStep(step) {
                                document.querySelectorAll('.wizard-step').forEach(el => el.style.display = 'none');
                                document.getElementById('step-' + step).style.display = 'block';
                            }
                            function prevStep(step) {
                                document.querySelectorAll('.wizard-step').forEach(el => el.style.display = 'none');
                                document.getElementById('step-' + step).style.display = 'block';
                            }
                        </script>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- RECOMMENDED JOBS -->
            <div class="dash-panel">
        <div class="dash-panel-header">
            <h2><i class="fa-solid fa-star" style="color:#F59E0B;margin-right:8px;"></i><?php echo $dash_t['recom']; ?></h2>
            <a href="jobs.php"><?php echo $dash_t['view_all']; ?></a>
        </div>
        <?php if ($recommended_jobs && $recommended_jobs->num_rows > 0): ?>
            <?php while ($job = $recommended_jobs->fetch_assoc()): ?>
                <?php
                    $loc = htmlspecialchars($job['district_name'] ?? 'N/A');
                    $sal = !empty($job['salary']) ? '৳'.translateNumber(number_format((float)$job['salary']), $lang) : 'Negotiable';
                    $type = $job['job_type'] ?? 'Contract';
                    $job_img = getJobImage($job['title'], $job['job_category'] ?? '');
                ?>
                <div class="job-item">
                    <div class="job-img" style="background-image:url('<?php echo $job_img; ?>');"></div>
                    <div class="job-text">
                        <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                        <div class="company"><?php echo htmlspecialchars($job['company_name'] ?? 'N/A'); ?></div>
                        <div class="job-meta-row">
                            <span><i class="fa-solid fa-location-dot"></i><?php echo $loc; ?></span>
                            <span><i class="fa-solid fa-sack-dollar"></i><?php echo $sal; ?></span>
                            <span><i class="fa-solid fa-briefcase"></i><?php echo $type; ?></span>
                        </div>
                    </div>
                    <div class="job-actions">
                        <span class="badge-match"><i class="fa-solid fa-bolt"></i> Skill Match</span>
                        <a href="jobs.php?apply=<?php echo $job['job_id']; ?>" class="btn-apply"><?php echo $dash_t['apply']; ?></a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="padding:32px 24px; color:#6B7280; text-align: center; background: #F9FAFB; border-radius: 8px; margin: 20px; border: 1px dashed #D1D5DB;">
                <i class="fa-solid <?php echo $has_profile_data ? 'fa-folder-open' : 'fa-clipboard-user'; ?>" style="font-size: 32px; color: #9CA3AF; margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 15px; margin: 0; font-weight: 500;">
                    <?php echo $has_profile_data ? $dash_t['no_jobs'] : $dash_t['no_jobs_missing']; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- RECENT APPLICATIONS -->
    <div class="dash-panel">
        <div class="dash-panel-header">
            <h2><i class="fa-solid fa-clock-rotate-left" style="color:#3B82F6;margin-right:8px;"></i><?php echo $dash_t['recent']; ?></h2>
            <a href="my_applications.php"><?php echo $dash_t['view_all']; ?></a>
        </div>
        <div class="table-responsive">
            <table class="dash-table">
                <thead><tr>
                    <th><?php echo $dash_t['th_job']; ?></th>
                    <th><?php echo $dash_t['th_comp']; ?></th>
                    <th><?php echo $dash_t['th_date']; ?></th>
                    <th><?php echo $dash_t['th_status']; ?></th>
                </tr></thead>
                <tbody>
                    <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>
                        <?php while ($app = $recent_applications->fetch_assoc()): ?>
                            <?php
                                $sr = $app['status']; $sc = strtolower(str_replace(' ','-',$sr));
                                $pc = 'pill-pending';
                                if ($sc=='selected'||$sc=='accepted') $pc='pill-accepted';
                                elseif ($sc=='rejected') $pc='pill-rejected';
                                elseif (str_contains($sc,'review')) $pc='pill-review';
                                elseif (str_contains($sc,'interview')) $pc='pill-interview';
                            ?>
                            <tr>
                                <td style="font-weight:600;"><?php echo htmlspecialchars($app['title']); ?></td>
                                <td><?php echo htmlspecialchars($app['company_name'] ?? '—'); ?></td>
                                <td style="color:#6B7280;"><?php echo translateNumber(date('d M Y', strtotime($app['applied_at'])), $lang); ?></td>
                                <td><span class="pill <?php echo $pc; ?>"><?php echo htmlspecialchars($sr); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="padding:24px;color:#6B7280;">No recent applications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

</body>
</html>