<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";
// SAVE JOB
if (isset($_GET['save'])) {

    $job_id = intval($_GET['save']);

    $check_saved = $conn->query("
        SELECT * 
        FROM saved_jobs 
        WHERE user_id='$user_id' 
        AND job_id='$job_id'
    ");

    if ($check_saved && $check_saved->num_rows > 0) {

        $message = "Job already saved.";
        $message_type = "warning";

    } else {

        $save_sql = "
            INSERT INTO saved_jobs (user_id, job_id)
            VALUES ('$user_id', '$job_id')
        ";

        if ($conn->query($save_sql)) {

            $message = "Job saved successfully!";
            $message_type = "success";

        } else {

            $message = "Error saving job.";
            $message_type = "danger";
        }
    }
}

// UNSAVE JOB
if (isset($_GET['unsave'])) {

    $job_id = intval($_GET['unsave']);

    $conn->query("
        DELETE FROM saved_jobs
        WHERE user_id='$user_id'
        AND job_id='$job_id'
    ");

    $message = "Saved job removed.";
    $message_type = "info";
}

if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);

    $job_check = $conn->query("SELECT status, application_deadline FROM jobs WHERE job_id='$job_id' LIMIT 1");

    if (!$job_check || $job_check->num_rows == 0) {
        $message = "Job not found.";
        $message_type = "danger";
    } else {
        $job_data = $job_check->fetch_assoc();

        $is_closed = (($job_data['status'] ?? 'active') == 'closed');
        $is_deadline_over = (!empty($job_data['application_deadline']) && $job_data['application_deadline'] < date('Y-m-d'));

        if ($is_closed) {
            $message = "This job is closed. You cannot apply.";
            $message_type = "warning";
        } elseif ($is_deadline_over) {
            $message = "Application deadline is over.";
            $message_type = "warning";
        } else {
            $check_sql = "SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'";
            $check_result = $conn->query($check_sql);

            if ($check_result && $check_result->num_rows > 0) {
                $message = "You already applied for this job.";
                $message_type = "warning";
            } else {
                $apply_sql = "INSERT INTO applications (job_id, user_id) VALUES ('$job_id', '$user_id')";

                if ($conn->query($apply_sql)) {
                    $message = "Job applied successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error: " . $conn->error;
                    $message_type = "danger";
                }
            }
        }
    }
}

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;
$job_type = isset($_GET['job_type']) ? trim($_GET['job_type']) : '';
$categories = isset($_GET['category']) ? $_GET['category'] : [];
$experiences = isset($_GET['experience']) ? $_GET['experience'] : [];

$sql = "SELECT 
            jobs.*,
            d.district_name,
            u.upazila_name,
            w.ward_name,
            users.full_name AS company_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN wards w ON jobs.ward_id = w.ward_id
        LEFT JOIN users ON jobs.employer_id = users.user_id
        WHERE 1=1";

if ($search != "") {
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND (
        jobs.title LIKE '%$search_safe%' 
        OR jobs.description LIKE '%$search_safe%' 
        OR jobs.job_category LIKE '%$search_safe%'
    )";
}

if ($district_id > 0) {
    $sql .= " AND jobs.district_id = '$district_id'";
}

$job_types_arr = isset($_GET['job_types']) ? $_GET['job_types'] : [];
if ($job_type != "") {
    if (!in_array($job_type, $job_types_arr)) {
        $job_types_arr[] = $job_type;
    }
}

if (!empty($job_types_arr)) {
    $t_safe = array_map(function($t) use ($conn) { return "'" . $conn->real_escape_string($t) . "'"; }, $job_types_arr);
    $sql .= " AND jobs.job_type IN (" . implode(",", $t_safe) . ")";
}

if (!empty($categories)) {
    $cat_safe = array_map(function($c) use ($conn) { return "'" . $conn->real_escape_string($c) . "'"; }, $categories);
    $sql .= " AND jobs.job_category IN (" . implode(",", $cat_safe) . ")";
}

if (!empty($experiences)) {
    $exp_clauses = [];
    foreach($experiences as $exp) {
        $exp_safe = $conn->real_escape_string($exp);
        $exp_clauses[] = "jobs.experience_required LIKE '%$exp_safe%'";
    }
    $sql .= " AND (" . implode(" OR ", $exp_clauses) . ")";
}

$sql .= " ORDER BY jobs.job_id DESC";

