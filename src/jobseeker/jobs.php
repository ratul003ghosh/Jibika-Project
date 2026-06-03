<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

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

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';
$message = "";
$message_type = "";

// SAVE JOB
if (isset($_GET['save'])) {
    $job_id = intval($_GET['save']);
    $stmt = $conn->prepare("SELECT id FROM saved_jobs WHERE user_id=? AND job_id=?");
    $stmt->bind_param("ii", $user_id, $job_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $message = $lang == 'bn' ? "চাকরিটি ইতিমধ্যে সংরক্ষণ করা হয়েছে।" : "Job already saved.";
        $message_type = "warning";
    } else {
        $stmt2 = $conn->prepare("INSERT INTO saved_jobs (user_id, job_id) VALUES (?,?)");
        $stmt2->bind_param("ii", $user_id, $job_id);
        if ($stmt2->execute()) {
            $message = $lang == 'bn' ? "চাকরি সংরক্ষণ করা হয়েছে!" : "Job saved!";
            $message_type = "success";
        } else {
            $message = $lang == 'bn' ? "সংরক্ষণ করতে ত্রুটি হয়েছে।" : "Error saving job.";
            $message_type = "danger";
        }
    }
}

// UNSAVE JOB
if (isset($_GET['unsave'])) {
    $job_id = intval($_GET['unsave']);
    $stmt = $conn->prepare("DELETE FROM saved_jobs WHERE user_id=? AND job_id=?");
    $stmt->bind_param("ii", $user_id, $job_id);
    $stmt->execute();
    $message = $lang == 'bn' ? "সংরক্ষিত চাকরিটি মুছে ফেলা হয়েছে।" : "Saved job removed.";
    $message_type = "info";
}

// APPLY JOB
if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);
    $job_check = $conn->prepare("SELECT status, application_deadline FROM jobs WHERE job_id=? LIMIT 1");
    $job_check->bind_param("i", $job_id);
    $job_check->execute();
    $res = $job_check->get_result();

    if (!$res || $res->num_rows == 0) {
        $message = $lang == 'bn' ? "চাকরিটি পাওয়া যায়নি।" : "Job not found.";
        $message_type = "danger";
    } else {
        $job_data = $res->fetch_assoc();
        $is_closed = (($job_data['status'] ?? 'active') == 'closed');
        $is_deadline_over = (!empty($job_data['application_deadline']) && $job_data['application_deadline'] < date('Y-m-d'));

        if ($is_closed) {
            $message = $lang == 'bn' ? "এই চাকরিটি বন্ধ রয়েছে।" : "This job is closed.";
            $message_type = "warning";
        } elseif ($is_deadline_over) {
            $message = $lang == 'bn' ? "আবেদনের শেষ সময়সীমা পার হয়ে গেছে।" : "Application deadline is over.";
            $message_type = "warning";
        } else {
            $check = $conn->prepare("SELECT application_id FROM applications WHERE job_id=? AND user_id=?");
            $check->bind_param("ii", $job_id, $user_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $message = $lang == 'bn' ? "আপনি ইতিমধ্যে এই চাকরিতে আবেদন করেছেন।" : "You already applied for this job.";
                $message_type = "warning";
            } else {
                $apply = $conn->prepare("INSERT INTO applications (job_id, user_id) VALUES (?,?)");
                $apply->bind_param("ii", $job_id, $user_id);
                if ($apply->execute()) {
                    $message = $lang == 'bn' ? "সফলভাবে আবেদন করা হয়েছে!" : "Applied successfully!";
                    $message_type = "success";
                } else {
                    $message = ($lang == 'bn' ? "আবেদনে ত্রুটি: " : "Error: ") . $conn->error;
                    $message_type = "danger";
                }
            }
        }
    }
}

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");

$search       = isset($_GET['search'])      ? trim($_GET['search'])          : '';
$district_id  = isset($_GET['district_id']) ? intval($_GET['district_id'])   : 0;
$job_type     = isset($_GET['job_type'])    ? trim($_GET['job_type'])        : '';
$categories   = isset($_GET['category'])    ? $_GET['category']              : [];
$experiences  = isset($_GET['experience'])  ? $_GET['experience']            : [];
$min_salary   = isset($_GET['min_salary'])  ? intval($_GET['min_salary'])    : 0;
$max_salary   = isset($_GET['max_salary'])  ? intval($_GET['max_salary'])    : 0;
$sort_by      = isset($_GET['sort_by'])     ? $_GET['sort_by']               : 'default';
$view_mode    = isset($_GET['view'])        ? $_GET['view']                  : 'grid';

$sql = "SELECT jobs.*, d.district_name, u.upazila_name, users.full_name AS company_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u  ON jobs.upazila_id  = u.upazila_id
        LEFT JOIN users       ON jobs.employer_id = users.user_id
        WHERE 1=1";

if ($search != "") {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (jobs.title LIKE '%$s%' OR jobs.description LIKE '%$s%' OR jobs.job_category LIKE '%$s%')";
}
if ($district_id > 0) $sql .= " AND jobs.district_id = '$district_id'";

$job_types_arr = isset($_GET['job_types']) ? $_GET['job_types'] : [];
if ($job_type != "" && !in_array($job_type, $job_types_arr)) $job_types_arr[] = $job_type;
if (!empty($job_types_arr)) {
    $t_safe = array_map(fn($t) => "'" . $conn->real_escape_string($t) . "'", $job_types_arr);
    $sql .= " AND jobs.job_type IN (" . implode(",", $t_safe) . ")";
}
if (!empty($categories)) {
    $c_safe = array_map(fn($c) => "'" . $conn->real_escape_string($c) . "'", $categories);
    $sql .= " AND jobs.job_category IN (" . implode(",", $c_safe) . ")";
}
if (!empty($experiences)) {
    $clauses = array_map(fn($e) => "jobs.experience_required LIKE '%" . $conn->real_escape_string($e) . "%'", $experiences);
    $sql .= " AND (" . implode(" OR ", $clauses) . ")";
}
if ($min_salary > 0) $sql .= " AND jobs.salary >= $min_salary";
if ($max_salary > 0) $sql .= " AND jobs.salary <= $max_salary AND jobs.salary > 0";

if ($sort_by == 'salary_high') {
    $sql .= " ORDER BY CAST(jobs.salary AS UNSIGNED) DESC, jobs.job_id DESC";
} else {
    $sql .= " ORDER BY jobs.job_id DESC";
}

