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

$profile_check = $conn->query("SELECT profile_id FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");

if (!$profile_check || $profile_check->num_rows == 0) {
    header("Location: profile.php");
    exit();
}

$total_jobs = 0;
$matched_jobs = 0;
$applied_jobs = 0;
$saved_jobs = 0;

$employment_status = 'unemployed';
$employment_remarks = '';

$status_query = $conn->query("
    SELECT current_status, remarks
    FROM employment_status
    WHERE user_id='$user_id'
    LIMIT 1
");

if ($status_query && $status_query->num_rows > 0) {
    $status_data = $status_query->fetch_assoc();
    $employment_status = $status_data['current_status'] ?? 'unemployed';
    $employment_remarks = $status_data['remarks'] ?? '';
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE status='active'");
if ($q) $total_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE user_id='$user_id'");
if ($q) $applied_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM saved_jobs WHERE user_id='$user_id'");
if ($q) $saved_jobs = $q->fetch_assoc()['total'] ?? 0;

$profile_result = $conn->query("
    SELECT district_id 
    FROM job_seeker_profiles 
    WHERE user_id='$user_id' 
    LIMIT 1
");

$profile = ($profile_result && $profile_result->num_rows > 0) ? $profile_result->fetch_assoc() : null;
$user_district_id = $profile['district_id'] ?? 0;

$user_skills_result = $conn->query("
    SELECT skill_name 
    FROM skills 
    WHERE user_id='$user_id'
");

$skill_conditions = [];

if ($user_skills_result && $user_skills_result->num_rows > 0) {
    while ($skill_row = $user_skills_result->fetch_assoc()) {
        $safe_skill = $conn->real_escape_string($skill_row['skill_name']);

        $skill_conditions[] = "
            (
                jobs.title LIKE '%$safe_skill%' 
                OR jobs.description LIKE '%$safe_skill%'
                OR jobs.job_category LIKE '%$safe_skill%'
            )
        ";
    }
}

$matched_condition = "jobs.status='active'";

if (!empty($skill_conditions)) {
    $matched_condition .= " AND (" . implode(" OR ", $skill_conditions) . ")";
}

if (!empty($user_district_id)) {
    $matched_condition .= " OR (jobs.status='active' AND jobs.district_id='$user_district_id')";
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE $matched_condition");
if ($q) $matched_jobs = $q->fetch_assoc()['total'] ?? 0;

$recommended_jobs = $conn->query("
    SELECT jobs.*, users.full_name AS company_name, d.district_name, u.upazila_name
    FROM jobs
    LEFT JOIN users ON jobs.employer_id = users.user_id
    LEFT JOIN districts d ON jobs.district_id = d.district_id
    LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
    WHERE ($matched_condition)
    AND jobs.job_id NOT IN (
        SELECT job_id
        FROM applications
        WHERE user_id='$user_id'
    )
    AND (
        jobs.application_deadline IS NULL
        OR jobs.application_deadline >= CURDATE()
    )
    ORDER BY jobs.job_id DESC
    LIMIT 3
");

$recent_applications = $conn->query("
    SELECT 
        applications.status,
        applications.applied_at,
        jobs.title,
        users.full_name AS company_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    LEFT JOIN users ON jobs.employer_id = users.user_id
    WHERE applications.user_id='$user_id'
    ORDER BY applications.application_id DESC
    LIMIT 5
");

$notifications = $lang === 'bn' ? [
    "চাকরি মিলানোর সুবিধা বাড়াতে আপনার প্রোফাইল এবং দক্ষতা সম্পূর্ণ করুন।",
    "আপনার ড্যাশবোর্ড থেকে সর্বশেষ এলাকাভিত্তিক চাকরি খুঁজুন।",
    "আপনি যদি একটি ছোট ব্যবসা শুরু করতে চান তবে পার্টনার ফাইন্ডার ব্যবহার করুন।"
] : [
    "Complete your profile and skills to improve job matching.",
    "Browse latest area-based jobs from your dashboard.",
    "Use Partner Finder if you want to start a small business."
];

$jt_dash = [
    'bn' => [
        'panel_title' => 'চাকরিপ্রার্থী প্যানেল',
        'dashboard' => 'ড্যাশবোর্ড',
        'browse_jobs' => 'চাকরি খুঁজুন',
        'saved_jobs' => 'সেভ করা চাকরি',
        'my_applications' => 'আমার আবেদনসমূহ',
        'my_skills' => 'আমার দক্ষতা',
        'partner_finder' => 'পার্টনার ফাইন্ডার',
        'my_profile' => 'আমার প্রোফাইল',
        'notifications' => 'বিজ্ঞপ্তি',
        'logout' => 'লগআউট',
        'welcome' => 'স্বাগতম, ',
        'welcome_sub' => 'আপনার দক্ষতা, অবস্থান এবং অভিজ্ঞতার ভিত্তিতে উপযুক্ত চাকরি খুঁজুন।',
        'update_profile' => 'প্রোফাইল আপডেট',
        'total_avail_jobs' => 'মোট উপলব্ধ চাকরি',
        'matched_jobs' => 'মেলা চাকরি',
        'applied_jobs' => 'আবেদনকৃত চাকরি',
        'current_status' => 'বর্তমান কর্মসংস্থান অবস্থা',
        'status_employed' => 'কর্মসংস্থানরত',
        'status_training' => 'প্রশিক্ষণাধীন',
        'status_self_employed' => 'স্ব-কর্মসংস্থান',
        'status_unemployed' => 'বেকার',
        'search_ph' => 'চাকরির শিরোনাম, কোম্পানি, দক্ষতা দিয়ে খুঁজুন...',
        'select_district' => 'জেলা নির্বাচন করুন',
        'any_job_type' => 'যেকোনো চাকরির ধরন',
        'search_btn' => 'খুঁজুন',
        'recommended_jobs' => 'সুপারিশকৃত চাকরি',
        'view_all' => 'সব দেখুন',
        'view_details' => 'বিস্তারিত দেখুন',
        'apply_now' => 'আবেদন করুন',
        'no_recom' => 'কোনো সুপারিশকৃত চাকরি পাওয়া যায়নি',
        'no_recom_sub' => 'উন্নত সুপারিশ পেতে আপনার প্রোফাইল এবং দক্ষতা আপডেট করুন।',
        'see_all' => 'সব দেখুন',
        'recent_apps' => 'সাম্প্রতিক আবেদনসমূহ',
        'th_title' => 'চাকরির নাম',
        'th_company' => 'কোম্পানি',
        'th_date' => 'আবেদনের তারিখ',
        'th_status' => 'অবস্থা',
        'no_recent_apps' => 'কোনো সাম্প্রতিক আবেদন পাওয়া যায়নি।',
    ],
    'en' => [
        'panel_title' => 'Job Seeker Panel',
        'dashboard' => 'Dashboard',
        'browse_jobs' => 'Browse Jobs',
        'saved_jobs' => 'Saved Jobs',
        'my_applications' => 'My Applications',
        'my_skills' => 'My Skills',
        'partner_finder' => 'Partner Finder',
        'my_profile' => 'My Profile',
        'notifications' => 'Notifications',
        'logout' => 'Logout',
        'welcome' => 'Welcome Back, ',
        'welcome_sub' => 'Find suitable jobs based on your skills, location, and experience.',
        'update_profile' => 'Update Profile',
        'total_avail_jobs' => 'Total Available Jobs',
        'matched_jobs' => 'Matched Jobs',
        'applied_jobs' => 'Applied Jobs',
        'current_status' => 'Current Employment Status',
        'status_employed' => 'Employed',
        'status_training' => 'Training',
        'status_self_employed' => 'Self Employed',
        'status_unemployed' => 'Unemployed',
        'search_ph' => 'Search by job title, company, skill...',
        'select_district' => 'Select District',
        'any_job_type' => 'Any Job Type',
        'search_btn' => 'Search',
        'recommended_jobs' => 'Recommended Jobs',
        'view_all' => 'View All',
        'view_details' => 'View Details',
        'apply_now' => 'Apply Now',
        'no_recom' => 'No recommended jobs found',
        'no_recom_sub' => 'Update your profile and skills to get better recommendations.',
        'see_all' => 'See All',
        'recent_apps' => 'Recent Applications',
        'th_title' => 'Job Title',
        'th_company' => 'Company',
        'th_date' => 'Applied Date',
        'th_status' => 'Status',
        'no_recent_apps' => 'No recent applications found.',
    ]
];
$t = $jt_dash[$lang];

$district_translations = [
    'bn' => [
        'Dhaka' => 'ঢাকা', 'Chattogram' => 'চট্টগ্রাম', 'Khulna' => 'খুলনা', 'Rajshahi' => 'রাজশাহী',
        'Barishal' => 'বরিশাল', 'Sylhet' => 'সিলেট', 'Rangpur' => 'রংপুর', 'Mymensingh' => 'ময়মনসিংহ'
    ],
    'en' => [
        'Dhaka' => 'Dhaka', 'Chattogram' => 'Chattogram', 'Khulna' => 'Khulna', 'Rajshahi' => 'Rajshahi',
        'Barishal' => 'Barishal', 'Sylhet' => 'Sylhet', 'Rangpur' => 'Rangpur', 'Mymensingh' => 'Mymensingh'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['dashboard']; ?></title>

    <link rel="stylesheet" href="../assets/css/job_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">
            <h2>JIBIKA</h2>
            <p><?php echo $t['panel_title']; ?></p>
        </div>

        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> <?php echo $t['dashboard']; ?></a></li>
            <li><a href="jobs.php"><i class="fa-solid fa-briefcase"></i> <?php echo $t['browse_jobs']; ?></a></li>
            <li><a href="saved_jobs.php"><i class="fa-solid fa-bookmark"></i> <?php echo $t['saved_jobs']; ?></a></li>
            <li><a href="my_applications.php"><i class="fa-solid fa-file-circle-check"></i> <?php echo $t['my_applications']; ?></a></li>
            <li><a href="skills.php"><i class="fa-solid fa-screwdriver-wrench"></i> <?php echo $t['my_skills']; ?></a></li>
            <li><a href="partner_finder.php"><i class="fa-solid fa-handshake"></i> <?php echo $t['partner_finder']; ?></a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> <?php echo $t['my_profile']; ?></a></li>
            <li><a href="../notifications.php"><i class="fa-solid fa-bell"></i> <?php echo $t['notifications']; ?></a></li>
            <li><a href="/auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> <?php echo $t['logout']; ?></a></li>
            <li style="margin-top: 24px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 15px;">
                <?php if ($lang === 'bn'): ?>
                    <a href="?lang=en" style="font-weight: 600;"><i class="fa-solid fa-globe"></i> English</a>
                <?php else: ?>
                    <a href="?lang=bn" style="font-weight: 600;"><i class="fa-solid fa-globe"></i> বাংলা</a>
                <?php endif; ?>
            </li>
        </ul>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1><?php echo $t['welcome']; ?><?php echo htmlspecialchars($full_name); ?></h1>
                <p><?php echo $t['welcome_sub']; ?></p>
            </div>

            <div class="topbar-actions">
                <a href="jobs.php" class="btn primary-btn"><?php echo $t['browse_jobs']; ?></a>
                <a href="profile.php" class="btn secondary-btn"><?php echo $t['update_profile']; ?></a>
            </div>
        </div>

        <section class="summary-cards">
            <div class="card">
                <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
                <div>
                    <h3><?php echo translateNumber($total_jobs, $lang); ?></h3>
                    <p><?php echo $t['total_avail_jobs']; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-star"></i></div>
                <div>
                    <h3><?php echo translateNumber($matched_jobs, $lang); ?></h3>
                    <p><?php echo $t['matched_jobs']; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-paper-plane"></i></div>
                <div>
                    <h3><?php echo translateNumber($applied_jobs, $lang); ?></h3>
                    <p><?php echo $t['applied_jobs']; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-bookmark"></i></div>
                <div>
                    <h3><?php echo translateNumber($saved_jobs, $lang); ?></h3>
                    <p><?php echo $t['saved_jobs']; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-user-check"></i></div>
                <div>
                    <?php
                    if ($employment_status == 'employed') {
                        echo "<h3 style='color:green;'>" . $t['status_employed'] . "</h3>";
                    } elseif ($employment_status == 'training') {
                        echo "<h3 style='color:orange;'>" . $t['status_training'] . "</h3>";
                    } elseif ($employment_status == 'self_employed') {
                        echo "<h3 style='color:deepskyblue;'>" . $t['status_self_employed'] . "</h3>";
                    } else {
                        echo "<h3 style='color:red;'>" . $t['status_unemployed'] . "</h3>";
                    }
                    ?>
                    <p><?php echo $t['current_status']; ?></p>
                </div>
            </div>
        </section>

        <section class="search-filter-section">
            <form class="search-filter-form" method="GET" action="jobs.php">
                <input type="text" name="search" placeholder="<?php echo $t['search_ph']; ?>">

                <select name="district_id">
                    <option value=""><?php echo $t['select_district']; ?></option>
                    <?php
                    $districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
                    if ($districts && $districts->num_rows > 0) {
                        while ($district = $districts->fetch_assoc()) {
                            $d_name = $district_translations[$lang][$district['district_name']] ?? $district['district_name'];
                            echo "<option value='" . $district['district_id'] . "'>" . htmlspecialchars($d_name) . "</option>";
                        }
                    }
                    ?>
                </select>
                
                <select name="job_type">
                    <option value=""><?php echo $t['any_job_type']; ?></option>
                    <option value="Part-time (Student)"><?php echo $lang=='bn'?'পার্ট-টাইম (ছাত্র)':'Part-time (Student)'; ?></option>
                    <option value="Day Labor"><?php echo $lang=='bn'?'দৈনিক শ্রমিক':'Day Labor'; ?></option>
                    <option value="Full-time"><?php echo $lang=='bn'?'পূর্ণকালীন':'Full-time'; ?></option>
                    <option value="Contract"><?php echo $lang=='bn'?'চুক্তিভিত্তিক':'Contract'; ?></option>
                </select>

                <button type="submit" class="btn primary-btn"><?php echo $t['search_btn']; ?></button>
            </form>
        </section>

        <div class="dashboard-grid">

            <section class="panel large-panel">
                <div class="panel-header">
                    <h2><?php echo $t['recommended_jobs']; ?></h2>
                    <a href="jobs.php"><?php echo $t['view_all']; ?></a>
                </div>

                <div class="job-cards">
                    <?php if ($recommended_jobs && $recommended_jobs->num_rows > 0): ?>
                        <?php while ($job = $recommended_jobs->fetch_assoc()): ?>

                            <?php
                            $match_reason = $lang == 'bn' ? 'সুপারিশকৃত' : 'Recommended';
                            $job_district_id = $job['district_id'] ?? 0;

                            if (!empty($user_district_id) && $job_district_id == $user_district_id) {
                                $match_reason = $lang == 'bn' ? 'এলাকা মিল' : 'Area Match';
                            }

                            $user_skills_result2 = $conn->query("
                                SELECT skill_name
                                FROM skills
                                WHERE user_id='$user_id'
                            ");

                            if ($user_skills_result2 && $user_skills_result2->num_rows > 0) {
                                while ($skill2 = $user_skills_result2->fetch_assoc()) {
                                    $skill_name2 = strtolower(trim($skill2['skill_name']));

                                    if (
                                        $skill_name2 != "" &&
                                        (
                                            strpos(strtolower($job['title'] ?? ''), $skill_name2) !== false
                                            || strpos(strtolower($job['description'] ?? ''), $skill_name2) !== false
                                            || strpos(strtolower($job['job_category'] ?? ''), $skill_name2) !== false
                                        )
                                    ) {
                                        $match_reason = $lang == 'bn' ? 'দক্ষতা মিল' : 'Skill Match';
                                        break;
                                    }
                                }
                            }
                            $job_img = getJobImage($job['title'], $job['job_category'] ?? '');
                            $sal_translated = !empty($job['salary']) ? '৳' . translateNumber(number_format((float)$job['salary']), $lang) : ($lang=='bn'?'আলোচনা সাপেক্ষে':'Negotiable');
                            $loc_translated = htmlspecialchars($district_translations[$lang][$job['district_name']] ?? ($job['location'] ?? 'N/A'));
                            ?>

                            <div class="job-card">
                                <div class="job-cover" style="background-image: url('<?php echo $job_img; ?>');"></div>
                                <div class="job-card-top">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="match-badge"><?php echo $match_reason; ?></span>
                                </div>

                                <p><strong><?php echo $lang=='bn'?'কোম্পানি:':'Company:'; ?></strong> <?php echo htmlspecialchars($job['company_name'] ?? 'N/A'); ?></p>
                                <p><strong><?php echo $lang=='bn'?'অবস্থান:':'Location:'; ?></strong> <?php echo $loc_translated; ?></p>
                                <p><strong><?php echo $lang=='bn'?'বেতন:':'Salary:'; ?></strong> <?php echo $sal_translated; ?></p>

                                <div class="job-card-actions">
                                    <a href="jobs.php" class="btn secondary-btn"><?php echo $t['view_details']; ?></a>
                                    <a href="jobs.php?apply=<?php echo $job['job_id']; ?>" 
                                       class="btn primary-btn" 
                                       onclick="return confirm('<?php echo $lang == 'bn' ? 'এই চাকরির জন্য আবেদন করবেন?' : 'Apply for this job?'; ?>')">
                                        <?php echo $t['apply_now']; ?>
                                    </a>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="job-card">
                            <h3><?php echo $t['no_recom']; ?></h3>
                            <p><?php echo $t['no_recom_sub']; ?></p>
                            <a href="profile.php" class="btn primary-btn"><?php echo $t['update_profile']; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="panel small-panel">
                <div class="panel-header">
                    <h2><?php echo $t['notifications']; ?></h2>
                    <a href="../notifications.php"><?php echo $t['see_all']; ?></a>
                </div>

                <ul class="notification-list">
                    <?php foreach ($notifications as $note): ?>
                        <li><i class="fa-solid fa-bell"></i> <?php echo htmlspecialchars($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="panel full-panel">
                <div class="panel-header">
                    <h2><?php echo $t['recent_apps']; ?></h2>
                    <a href="my_applications.php"><?php echo $t['view_all']; ?></a>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo $t['th_title']; ?></th>
                                <th><?php echo $t['th_company']; ?></th>
                                <th><?php echo $t['th_date']; ?></th>
                                <th><?php echo $t['th_status']; ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>
                                <?php while ($app = $recent_applications->fetch_assoc()): ?>
                                    <?php
                                    $status_raw = $app['status'];
                                    $status_disp = $status_raw;
                                    if ($status_raw == 'Pending') $status_disp = $lang == 'bn' ? 'অপেক্ষমান' : 'Pending';
                                    elseif ($status_raw == 'Accepted') $status_disp = $lang == 'bn' ? 'গৃহীত' : 'Accepted';
                                    elseif ($status_raw == 'Rejected') $status_disp = $lang == 'bn' ? 'বাতিল' : 'Rejected';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['title']); ?></td>
                                        <td><?php echo htmlspecialchars($app['company_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo translateNumber(date('d M Y, h:i A', strtotime($app['applied_at'])), $lang); ?></td>
                                        <td>
                                            <span class="status <?php echo strtolower($status_raw); ?>">
                                                <?php echo htmlspecialchars($status_disp); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4"><?php echo $t['no_recent_apps']; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </main>
</div>

</body>
</html>
