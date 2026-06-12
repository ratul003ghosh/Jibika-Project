<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
include('../includes/recommendation_engine.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$apText = [
    'bn' => [
        'title' => 'আবেদনকারী তালিকা',
        'subtitle' => 'আবেদনকারীদের পর্যালোচনা করুন। গৃহীত আবেদনকারীদের স্বয়ংক্রিয়ভাবে নিযুক্ত হিসেবে চিহ্নিত করা হবে।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'msg_success' => 'আবেদনকারীর অবস্থা সফলভাবে আপডেট করা হয়েছে!',
        'msg_error' => 'ত্রুটি: ',
        'msg_not_found' => 'আবেদনপত্র পাওয়া যায়নি বা অ্যাক্সেস করার অনুমতি নেই।',
        'th_serial' => '#',
        'th_job_info' => 'চাকরির তথ্য',
        'th_applicant_info' => 'আবেদনকারীর তথ্য',
        'th_skills_edu' => 'দক্ষতা ও শিক্ষা',
        'th_status' => 'আবেদনের অবস্থা',
        'th_applied_at' => 'আবেদনের সময়',
        'th_action' => 'পদক্ষেপ',
        'label_salary' => 'বেতন:',
        'label_deadline' => 'শেষ সময়সীমা:',
        'label_education' => 'শিক্ষা:',
        'label_skills' => 'দক্ষতা:',
        'status_accepted' => 'গৃহীত',
        'status_rejected' => 'প্রত্যাখ্যাত',
        'status_shortlisted' => 'সংক্ষিপ্ত তালিকাভুক্ত',
        'status_pending' => 'অপেক্ষমাণ',
        'status_emp_updated' => 'কর্মসংস্থান আপডেট করা হয়েছে',
        'btn_accept' => 'গ্রহণ করুন',
        'btn_reject' => 'প্রত্যাখ্যান করুন',
        'btn_shortlist' => 'শর্টলিস্ট করুন',
        'btn_done' => 'সিদ্ধান্ত সম্পন্ন',
        'btn_biodata' => 'বিস্তারিত জীবনবৃত্তান্ত দেখুন',
        'confirm_accept' => 'আপনি কি এই আবেদনকারীকে গ্রহণ করতে চান?',
        'confirm_reject' => 'আপনি কি এই আবেদনকারীকে প্রত্যাখ্যান করতে চান?',
        'confirm_shortlist' => 'আপনি কি এই আবেদনকারীকে শর্টলিস্ট করতে চান?',
        'no_applicants' => 'এখনও কোনো আবেদনকারী পাওয়া যায়নি।',
        'not_specified' => 'নির্দিষ্ট করা নেই',
        'negotiable' => 'আলোচনা সাপেক্ষে',
        'general' => 'সাধারণ',
        'na' => 'প্রযোজ্য নয়',
        'top5_title' => 'শীর্ষ ৫ জন প্রস্তাবিত প্রার্থী',
        'others_title' => 'অন্যান্য আবেদনকারী',
        'filter_btn' => 'ফিল্টার করুন',
        'reset_btn' => 'রিসেট করুন',
        'showing_x_of_y' => 'মোট {Y} জন থেকে {X} জন দেখাচ্ছে',
        'filter_status' => 'আবেদনের অবস্থা',
        'filter_district' => 'জেলা',
        'filter_upazila' => 'উপজেলা',
        'filter_category' => 'চাকরির ক্যাটাগরি',
        'filter_edu' => 'শিক্ষাগত যোগ্যতা',
        'filter_exp' => 'অভিজ্ঞতা (বছর)',
        'sort_label' => 'সাজান',
        'sort_rec_desc' => 'সর্বোচ্চ প্রস্তাবিত স্কোর',
        'sort_rec_asc' => 'সর্বনিম্ন প্রস্তাবিত স্কোর',
        'sort_exp_desc' => 'সর্বোচ্চ অভিজ্ঞতা',
        'sort_exp_asc' => 'সর্বনিম্ন অভিজ্ঞতা',
        'sort_date_desc' => 'সর্বশেষ আবেদন',
        'sort_date_asc' => 'পুরাতন আবেদন',
        'sort_az' => 'এ-জেড',
        'sort_za' => 'জেড-এ'
    ],
    'en' => [
        'title' => 'Applicants List',
        'subtitle' => 'Review applicants. Accepted applicants are automatically marked as employed.',
        'back_btn' => 'Back to Dashboard',
        'msg_success' => 'Application status updated successfully!',
        'msg_error' => 'Error: ',
        'msg_not_found' => 'Application not found or permission denied.',
        'th_serial' => '#',
        'th_job_info' => 'Job Information',
        'th_applicant_info' => 'Applicant Information',
        'th_skills_edu' => 'Skills & Education',
        'th_status' => 'Application Status',
        'th_applied_at' => 'Applied At',
        'th_action' => 'Action',
        'label_salary' => 'Salary:',
        'label_deadline' => 'Deadline:',
        'label_education' => 'Education:',
        'label_skills' => 'Skills:',
        'status_accepted' => 'Accepted',
        'status_rejected' => 'Rejected',
        'status_shortlisted' => 'Shortlisted',
        'status_pending' => 'Pending',
        'status_emp_updated' => 'Employment updated',
        'btn_accept' => 'Accept',
        'btn_reject' => 'Reject',
        'btn_shortlist' => 'Shortlist',
        'btn_done' => 'Decision Done',
        'btn_biodata' => 'View Biodata',
        'confirm_accept' => 'Accept this applicant?',
        'confirm_reject' => 'Reject this applicant?',
        'confirm_shortlist' => 'Shortlist this applicant?',
        'no_applicants' => 'No applicants found yet.',
        'not_specified' => 'Not specified',
        'negotiable' => 'Negotiable',
        'general' => 'General',
        'na' => 'N/A',
        'top5_title' => 'Top 5 Recommended Candidates',
        'others_title' => 'Other Applicants',
        'filter_btn' => 'Filter',
        'reset_btn' => 'Reset Filters',
        'showing_x_of_y' => 'Showing {X} of {Y} applicants',
        'filter_status' => 'Application Status',
        'filter_district' => 'District',
        'filter_upazila' => 'Upazila',
        'filter_category' => 'Job Category',
        'filter_edu' => 'Education Level',
        'filter_exp' => 'Experience (Years)',
        'sort_label' => 'Sort By',
        'sort_rec_desc' => 'Highest Recommendation Score',
        'sort_rec_asc' => 'Lowest Recommendation Score',
        'sort_exp_desc' => 'Most Experience',
        'sort_exp_asc' => 'Least Experience',
        'sort_date_desc' => 'Latest Applications',
        'sort_date_asc' => 'Oldest Applications',
        'sort_az' => 'A-Z',
        'sort_za' => 'Z-A'
    ]
];
$ct = $apText[$lang];