$jobs_result = $conn->query($sql);
$total_jobs  = $jobs_result ? $jobs_result->num_rows : 0;
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>
<?php
$lang = $_SESSION['lang'] ?? 'bn';
$jt = [
    'bn' => [
        'badge'=>'জীবিকা জবস','hero_title'=>'আপনার স্বপ্নের চাকরি খুঁজুন',
        'hero_desc'=>'সারা বাংলাদেশের শীর্ষ নিয়োগকর্তাদের সাথে যুক্ত হন এবং আপনার ক্যারিয়ারে পরবর্তী পদক্ষেপ নিন।',
        'search_ph'=>'চাকরির নাম, কীওয়ার্ড বা কোম্পানি','all_locations'=>'সকল জেলা','any_type'=>'সকল ধরন',
        'search_btn'=>'খুঁজুন','filters'=>'ফিল্টার','clear_all'=>'সব মুছুন',
        'job_cat'=>'ক্যাটাগরি','job_type'=>'চাকরির ধরন','exp_level'=>'অভিজ্ঞতা',
        'jobs_found'=>'টি চাকরি পাওয়া গেছে','deadline'=>'শেষ তারিখ','closed'=>'বন্ধ',
        'applied'=>'আবেদনকৃত','apply_now'=>'আবেদন করুন','save_job'=>'সেভ করুন','saved'=>'সেভ হয়েছে',
        'no_jobs'=>'কোনো চাকরি পাওয়া যায়নি',
        'no_jobs_desc'=>'আপনার মানদণ্ডের সাথে মিলে এমন কোনো সুযোগ খুঁজে পাওয়া যায়নি।',
        'reset_search'=>'সার্চ রিসেট করুন','not_specified'=>'উল্লেখ নেই','negotiable'=>'আলোচনা সাপেক্ষে',
        'multiple_loc'=>'একাধিক স্থান','unknown_emp'=>'অজ্ঞাত নিয়োগকর্তা',
        'student_pt'=>'স্টুডেন্ট পার্ট-টাইম','day_labor'=>'দৈনিক শ্রমিক',
        'internships'=>'ইন্টার্নশিপ','full_time'=>'পূর্ণকালীন','remote'=>'রিমোট',
        'posted'=>'পোস্ট হয়েছে','job_alerts'=>'জব অ্যালার্ট',
        'job_alerts_sub'=>'নতুন চাকরির বিজ্ঞপ্তি পান','update_profile'=>'প্রোফাইল আপডেট',
        'view_details'=>'বিস্তারিত দেখুন','quick'=>'দ্রুত ফিল্টার:',
    ],
    'en' => [
        'badge'=>'JIBIKA JOBS','hero_title'=>'Find Your Dream Job',
        'hero_desc'=>'Connect with top employers across Bangladesh and take the next step in your career.',
        'search_ph'=>'Job title, keywords or company','all_locations'=>'All Districts','any_type'=>'All Types',
        'search_btn'=>'Search','filters'=>'Filters','clear_all'=>'Clear All',
        'job_cat'=>'Category','job_type'=>'Job Type','exp_level'=>'Experience',
        'jobs_found'=>'jobs found','deadline'=>'Deadline','closed'=>'Closed',
        'applied'=>'Applied','apply_now'=>'Apply Now','save_job'=>'Save','saved'=>'Saved',
        'no_jobs'=>'No Jobs Found',
        'no_jobs_desc'=>'We couldn\'t find any opportunities matching your criteria. Try adjusting your filters.',
        'reset_search'=>'Reset Search','not_specified'=>'Not specified','negotiable'=>'Negotiable',
        'multiple_loc'=>'Multiple Locations','unknown_emp'=>'Unknown Employer',
        'student_pt'=>'Student Part-time','day_labor'=>'Day Labor',
        'internships'=>'Internships','full_time'=>'Full-time','remote'=>'Remote',
        'posted'=>'Posted','job_alerts'=>'Job Alerts',
        'job_alerts_sub'=>'Get notified for new matching jobs','update_profile'=>'Update Profile',
        'view_details'=>'View Details','quick'=>'Quick:',
    ]
];
$t = $jt[$lang];

$job_type_translations = [
    'bn' => [
        'Full-time' => 'পূর্ণকালীন',
        'Part-time' => 'পার্ট-টাইম',
        'Part-time (Student)' => 'পার্ট-টাইম (ছাত্র)',
        'Day Labor' => 'দৈনিক শ্রমিক',
        'Internship' => 'ইন্টার্নশিপ',
        'Contract' => 'চুক্তিভিত্তিক',
        'Remote' => 'রিমোট',
    ],
    'en' => [
        'Full-time' => 'Full-time',
        'Part-time' => 'Part-time',
        'Part-time (Student)' => 'Part-time (Student)',
        'Day Labor' => 'Day Labor',
        'Internship' => 'Internship',
        'Contract' => 'Contract',
        'Remote' => 'Remote',
    ]
];

$exp_translations = [
    'bn' => [
        'Entry Level' => 'প্রবেশ স্তর',
        'Mid Level' => 'মধ্যম স্তর',
        'Senior Level' => 'উচ্চ স্তর',
        '1 Year' => '১ বছর',
        '2 Years' => '২ বছর',
        '3+ Years' => '৩+ বছর',
    ],
    'en' => [
        'Entry Level' => 'Entry Level',
        'Mid Level' => 'Mid Level',
        'Senior Level' => 'Senior Level',
        '1 Year' => '1 Year',
        '2 Years' => '2 Years',
        '3+ Years' => '3+ Years',
    ]
];

$cat_translations = [
    'bn' => [
        'IT & Tech' => 'আইটি ও টেক',
        'Garments' => 'গার্মেন্টস',
        'Driving' => 'ড্রাইভিং',
        'Education' => 'শিক্ষা',
        'Health' => 'স্বাস্থ্য',
        'Marketing' => 'মার্কেটিং',
        'Engineering' => 'ইঞ্জিনিয়ারিং',
        'Finance' => 'অর্থনীতি ও অর্থ',
    ],
    'en' => [
        'IT & Tech' => 'IT & Tech',
        'Garments' => 'Garments',
        'Driving' => 'Driving',
        'Education' => 'Education',
        'Health' => 'Health',
        'Marketing' => 'Marketing',
        'Engineering' => 'Engineering',
        'Finance' => 'Finance',
    ]
];

$district_translations = [
    'bn' => [
        'Dhaka' => 'ঢাকা',
        'Chattogram' => 'চট্টগ্রাম',
        'Khulna' => 'খুলনা',
        'Rajshahi' => 'রাজশাহী',
        'Barishal' => 'বরিশাল',
        'Sylhet' => 'সিলেট',
        'Rangpur' => 'রংপুর',
        'Mymensingh' => 'ময়মনসিংহ'
    ],
    'en' => [
        'Dhaka' => 'Dhaka',
        'Chattogram' => 'Chattogram',
        'Khulna' => 'Khulna',
        'Rajshahi' => 'Rajshahi',
        'Barishal' => 'Barishal',
        'Sylhet' => 'Sylhet',
        'Rangpur' => 'Rangpur',
        'Mymensingh' => 'Mymensingh'
    ]
];

$logo_colors = [
    ['bg'=>'#EEF2FF','color'=>'#4F46E5','grad'=>'linear-gradient(135deg,#EEF2FF,#C7D2FE)'],
    ['bg'=>'#FEF3C7','color'=>'#D97706','grad'=>'linear-gradient(135deg,#FEF3C7,#FDE68A)'],
    ['bg'=>'#ECFDF5','color'=>'#059669','grad'=>'linear-gradient(135deg,#ECFDF5,#A7F3D0)'],
    ['bg'=>'#FFF1F2','color'=>'#E11D48','grad'=>'linear-gradient(135deg,#FFF1F2,#FECDD3)'],
    ['bg'=>'#F0F9FF','color'=>'#0284C7','grad'=>'linear-gradient(135deg,#F0F9FF,#BAE6FD)'],
    ['bg'=>'#FDF4FF','color'=>'#9333EA','grad'=>'linear-gradient(135deg,#FDF4FF,#E9D5FF)'],
    ['bg'=>'#FFF7ED','color'=>'#EA580C','grad'=>'linear-gradient(135deg,#FFF7ED,#FED7AA)'],
    ['bg'=>'#F0FDF4','color'=>'#16A34A','grad'=>'linear-gradient(135deg,#F0FDF4,#BBF7D0)'],
];

$type_icons = [
    'Full-time'=>'fa-regular fa-clock','Part-time'=>'fa-regular fa-clock',
    'Part-time (Student)'=>'fa-solid fa-graduation-cap','Day Labor'=>'fa-solid fa-hammer',
    'Internship'=>'fa-solid fa-star','Contract'=>'fa-solid fa-file-contract',
    'Remote'=>'fa-solid fa-laptop-house',
];
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

/* =============================================
   JOB PORTAL V4 — ENTERPRISE REDESIGN
   Theme: Jibika Bangladesh Gov Portal
   Primary: #006a4e | Accent: #f42a41
   ============================================= */

