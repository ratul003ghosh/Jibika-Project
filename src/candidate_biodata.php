<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['employer', 'admin'])) {
    header("Location: auth/login.php");
    exit();
}

include('assets/config/db.php');

$application_id = isset($_GET['application_id']) ? intval($_GET['application_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$lang = $_SESSION['lang'] ?? 'bn';

// Check access control if employer
if ($_SESSION['role'] == 'employer') {
    $emp_id = $_SESSION['user_id'];
    
    if ($application_id > 0) {
        // Verify employer owns the job
        $check = $conn->query("SELECT applications.user_id FROM applications JOIN jobs ON applications.job_id = jobs.job_id WHERE applications.application_id = $application_id AND jobs.employer_id = $emp_id");
        if ($check->num_rows == 0) {
            die($lang == 'bn' ? "অ্যাক্সেস প্রত্যাখ্যান করা হয়েছে" : "Access Denied");
        }
        $user_id = $check->fetch_assoc()['user_id'];
    } elseif ($user_id > 0) {
        // Candidates can be viewed by employers freely through Partner Finder
        // Access is granted.
    } else {
        die("Invalid request");
    }
}

if ($user_id == 0) die("Invalid user ID");

// Fetch candidate data
$user_sql = "SELECT u.full_name, u.email, u.phone, jsp.* FROM users u LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id WHERE u.user_id = $user_id";
$res = $conn->query($user_sql);
if (!$res || $res->num_rows == 0) die("Candidate not found");
$candidate = $res->fetch_assoc();

// Fetch Skills
$skills = [];
$skill_res = $conn->query("SELECT ds.skill_name FROM job_seeker_skills jss JOIN dic_skills ds ON jss.skill_id = ds.skill_id WHERE jss.user_id = $user_id");
if ($skill_res) {
    while ($row = $skill_res->fetch_assoc()) {
        $skills[] = $row['skill_name'];
    }
}

// Fetch Education
$education_level = t('N/A', 'প্রযোজ্য নয়');
$edu_res = $conn->query("SELECT del.level_name FROM seeker_education se JOIN dic_education_levels del ON se.level_id = del.level_id WHERE se.user_id = $user_id LIMIT 1");
if ($edu_res && $edu_res->num_rows > 0) {
    $education_level = $edu_res->fetch_assoc()['level_name'];
}

function t($en, $bn) {
    global $lang;
    return $lang == 'bn' ? $bn : $en;
}

$age = t('N/A', 'প্রযোজ্য নয়');
if (!empty($candidate['dob'])) {
    $dob = new DateTime($candidate['dob']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2><?php echo t('Candidate Biodata', 'প্রার্থীর জীবনবৃত্তান্ত'); ?></h2>
            <button onclick="window.history.back()" class="btn btn-secondary"><?php echo t('Back', 'ফিরে যান'); ?></button>
        </div>

        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <?php $img = !empty($candidate['profile_photo']) ? 'uploads/'.$candidate['profile_photo'] : 'assets/images/default_user.png'; ?>
                <img src="<?php echo $img; ?>" alt="Profile" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h4><?php echo htmlspecialchars($candidate['full_name'] ?? ''); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($candidate['job_position'] ?? t('Job Seeker', 'চাকরি প্রার্থী')); ?></p>
                <?php if (!empty($candidate['cv_file'])): ?>
                    <a href="uploads/<?php echo htmlspecialchars($candidate['cv_file']); ?>" download class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="fa fa-download"></i> <?php echo t('Download CV', 'সিভি ডাউনলোড করুন'); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($candidate['portfolio_link'])): ?>
                    <a href="<?php echo htmlspecialchars($candidate['portfolio_link']); ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa fa-link"></i> <?php echo t('Portfolio Link', 'পোর্টফোলিও লিংক'); ?>
                    </a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'employer' && $application_id == 0): ?>
                    <a href="employer/schedule_interview.php?candidate_id=<?php echo $user_id; ?>" class="btn btn-success btn-sm w-100 mt-2" style="background-color: #006a4e; border-color: #006a4e;">
                        <i class="fa fa-calendar-check"></i> <?php echo t('Invite for Interview', 'সাক্ষাৎকার নির্ধারণ করুন'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="col-md-8">
                <h5 class="border-bottom pb-2 text-primary"><?php echo t('Personal Info', 'ব্যক্তিগত তথ্য'); ?></h5>
                <div class="row mb-4">
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Gender:', 'লিঙ্গ:'); ?></strong> <?php echo htmlspecialchars(t($candidate['gender'] ?? 'N/A', $candidate['gender'] ?? 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Date of Birth:', 'জন্ম তারিখ:'); ?></strong> <?php echo htmlspecialchars(translateDate($candidate['dob'] ?? '', $lang) ?: t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Age:', 'বয়স:'); ?></strong> <?php echo translateNumber($age, $lang); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Phone:', 'ফোন:'); ?></strong> <?php echo translateNumber($candidate['phone'] ?? '', $lang); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Email:', 'ইমেইল:'); ?></strong> <?php echo htmlspecialchars($candidate['email'] ?? ''); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('District:', 'জেলা:'); ?></strong> <?php echo htmlspecialchars(translateDistrict($candidate['district'] ?? '', $lang)); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Upazila:', 'উপজেলা:'); ?></strong> <?php echo htmlspecialchars(translateUpazila($candidate['upazila'] ?? '', $lang)); ?></div>
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Address:', 'ঠিকানা:'); ?></strong> <?php echo htmlspecialchars($candidate['address'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                </div>

                <h5 class="border-bottom pb-2 text-primary"><?php echo t('Professional Info', 'পেশাগত তথ্য'); ?></h5>
                <div class="row mb-4">
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Preferred Job Category:', 'পছন্দের চাকরির বিভাগ:'); ?></strong> <?php echo htmlspecialchars(translateJobCategory($candidate['preferred_job_category'] ?? '', $lang) ?: t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Expected Salary:', 'প্রত্যাশিত বেতন:'); ?></strong> <?php echo htmlspecialchars(translateSalary($candidate['expected_salary'] ?? '', $lang)); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Experience:', 'অভিজ্ঞতা:'); ?></strong> <?php echo translateNumber($candidate['experience_years'] ?? 0, $lang) . t(' Years', ' বছর'); ?></div>
                    <div class="col-sm-12 mb-2">
                        <strong><?php echo t('Skills:', 'দক্ষতা:'); ?></strong> 
                        <?php 
                        if (!empty($skills)) {
                            foreach($skills as $s) echo '<span class="badge bg-secondary me-1">'.htmlspecialchars($s).'</span>';
                        } else {
                            echo t('Not specified', 'উল্লেখ নেই');
                        }
                        ?>
                    </div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Certifications:', 'সার্টিফিকেশন:'); ?></strong> <?php echo htmlspecialchars($candidate['certifications'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Languages:', 'ভাষা:'); ?></strong> <?php echo htmlspecialchars($candidate['languages'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                </div>

                <h5 class="border-bottom pb-2 text-primary"><?php echo t('Education', 'শিক্ষা'); ?></h5>
                <div class="row mb-4">
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Degree:', 'ডিগ্রি:'); ?></strong> <?php echo htmlspecialchars($education_level); ?></div>
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Institution:', 'প্রতিষ্ঠান:'); ?></strong> <?php echo htmlspecialchars($candidate['institution'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Result/GPA:', 'ফলাফল/জিপিএ:'); ?></strong> <?php echo htmlspecialchars(translateNumber($candidate['gpa'] ?? '', $lang) ?: t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Passing Year:', 'পাসের বছর:'); ?></strong> <?php echo htmlspecialchars(translateNumber($candidate['passing_year'] ?? '', $lang) ?: t('N/A', 'প্রযোজ্য নয়')); ?></div>
                </div>

                <h5 class="border-bottom pb-2 text-primary"><?php echo t('Work Experience', 'কাজের অভিজ্ঞতা'); ?></h5>
                <div class="row mb-4">
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Company Name:', 'কোম্পানির নাম:'); ?></strong> <?php echo htmlspecialchars($candidate['company_name'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Position:', 'পদবি:'); ?></strong> <?php echo htmlspecialchars($candidate['job_position'] ?? t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-6 mb-2"><strong><?php echo t('Duration:', 'সময়কাল:'); ?></strong> <?php echo htmlspecialchars(translateNumber($candidate['work_duration'] ?? '', $lang) ?: t('N/A', 'প্রযোজ্য নয়')); ?></div>
                    <div class="col-sm-12 mb-2"><strong><?php echo t('Responsibilities:', 'দায়িত্ব:'); ?></strong> <p><?php echo nl2br(htmlspecialchars($candidate['responsibilities'] ?? t('N/A', 'প্রযোজ্য নয়'))); ?></p></div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