$jobs_result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>
<?php
$lang = $_SESSION['lang'] ?? 'bn';
$jobs_text = [
    'bn' => [
        'badge' => 'জীবিকা জবস',
        'hero_title' => 'আপনার পরবর্তী সুযোগ আবিষ্কার করুন',
        'hero_desc' => 'সারা বাংলাদেশের শীর্ষ নিয়োগকর্তাদের সাথে যুক্ত হন এবং আপনার ক্যারিয়ারে পরবর্তী পদক্ষেপ নিন।',
        'search_placeholder' => 'চাকরির নাম, কীওয়ার্ড বা কোম্পানি',
        'all_locations' => 'সকল অবস্থান',
        'any_job_type' => 'যেকোনো চাকরির ধরন',
        'search_btn' => 'চাকরি খুঁজুন',
        'quick_filters' => 'দ্রুত ফিল্টার',
        'student_pt' => 'স্টুডেন্ট পার্ট-টাইম',
        'student_pt_sub' => 'শিক্ষার্থীদের জন্য নমনীয় সময়',
        'day_labor' => 'দৈনিক শ্রমিক',
        'day_labor_sub' => 'দৈনিক ও সাপ্তাহিক মজুরি',
        'internships' => 'ইন্টার্নশিপ',
        'internships_sub' => 'আপনার ক্যারিয়ার শুরু করুন',
        'filters' => 'ফিল্টারসমূহ',
        'clear_all' => 'সব মুছুন',
        'job_cat' => 'চাকরির ক্যাটাগরি',
        'job_type' => 'চাকরির ধরন',
        'exp_level' => 'অভিজ্ঞতার স্তর',
        'job_alerts' => 'জব অ্যালার্ট',
        'job_alerts_sub' => 'নতুন চাকরি আপনার দক্ষতার সাথে মিললে বিজ্ঞপ্তি পান।',
        'update_profile' => 'প্রোফাইল আপডেট করুন',
        'recommended' => 'আপনার জন্য প্রস্তাবিত',
        'jobs_found' => 'টি চাকরি পাওয়া গেছে',
        'deadline' => 'শেষ তারিখ',
        'closed' => 'বন্ধ',
        'applied' => 'আবেদনকৃত',
        'apply_now' => 'আবেদন করুন',
        'no_jobs' => 'কোনো চাকরি পাওয়া যায়নি',
        'no_jobs_desc' => 'আপনার মানদণ্ডের সাথে মিলে এমন কোনো সুযোগ আমরা খুঁজে পাইনি। আপনার ফিল্টারগুলি পরিবর্তন করে আবার চেষ্টা করুন।',
        'reset_search' => 'সার্চ রিসেট করুন',
        'not_specified' => 'উল্লেখ নেই',
        'negotiable' => 'আলোচনা সাপেক্ষে',
        'multiple_loc' => 'একাধিক স্থান',
        'unknown_emp' => 'অজ্ঞাত নিয়োগকর্তা'
    ],
    'en' => [
        'badge' => 'JIBIKA JOBS',
        'hero_title' => 'Discover Your Next Opportunity',
        'hero_desc' => 'Connect with top employers across Bangladesh and take the next step in your career journey.',
        'search_placeholder' => 'Job title, keywords, or company',
        'all_locations' => 'All Locations',
        'any_job_type' => 'Any Job Type',
        'search_btn' => 'Search Jobs',
        'quick_filters' => 'Quick Filters',
        'student_pt' => 'Student Part-time',
        'student_pt_sub' => 'Flexible hours for students',
        'day_labor' => 'Day Labor',
        'day_labor_sub' => 'Daily & Weekly wages',
        'internships' => 'Internships',
        'internships_sub' => 'Kickstart your career',
        'filters' => 'Filters',
        'clear_all' => 'Clear All',
        'job_cat' => 'Job Category',
        'job_type' => 'Job Type',
        'exp_level' => 'Experience Level',
        'job_alerts' => 'Job Alerts',
        'job_alerts_sub' => 'Get notified when new jobs match your skills.',
        'update_profile' => 'Update Profile',
        'recommended' => 'Recommended for you',
        'jobs_found' => 'jobs found',
        'deadline' => 'Deadline',
        'closed' => 'Closed',
        'applied' => 'Applied',
        'apply_now' => 'Apply Now',
        'no_jobs' => 'No Jobs Found',
        'no_jobs_desc' => 'We couldn\'t find any opportunities matching your exact criteria. Try adjusting your filters or searching with different keywords.',
        'reset_search' => 'Reset Search',
        'not_specified' => 'Not specified',
        'negotiable' => 'Negotiable',
        'multiple_loc' => 'Multiple Locations',
        'unknown_emp' => 'Unknown Employer'
    ]
];
$jt = $jobs_text[$lang];
?>