:root {
    --primary-color: #006a4e;      /* Brand Green */
    --primary-hover: #00523c;
    --primary-light: #f0fdf4;
    --primary-border: #bbf7d0;
    
    --accent-color: #f42a41;       /* Brand Red */
    --accent-hover: #d31e33;
    --accent-light: #fff1f2;
    --accent-border: #fecdd3;
    
    --text-primary: #0f172a;       /* Slate 900 */
    --text-secondary: #475569;     /* Slate 600 */
    --text-muted: #94a3b8;         /* Slate 400 */
    --bg-main: #f8fafc;            /* Soft slate background */
    --border-light: #e2e8f0;       /* Slate 200 */
    --border-hover: #cbd5e1;       /* Slate 300 */
    
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    
    --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.03), 0 1px 2px rgba(15, 23, 42, 0.06);
    --shadow-md: 0 8px 20px -4px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
    --shadow-lg: 0 20px 32px -8px rgba(15, 23, 42, 0.08), 0 8px 16px -4px rgba(15, 23, 42, 0.04);
    --shadow-hover: 0 24px 48px -12px rgba(0, 106, 78, 0.14), 0 8px 24px -4px rgba(0, 106, 78, 0.06);
    --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* --- GLOBAL OVERRIDES --- */
body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
    background-color: var(--bg-main) !important;
    color: var(--text-primary) !important;
    -webkit-font-smoothing: antialiased;
}

/* Container limits to keep it center-aligned on ultra-wide screens */
.container-main-content {
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
}

/* Custom focus rings for accessibility */
a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible {
    outline: 2px solid var(--primary-color) !important;
    outline-offset: 2px !important;
}

/* --- HERO SECTION --- */
.jp-hero {
    background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
    padding: 75px 0 115px;
    position: relative;
    overflow: hidden;
}
.jp-hero::before {
    content: ''; position: absolute; top: -120px; right: -120px;
    width: 450px; height: 450px;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    border-radius: 50%;
}
.jp-hero::after {
    content: ''; position: absolute; bottom: -100px; left: -80px;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(244,42,65,0.08) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-grid-pattern {
    position: absolute; inset: 0; z-index: 0;
    background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 24px 24px;
}
.hero-inner { position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; }
.jp-hero .hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff; border-radius: 50px; padding: 6px 16px;
    font-size: 0.75rem; font-weight: 700; letter-spacing: 1.5px;
    text-transform: uppercase; margin-bottom: 24px;
}
.jp-hero h1 {
    font-size: 2.75rem; font-weight: 800; color: #fff;
    line-height: 1.2; margin-bottom: 16px; letter-spacing: -0.5px;
}
.jp-hero p.lead {
    color: rgba(255,255,255,0.8); font-size: 1.05rem;
    max-width: 600px; margin: 0 auto; line-height: 1.6;
}
.hero-stats { display: flex; gap: 0; margin-top: 36px; justify-content: center; }
.hero-stat {
    text-align: center; padding: 0 32px;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.hero-stat:last-child { border-right: none; }
.hero-stat .num { font-size: 1.8rem; font-weight: 800; color: #fff; line-height: 1; }
.hero-stat .lbl { font-size: 0.75rem; color: rgba(255,255,255,0.5); font-weight: 600; margin-top: 6px; letter-spacing: 0.5px; text-transform: uppercase; }

/* --- SEARCH CARD --- */
.search-card {
    background: #ffffff;
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    margin-top: -55px;
    position: relative; z-index: 8;
    border: 1px solid var(--border-light);
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}
.search-field {
    display: flex; align-items: center;
    background: #f8fafc; border-radius: var(--radius-md);
    padding: 4px 16px; border: 1.5px solid var(--border-light);
    transition: var(--transition-smooth); height: 52px;
}
.search-field:focus-within {
    background: #fff; border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(0, 106, 78, 0.08);
}
.search-field .fi { color: var(--text-secondary); font-size: 1rem; flex-shrink: 0; }
.search-field input, .search-field select {
    border: none !important; background: transparent !important;
    box-shadow: none !important; padding: 8px 12px;
    font-size: 0.9rem; font-weight: 500; color: var(--text-primary); width: 100%;
}
.search-field input::placeholder { color: var(--text-muted); }
.search-field input:focus, .search-field select:focus { outline: none; }
.btn-search {
    background: var(--primary-color);
    color: #fff; border: none; border-radius: var(--radius-md);
    height: 52px; font-weight: 600; font-size: 0.95rem; width: 100%;
    transition: var(--transition-smooth); cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(0, 106, 78, 0.2);
}
.btn-search:hover {
    background: var(--primary-hover);
    box-shadow: 0 6px 16px rgba(0, 106, 78, 0.3);
    color: #fff;
}

/* --- QUICK FILTERS --- */
.quick-filters { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; align-items: center; }
.qf-label { font-size: 0.82rem; color: var(--text-secondary); font-weight: 600; margin-right: 4px; }
.filter-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f8fafc; color: var(--text-secondary);
    border: 1.5px solid var(--border-light); border-radius: 50px;
    padding: 6px 16px; font-size: 0.82rem; font-weight: 600;
    transition: var(--transition-smooth); cursor: pointer;
    white-space: nowrap; user-select: none;
}
.filter-pill:hover { background: var(--primary-light); border-color: var(--primary-border); color: var(--primary-color); }
.filter-pill.active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); box-shadow: 0 2px 8px rgba(0,106,78,0.25); }
.filter-pill.red { background: var(--accent-light); color: var(--accent-color); border-color: var(--accent-border); }
.filter-pill.red:hover { background: var(--accent-color); color: #fff; border-color: var(--accent-color); }

/* --- TOP CATEGORY PILLS --- */
.top-category-pills {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: 12px;
    margin-top: 24px;
    margin-bottom: 28px;
    padding: 4px 12px;
    scrollbar-width: none; /* Firefox */
    -webkit-overflow-scrolling: touch;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}
.top-category-pills::-webkit-scrollbar { display: none; } /* Chrome/Safari */
.cat-pill-label {
    background: #fff;
    border: 1px solid var(--border-light);
    border-radius: 50px;
    padding: 10px 22px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition-smooth);
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: var(--shadow-sm);
    white-space: nowrap;
}
.cat-pill-label:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}
.cat-pill-label.active {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
    box-shadow: 0 4px 10px rgba(0,106,78,0.15);
}

/* Ensure navbar stays on top of sticky sidebar */
.main-navbar-gov {
    z-index: 1030 !important;
}

/* --- SIDEBAR --- */
.filter-sidebar {
    background: #fff; border-radius: 16px;
    border: 1px solid var(--border-light); overflow: hidden;
    position: sticky !important;
    top: 80px !important;   /* below the sticky navbar */
    z-index: 100 !important;   /* below navbar z-index:1030 */
    box-shadow: var(--shadow-sm);
}
.sidebar-header {
    padding: 18px 20px; border-bottom: 1px solid var(--border-light);
    display: flex; justify-content: space-between; align-items: center;
}
.sidebar-section { padding: 18px 20px; border-bottom: 1px solid var(--border-light); }
.sidebar-section:last-child { border-bottom: none; }
.sidebar-label {
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 12px;
}
.sidebar-check .form-check { padding: 4px 0 4px 28px; }
.sidebar-check .form-check-label { font-size: 0.88rem; color: var(--text-secondary); font-weight: 500; cursor: pointer; transition: var(--transition-smooth); }
.sidebar-check .form-check-label:hover { color: var(--primary-color); }
.sidebar-check .form-check-input { margin-top: 0.18rem; cursor: pointer; accent-color: var(--primary-color); }
.sidebar-check .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
.sidebar-check .form-check-input:focus { box-shadow: 0 0 0 0.2rem rgba(0,106,78,0.15); }