$message = "";
if (isset($_GET['action']) && isset($_GET['application_id'])) {
    $action = $_GET['action'];
    $application_id = intval($_GET['application_id']);
    $statusMap = [
        'accept' => 'Accepted',
        'reject' => 'Rejected',
        'shortlist' => 'Shortlisted'
    ];
    $status = $statusMap[$action] ?? '';

    if ($status != "") {
        $stmt = $conn->prepare("UPDATE applications JOIN jobs ON applications.job_id = jobs.job_id SET applications.status = ? WHERE applications.application_id = ? AND jobs.employer_id = ?");
        if ($stmt) {
            $stmt->bind_param("sii", $status, $application_id, $employer_id);
            $stmt->execute();
            $message = $ct['msg_success'];
        } else {
            $message = $ct['msg_error'] . $conn->error;
        }
    }
}

// SQL Query Builder
// Fetch Employer Jobs for Filter
$emp_jobs_query = $conn->query("SELECT j.job_id, j.title, COUNT(a.application_id) AS app_count 
                                FROM jobs j 
                                LEFT JOIN applications a ON j.job_id = a.job_id 
                                WHERE j.employer_id = '$employer_id' 
                                GROUP BY j.job_id 
                                ORDER BY j.created_at DESC");
$emp_jobs = [];
if ($emp_jobs_query) {
    while($j = $emp_jobs_query->fetch_assoc()) {
        $emp_jobs[] = $j;
    }
}

$job_id_filter = 0;
if (isset($_GET['job_id']) && $_GET['job_id'] !== 'all') {
    $job_id_filter = intval($_GET['job_id']);
}

// Fetch selected job details if a job is filtered
$selected_job = null;
if ($job_id_filter > 0) {
    $sj_query = $conn->query("SELECT j.*, d.district_name, u.upazila_name, w.ward_name 
                              FROM jobs j 
                              LEFT JOIN districts d ON j.district_id = d.district_id 
                              LEFT JOIN upazilas u ON j.upazila_id = u.upazila_id 
                              LEFT JOIN wards w ON j.ward_id = w.ward_id 
                              WHERE j.job_id = $job_id_filter AND j.employer_id = '$employer_id'");
    if ($sj_query && $sj_query->num_rows > 0) {
        $selected_job = $sj_query->fetch_assoc();
    }
}

$f_status = $_GET['f_status'] ?? '';
$f_category = $_GET['f_category'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'rec_desc';

$where_clauses = ["jobs.employer_id = '$employer_id'"];
if ($job_id_filter > 0) $where_clauses[] = "jobs.job_id = '$job_id_filter'";
if (!empty($f_status)) $where_clauses[] = "applications.status = '".$conn->real_escape_string($f_status)."'";
if (!empty($f_category)) $where_clauses[] = "jobs.job_category = '".$conn->real_escape_string($f_category)."'";

$where_sql = implode(" AND ", $where_clauses);

        $sql = "SELECT 
            applications.application_id, applications.status, applications.applied_at,
            jobs.job_id, jobs.title AS job_title, jobs.job_category, jobs.job_type, jobs.salary, jobs.salary_type, jobs.application_deadline, jobs.education_required, jobs.experience_required, jobs.location, jobs.upazila_id,
            users.user_id AS applicant_id, users.full_name, users.email,
            jsp.about, jsp.district, jsp.upazila, jsp.preferred_job_category, jsp.experience_years,
            jsp.cv_path AS cv_file, '' AS portfolio_link, '' AS certifications, COALESCE(jsp.degree, jsp.education) AS degree, jsp.education, jsp.institution, '' AS gpa,
            '' AS gender, NULL AS dob, jsp.address, jsp.nid, '' AS profile_photo
        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
        JOIN users ON applications.user_id = users.user_id
        LEFT JOIN job_seeker_profiles jsp ON applications.user_id = jsp.user_id
        WHERE $where_sql";

$result = $conn->query($sql);
$applicants = [];
$total_applicants = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $skills_res = $conn->query("SELECT skill_name FROM skills WHERE user_id='{$row['applicant_id']}'");
        $skills = [];
        if ($skills_res) {
            while ($s = $skills_res->fetch_assoc()) $skills[] = $s['skill_name'];
        }
        $row['skills'] = $skills;
        
        // Use Recommendation Engine
        $rec = calculateRecommendationScore($row, $row, $skills, $row['job_category']);
        $row['score'] = $rec['total'];
        $row['rec_details'] = $rec;

        // Calculate Profile Completeness Score (0-100)
        $profile_fields = ['education', 'about', 'district', 'upazila', 'gender', 'dob', 'address', 'nid', 'profile_photo', 'degree', 'institution'];
        $filled = 0;
        foreach ($profile_fields as $f) {
            if (!empty($row[$f])) $filled++;
        }
        $has_skills = count($skills) > 0 ? 1 : 0;
        $has_cv = !empty($row['cv_file']) ? 1 : 0;
        $has_portfolio = !empty($row['portfolio_link']) ? 1 : 0;
        $has_certs = !empty($row['certifications']) ? 1 : 0;
        $total_checks = count($profile_fields) + 4; // +skills, cv, portfolio, certs
        $row['profile_score'] = round((($filled + $has_skills + $has_cv + $has_portfolio + $has_certs) / $total_checks) * 100);

        // Calculate CV Strength Score (0-100) based on skills count, education level, experience
        $skill_pts = min(40, count($skills) * 8); // up to 40 pts for skills
        $edu_hierarchy = ['ssc' => 10, 'hsc' => 15, 'diploma' => 20, 'bachelor' => 25, 'honours' => 25, 'bsc' => 25, 'masters' => 30, 'phd' => 35];
        $edu_pts = 0;
        foreach ($edu_hierarchy as $lvl => $pts) {
            if (stripos($row['education'] ?? '', $lvl) !== false) $edu_pts = max($edu_pts, $pts);
        }
        $exp_pts = min(25, intval($row['experience_years'] ?? 0) * 5); // up to 25 pts
        $row['cv_score'] = min(100, $skill_pts + $edu_pts + $exp_pts);

        $applicants[] = $row;
    }
}
$total_applicants = count($applicants);

// PHP Filtering
$f_edu = strtolower($_GET['f_edu'] ?? '');
$f_exp = $_GET['f_exp'] ?? '';
$f_district = strtolower($_GET['f_district'] ?? '');

if (!empty($f_edu) || $f_exp !== '' || !empty($f_district)) {
    $applicants = array_filter($applicants, function($a) use ($f_edu, $f_exp, $f_district) {
        if (!empty($f_edu) && stripos($a['education'] ?? '', $f_edu) === false) return false;
        if ($f_exp !== '' && ($a['experience_years'] ?? 0) < intval($f_exp)) return false;
        if (!empty($f_district) && stripos($a['district'] ?? '', $f_district) === false) return false;
        return true;
    });
}

// Sorting
usort($applicants, function($a, $b) use ($sort_by) {
    if ($sort_by == 'rec_desc') return $b['score'] <=> $a['score'];
    if ($sort_by == 'rec_asc') return $a['score'] <=> $b['score'];
    if ($sort_by == 'profile_desc') return ($b['profile_score']??0) <=> ($a['profile_score']??0);
    if ($sort_by == 'cv_desc') return ($b['cv_score']??0) <=> ($a['cv_score']??0);
    if ($sort_by == 'exp_desc') return ($b['experience_years']??0) <=> ($a['experience_years']??0);
    if ($sort_by == 'exp_asc') return ($a['experience_years']??0) <=> ($b['experience_years']??0);
    if ($sort_by == 'date_desc') return strtotime($b['applied_at']) <=> strtotime($a['applied_at']);
    if ($sort_by == 'date_asc') return strtotime($a['applied_at']) <=> strtotime($b['applied_at']);
    if ($sort_by == 'az') return strcmp($a['full_name'], $b['full_name']);
    if ($sort_by == 'za') return strcmp($b['full_name'], $a['full_name']);
    return $b['score'] <=> $a['score'];
});

$filtered_count = count($applicants);
$top_5 = [];
$others = [];

if ($job_id_filter > 0 && $sort_by == 'rec_desc') {
    $top_5 = array_slice($applicants, 0, 5);
    $others = array_slice($applicants, 5);
} else {
    $others = $applicants;
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1"><?php echo $ct['title']; ?></h2>
                <p class="text-muted mb-0"><?php echo $ct['subtitle']; ?></p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary"><?php echo $ct['back_btn']; ?></a>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- SELECTED JOB DETAILS CARD -->
        <?php if ($selected_job): ?>
            <div class="card border-0 mb-4 rounded-4 shadow-sm" style="background-color: rgba(0, 106, 78, 0.06); border-left: 5px solid #006a4e !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <span class="badge bg-success bg-opacity-75 mb-2 px-2 py-1 fs-7">
                                <?php echo htmlspecialchars(translateJobCategory($selected_job['job_category'], $lang)); ?>
                            </span>
                            <h3 class="fw-bold mb-1" style="color: #00563f;">
                                <?php echo htmlspecialchars(translateJobTitle($selected_job['title'], $lang)); ?>
                            </h3>
                            <p class="mb-0 text-muted small">
                                <i class="fa-solid fa-location-dot me-1"></i> 
                                <strong><?php echo $lang == 'bn' ? 'অবস্থান:' : 'Location:'; ?></strong>
                                <?php echo htmlspecialchars(translateDistrict($selected_job['district_name'] ?? '', $lang) ?: 'N/A'); ?> / 
                                <?php echo htmlspecialchars(translateUpazila($selected_job['upazila_name'] ?? '', $lang) ?: 'N/A'); ?> / 
                                <?php echo htmlspecialchars(translateWard($selected_job['ward_name'] ?? '', $lang) ?: 'N/A'); ?> 
                                (<?php echo htmlspecialchars($selected_job['location'] ?? ''); ?>)
                            </p>
                        </div>
                        <div>
                            <span class="badge <?php echo $selected_job['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?> px-3 py-2 fs-6">
                                <i class="fa-solid <?php echo $selected_job['status'] == 'active' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                <?php echo $selected_job['status'] == 'active' ? ($lang == 'bn' ? 'সক্রিয়' : 'Active') : ($lang == 'bn' ? 'বন্ধ' : 'Closed'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <hr class="my-3 opacity-25">
                    
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block fw-semibold mb-1"><?php echo $ct['label_salary']; ?></small>
                            <span class="fs-5 fw-bold text-dark">
                                <?php echo (empty($selected_job['salary']) || strtolower($selected_job['salary']) === 'negotiable') ? $ct['negotiable'] : '৳ ' . translateSalary($selected_job['salary'], $lang); ?>
                            </span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block fw-semibold mb-1"><?php echo $ct['label_deadline']; ?></small>
                            <span class="fs-5 fw-bold text-dark">
                                <?php echo htmlspecialchars(translateDate($selected_job['application_deadline'] ?? 'N/A', $lang)); ?>
                            </span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block fw-semibold mb-1"><?php echo $lang == 'bn' ? 'খালি পদ' : 'Vacancy'; ?></small>
                            <span class="fs-5 fw-bold text-dark">
                                <?php echo htmlspecialchars(translateNumber($selected_job['vacancy'] ?? 'N/A', $lang)); ?> <?php echo $lang == 'bn' ? 'টি' : ''; ?>
                            </span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block fw-semibold mb-1"><?php echo $ct['label_education']; ?></small>
                            <span class="fs-6 fw-bold text-dark">
                                <?php echo htmlspecialchars($selected_job['education_required'] ?: ($lang == 'bn' ? 'উল্লেখ নেই' : 'Not specified')); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- FILTER & SORT PANEL -->
        <div class="card bg-light mb-4">
            <div class="card-body">
                <form method="GET" action="applicants.php" class="row g-3">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold text-primary"><i class="fa-solid fa-briefcase"></i> <?php echo $lang == 'bn' ? 'চাকরি নির্বাচন করুন' : 'Select Job to View Applicants'; ?></label>
                        <select name="job_id" class="form-select form-select-sm border-primary" onchange="this.form.submit()">
                            <option value="all" <?php if($job_id_filter == 0) echo 'selected'; ?>>
                                <?php echo $lang == 'bn' ? 'সকল চাকরি' : 'All Jobs'; ?>
                            </option>
                            <?php foreach ($emp_jobs as $j): ?>
                                <option value="<?php echo $j['job_id']; ?>" <?php if($job_id_filter == $j['job_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(translateJobTitle($j['title'], $lang)); ?> (<?php echo $lang == 'bn' ? translateNumber($j['app_count'], $lang) . ' জন আবেদনকারী' : $j['app_count'] . ' applicants'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $ct['filter_status']; ?></label>
                        <select name="f_status" class="form-select form-select-sm">
                            <option value=""><?php echo $ct['general']; ?></option>
                            <option value="Pending" <?php if($f_status=='Pending') echo 'selected'; ?>><?php echo $ct['status_pending']; ?></option>
                            <option value="Shortlisted" <?php if($f_status=='Shortlisted') echo 'selected'; ?>><?php echo $ct['status_shortlisted']; ?></option>
                            <option value="Accepted" <?php if($f_status=='Accepted') echo 'selected'; ?>><?php echo $ct['status_accepted']; ?></option>
                            <option value="Rejected" <?php if($f_status=='Rejected') echo 'selected'; ?>><?php echo $ct['status_rejected']; ?></option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $ct['filter_edu']; ?></label>
                        <select name="f_edu" class="form-select form-select-sm">
                            <option value="">Any</option>
                            <option value="ssc" <?php if($f_edu=='ssc') echo 'selected'; ?>>SSC</option>
                            <option value="hsc" <?php if($f_edu=='hsc') echo 'selected'; ?>>HSC</option>
                            <option value="diploma" <?php if($f_edu=='diploma') echo 'selected'; ?>>Diploma</option>
                            <option value="bachelor" <?php if($f_edu=='bachelor') echo 'selected'; ?>>Bachelor</option>
                            <option value="masters" <?php if($f_edu=='masters') echo 'selected'; ?>>Masters</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $ct['filter_exp']; ?></label>
                        <input type="number" name="f_exp" class="form-control form-control-sm" value="<?php echo htmlspecialchars($f_exp); ?>" placeholder="Min Years">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $ct['sort_label']; ?></label>
                        <select name="sort_by" class="form-select form-select-sm">
                            <option value="rec_desc" <?php if($sort_by=='rec_desc') echo 'selected'; ?>><?php echo $ct['sort_rec_desc']; ?></option>
                            <option value="rec_asc" <?php if($sort_by=='rec_asc') echo 'selected'; ?>><?php echo $ct['sort_rec_asc']; ?></option>
                            <option value="exp_desc" <?php if($sort_by=='exp_desc') echo 'selected'; ?>><?php echo $ct['sort_exp_desc']; ?></option>
                            <option value="exp_asc" <?php if($sort_by=='exp_asc') echo 'selected'; ?>><?php echo $ct['sort_exp_asc']; ?></option>
                            <option value="date_desc" <?php if($sort_by=='date_desc') echo 'selected'; ?>><?php echo $ct['sort_date_desc']; ?></option>
                            <option value="date_asc" <?php if($sort_by=='date_asc') echo 'selected'; ?>><?php echo $ct['sort_date_asc']; ?></option>
                            <option value="az" <?php if($sort_by=='az') echo 'selected'; ?>><?php echo $ct['sort_az']; ?></option>
                            <option value="za" <?php if($sort_by=='za') echo 'selected'; ?>><?php echo $ct['sort_za']; ?></option>
                        </select>
                    </div>
                    
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <small class="text-muted fw-bold">
                            <?php echo str_replace(['{X}', '{Y}'], [translateNumber($filtered_count, $lang), translateNumber($total_applicants, $lang)], $ct['showing_x_of_y']); ?>
                        </small>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> <?php echo $ct['filter_btn']; ?></button>
                            <a href="applicants.php<?php echo $job_id_filter > 0 ? '?job_id='.$job_id_filter : ''; ?>" class="btn btn-outline-secondary btn-sm"><?php echo $ct['reset_btn']; ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php 
        function renderApplicantRow($row, $ct, $lang, $count, $show_job_title = false) {
            $status_html = "";
            if ($row['status'] == 'Accepted' || $row['status'] == 'Selected') {
                $status_html = "<span class='badge bg-success'>{$row['status']}</span>";
            } elseif (strpos($row['status'], 'Rejected') !== false) {
                $status_html = "<span class='badge bg-danger'>{$row['status']}</span>";
            } elseif ($row['status'] == 'Shortlisted') {
                $status_html = "<span class='badge bg-primary'>{$row['status']}</span>";
            } elseif (strpos($row['status'], 'Interview') !== false) {
                $status_html = "<span class='badge bg-info text-dark'>{$row['status']}</span>";
            } else {
                $status_html = "<span class='badge bg-warning text-dark'>{$row['status']}</span>";
            }
            
            $skills_html = empty($row['skills']) ? $ct['not_specified'] : implode('</span> <span class="badge bg-secondary mb-1">', array_map('htmlspecialchars', $row['skills']));
            if (!empty($row['skills'])) $skills_html = '<span class="badge bg-secondary mb-1">'.$skills_html.'</span>';

            echo "<tr>
                <td>".translateNumber($count, $lang)."</td>
                <td>
                    <small class='text-muted'>".($lang == 'bn' ? 'আবেদনের তারিখ:' : 'Applied On:')."</small><br>
                    <strong>".translateDate(date('d M Y', strtotime($row['applied_at'])), $lang)."</strong>
                </td>
                <td>
                    <a href='../candidate_biodata.php?application_id={$row['application_id']}' class='text-decoration-none'>
                        <strong>".htmlspecialchars($row['full_name'])."</strong>
                    </a><br>
                    <small>{$row['email']}</small><br>";
            if ($show_job_title) {
                echo "<span class='badge bg-dark mb-1'>" . htmlspecialchars(translateJobTitle($row['job_title'] ?? '', $lang)) . "</span><br>";
            }
            echo "<span class='badge bg-info text-dark'>Match: ".translateNumber($row['score'], $lang)."%</span>
                    <span class='badge bg-success bg-opacity-75 ms-1' title='Profile Completeness'>Profile: ".translateNumber($row['profile_score'] ?? 0, $lang)."%</span>
                    <span class='badge bg-warning text-dark ms-1' title='CV Strength'>CV: ".translateNumber($row['cv_score'] ?? 0, $lang)."%</span>
                </td>
                <td>
                    <small><strong>{$ct['label_education']}</strong> ".(!empty($row['education']) ? htmlspecialchars($row['education']) : $ct['not_specified'])."</small><br>
                    <small><strong>{$ct['filter_exp']}:</strong> ".translateNumber($row['experience_years']??0, $lang)."</small><br>
                    <div class='mt-1'>{$skills_html}</div>
                </td>
                <td>{$status_html}</td>
                <td>
                    <a href='../candidate_biodata.php?application_id={$row['application_id']}' class='btn btn-info btn-sm mb-1 text-white'>
                        <i class='fa fa-user'></i> {$ct['btn_biodata']}
                    </a><br>";
                    
            if ($row['status'] == 'Interview Scheduled' || $row['status'] == 'Interview Proposed') {
                echo "<button class='btn btn-secondary btn-sm mb-1 w-100' disabled><i class='fa fa-lock'></i> Scheduling Locked</button><br>";
            } elseif ($row['status'] == 'Pending' || $row['status'] == 'Under Review' || $row['status'] == 'Shortlisted' || $row['status'] == 'Interview Cancelled') {
                echo "<a href='schedule_interview.php?application_id={$row['application_id']}' class='btn btn-primary btn-sm mb-1 w-100'>
                        <i class='fa fa-calendar-check'></i> Interview
                      </a><br>";
            }
            
            if ($row['status'] == 'Pending' || $row['status'] == 'Under Review' || $row['status'] == 'Shortlisted') {
                if ($row['status'] != 'Shortlisted') {
                    echo "<a href='?action=shortlist&application_id={$row['application_id']}' class='btn btn-primary btn-sm me-1' onclick=\"return confirm('".addslashes($ct['confirm_shortlist'])."')\">{$ct['btn_shortlist']}</a>";
                }
                echo "<a href='?action=accept&application_id={$row['application_id']}' class='btn btn-success btn-sm me-1' onclick=\"return confirm('".addslashes($ct['confirm_accept'])."')\">{$ct['btn_accept']}</a>
                      <a href='?action=reject&application_id={$row['application_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('".addslashes($ct['confirm_reject'])."')\">{$ct['btn_reject']}</a>";
            }
            echo "</td></tr>";
        }
        ?>

        <?php if (!empty($top_5)): ?>
            <h4 class="text-success mb-3 border-bottom pb-2"><i class="fa fa-star text-warning"></i> <?php echo $ct['top5_title']; ?></h4>
            <div class="table-responsive mb-5">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo $ct['th_serial']; ?></th>
                            <th><?php echo $lang == 'bn' ? 'আবেদনের তারিখ' : 'Applied On'; ?></th>
                            <th><?php echo $ct['th_applicant_info']; ?></th>
                            <th><?php echo $ct['th_skills_edu']; ?></th>
                            <th><?php echo $ct['th_status']; ?></th>
                            <th><?php echo $ct['th_action']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $c=1; foreach($top_5 as $r) renderApplicantRow($r, $ct, $lang, $c++, ($job_id_filter == 0)); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($others) || empty($top_5)): ?>
            <?php if (!empty($top_5)): ?>
                <h4 class="text-secondary mb-3 border-bottom pb-2"><?php echo $ct['others_title']; ?></h4>
            <?php endif; ?>
            
            <?php if (empty($others) && empty($top_5)): ?>
                <div class="alert alert-warning"><?php echo $ct['no_applicants']; ?></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th><?php echo $ct['th_serial']; ?></th>
                                <th><?php echo $lang == 'bn' ? 'আবেদনের তারিখ' : 'Applied On'; ?></th>
                                <th><?php echo $ct['th_applicant_info']; ?></th>
                                <th><?php echo $ct['th_skills_edu']; ?></th>
                                <th><?php echo $ct['th_status']; ?></th>
                                <th><?php echo $ct['th_action']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $c=1; foreach($others as $r) renderApplicantRow($r, $ct, $lang, $c++, ($job_id_filter == 0)); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>
