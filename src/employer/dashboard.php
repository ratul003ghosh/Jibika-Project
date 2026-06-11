<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$empDashText = [
    'bn' => [
        'welcome' => 'স্বাগতম, ',
        'welcome_sub' => 'আজ আপনার চাকরির সার্কুলারগুলোর পরিস্থিতি নিচে দেওয়া হলো।',
        'post_job' => 'নতুন চাকরি পোস্ট করুন',
        'complete_profile' => 'চাকরি পোস্ট করতে প্রোফাইল সম্পূর্ণ করুন',
        'action_required' => 'প্রয়োজনীয় পদক্ষেপ: প্রোফাইল অসম্পূর্ণ',
        'profile_warning' => 'আপনার অফিশিয়াল কোম্পানি প্রোফাইল সম্পূর্ণ ও যাচাই না করা পর্যন্ত আপনি কোনো চাকরি পোস্ট বা আবেদনকারী পর্যালোচনা করতে পারবেন না।',
        'setup_now' => 'এখনই কোম্পানি প্রোফাইল সেট আপ করুন',
        'active_jobs' => 'সক্রিয় চাকরি',
        'total_apps' => 'মোট আবেদনসমূহ',
        'pending_review' => 'পর্যালোচনাধীন',
        'hired' => 'নিয়োগকৃত প্রার্থী',
        'quick_actions' => 'দ্রুত পদক্ষেপ',
        'create_job' => 'চাকরি তৈরি করুন',
        'manage_listings' => 'সার্কুলার পরিচালনা',
        'review_candidates' => 'প্রার্থী পর্যালোচনা',
        'company_profile' => 'কোম্পানি প্রোফাইল',
        'recent_jobs' => 'সাম্প্রতিক পোস্ট করা চাকরি',
        'recent_applicants' => 'সাম্প্রতিক আবেদনকারীগণ',
        'view_all' => 'সব দেখুন',
        'th_title' => 'চাকরির শিরোনাম',
        'th_location' => 'অবস্থান',
        'th_salary' => 'বেতন',
        'th_candidate' => 'প্রার্থী',
        'th_applied_for' => 'আবেদনকৃত পদ',
        'th_status' => 'অবস্থা',
        'status_accepted' => 'গৃহীত',
        'status_rejected' => 'প্রত্যাখ্যাত',
        'status_pending' => 'অপেক্ষমাণ',
        'no_jobs_posted' => 'এখনও কোনো চাকরি পোস্ট করা হয়নি',
        'no_jobs_posted_sub' => 'প্রতিভা আকর্ষণ করতে আপনার প্রথম চাকরির সার্কুলার তৈরি করুন।',
        'no_apps' => 'এখনও কোনো আবেদন পড়েনি',
        'no_apps_sub' => 'প্রার্থীদের আপনার সার্কুলার খুঁজে পাওয়ার জন্য অপেক্ষা করুন।',
        'negotiable' => 'আলোচনা সাপেক্ষে',
    ],
    'en' => [
        'welcome' => 'Welcome back, ',
        'welcome_sub' => 'Here is what\'s happening with your job listings today.',
        'post_job' => 'Post a New Job',
        'complete_profile' => 'Complete Profile to Post Jobs',
        'action_required' => 'Action Required: Profile Incomplete',
        'profile_warning' => 'You cannot post jobs or review applicants until your official company profile is completed and verified.',
        'setup_now' => 'Set up Company Profile now',
        'active_jobs' => 'Active Jobs',
        'total_apps' => 'Total Applications',
        'pending_review' => 'Pending Review',
        'hired' => 'Hired Candidates',
        'quick_actions' => 'Quick Actions',
        'create_job' => 'Create Job',
        'manage_listings' => 'Manage Listings',
        'review_candidates' => 'Review Candidates',
        'company_profile' => 'Company Profile',
        'recent_jobs' => 'Recently Posted Jobs',
        'recent_applicants' => 'Recent Applicants',
        'view_all' => 'View All',
        'th_title' => 'Job Title',
        'th_location' => 'Location',
        'th_salary' => 'Salary',
        'th_candidate' => 'Candidate',
        'th_applied_for' => 'Applied For',
        'th_status' => 'Status',
        'status_accepted' => 'Accepted',
        'status_rejected' => 'Rejected',
        'status_pending' => 'Pending',
        'no_jobs_posted' => 'No Jobs Posted Yet',
        'no_jobs_posted_sub' => 'Create your first job listing to attract talent.',
        'no_apps' => 'No Applications Yet',
        'no_apps_sub' => 'Wait for candidates to discover your postings.',
        'negotiable' => 'Negotiable',
    ]
];
$ct = $empDashText[$lang];