/* Sidebar CTA */
.sidebar-cta {
    margin: 16px; padding: 22px 18px;
    background: radial-gradient(circle at 100% 0%, var(--primary-hover) 0%, var(--primary-color) 100%);
    border-radius: var(--radius-md); text-align: center; color: #fff;
    position: relative; overflow: hidden;
}
.sidebar-cta i.bell { font-size: 1.6rem; display: block; margin-bottom: 10px; color: #fbbf24; }
.sidebar-cta h6 { font-weight: 700; font-size: 0.95rem; margin-bottom: 6px; }
.sidebar-cta p { font-size: 0.8rem; opacity: 0.85; margin-bottom: 14px; line-height: 1.4; }

/* --- JOBS HEADER --- */
.jobs-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.jobs-count { font-size: 0.95rem; color: var(--text-secondary); font-weight: 500; }
.jobs-count strong { color: var(--primary-color); font-size: 1.1rem; }
.view-toggle { display: flex; gap: 4px; background: #e2e8f0; border-radius: var(--radius-sm); padding: 4px; }
.view-btn {
    width: 34px; height: 34px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent;
    color: var(--text-secondary); font-size: 0.9rem; cursor: pointer; transition: var(--transition-smooth);
    text-decoration: none;
}
.view-btn.active, .view-btn:hover { background: #fff; color: var(--primary-color); box-shadow: var(--shadow-sm); }

.active-filter-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--primary-light); border: 1px solid var(--primary-border); color: var(--primary-color);
    border-radius: 50px; padding: 6px 14px; font-size: 0.78rem; font-weight: 600;
}

/* ============ JOB GRID CARD (Airbnb Overhaul Style) ============ */
.job-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

.job-card {
    background: #fff;
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-smooth);
}

.job-card .job-cover {
    height: 180px;
    background-size: cover;
    background-position: center;
    position: relative;
    border-bottom: 1px solid var(--border-light);
}

/* Translucent overlays on the cover image */
.grid-badge-top-left {
    position: absolute;
    top: 14px;
    left: 14px;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: #fff;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.72rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 2;
}

.grid-save-btn {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 32px;
    height: 32px;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: #fff !important;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition-smooth);
    z-index: 2;
    border: none;
    box-shadow: none;
}
.grid-save-btn:hover {
    background: rgba(244, 42, 65, 0.85);
    color: #fff !important;
}
.grid-save-btn.saved {
    background: rgba(244, 42, 65, 0.9);
    color: #fff !important;
}

/* Card Body Details */
.grid-card-body {
    padding: 18px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    gap: 10px;
}

.grid-job-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Subtitle Row (Company & Location) */
.grid-subtitle {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}
.grid-company {
    font-weight: 600;
    color: var(--text-primary);
}
.grid-separator {
    color: var(--text-muted);
}
.grid-location {
    color: var(--text-secondary);
}

/* Tags Row (Full Width underneath subtitle) */
.grid-tags-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
}

.grid-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid var(--border-light);
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 0.76rem;
    color: var(--text-secondary);
    font-weight: 600;
    background: #f8fafc;
    white-space: nowrap;
}
.grid-meta-pill i {
    color: var(--primary-color);
}
.grid-meta-pill.overdue {
    background: var(--accent-light);
    border-color: var(--accent-border);
    color: var(--accent-color);
}
.grid-meta-pill.overdue i {
    color: var(--accent-color);
}

/* Footer Row: Salary & Apply Button */
.grid-footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px dashed var(--border-light);
}

.grid-salary-info {
    display: flex;
    align-items: center;
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--primary-color);
    white-space: nowrap;
}
.grid-salary-info i {
    color: var(--primary-color);
}

/* ============ JOB LIST CARD (Mockup Overhaul) ============ */
.job-list-card {
    background: #fff;
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: 20px;
    display: flex;
    flex-direction: row;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-smooth);
    align-items: stretch;
}

.list-image-container {
    width: 140px;
    height: auto;
    min-height: 120px;
    background-size: cover;
    background-position: center;
    border-radius: var(--radius-md);
    flex-shrink: 0;
    border: 1px solid var(--border-light);
}

.list-content-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 12px;
}

.list-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.list-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background-color: #ecfdf5;
    color: #059669;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.78rem;
    font-weight: 600;
}

.job-list-card .save-btn {
    width: auto;
    height: auto;
    border: none;
    background: transparent;
    color: var(--text-muted);
    font-size: 1.15rem;
    padding: 0;
    transition: var(--transition-smooth);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: none;
}
.job-list-card .save-btn:hover,
.job-list-card .save-btn.saved {
    color: var(--accent-color);
    background: transparent;
}

.list-card-middle {
    display: flex;
    align-items: center;
    gap: 16px;
}

.list-logo-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.list-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1.3;
    letter-spacing: -0.2px;
}

.list-company {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.list-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.list-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.lmeta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.lmeta i {
    color: var(--primary-color);
    font-size: 0.95rem;
}

/* Button style overrides for both grid and list cards */
.btn-apply-card {
    background-color: var(--primary-color);
    color: #fff !important;
    border: none;
    border-radius: 50px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition-smooth);
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(0, 106, 78, 0.15);
    white-space: nowrap;
    cursor: pointer;
}
.btn-apply-card:hover {
    background-color: var(--primary-hover);
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 106, 78, 0.25);
}
.btn-apply-card.applied-btn {
    background-color: #ecfdf4;
    color: var(--primary-color) !important;
    border: 1px solid var(--primary-border);
    box-shadow: none;
    cursor: default;
    pointer-events: none;
}
.btn-apply-card.disabled-btn {
    background-color: #f1f5f9;
    color: var(--text-muted) !important;
    border: 1px solid var(--border-light);
    box-shadow: none;
    cursor: not-allowed;
    pointer-events: none;
}

@media (max-width: 767px) {
    .job-list-card {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
    .list-image-container {
        width: 100%;
        height: 160px;
    }
    .list-card-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    .list-card-bottom .list-action-btn,
    .list-card-bottom .btn-apply-card {
        width: 100%;
        justify-content: center;
    }
}
/* --- EMPTY STATE --- */
.empty-jobs {
    background: #fff; border-radius: var(--radius-lg); padding: 80px 24px;
    text-align: center; border: 2px dashed var(--border-light);
    box-shadow: var(--shadow-sm);
}
.empty-jobs .empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; font-size: 2.2rem; color: var(--primary-color);
}

/* --- TOAST --- */
.msg-toast {
    border-radius: var(--radius-md); border: none; font-weight: 500;
    box-shadow: var(--shadow-md);
    animation: slideIn 0.4s ease-out;
}
@keyframes slideIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

/* --- SKELETON LOADERS --- */
.skeleton-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-light);
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    overflow: hidden;
    height: 360px;
}
.skeleton-card::after, .skeleton-list-card::after {
    content: "";
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    transform: translateX(-100%);
    animation: loadingShimmer 1.5s infinite;
}
@keyframes loadingShimmer { 100% { transform: translateX(100%); } }

.skeleton-thumb {
    height: 180px;
    background: #e2e8f0;
    margin: -22px -22px 12px -22px;
}
.skeleton-text {
    background: #e2e8f0;
    border-radius: 4px;
}
.skeleton-title {
    width: 65%;
    height: 20px;
}
.skeleton-salary {
    width: 60px;
    height: 24px;
    border-radius: 6px;
}
.skeleton-subtitle {
    width: 45%;
    height: 14px;
}
.skeleton-pills {
    display: flex;
    gap: 8px;
    margin-top: 4px;
}
.skeleton-pill {
    width: 70px;
    height: 22px;
    background: #e2e8f0;
    border-radius: 6px;
}
.skeleton-btn {
    width: 90px;
    height: 34px;
    background: #e2e8f0;
    border-radius: 50px;
}

.skeleton-list-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-light);
    padding: 20px;
    display: flex;
    flex-direction: row;
    gap: 20px;
    position: relative;
    overflow: hidden;
    height: 160px;
}
.skeleton-list-thumb {
    width: 120px;
    height: 120px;
    background: #e2e8f0;
    border-radius: var(--radius-md);
    flex-shrink: 0;
}
.skeleton-list-badge {
    width: 90px;
    height: 24px;
    background: #e2e8f0;
    border-radius: 6px;
}
.skeleton-list-save {
    width: 20px;
    height: 20px;
    background: #e2e8f0;
    border-radius: 4px;
}
.skeleton-list-logo {
    width: 48px;
    height: 48px;
    background: #e2e8f0;
    border-radius: 50%;
    flex-shrink: 0;
}
.skeleton-list-title {
    width: 50%;
    height: 20px;
}
.skeleton-list-company {
    width: 30%;
    height: 14px;
}
.skeleton-list-meta {
    display: flex;
    gap: 16px;
}
.skeleton-list-meta-item {
    width: 80px;
    height: 16px;
    background: #e2e8f0;
    border-radius: 4px;
}
.skeleton-list-btn {
    width: 100px;
    height: 38px;
    background: #e2e8f0;
    border-radius: 50px;
}

