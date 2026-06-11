<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}
include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';
$message = "";
$message_type = "";

if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            $num = str_replace($eng_nums, $bng_nums, (string)$num);
            
            $en_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'AM', 'PM'];
            $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'এএম', 'পিএম'];
            $num = str_replace($en_months, $bn_months, $num);
        }
        return $num;
    }
}

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

$job_type_translations = [
    'bn' => [
        'Full-time' => 'পূর্ণকালীন',
        'Part-time' => 'খণ্ডকালীন',
        'Contract' => 'চুক্তিভিত্তিক',
        'Part-time (Student)' => 'পার্ট-টাইম (ছাত্র)',
        'Day Labor' => 'দৈনিক শ্রমিক'
    ],
    'en' => [
        'Full-time' => 'Full-time',
        'Part-time' => 'Part-time',
        'Contract' => 'Contract',
        'Part-time (Student)' => 'Part-time (Student)',
        'Day Labor' => 'Day Labor'
    ]
];

// REMOVE SAVED JOB
if (isset($_GET['remove'])) {
    $job_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM saved_jobs WHERE user_id=? AND job_id=?");
    $stmt->bind_param("ii", $user_id, $job_id);
    $stmt->execute();
    $message = $lang == 'bn' ? "সেভ করা চাকরিটি মুছে ফেলা হয়েছে।" : "Saved job removed.";
    $message_type = "info";
}

// APPLY JOB
if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);
    $ch = $conn->prepare("SELECT status, application_deadline FROM jobs WHERE job_id=? LIMIT 1");
    $ch->bind_param("i", $job_id);
    $ch->execute();
    $res = $ch->get_result();
    if ($res && $res->num_rows > 0) {
        $jd = $res->fetch_assoc();
        $dl_over = (!empty($jd['application_deadline']) && $jd['application_deadline'] < date('Y-m-d'));
        if (($jd['status'] ?? 'active') == 'closed') {
            $message = $lang == 'bn' ? "এই চাকরিটি বন্ধ রয়েছে।" : "This job is closed."; $message_type = "warning";
        } elseif ($dl_over) {
            $message = $lang == 'bn' ? "আবেদনের শেষ সময়সীমা পার হয়ে গেছে।" : "Deadline is over."; $message_type = "warning";
        } else {
            $chk = $conn->prepare("SELECT application_id FROM applications WHERE job_id=? AND user_id=?");
            $chk->bind_param("ii", $job_id, $user_id);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $message = $lang == 'bn' ? "আপনি ইতিমধ্যে এই চাকরিতে আবেদন করেছেন।" : "Already applied."; $message_type = "warning";
            } else {
                $ap = $conn->prepare("INSERT INTO applications (job_id, user_id) VALUES (?,?)");
                $ap->bind_param("ii", $job_id, $user_id);
                if ($ap->execute()) { $message = $lang == 'bn' ? "সফলভাবে আবেদন করা হয়েছে!" : "Applied successfully!"; $message_type = "success"; }
                else { $message = $lang == 'bn' ? "আবেদনে ত্রুটি হয়েছে।" : "Error applying."; $message_type = "danger"; }
            }
        }
    }
}

$sql = "SELECT jobs.*, d.district_name, up.upazila_name, users.full_name AS company_name,
               saved_jobs.saved_at AS saved_at
        FROM saved_jobs
        JOIN jobs ON saved_jobs.job_id = jobs.job_id
        LEFT JOIN districts d  ON jobs.district_id = d.district_id
        LEFT JOIN upazilas up  ON jobs.upazila_id  = up.upazila_id
        LEFT JOIN users        ON jobs.employer_id  = users.user_id
        WHERE saved_jobs.user_id='$user_id'
        ORDER BY saved_jobs.id DESC";
$result = $conn->query($sql);
$total  = $result ? $result->num_rows : 0;
$logo_colors = [
    ['bg'=>'#EEF2FF','color'=>'#4F46E5'], ['bg'=>'#FEF3C7','color'=>'#D97706'],
    ['bg'=>'#ECFDF5','color'=>'#059669'], ['bg'=>'#FFF1F2','color'=>'#E11D48'],
    ['bg'=>'#F0F9FF','color'=>'#0284C7'], ['bg'=>'#FDF4FF','color'=>'#9333EA'],
];