<style>
    body { background-color: #f8f9fa; }
    
    .jobs-hero {
        background: linear-gradient(135deg, #0a4f32 0%, #1a8e56 100%);
        padding: 80px 0 100px;
        margin-top: -1px;
        position: relative;
    }
    
    .search-floating-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        margin-top: -60px;
        position: relative;
        z-index: 10;
        border: 1px solid #eaeaea;
    }

    .search-input-group {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 5px 15px;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.2s;
    }
    
    .search-input-group:focus-within {
        background: white;
        border-color: #006a4e;
        box-shadow: 0 0 0 4px rgba(0, 106, 78, 0.1);
    }

    .search-input-group input, .search-input-group select {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 12px 10px;
        color: #1e293b;
        font-size: 1rem;
        font-weight: 500;
        width: 100%;
    }
    
    .search-input-group input:focus, .search-input-group select:focus {
        outline: none;
    }

    .filter-sidebar {
        background: white;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
    }

    .job-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .job-card:hover {
        transform: translateY(-5px);
        border-color: #a7f3d0;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    .job-logo {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        color: #166534;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .job-meta-pill {
        background: #f3f4f6;
        color: #4b5563;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-apply {
        background: #198754;
        color: white;
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-apply:hover {
        background: #146c43;
        color: white;
        transform: scale(1.02);
    }
</style>

<!-- Hero Banner -->
<div class="jobs-hero text-center">
    <div class="container px-4">
        <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-3 shadow-sm fw-bold" style="letter-spacing:1px;"><?php echo $jt['badge']; ?></span>
        <h1 class="text-white fw-bold mb-3" style="font-size: 3rem;"><?php echo $jt['hero_title']; ?></h1>
        <p class="text-white-50 fs-5 mb-0" style="max-width: 600px; margin: 0 auto;"><?php echo $jt['hero_desc']; ?></p>
    </div>
</div>

<div class="container px-4 mb-5">
    
    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?> shadow-sm border-0 rounded-4 mt-4">
            <i class="fa-solid fa-circle-info me-2"></i><?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="jobs.php" id="mainJobSearchForm">
        <!-- Floating Search Bar -->
        <div class="search-floating-card">
            <div class="row g-3 align-items-center">
                <div class="col-lg-4">
                    <div class="search-input-group">
                        <i class="fa-solid fa-magnifying-glass text-muted ms-2"></i>
                        <input type="text" name="search" class="form-control" placeholder="<?php echo $jt['search_placeholder']; ?>" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="search-input-group">
                        <i class="fa-solid fa-location-dot text-muted ms-2"></i>
                        <select name="district_id" class="form-select text-muted">
                            <option value=""><?php echo $jt['all_locations']; ?></option>
                            <?php
                            if ($districts && $districts->num_rows > 0) {
                                $districts->data_seek(0);
                                while ($row = $districts->fetch_assoc()) {
                                    $selected = ($district_id == $row['district_id']) ? 'selected' : '';
                                    echo "<option value='" . $row['district_id'] . "' $selected>" . htmlspecialchars($row['district_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="search-input-group">
                        <i class="fa-solid fa-briefcase text-muted ms-2"></i>
                        <select name="job_type" class="form-select text-muted">
                            <option value=""><?php echo $jt['any_job_type']; ?></option>
                            <?php
                            $types = ['Full-time', 'Part-time', 'Part-time (Student)', 'Day Labor', 'Internship', 'Contract', 'Remote'];
                            foreach ($types as $type) {
                                $selected = ($job_type == $type) ? 'selected' : '';
                                echo "<option value='$type' $selected>$type</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-apply w-100 rounded-3 py-3 shadow-sm" style="font-size: 1.05rem;"><?php echo $jt['search_btn']; ?></button>
                </div>
            </div>
        </div>

            <div class="col-md-4">
                <a href="jobs.php?job_type=Part-time+(Student)" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 180px;">
                    <div style="background-image: url('/assets/image/student_jobs.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 1.5rem;">
                        <h5 class="fw-bold text-white mb-1"><?php echo $jt['student_pt']; ?></h5>
                        <small class="text-white-50"><?php echo $jt['student_pt_sub']; ?></small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="jobs.php?job_type=Day+Labor" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 180px;">
                    <div style="background-image: url('/assets/image/day_labor.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 1.5rem;">
                        <h5 class="fw-bold text-white mb-1"><?php echo $jt['day_labor']; ?></h5>
                        <small class="text-white-50"><?php echo $jt['day_labor_sub']; ?></small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="jobs.php?job_type=Internship" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 180px;">
                    <div style="background-image: url('/assets/image/internships.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 1.5rem;">
                        <h5 class="fw-bold text-white mb-1"><?php echo $jt['internships']; ?></h5>
                        <small class="text-white-50"><?php echo $jt['internships_sub']; ?></small>
                    </div>
                </a>
            </div>
        </div>
        <style>
            .hover-lift:hover .specialized-bg { transform: scale(1.05); }
            .hover-lift { transition: transform 0.2s; }
            .hover-lift:hover { transform: translateY(-5px); }
        </style>

        <!-- Main Content -->
        <div class="row g-4 mt-2">
            
            <!-- Left Sidebar: Active Filters -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filter-sidebar p-4 sticky-top" style="top: 100px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $jt['filters']; ?></h5>
                        <?php if(!empty($search) || !empty($district_id) || !empty($job_type) || !empty($categories) || !empty($experiences)): ?>
                            <a href="jobs.php" class="text-danger small text-decoration-none fw-bold"><?php echo $jt['clear_all']; ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-3"><?php echo $jt['job_cat']; ?></h6>
                        <?php
                        $cat_list = ['IT & Tech', 'Garments', 'Driving', 'Education', 'Health', 'Marketing'];
                        foreach ($cat_list as $cat):
                            $checked = in_array($cat, $categories) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="category[]" value="<?php echo $cat; ?>" id="cat_<?php echo md5($cat); ?>" <?php echo $checked; ?> onchange="document.getElementById('mainJobSearchForm').submit()">
                            <label class="form-check-label text-secondary fw-medium" for="cat_<?php echo md5($cat); ?>"><?php echo $cat; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-3"><?php echo $jt['job_type']; ?></h6>
                        <?php
                        $t_list = ['Full-time', 'Part-time (Student)', 'Day Labor', 'Internship', 'Contract'];
                        foreach ($t_list as $t):
                            $checked = in_array($t, $job_types_arr) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="job_types[]" value="<?php echo $t; ?>" id="t_<?php echo md5($t); ?>" <?php echo $checked; ?> onchange="document.getElementById('mainJobSearchForm').submit()">
                            <label class="form-check-label text-secondary fw-medium" for="t_<?php echo md5($t); ?>"><?php echo $t; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-3"><?php echo $jt['exp_level']; ?></h6>
                        <?php
                        $exp_list = ['Entry Level', 'Mid Level', 'Senior Level', '1 Year', '2 Years', '3 Years'];
                        foreach ($exp_list as $exp):
                            $checked = in_array($exp, $experiences) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="experience[]" value="<?php echo $exp; ?>" id="exp_<?php echo md5($exp); ?>" <?php echo $checked; ?> onchange="document.getElementById('mainJobSearchForm').submit()">
                            <label class="form-check-label text-secondary fw-medium" for="exp_<?php echo md5($exp); ?>"><?php echo $exp; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="p-3 bg-light rounded-3 mt-4 text-center border">
                        <i class="fa-solid fa-bell text-warning fs-3 mb-2"></i>
                        <h6 class="fw-bold mb-1"><?php echo $jt['job_alerts']; ?></h6>
                        <p class="small text-muted mb-3"><?php echo $jt['job_alerts_sub']; ?></p>
                        <a href="profile.php" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold"><?php echo $jt['update_profile']; ?></a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Job List -->
            <div class="col-lg-9">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0"><?php echo $jt['recommended']; ?></h4>
                    <span class="text-muted fw-medium"><?php echo ($jobs_result ? $jobs_result->num_rows : 0) . ' ' . $jt['jobs_found']; ?></span>
                </div>

                <?php if ($jobs_result && $jobs_result->num_rows > 0): ?>
                
                <div class="d-flex flex-column gap-4">
                    <?php while ($job = $jobs_result->fetch_assoc()): ?>
                        <?php
                        $job_id = $job['job_id'];

                        $check_applied = $conn->query("SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'");
                        $already_applied = ($check_applied && $check_applied->num_rows > 0);
                        
                        $check_saved = $conn->query("SELECT * FROM saved_jobs WHERE user_id='$user_id' AND job_id='$job_id'");
                        $already_saved = ($check_saved && $check_saved->num_rows > 0);
                        
                        $deadline = $job['application_deadline'] ?? '';
                        $deadline_over = (!empty($deadline) && $deadline < date('Y-m-d'));
                        
                        $company_initial = strtoupper(substr($job['company_name'] ?? 'U', 0, 1));
                        ?>

                        <div class="job-card p-4">
                            <div class="row align-items-center">
                                
                                <div class="col-md-8">
                                    <div class="d-flex gap-4">
                                        <div class="job-logo flex-shrink-0">
                                            <?php echo $company_initial; ?>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="fw-bold text-dark mb-0 fs-4">
                                                    <?php echo htmlspecialchars($job['title']); ?>
                                                </h5>
                                                <?php if ($deadline_over): ?>
                                                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size:0.7rem;"><?php echo $jt['closed']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <p class="text-success fw-bold mb-3" style="font-size: 1.05rem;">
                                                <?php echo htmlspecialchars($job['company_name'] ?? $jt['unknown_emp']); ?>
                                            </p>
                                            
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="job-meta-pill"><i class="fa-solid fa-location-dot text-muted"></i> <?php echo htmlspecialchars($job['district_name'] ?? $jt['multiple_loc']); ?></span>
                                                <span class="job-meta-pill"><i class="fa-solid fa-briefcase text-muted"></i> <?php echo htmlspecialchars($job['job_type'] ?? 'Full-time'); ?></span>
                                                <span class="job-meta-pill"><i class="fa-solid fa-money-bill-wave text-muted"></i> <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : $jt['negotiable']; ?></span>
                                            </div>

                                            <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 0.95rem;">
                                                <?php echo htmlspecialchars(substr($job['description'] ?? '', 0, 180)); ?>...
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mt-4 mt-md-0 border-start-md ps-md-4 text-md-end d-flex flex-column justify-content-center h-100">
                                    <div class="mb-3 text-start text-md-end">
                                        <small class="text-danger fw-bold d-block mb-1"><i class="fa-regular fa-clock me-1"></i> <?php echo $jt['deadline']; ?></small>
                                        <span class="fw-semibold text-dark"><?php echo !empty($deadline) ? date('d M, Y', strtotime($deadline)) : $jt['not_specified']; ?></span>
                                    </div>
                                    
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <?php if ($already_saved): ?>
                                            <a href="jobs.php?unsave=<?php echo $job['job_id']; ?>" class="btn btn-light border text-danger" style="border-radius:10px; width:45px; height:45px; display:flex; align-items:center; justify-content:center;" title="Remove Saved Job">
                                                <i class="fa-solid fa-bookmark"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="jobs.php?save=<?php echo $job['job_id']; ?>" class="btn btn-light border text-secondary hover-primary" style="border-radius:10px; width:45px; height:45px; display:flex; align-items:center; justify-content:center;" title="Save Job">
                                                <i class="fa-regular fa-bookmark"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($already_applied): ?>
                                            <button class="btn btn-light border text-success fw-bold flex-grow-1" style="border-radius:10px;" disabled>
                                                <i class="fa-solid fa-check me-2"></i><?php echo $jt['applied']; ?>
                                            </button>
                                        <?php elseif ($deadline_over): ?>
                                            <button class="btn btn-light border text-danger fw-bold flex-grow-1" style="border-radius:10px;" disabled>
                                                <?php echo $jt['closed']; ?>
                                            </button>
                                        <?php else: ?>
                                            <a href="jobs.php?apply=<?php echo $job['job_id']; ?>&search=<?php echo urlencode($search); ?>&district_id=<?php echo urlencode($district_id); ?>&job_type=<?php echo urlencode($job_type); ?>"
                                               class="btn btn-apply flex-grow-1"
                                               onclick="return confirm('Apply for this job?')">
                                                <?php echo $jt['apply_now']; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
            <?php else: ?>
                <div class="job-card p-5 text-center d-flex flex-column justify-content-center align-items-center border-0 shadow-sm" style="min-height: 400px;">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px; height:100px;">
                        <i class="fa-solid fa-briefcase text-muted opacity-50" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2"><?php echo $jt['no_jobs']; ?></h3>
                    <p class="text-muted fs-5 mb-4" style="max-width: 500px;"><?php echo $jt['no_jobs_desc']; ?></p>
                    <a href="jobs.php" class="btn btn-dark rounded-pill px-5 py-2 fw-bold"><?php echo $jt['reset_search']; ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>