$employer_id = $_SESSION['user_id'];
$profile_check = $conn->query("SELECT employer_profile_id FROM employer_profiles WHERE user_id='$employer_id' LIMIT 1");
$has_profile = ($profile_check && $profile_check->num_rows > 0);

$full_name = $_SESSION['full_name'] ?? 'Employer';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? '';

$total_jobs = 0;
$total_applicants = 0;
$pending_applicants = 0;
$accepted_applicants = 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE employer_id='$employer_id'");
if ($q) $total_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
");
if ($q) $total_applicants = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
    AND applications.status='Pending'
");
if ($q) $pending_applicants = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
    AND applications.status='Accepted'
");
if ($q) $accepted_applicants = $q->fetch_assoc()['total'] ?? 0;

$recent_jobs = $conn->query("
    SELECT jobs.*, 
           d.district_name, 
           u.upazila_name, 
           w.ward_name
    FROM jobs
    LEFT JOIN districts d ON jobs.district_id = d.district_id
    LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
    LEFT JOIN wards w ON jobs.ward_id = w.ward_id
    WHERE jobs.employer_id='$employer_id'
    ORDER BY jobs.job_id DESC
    LIMIT 5
");

$recent_applicants = $conn->query("
    SELECT 
        applications.application_id,
        applications.status,
        applications.applied_at,
        jobs.title AS job_title,
        users.full_name,
        users.email
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    JOIN users ON applications.user_id = users.user_id
    WHERE jobs.employer_id='$employer_id'
    ORDER BY applications.application_id DESC
    LIMIT 5
");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<link rel="stylesheet" href="../assets/css/employer_dashboard.css">

<div class="container py-4">

    <!-- Hero Section -->
    <div class="dash-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="z-index: 1;">
            <h2 class="fw-bold mb-2"><?php echo $ct['welcome'] . htmlspecialchars(translateEmployerName($full_name, $lang)); ?>!</h2>
            <p class="mb-0 fs-5 opacity-75"><?php echo $ct['welcome_sub']; ?></p>
        </div>
        <div style="z-index: 1;">
            <?php if ($has_profile): ?>
            <a href="post_job.php" class="btn btn-light text-success fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> <?php echo $ct['post_job']; ?>
            </a>
            <?php else: ?>
            <a href="profile.php" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $ct['complete_profile']; ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$has_profile): ?>
    <div class="alert shadow-sm rounded-4 mb-4 border-0 border-start border-danger border-4" style="background-color: #fff5f5; color: #c53030;">
        <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo $ct['action_required']; ?></h5>
        <p class="mb-0"><?php echo $ct['profile_warning']; ?> <a href="profile.php" class="fw-bold text-danger text-decoration-underline ms-2"><?php echo $ct['setup_now']; ?> <i class="fa-solid fa-arrow-right"></i></a></p>
    </div>
    <?php endif; ?>

    <!-- KPI Stats -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase"><?php echo $ct['active_jobs']; ?></p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo translateNumber($total_jobs, $lang); ?></h2>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase"><?php echo $ct['total_apps']; ?></p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo translateNumber($total_applicants, $lang); ?></h2>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase"><?php echo $ct['pending_review']; ?></p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo translateNumber($pending_applicants, $lang); ?></h2>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="applicants.php?f_status=Accepted" class="text-decoration-none">
                <div class="stat-card hover-shadow transition-all">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fw-bold mb-1 small text-uppercase"><?php echo $ct['hired']; ?></p>
                            <h2 class="fw-bold text-dark mb-0"><?php echo translateNumber($accepted_applicants, $lang); ?></h2>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <h5 class="fw-bold text-dark mb-3"><?php echo $ct['quick_actions']; ?></h5>
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="post_job.php" class="action-card shadow-sm">
                <i class="fa-solid fa-file-circle-plus action-icon text-success fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0"><?php echo $ct['create_job']; ?></h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="manage_jobs.php" class="action-card shadow-sm">
                <i class="fa-solid fa-list-check action-icon text-primary fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0"><?php echo $ct['manage_listings']; ?></h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="applicants.php" class="action-card shadow-sm">
                <i class="fa-solid fa-user-tie action-icon text-warning fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0"><?php echo $ct['review_candidates']; ?></h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="profile.php" class="action-card shadow-sm">
                <i class="fa-solid fa-building action-icon text-secondary fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0"><?php echo $ct['company_profile']; ?></h6>
            </a>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent Jobs -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0"><?php echo $ct['recent_jobs']; ?></h5>
                    <a href="manage_jobs.php" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-3"><?php echo $ct['view_all']; ?></a>
                </div>
                <div class="card-body p-4">
                    <?php if ($recent_jobs && $recent_jobs->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table custom-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo $ct['th_title']; ?></th>
                                        <th><?php echo $ct['th_location']; ?></th>
                                        <th><?php echo $ct['th_salary']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                                        <tr>
                                            <td><span class="fw-bold text-dark"><?php echo htmlspecialchars(translateJobTitle($job['title'] ?? '', $lang)); ?></span></td>
                                            <td class="text-secondary"><i class="fa-solid fa-location-dot me-1 text-muted small"></i> <?php echo htmlspecialchars(translateDistrict($job['district_name'] ?? '', $lang) ?: 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-light text-success border px-2 py-1">
                                                    <?php echo (empty($job['salary']) || strtolower($job['salary']) === 'negotiable') ? $ct['negotiable'] : '৳ ' . translateSalary($job['salary'], $lang); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-folder-open text-muted fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark"><?php echo $ct['no_jobs_posted']; ?></h6>
                            <p class="text-muted small"><?php echo $ct['no_jobs_posted_sub']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Applicants -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0"><?php echo $ct['recent_applicants']; ?></h5>
                    <a href="applicants.php" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-3"><?php echo $ct['view_all']; ?></a>
                </div>
                <div class="card-body p-4">
                    <?php if ($recent_applicants && $recent_applicants->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table custom-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo $ct['th_candidate']; ?></th>
                                        <th><?php echo $ct['th_applied_for']; ?></th>
                                        <th><?php echo $ct['th_status']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = $recent_applicants->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars(translateEmployerName($app['full_name'] ?? '', $lang)); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($app['email']); ?></div>
                                            </td>
                                            <td class="text-secondary"><?php echo htmlspecialchars(translateJobTitle($app['job_title'] ?? '', $lang)); ?></td>
                                            <td>
                                                <?php
                                                if ($app['status'] == 'Accepted') {
                                                    echo "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1'>" . $ct['status_accepted'] . "</span>";
                                                } elseif ($app['status'] == 'Rejected') {
                                                    echo "<span class='badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1'>" . $ct['status_rejected'] . "</span>";
                                                } else {
                                                    echo "<span class='badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 px-2 py-1'>" . $ct['status_pending'] . "</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-user-xmark text-muted fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark"><?php echo $ct['no_apps']; ?></h6>
                            <p class="text-muted small"><?php echo $ct['no_apps_sub']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>