include('../includes/header.php');
include('../includes/navbar.php');
?>

<link rel="stylesheet" href="../assets/css/jobseeker_saved_jobs.css">

<!-- Hero -->
<div class="saved-hero text-center">
    <div class="container px-4">
        <span style="background:rgba(255,255,255,0.15); border-radius:50px; padding:5px 18px; font-size:0.82rem; font-weight:700; color:#fff; letter-spacing:1px; text-transform:uppercase; border:1px solid rgba(255,255,255,0.25); display:inline-block; margin-bottom:16px;">
            <i class="fa-solid fa-bookmark me-2" style="color:#fbbf24;"></i>
            <?php echo $lang=='bn' ? 'সেভ করা চাকরি' : 'Saved Jobs'; ?>
        </span>
        <h1><?php echo $lang=='bn' ? 'আপনার সেভ করা চাকরিসমূহ' : 'Your Saved Jobs'; ?></h1>
        <p><?php echo $lang=='bn' ? 'পরে আবেদনের জন্য বুকমার্ক করা সকল চাকরি।' : 'All jobs you have bookmarked for later applications.'; ?></p>
    </div>
</div>

<div class="container px-3 px-lg-4 saved-wrap">

    <?php if ($message != ""): ?>
    <div class="alert alert-<?php echo $message_type; ?> rounded-3 border-0 shadow-sm mb-4 d-flex align-items-center gap-2 mt-0">
        <i class="fa-solid <?php echo $message_type=='success'?'fa-circle-check':($message_type=='warning'?'fa-triangle-exclamation':'fa-circle-info'); ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Top bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <span style="font-size:0.95rem; color:#64748b; font-weight:500;">
            <strong style="color:#006a4e; font-size:1.1rem;"><?php echo translateNumber($total, $lang); ?></strong>
            <?php echo $lang=='bn' ? ' টি সেভ করা চাকরি' : ' saved job(s)'; ?>
        </span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="jobs.php" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-plus me-1"></i>
                <?php echo $lang=='bn' ? 'আরো চাকরি দেখুন' : 'Browse More Jobs'; ?>
            </a>
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i>
                <?php echo $lang=='bn' ? 'ড্যাশবোর্ড' : 'Dashboard'; ?>
            </a>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="row g-4">
        <?php
        $idx = 0;
        while ($job = $result->fetch_assoc()):
            $job_id = $job['job_id'];

            $ch2 = $conn->prepare("SELECT application_id FROM applications WHERE user_id=? AND job_id=?");
            $ch2->bind_param("ii", $user_id, $job_id);
            $ch2->execute();
            $already_applied = ($ch2->get_result()->num_rows > 0);

            $deadline     = $job['application_deadline'] ?? '';
            $deadline_over = (!empty($deadline) && $deadline < date('Y-m-d'));
            $is_closed    = (($job['status'] ?? 'active') === 'closed');
            $cant_apply   = $deadline_over || $is_closed;

            $lc      = $logo_colors[$idx % count($logo_colors)];
            $company = translateEmployerName($job['company_name'] ?? ($lang=='bn' ? 'অজ্ঞাত নিয়োগকর্তা' : 'Unknown Employer'), $lang);
            $initial = strtoupper(mb_substr($company, 0, 1, 'UTF-8'));
            $d_name = translateDistrict($job['district_name'] ?? '', $lang) ?: ($district_translations[$lang][$job['district_name']] ?? $job['district_name']);
            $loc_str = htmlspecialchars($d_name ?? ($job['location'] ?? ($lang=='bn' ? 'একাধিক স্থান' : 'Multiple Locations')));
            $sal_str = (empty($job['salary']) || strtolower($job['salary']) === 'negotiable') ? ($lang=='bn' ? 'আলোচনা সাপেক্ষে' : 'Negotiable') : '৳' . translateSalary($job['salary'], $lang);
            $j_type  = $job['job_type'] ?? 'Full-time';
            $j_type_disp = $job_type_translations[$lang][$j_type] ?? $j_type;
        ?>
        <div class="col-md-6 col-xl-4">
            <div class="saved-card">
                <div class="job-cover" style="background-image: url('<?php echo getJobImage($job['title'], $job['job_category'] ?? ''); ?>');"></div>
                <!-- Card top: logo + info + status -->
                <div class="d-flex align-items-start gap-3 mb-12" style="margin-bottom:12px;">
                    <div class="sc-logo" style="background:<?php echo $lc['bg']; ?>;color:<?php echo $lc['color']; ?>; margin-top:-45px; border:3px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                        <?php echo $initial; ?>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div class="sc-company"><?php echo htmlspecialchars($company); ?></div>
                        <div class="sc-title"><?php echo htmlspecialchars(translateJobTitle($job['title'] ?? '', $lang)); ?></div>
                    </div>
                    <?php if ($cant_apply): ?>
                    <span class="sctag sctag-closed"><i class="fa-solid fa-lock"></i><?php echo $lang=='bn'?'বন্ধ':'Closed'; ?></span>
                    <?php else: ?>
                    <span class="sctag sctag-active"><i class="fa-solid fa-circle-dot"></i><?php echo $lang=='bn'?'সক্রিয়':'Active'; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tags -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="sctag sctag-type"><i class="fa-solid fa-briefcase"></i><?php echo htmlspecialchars($j_type_disp); ?></span>
                    <span class="sctag sctag-loc"><i class="fa-solid fa-location-dot"></i><?php echo $loc_str; ?></span>
                </div>

                <!-- Meta -->
                <div class="sc-meta mb-2">
                    <span style="color:#006a4e; font-weight:700;"><i class="fa-solid fa-bangladeshi-taka-sign"></i> <?php echo $sal_str; ?></span>
                    <?php if (!empty($deadline)): ?>
                    <span class="<?php echo $deadline_over?'text-danger':''; ?>">
                        <i class="fa-regular fa-clock"></i> <?php echo translateNumber(date('d M Y', strtotime($deadline)), $lang); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="saved-at-lbl mb-2">
                    <i class="fa-regular fa-bookmark me-1"></i>
                    <?php echo $lang=='bn'?'সেভ করা হয়েছে: ':'Saved: '; ?>
                    <?php echo translateNumber(date('d M Y', strtotime($job['saved_at'])), $lang); ?>
                </div>

                <!-- Actions -->
                <div class="sc-actions">
                    <?php if ($already_applied): ?>
                        <span class="btn-sc-apply btn-applied"><i class="fa-solid fa-check"></i><?php echo $lang=='bn'?'আবেদনকৃত':'Applied'; ?></span>
                    <?php elseif ($cant_apply): ?>
                        <span class="btn-sc-apply btn-closed"><i class="fa-solid fa-ban"></i><?php echo $lang=='bn'?'বন্ধ':'Closed'; ?></span>
                    <?php else: ?>
                        <a href="saved_jobs.php?apply=<?php echo $job_id; ?>"
                           class="btn-sc-apply"
                           onclick="return confirm('<?php echo $lang == 'bn' ? 'এই চাকরির জন্য আবেদন করবেন?' : 'Apply for this job?'; ?>')">
                            <?php echo $lang=='bn'?'আবেদন করুন':'Apply Now'; ?> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                    <a href="saved_jobs.php?remove=<?php echo $job_id; ?>"
                       class="btn-sc-remove"
                       onclick="return confirm('<?php echo $lang == 'bn' ? 'এই সেভ করা চাকরিটি মুছে ফেলতে চান?' : 'Remove this saved job?'; ?>')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php $idx++; endwhile; ?>
    </div>

    <?php else: ?>
    <div class="empty-saved">
        <i class="fa-regular fa-bookmark" style="font-size:3.5rem; color:#cbd5e1; display:block; margin-bottom:16px;"></i>
        <h4 class="fw-bold text-dark mb-2">
            <?php echo $lang=='bn' ? 'কোনো সেভ করা চাকরি নেই' : 'No Saved Jobs Yet'; ?>
        </h4>
        <p class="text-muted mb-4">
            <?php echo $lang=='bn' ? 'পরে আবেদনের জন্য চাকরি সেভ করুন।' : 'Save jobs to view and apply to them later.'; ?>
        </p>
        <a href="jobs.php" class="btn btn-success rounded-pill px-5 py-2 fw-bold">
            <i class="fa-solid fa-magnifying-glass me-2"></i>
            <?php echo $lang=='bn' ? 'চাকরি খুঁজুন' : 'Browse Jobs'; ?>
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>