/* --- RESPONSIVE --- */
@media (max-width: 1199px) {
    .job-grid { grid-template-columns: repeat(3, 1fr); gap: 12px; }
}
@media (max-width: 991px) {
    .jp-hero h1 { font-size: 2.2rem; }
    .job-grid { grid-template-columns: 1fr 1fr; gap: 16px; }
}
@media (max-width: 767px) {
    .job-grid { grid-template-columns: 1fr; }
    .hero-stat { padding: 0 16px; }
    .hero-stat .num { font-size: 1.35rem; }
}
@media (max-width: 575px) {
    .jp-hero { padding: 50px 0 90px; }
    .jp-hero h1 { font-size: 1.8rem; }
    .search-card { padding: 18px; margin-top: -45px; }
}
</style>

<!-- ======== HERO ======== -->
<div class="jp-hero text-center">
    <div class="hero-grid-pattern"></div>
    <div class="container px-4 hero-inner">
        <div class="hero-badge">
            <i class="fa-solid fa-circle-dot" style="color:#f42a41; font-size:0.65rem;"></i>
            <?php echo $t['badge']; ?>
        </div>
        <h1><?php echo $t['hero_title']; ?></h1>
        <p class="lead"><?php echo $t['hero_desc']; ?></p>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num"><?php echo translateNumber($total_jobs, $lang); ?>+</div>
                <div class="lbl"><?php echo $lang=='bn'?'মোট চাকরি':'Total Jobs'; ?></div>
            </div>
            <div class="hero-stat">
                <div class="num"><?php echo $lang=='bn'?'৬৪':'64'; ?></div>
                <div class="lbl"><?php echo $lang=='bn'?'জেলা':'Districts'; ?></div>
            </div>
            <div class="hero-stat">
                <div class="num"><?php echo $lang=='bn'?'১০০%':'100%'; ?></div>
                <div class="lbl"><?php echo $lang=='bn'?'বিনামূল্যে':'Free'; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 pb-5 container-main-content">

    <?php if ($message != ""): ?>
    <div class="alert alert-<?php echo $message_type; ?> msg-toast mt-4 d-flex align-items-center gap-2">
        <i class="fa-solid <?php echo $message_type=='success'?'fa-circle-check':($message_type=='warning'?'fa-triangle-exclamation':'fa-circle-info'); ?>"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:0.7rem;"></button>
    </div>
    <?php endif; ?>

    <form method="GET" action="jobs.php" id="mainJobSearchForm">
        <input type="hidden" name="view" value="<?php echo htmlspecialchars($view_mode); ?>">

        <!-- ======== SEARCH CARD ======== -->
        <div class="search-card" id="searchSection">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-12">
                    <div class="search-field">
                        <i class="fi fa-solid fa-magnifying-glass me-2"></i>
                        <input type="text" name="search" placeholder="<?php echo $t['search_ph']; ?>"
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="search-field">
                        <i class="fi fa-solid fa-location-dot me-2"></i>
                        <select name="district_id">
                            <option value=""><?php echo $t['all_locations']; ?></option>
                            <?php
                            if ($districts && $districts->num_rows > 0) {
                                $districts->data_seek(0);
                                while ($row = $districts->fetch_assoc()) {
                                    $sel = ($district_id == $row['district_id']) ? 'selected' : '';
                                    $d_name_translated = $district_translations[$lang][$row['district_name']] ?? $row['district_name'];
                                    echo "<option value='{$row['district_id']}' $sel>" . htmlspecialchars($d_name_translated) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="search-field">
                        <i class="fi fa-solid fa-briefcase me-2"></i>
                        <select name="job_type">
                            <option value=""><?php echo $t['any_type']; ?></option>
                            <?php
                            $types = ['Full-time','Part-time','Part-time (Student)','Day Labor','Internship','Contract','Remote'];
                            foreach ($types as $tp) {
                                $sel = ($job_type == $tp) ? 'selected' : '';
                                $tp_translated = $job_type_translations[$lang][$tp] ?? $tp;
                                echo "<option value='$tp' $sel>$tp_translated</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-12">
                    <button type="submit" class="btn-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <?php echo $t['search_btn']; ?>
                    </button>
                </div>
            </div>
            
            <!-- Quick Filters inside Search Card -->
            <div class="quick-filters" id="quickFilters">
                <span class="qf-label"><i class="fa-solid fa-bolt me-1 text-warning"></i><?php echo $t['quick']; ?></span>
                <?php
                $q_types = [
                    'Full-time' => ['en' => 'Full-time', 'bn' => 'পূর্ণকালীন', 'icon' => 'fa-solid fa-briefcase'],
                    'Part-time' => ['en' => 'Part-time', 'bn' => 'পার্ট-টাইম', 'icon' => 'fa-regular fa-clock'],
                    'Remote' => ['en' => 'Remote', 'bn' => 'রিমোট', 'icon' => 'fa-solid fa-laptop-house'],
                    'Day Labor' => ['en' => 'Day Labor', 'bn' => 'দৈনিক শ্রমিক', 'icon' => 'fa-solid fa-hammer'],
                ];
                foreach ($q_types as $tp_val => $tp_info):
                    $is_active = ($job_type === $tp_val);
                ?>
                <button type="button" class="filter-pill <?php echo $is_active ? 'active' : ''; ?>" data-type="<?php echo $tp_val; ?>">
                    <i class="<?php echo $tp_info['icon']; ?>"></i> <?php echo $lang == 'bn' ? $tp_info['bn'] : $tp_info['en']; ?>
                </button>
                <?php endforeach; ?>
                <?php if ($job_type != ""): ?>
                <button type="button" class="filter-pill red" data-clear="true">
                    <i class="fa-solid fa-xmark"></i> <?php echo $lang == 'bn' ? 'মুছুন' : 'Clear'; ?>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Category Pills -->
        <div class="top-category-pills">
            <?php
            $cat_list = ['IT & Tech','Garments','Driving','Education','Health','Marketing','Engineering','Finance'];
            foreach ($cat_list as $cat):
                $is_active = in_array($cat, $categories);
            ?>
            <label class="cat-pill-label <?php echo $is_active ? 'active' : ''; ?>">
                <input type="checkbox" name="category[]" value="<?php echo $cat; ?>" class="d-none" <?php echo $is_active ? 'checked' : ''; ?> onchange="submitWithScroll()">
                <span><?php echo $cat_translations[$lang][$cat] ?? $cat; ?></span>
            </label>
            <?php endforeach; ?>
            <?php if (!empty($search) || $district_id > 0 || !empty($job_type) || !empty($categories) || !empty($experiences) || $min_salary > 0 || $max_salary > 0 || $sort_by != 'default'): ?>
            <a href="jobs.php" class="cat-pill-label" style="background:#fff1f2; color:#f42a41; border-color:#fecdd3; text-decoration:none;">
                <i class="fa-solid fa-xmark"></i> <span><?php echo $t['clear_all']; ?></span>
            </a>
            <?php endif; ?>
        </div>

        <!-- ======== MAIN ROW ======== -->
        <div class="row g-4 mt-1">

            <!-- ===== SIDEBAR (z-index fixed, won't overlap navbar) ===== -->
            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                <div class="filter-sidebar">
                    <div class="sidebar-header">
                        <span style="font-weight:700; color:#1e293b; font-size:0.92rem;">
                            <i class="fa-solid fa-sliders me-2" style="color:#006a4e;"></i><?php echo $t['filters']; ?>
                        </span>
                        <?php if (!empty($search) || $district_id > 0 || !empty($job_type) || !empty($categories) || !empty($experiences)): ?>
                        <a href="jobs.php?view=<?php echo $view_mode; ?>"
                           style="color:#f42a41; font-size:0.78rem; font-weight:600; text-decoration:none;">
                            <?php echo $t['clear_all']; ?>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Layout / View Mode -->
                    <div class="sidebar-section">
                        <div class="sidebar-label"><?php echo $lang=='bn'?'লেআউট':'Layout View'; ?></div>
                        <div class="d-flex gap-2">
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view'=>'grid'])); ?>#jobResults"
                               class="btn btn-sm flex-fill d-flex align-items-center justify-content-center gap-2 <?php echo $view_mode=='grid'?'btn-success active':'btn-outline-secondary'; ?> view-btn-toggle"
                               data-view="grid"
                               style="<?php echo $view_mode=='grid'?'background:#006a4e; border-color:#006a4e; color:#fff;':'color:#64748b; border-color:#cbd5e1; background:#fff;'; ?> font-weight:600; border-radius:8px; height: 38px; transition: all 0.2s;">
                                <i class="fa-solid fa-grip"></i> <?php echo $lang=='bn'?'গ্রিড':'Grid'; ?>
                            </a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view'=>'list'])); ?>#jobResults"
                               class="btn btn-sm flex-fill d-flex align-items-center justify-content-center gap-2 <?php echo $view_mode=='list'?'btn-success active':'btn-outline-secondary'; ?> view-btn-toggle"
                               data-view="list"
                               style="<?php echo $view_mode=='list'?'background:#006a4e; border-color:#006a4e; color:#fff;':'color:#64748b; border-color:#cbd5e1; background:#fff;'; ?> font-weight:600; border-radius:8px; height: 38px; transition: all 0.2s;">
                                <i class="fa-solid fa-list"></i> <?php echo $lang=='bn'?'লিস্ট':'List'; ?>
                            </a>
                        </div>
                    </div>

                    <!-- Sort By -->
                    <div class="sidebar-section">
                        <div class="sidebar-label"><?php echo $lang=='bn'?'সাজান':'Sort By'; ?></div>
                        <select name="sort_by" class="form-select form-select-sm border-0 bg-light" style="font-weight:600; cursor:pointer; height: 38px; border-radius:8px; padding-left:10px;" onchange="submitWithScroll()">
                            <option value="default"><?php echo $lang=='bn'?'ডিফল্ট':'Default'; ?></option>
                            <option value="salary_high" <?php echo $sort_by=='salary_high'?'selected':''; ?>><?php echo $lang=='bn'?'বেতন: বেশি থেকে কম':'Salary: High to Low'; ?></option>
                        </select>
                    </div>

                    <!-- Salary Range -->
                    <div class="sidebar-section">
                        <div class="sidebar-label"><?php echo $lang=='bn'?'বেতন (৳)':'Salary Range (৳)'; ?></div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <input type="number" name="min_salary" class="form-control form-control-sm border-0 bg-light" placeholder="<?php echo $lang=='bn'?'সর্বনিম্ন':'Min'; ?>" value="<?php echo htmlspecialchars($_GET['min_salary'] ?? ''); ?>">
                            <span class="text-muted">-</span>
                            <input type="number" name="max_salary" class="form-control form-control-sm border-0 bg-light" placeholder="<?php echo $lang=='bn'?'সর্বোচ্চ':'Max'; ?>" value="<?php echo htmlspecialchars($_GET['max_salary'] ?? ''); ?>">
                        </div>
                        <button type="button" class="btn btn-sm w-100" style="background:#006a4e; color:#fff; font-weight:600; border-radius:8px;" onclick="submitWithScroll()"><?php echo $lang=='bn'?'প্রয়োগ করুন':'Apply'; ?></button>
                    </div>

                    <!-- Job Type -->
                    <div class="sidebar-section sidebar-check">
                        <div class="sidebar-label"><?php echo $t['job_type']; ?></div>
                        <?php
                        $t_list = ['Full-time','Part-time','Part-time (Student)','Day Labor','Internship','Contract','Remote'];
                        foreach ($t_list as $tp):
                            $checked = in_array($tp, $job_types_arr) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="job_types[]"
                                   value="<?php echo $tp; ?>" id="t_<?php echo md5($tp); ?>"
                                   <?php echo $checked; ?>
                                   onchange="submitWithScroll()">
                            <label class="form-check-label" for="t_<?php echo md5($tp); ?>"><?php echo $job_type_translations[$lang][$tp] ?? $tp; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Experience -->
                    <div class="sidebar-section sidebar-check">
                        <div class="sidebar-label"><?php echo $t['exp_level']; ?></div>
                        <?php
                        $exp_list = ['Entry Level','Mid Level','Senior Level','1 Year','2 Years','3+ Years'];
                        foreach ($exp_list as $exp):
                            $checked = in_array($exp, $experiences) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="experience[]"
                                   value="<?php echo $exp; ?>" id="exp_<?php echo md5($exp); ?>"
                                   <?php echo $checked; ?>
                                   onchange="submitWithScroll()">
                            <label class="form-check-label" for="exp_<?php echo md5($exp); ?>"><?php echo $exp_translations[$lang][$exp] ?? $exp; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Alert CTA -->
                    <div class="sidebar-cta">
                        <i class="fa-solid fa-bell bell"></i>
                        <h6><?php echo $t['job_alerts']; ?></h6>
                        <p><?php echo $t['job_alerts_sub']; ?></p>
                        <a href="profile.php" class="btn btn-sm btn-light fw-bold rounded-pill w-100"><?php echo $t['update_profile']; ?></a>
                    </div>
                </div>
            </div>

            <!-- ===== JOB LISTING ===== -->
            <div class="col-xl-9 col-lg-9">

                <div class="jobs-header">
                    <div class="jobs-count">
                        <strong><?php echo translateNumber($total_jobs, $lang); ?></strong> <?php echo $t['jobs_found']; ?>
                        <?php if (!empty($search)): ?>
                            — "<?php echo htmlspecialchars($search); ?>"
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mobile-only sort and layout toggles (hidden on lg and larger) -->
                    <div class="d-flex align-items-center d-lg-none">
                        <select name="sort_by_mobile" class="form-select form-select-sm border-0 bg-light me-2" style="font-weight:600; cursor:pointer; height:36px; border-radius:8px;" onchange="document.getElementsByName('sort_by')[0].value = this.value; submitWithScroll();">
                            <option value="default" <?php echo $sort_by=='default'?'selected':''; ?>><?php echo $lang=='bn'?'সাজান':'Sort'; ?></option>
                            <option value="salary_high" <?php echo $sort_by=='salary_high'?'selected':''; ?>><?php echo $lang=='bn'?'বেতন: বেশি':'Salary: High'; ?></option>
                        </select>
                        <div class="view-toggle">
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view'=>'grid'])); ?>#jobResults"
                               class="view-btn <?php echo $view_mode=='grid'?'active':''; ?>" title="Grid">
                                <i class="fa-solid fa-grip"></i>
                            </a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view'=>'list'])); ?>#jobResults"
                               class="view-btn <?php echo $view_mode=='list'?'active':''; ?>" title="List">
                                <i class="fa-solid fa-list"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div id="jobResults">
                <?php if ($jobs_result && $jobs_result->num_rows > 0): ?>

                <?php if ($view_mode == 'grid'): ?>
                <div class="job-grid">
                <?php else: ?>
                <div class="d-flex flex-column gap-3">
                <?php endif; ?>

                    <?php
                    $idx = 0;
                    while ($job = $jobs_result->fetch_assoc()):
                        $job_id = $job['job_id'];
                        $ch_app = $conn->prepare("SELECT application_id FROM applications WHERE job_id=? AND user_id=?");
                        $ch_app->bind_param("ii", $job_id, $user_id); $ch_app->execute();
                        $already_applied = ($ch_app->get_result()->num_rows > 0);

                        $ch_save = $conn->prepare("SELECT id FROM saved_jobs WHERE user_id=? AND job_id=?");
                        $ch_save->bind_param("ii", $user_id, $job_id); $ch_save->execute();
                        $already_saved = ($ch_save->get_result()->num_rows > 0);

                        $deadline = $job['application_deadline'] ?? '';
                        $deadline_over = (!empty($deadline) && $deadline < date('Y-m-d'));
                        $is_closed = (($job['status'] ?? 'active') === 'closed');
                        $cant_apply = $deadline_over || $is_closed;

                        $company = $job['company_name'] ?? $t['unknown_emp'];
                        $company_translated = translateEmployerName($company, $lang);
                        $title_translated = translateJobTitle($job['title'] ?? '', $lang);
                        $initial = strtoupper(mb_substr($company_translated, 0, 1, 'UTF-8'));
                        $lc = $logo_colors[$idx % count($logo_colors)];
                        $j_type = $job['job_type'] ?? 'Full-time';
                        $j_type_disp = $job_type_translations[$lang][$j_type] ?? $j_type;
                        $j_icon = $type_icons[$j_type] ?? 'fa-solid fa-briefcase';
                        
                        $loc_raw = $job['district_name'] ?? ($job['location'] ?? '');
                        $location_str = !empty($loc_raw) ? htmlspecialchars($district_translations[$lang][$loc_raw] ?? $loc_raw) : $t['multiple_loc'];
                        
                        $salary_str = (empty($job['salary']) || strtolower($job['salary']) === 'negotiable') ? $t['negotiable'] : '৳' . translateSalary($job['salary'], $lang);
                        $desc_short = htmlspecialchars(mb_substr($job['description'] ?? '', 0, 110, 'UTF-8'));
                        $days_ago = max(0, floor((time() - strtotime($job['created_at'])) / 86400));
                        $days_ago_str = $days_ago == 0 ? ($lang == 'bn' ? 'আজ' : 'Today') : translateNumber($days_ago, $lang) . ($lang == 'bn' ? ' দিন আগে' : 'd ago');
                        
                        $exp_disp = !empty($job['experience_required']) ? ($exp_translations[$lang][$job['experience_required']] ?? $job['experience_required']) : '';
                        $cat_disp = !empty($job['job_category']) ? ($cat_translations[$lang][$job['job_category']] ?? $job['job_category']) : $t['not_specified'];

                        $apply_url = "jobs.php?apply={$job_id}&search=".urlencode($search)."&district_id=".urlencode($district_id)."&job_type=".urlencode($job_type)."&view=".urlencode($view_mode);
                        $save_url  = $already_saved ? "jobs.php?unsave={$job_id}&view={$view_mode}" : "jobs.php?save={$job_id}&view={$view_mode}";
                        $job_img   = getJobImage($job['title'], $job['job_category'] ?? '');

                        $delay = min($idx * 60, 400);

                        if ($view_mode == 'grid'):
                    ?>
                    <!-- GRID CARD (Airbnb Overhaul Style) -->
                    <div class="job-card" style="animation-delay:<?php echo $delay; ?>ms;">
                        <!-- Cover Image with overlays -->
                        <div class="job-cover" style="background-image: url('<?php echo $job_img; ?>');">
                            <!-- Top Left Translucent Badge: Time Posted -->
                            <div class="grid-badge-top-left">
                                <i class="fa-regular fa-clock me-1"></i>
                                <?php echo $days_ago_str; ?>
                            </div>
                            <!-- Top Right Translucent Save Button -->
                            <a href="<?php echo $save_url; ?>"
                               class="grid-save-btn <?php echo $already_saved?'saved':''; ?>"
                               title="<?php echo $already_saved?$t['saved']:$t['save_job']; ?>">
                                <i class="fa-<?php echo $already_saved?'solid':'regular'; ?> fa-bookmark"></i>
                            </a>
                        </div>
                        
                        <!-- Card Body details -->
                        <div class="grid-card-body">
                            <!-- Title Row -->
                            <h3 class="grid-job-title" title="<?php echo htmlspecialchars($title_translated); ?>">
                                <?php echo htmlspecialchars($title_translated); ?>
                            </h3>
                            
                            <!-- Subtitle: Company and Location -->
                            <div class="grid-subtitle">
                                <span class="grid-company"><?php echo htmlspecialchars($company_translated); ?></span>
                                <span class="grid-separator">•</span>
                                <span class="grid-location"><?php echo $location_str; ?></span>
                            </div>

                            <!-- Tags Row (Full Width, under subtitle) -->
                            <div class="grid-tags-row">
                                <span class="grid-meta-pill" title="<?php echo $t['job_type']; ?>">
                                    <i class="<?php echo $j_icon; ?>"></i> <?php echo htmlspecialchars($j_type_disp); ?>
                                </span>
                                <?php if (!empty($exp_disp)): ?>
                                <span class="grid-meta-pill" title="<?php echo $t['exp_level']; ?>">
                                    <i class="fa-solid fa-layer-group"></i> <?php echo htmlspecialchars($exp_disp); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Bottom Row: Salary & Apply Button -->
                            <div class="grid-footer-row">
                                <div class="grid-salary-info">
                                    <i class="fa-solid fa-wallet me-1"></i>
                                    <span class="grid-salary-text"><?php echo $salary_str; ?></span>
                                </div>
                                
                                <div class="grid-action-btn">
                                    <?php if ($already_applied): ?>
                                    <span class="btn-apply-card applied-btn"><i class="fa-solid fa-check"></i> <?php echo $t['applied']; ?></span>
                                    <?php elseif ($cant_apply): ?>
                                    <span class="btn-apply-card disabled-btn"><i class="fa-solid fa-ban"></i> <?php echo $t['closed']; ?></span>
                                    <?php else: ?>
                                    <a href="<?php echo $apply_url; ?>" class="btn-apply-card" onclick="return confirm('<?php echo $lang == 'bn' ? 'এই চাকরির জন্য আবেদন করবেন?' : 'Apply for this job?'; ?>')">
                                        <?php echo $t['apply_now']; ?> <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- LIST CARD (Standard List Overhaul Style) -->
                    <div class="job-list-card" style="animation-delay:<?php echo $delay; ?>ms;">
                        <!-- Left Side: Rectangular Job Cover Image -->
                        <div class="list-image-container" style="background-image: url('<?php echo $job_img; ?>');"></div>
                        
                        <!-- Right Side: Content Container -->
                        <div class="list-content-container">
                            <!-- Top Row: Time Badge & Save Bookmark -->
                            <div class="list-card-top">
                                <span class="list-time-badge">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    <?php echo $days_ago_str; ?>
                                </span>
                                <a href="<?php echo $save_url; ?>" class="save-btn <?php echo $already_saved?'saved':''; ?>" title="<?php echo $already_saved?$t['saved']:$t['save_job']; ?>">
                                    <i class="fa-<?php echo $already_saved?'solid':'regular'; ?> fa-bookmark"></i>
                                </a>
                            </div>
                            
                            <!-- Middle Row: Company Logo + Job Title & Company Name -->
                            <div class="list-card-middle">
                                <div class="list-logo-circle" style="background:<?php echo $lc['grad']; ?>; color:<?php echo $lc['color']; ?>;">
                                    <?php echo $initial; ?>
                                </div>
                                <div>
                                    <h3 class="list-title mb-1"><?php echo htmlspecialchars($title_translated); ?></h3>
                                    <div class="list-company"><?php echo htmlspecialchars($company_translated); ?></div>
                                </div>
                            </div>
                            
                            <!-- Bottom Row: Metadata row & Apply Button -->
                            <div class="list-card-bottom">
                                <div class="list-meta">
                                    <span class="lmeta"><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($cat_disp); ?></span>
                                    <span class="lmeta"><i class="<?php echo $j_icon; ?>"></i> <?php echo htmlspecialchars($j_type_disp); ?></span>
                                    <span class="lmeta"><i class="fa-solid fa-wallet"></i> <?php echo $salary_str; ?></span>
                                    <span class="lmeta"><i class="fa-solid fa-location-dot"></i> <?php echo $location_str; ?></span>
                                </div>
                                <div class="list-action-btn">
                                    <?php if ($already_applied): ?>
                                    <span class="btn-apply-card applied-btn"><i class="fa-solid fa-check"></i> <?php echo $t['applied']; ?></span>
                                    <?php elseif ($cant_apply): ?>
                                    <span class="btn-apply-card disabled-btn"><i class="fa-solid fa-ban"></i> <?php echo $t['closed']; ?></span>
                                    <?php else: ?>
                                    <a href="<?php echo $apply_url; ?>" class="btn-apply-card" onclick="return confirm('<?php echo $lang == 'bn' ? 'এই চাকরির জন্য আবেদন করবেন?' : 'Apply for this job?'; ?>')">
                                        <?php echo $t['apply_now']; ?> <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php $idx++; endwhile; ?>
                </div>

                <?php else: ?>
                <div class="empty-jobs">
                    <div class="empty-icon"><i class="fa-solid fa-briefcase"></i></div>
                    <h4 class="fw-bold text-dark mb-2"><?php echo $t['no_jobs']; ?></h4>
                    <p class="text-muted mb-4" style="max-width:420px; margin:0 auto 24px;"><?php echo $t['no_jobs_desc']; ?></p>
                    <a href="jobs.php" class="btn btn-success rounded-pill px-5 py-2 fw-bold">
                        <i class="fa-solid fa-rotate-left me-2"></i><?php echo $t['reset_search']; ?>
                    </a>
                </div>
                <?php endif; ?>
                </div><!-- #jobResults -->

            </div>
        </div>
    </form>
</div>

<!-- ======== JAVASCRIPT: Smooth AJAX Filtering & Interactions ======== -->
<script>
// Disable default scroll restoration
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

// Fetch and render filtered jobs dynamically
function loadJobs(url, updateUrl = true) {
    const resultsDiv = document.getElementById('jobResults');
    const viewMode = document.querySelector('input[name="view"]').value || 'grid';

    // Inject skeleton loader for dynamic feedback
    if (resultsDiv) {
        let skeletons = '';
        if (viewMode === 'grid') {
            skeletons = '<div class="job-grid">';
            for (let i = 0; i < 6; i++) {
                skeletons += `
                    <div class="skeleton-card">
                        <div class="skeleton-thumb"></div>
                        <div class="skeleton-text skeleton-title"></div>
                        <div class="skeleton-text skeleton-subtitle"></div>
                        <div class="skeleton-pills">
                            <div class="skeleton-pill"></div>
                            <div class="skeleton-pill"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2" style="border-top: 1px dashed var(--border-light);">
                            <div class="skeleton-salary"></div>
                            <div class="skeleton-btn"></div>
                        </div>
                    </div>`;
            }
            skeletons += '</div>';
        } else {
            skeletons = '<div class="d-flex flex-column gap-3">';
            for (let i = 0; i < 4; i++) {
                skeletons += `
                    <div class="skeleton-list-card">
                        <div class="skeleton-list-thumb"></div>
                        <div class="list-content-container">
                            <div class="d-flex justify-content-between">
                                <div class="skeleton-list-badge"></div>
                                <div class="skeleton-list-save"></div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="skeleton-list-logo"></div>
                                <div class="d-flex flex-column gap-2" style="flex: 1;">
                                    <div class="skeleton-text skeleton-list-title"></div>
                                    <div class="skeleton-text skeleton-list-company"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="skeleton-list-meta">
                                    <div class="skeleton-list-meta-item"></div>
                                    <div class="skeleton-list-meta-item"></div>
                                    <div class="skeleton-list-meta-item"></div>
                                </div>
                                <div class="skeleton-list-btn"></div>
                            </div>
                        </div>
                    </div>`;
            }
            skeletons += '</div>';
        }
        resultsDiv.innerHTML = skeletons;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // 1. Update Job Results
            const newResults = doc.getElementById('jobResults');
            if (newResults && resultsDiv) {
                resultsDiv.innerHTML = newResults.innerHTML;
                resultsDiv.style.opacity = '1';
                
                // Re-trigger stagger animation
                document.querySelectorAll('.job-card, .job-list-card').forEach(function(card, i) {
                    card.style.animationDelay = (i * 50) + 'ms';
                });
            }

            // 2. Update Jobs Count
            const countDiv = document.querySelector('.jobs-count');
            const newCountDiv = doc.querySelector('.jobs-count');
            if (countDiv && newCountDiv) {
                countDiv.innerHTML = newCountDiv.innerHTML;
            }

            // 3. Update Top Category Pills
            const pillsDiv = document.querySelector('.top-category-pills');
            const newPillsDiv = doc.querySelector('.top-category-pills');
            if (pillsDiv && newPillsDiv) {
                pillsDiv.innerHTML = newPillsDiv.innerHTML;
            }

            // 4. Update Filter Sidebar
            const sidebarDiv = document.querySelector('.filter-sidebar');
            const newSidebarDiv = doc.querySelector('.filter-sidebar');
            if (sidebarDiv && newSidebarDiv) {
                sidebarDiv.innerHTML = newSidebarDiv.innerHTML;
            }

            // 5. Update Quick Filters
            const quickDiv = document.getElementById('quickFilters');
            const newQuickDiv = doc.getElementById('quickFilters');
            if (quickDiv && newQuickDiv) {
                quickDiv.innerHTML = newQuickDiv.innerHTML;
            }

            // 6. Sync Form State
            const form = document.getElementById('mainJobSearchForm');
            const newForm = doc.getElementById('mainJobSearchForm');
            if (form && newForm) {
                form.querySelector('input[name="view"]').value = newForm.querySelector('input[name="view"]').value;
                form.querySelector('select[name="job_type"]').value = newForm.querySelector('select[name="job_type"]').value;
                form.querySelector('select[name="district_id"]').value = newForm.querySelector('select[name="district_id"]').value;
                form.querySelector('input[name="search"]').value = newForm.querySelector('input[name="search"]').value;
                
                // Sync sort dropdown in sidebar
                const newSortVal = newForm.querySelector('select[name="sort_by"]').value;
                form.querySelector('select[name="sort_by"]').value = newSortVal;
                
                // Sync mobile sort dropdown if it exists
                const mobileSort = form.querySelector('select[name="sort_by_mobile"]');
                if (mobileSort) {
                    mobileSort.value = newSortVal;
                }
            }

            if (updateUrl) {
                window.history.pushState({ path: url }, '', url);
            }
        })
        .catch(err => {
            console.error('Error fetching jobs:', err);
            if (resultsDiv) resultsDiv.style.opacity = '1';
        });
}

// Sidebar Checkboxes & Text Input trigger submit
function submitWithScroll() {
    const form = document.getElementById('mainJobSearchForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    const url = 'jobs.php?' + params.toString();
    loadJobs(url);
}

document.addEventListener('DOMContentLoaded', function() {
    // Intercept View Mode Buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.view-toggle a, .view-btn-toggle');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            loadJobs(url);
        }
    });

    // Intercept Quick Filter Pills
    document.addEventListener('click', function(e) {
        const pill = e.target.closest('#quickFilters .filter-pill');
        if (pill) {
            e.preventDefault();
            const type = pill.getAttribute('data-type');
            const isClear = pill.getAttribute('data-clear');
            const form = document.getElementById('mainJobSearchForm');
            const sel = form.querySelector('select[name="job_type"]');

            if (isClear) {
                loadJobs('jobs.php?view=' + form.querySelector('input[name="view"]').value);
                return;
            }

            if (sel.value === type) {
                sel.value = '';
            } else {
                sel.value = type;
            }

            submitWithScroll();
        }
    });

    // Intercept search form submit
    document.getElementById('mainJobSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        const url = 'jobs.php?' + params.toString();
        loadJobs(url);
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        loadJobs(window.location.href, false);
    });

    // Stagger animation on initial load
    document.querySelectorAll('.job-card, .job-list-card').forEach(function(card, i) {
        card.style.animationDelay = (i * 50) + 'ms';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include('../includes/footer.php'); ?>