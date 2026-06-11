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

$skills_check = $conn->query("SELECT skill_id FROM skills WHERE user_id='$user_id' LIMIT 1");
if (!$skills_check || $skills_check->num_rows == 0) {
    header("Location: skills.php?msg=mandatory");
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
} else {
    $matched_condition .= " AND 1=0";
}

if (!empty($user_district_id)) {
    $matched_condition .= " AND jobs.district_id='$user_district_id'";
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

<?php
include('../includes/header.php');
include('../includes/navbar.php');
$t = $jt_dash[$lang]; // Restore dashboard translations because navbar.php overwrites $t
?>

<link rel="stylesheet" href="../assets/css/jobseeker_dashboard.css">

<!-- Hero -->
<div class="dash-hero text-center">
    <div class="container px-4">
        <span style="background:rgba(255,255,255,0.15); border-radius:50px; padding:5px 18px; font-size:0.82rem; font-weight:700; color:#fff; letter-spacing:1px; text-transform:uppercase; border:1px solid rgba(255,255,255,0.25); display:inline-block; margin-bottom:16px;">
            <i class="fa-solid fa-user me-2" style="color:#a7f3d0;"></i>
            <?php echo $t['panel_title']; ?>
        </span>
        <h1><?php echo $t['welcome'] . htmlspecialchars($full_name); ?></h1>
        <p><?php echo $t['welcome_sub']; ?></p>
    </div>
</div>

<div class="container px-3 px-lg-4 dash-wrap">

    <!-- Summary Cards -->
    <div class="summary-cards-grid">
        <div class="summary-card">
            <div class="icon-circle"><i class="fa-solid fa-briefcase"></i></div>
            <div>
                <h4><?php echo translateNumber($total_jobs, $lang); ?></h4>
                <p><?php echo $t['total_avail_jobs']; ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon-circle"><i class="fa-solid fa-star"></i></div>
            <div>
                <h4><?php echo translateNumber($matched_jobs, $lang); ?></h4>
                <p><?php echo $t['matched_jobs']; ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon-circle"><i class="fa-solid fa-paper-plane"></i></div>
            <div>
                <h4><?php echo translateNumber($applied_jobs, $lang); ?></h4>
                <p><?php echo $t['applied_jobs']; ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon-circle"><i class="fa-solid fa-bookmark"></i></div>
            <div>
                <h4><?php echo translateNumber($saved_jobs, $lang); ?></h4>
                <p><?php echo $t['saved_jobs']; ?></p>
            </div>
        </div>

        <div class="summary-card">
            <div class="icon-circle"><i class="fa-solid fa-user-check"></i></div>
            <div>
                <?php
                if ($employment_status == 'employed') {
                    echo "<h4 class='status-text' style='color:#059669;'>" . $t['status_employed'] . "</h4>";
                } elseif ($employment_status == 'training') {
                    echo "<h4 class='status-text' style='color:#d97706;'>" . $t['status_training'] . "</h4>";
                } elseif ($employment_status == 'self_employed') {
                    echo "<h4 class='status-text' style='color:#0284c7;'>" . $t['status_self_employed'] . "</h4>";
                } else {
                    echo "<h4 class='status-text' style='color:#dc2626;'>" . $t['status_unemployed'] . "</h4>";
                }
                ?>
                <p><?php echo $t['current_status']; ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Search Filter Section -->
    <div class="quick-search-card">
        <form class="quick-search-form" method="GET" action="jobs.php">
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

            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold hover-btn" style="background-color:#006a4e; border:none; height:45px;">
                <i class="fa-solid fa-magnifying-glass me-1"></i> <?php echo $t['search_btn']; ?>
            </button>
        </form>
    </div>

    <div class="row g-4">
        <!-- Recommended Jobs Column -->
        <div class="col-12 col-lg-8">
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-star"></i> <?php echo $t['recommended_jobs']; ?></h2>
                    <a href="jobs.php"><?php echo $t['view_all']; ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="recommended-job-list">
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

                            <div class="recommended-job-item">
                                <div class="job-img-box" style="background-image: url('<?php echo $job_img; ?>');"></div>
                                <div class="recommended-job-info">
                                    <div class="recommended-job-title"><?php echo htmlspecialchars(translateJobTitle($job['title'] ?? '', $lang)); ?></div>
                                    <div style="font-size: 0.85rem; color: #475569; font-weight: 600; margin-bottom: 4px;"><?php echo htmlspecialchars($job['company_name'] ?? 'N/A'); ?></div>
                                    <div class="recommended-job-meta">
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo $loc_translated; ?></span>
                                        <span><i class="fa-solid fa-bangladeshi-taka-sign"></i> <?php echo $sal_translated; ?></span>
                                        <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                                    </div>
                                    <span class="match-tag"><?php echo $match_reason; ?></span>
                                </div>
                                <div class="recommended-job-actions">
                                    <a href="jobs.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold hover-btn"><?php echo $t['view_details']; ?></a>
                                    <a href="jobs.php?apply=<?php echo $job['job_id']; ?>" 
                                       class="btn btn-success btn-sm rounded-pill px-3 fw-bold hover-btn"
                                       style="background-color:#006a4e; border:none;"
                                       onclick="return confirm('<?php echo $lang == 'bn' ? 'এই চাকরির জন্য আবেদন করবেন?' : 'Apply for this job?'; ?>')">
                                        <?php echo $t['apply_now']; ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa-solid fa-circle-exclamation mb-3" style="font-size:2.5rem; color:#94a3b8;"></i>
                            <h5 class="fw-bold text-dark mb-1"><?php echo $t['no_recom']; ?></h5>
                            <p class="text-muted small mb-3"><?php echo $t['no_recom_sub']; ?></p>
                            <a href="profile.php" class="btn btn-success btn-sm rounded-pill px-4 fw-bold hover-btn" style="background-color:#006a4e; border:none;"><?php echo $t['update_profile']; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications Column -->
        <div class="col-12 col-lg-4">
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-bell"></i> <?php echo $t['notifications']; ?></h2>
                    <a href="#"><?php echo $t['see_all']; ?></a>
                </div>

                <ul class="dash-notifications">
                    <?php foreach ($notifications as $note): ?>
                        <li><i class="fa-solid fa-bell"></i> <?php echo htmlspecialchars($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Recent Applications Table Column -->
        <div class="col-12">
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-history"></i> <?php echo $t['recent_apps']; ?></h2>
                    <a href="my_applications.php"><?php echo $t['view_all']; ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="table-responsive">
                    <table class="dash-table">
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
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars(translateJobTitle($app['title'] ?? '', $lang)); ?></td>
                                        <td><?php echo htmlspecialchars(translateEmployerName($app['company_name'] ?? 'N/A', $lang)); ?></td>
                                        <td><?php echo translateNumber(date('d M Y, h:i A', strtotime($app['applied_at'])), $lang); ?></td>
                                        <td>
                                            <span class="dash-status <?php echo strtolower($status_raw); ?>">
                                                <?php echo htmlspecialchars($status_disp); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted"><?php echo $t['no_recent_apps']; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include('../includes/footer.php'); ?>