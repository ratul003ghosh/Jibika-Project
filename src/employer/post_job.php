<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$pjText = [
    'bn' => [
        'title' => 'চাকরি পোস্ট করুন (উইজার্ড)',
        'subtitle' => 'এর জন্য একটি পেশাদার চাকরির সার্কুলার তৈরি করুন।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'err_fill' => 'অনুগ্রহ করে সব প্রয়োজনীয় ক্ষেত্র পূরণ করুন।',
        'step1' => 'প্রাথমিক তথ্য',
        'step2' => 'কাজের বিবরণ',
        'step3' => 'অবস্থান',
        'step4' => 'বেতন ও সুবিধা',
        'step5' => 'যোগ্যতা',
        'step6' => 'পর্যালোচনা ও সাবমিট',
        'job_title' => 'চাকরির শিরোনাম',
        'vacancy' => 'খালি পদ সংখ্যা',
        'category' => 'চাকরির ক্যাটাগরি',
        'select_category' => 'ক্যাটাগরি নির্বাচন করুন',
        'type' => 'কাজের ধরন',
        'select_type' => 'ধরন নির্বাচন করুন',
        'deadline' => 'আবেদনের শেষ সময়',
        'job_desc' => 'কাজের বিবরণ',
        'responsibilities' => 'দায়িত্বসমূহ',
        'requirements_desc' => 'কাজের প্রয়োজনীয়তা',
        'district' => 'জেলা',
        'select_district' => 'জেলা নির্বাচন করুন',
        'upazila' => 'উপজেলা',
        'select_upazila' => 'উপজেলা নির্বাচন করুন',
        'location_details' => 'অবস্থানের বিবরণ',
        'salary_min' => 'সর্বনিম্ন বেতন',
        'salary_max' => 'সর্বোচ্চ বেতন',
        'benefits' => 'সুবিধাসমূহ',
        'exp_req' => 'অভিজ্ঞতার যোগ্যতা (বছর)',
        'edu_req' => 'শিক্ষাগত যোগ্যতা',
        'skills_req' => 'প্রয়োজনীয় দক্ষতা (কমা দিয়ে লিখুন)',
        'btn_next' => 'পরবর্তী',
        'btn_back' => 'পিছনে',
        'publish_btn' => 'চাকরি প্রকাশ করুন',
        'alert_success' => 'চাকরি সফলভাবে পোস্ট করা হয়েছে!',
        'review_title' => 'আপনার তথ্য পর্যালোচনা করুন',
        // Categories
        'cat_it' => 'আইটি ও কম্পিউটার', 'cat_garments' => 'গার্মেন্টস', 'cat_driving' => 'ড্রাইভিং',
        'cat_sales' => 'বিক্রয় ও বিপণন', 'cat_office' => 'অফিস সাপোর্ট', 'cat_health' => 'স্বাস্থ্যসেবা',
        'cat_edu' => 'শিক্ষা', 'cat_biz' => 'ক্ষুদ্র ব্যবসা', 'cat_other' => 'অন্যান্য',
        // Types
        'type_ft' => 'পূর্ণকালীন', 'type_pt' => 'খণ্ডকালীন', 'type_pts' => 'খণ্ডকালীন (শিক্ষার্থী)',
        'type_dl' => 'দৈনিক শ্রমিক', 'type_intern' => 'ইন্টার্নশিপ', 'type_contract' => 'চুক্তিভিত্তিক', 'type_remote' => 'রিমোট',
    ],
    'en' => [
        'title' => 'Post a Job (Wizard)',
        'subtitle' => 'Create a professional job post for ',
        'back_btn' => 'Back to Dashboard',
        'err_fill' => 'Please fill all required fields.',
        'step1' => 'Basic Info',
        'step2' => 'Job Description',
        'step3' => 'Location',
        'step4' => 'Salary & Benefits',
        'step5' => 'Requirements',
        'step6' => 'Review & Submit',
        'job_title' => 'Job Title',
        'vacancy' => 'Vacancy',
        'category' => 'Job Category',
        'select_category' => 'Select Category',
        'type' => 'Job Type',
        'select_type' => 'Select Type',
        'deadline' => 'Application Deadline',
        'job_desc' => 'Job Description',
        'responsibilities' => 'Responsibilities',
        'requirements_desc' => 'Requirements',
        'district' => 'District',
        'select_district' => 'Select District',
        'upazila' => 'Upazila',
        'select_upazila' => 'Select Upazila',
        'location_details' => 'Location Details',
        'salary_min' => 'Minimum Salary',
        'salary_max' => 'Maximum Salary',
        'benefits' => 'Benefits',
        'exp_req' => 'Required Experience (years)',
        'edu_req' => 'Required Education',
        'skills_req' => 'Required Skills (comma separated)',
        'btn_next' => 'Next',
        'btn_back' => 'Back',
        'publish_btn' => 'Publish Job',
        'alert_success' => 'Job posted successfully!',
        'review_title' => 'Review your information',
        // Categories
        'cat_it' => 'IT & Computer', 'cat_garments' => 'Garments', 'cat_driving' => 'Driving',
        'cat_sales' => 'Sales & Marketing', 'cat_office' => 'Office Support', 'cat_health' => 'Healthcare',
        'cat_edu' => 'Education', 'cat_biz' => 'Small Business', 'cat_other' => 'Other',
        // Types
        'type_ft' => 'Full-time', 'type_pt' => 'Part-time', 'type_pts' => 'Part-time (Student)',
        'type_dl' => 'Day Labor', 'type_intern' => 'Internship', 'type_contract' => 'Contract', 'type_remote' => 'Remote',
    ]
];
$ct = $pjText[$lang];

$message = "";
$message_type = "info";

$profile_check = $conn->query("SELECT employer_profile_id, company_name FROM employer_profiles WHERE user_id='$employer_id' LIMIT 1");

if (!$profile_check || $profile_check->num_rows == 0) {
    header("Location: profile.php");
    exit();
}
$company_profile = $profile_check->fetch_assoc();

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");

if (isset($_POST['post_job'])) {
    $title = trim($_POST['title']);
    $job_category = trim($_POST['job_category']);
    $job_type = trim($_POST['job_type']);
    $vacancy = !empty($_POST['vacancy']) ? intval($_POST['vacancy']) : 1;
    $application_deadline = trim($_POST['application_deadline'] ?? '');
    
    $description = trim($_POST['description']);
    $responsibilities = trim($_POST['responsibilities'] ?? '');
    $requirements_desc = trim($_POST['requirements_desc'] ?? '');
    
    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $location = trim($_POST['location'] ?? '');
    
    $salary_min = trim($_POST['salary_min'] ?? '');
    $salary_max = trim($_POST['salary_max'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    
    $experience_required = trim($_POST['experience_required'] ?? '');
    $education_required = trim($_POST['education_required'] ?? '');
    $skills_req = trim($_POST['skills_req'] ?? '');

    // Formulate final description
    $final_description = $description;
    if (!empty($responsibilities)) $final_description .= "\n\nResponsibilities:\n" . $responsibilities;
    if (!empty($requirements_desc)) $final_description .= "\n\nRequirements:\n" . $requirements_desc;
    if (!empty($skills_req)) $final_description .= "\n\nSkills Required:\n" . $skills_req;
    if (!empty($benefits)) $final_description .= "\n\nBenefits:\n" . $benefits;

    // Formulate salary
    $salary = "";
    $salary_type = "Negotiable";
    if (!empty($salary_min) && !empty($salary_max)) {
        $salary = $salary_min . " - " . $salary_max;
        $salary_type = "Range";
    } elseif (!empty($salary_min)) {
        $salary = $salary_min;
        $salary_type = "Fixed";
    }

    if ($title == "" || $description == "" || $job_category == "" || $job_type == "" || $district_id == "NULL" || $application_deadline == "") {
        $message = $ct['err_fill'];
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO jobs 
                (employer_id, title, description, location, salary, district_id, upazila_id, job_type, job_category, vacancy, experience_required, education_required, application_deadline, salary_type, status)
                VALUES
                ('$employer_id', '$title', '".$conn->real_escape_string($final_description)."', '$location', '$salary', $district_id, $upazila_id, '$job_type', '$job_category', '$vacancy', '$experience_required', '$education_required', '$application_deadline', '$salary_type', 'active')";

        if ($conn->query($sql)) {
            $new_job_id = $conn->insert_id;

            // Trigger Notifications
            $d_name = ''; $u_name = '';
            if ($district_id != "NULL") {
                $d_res = $conn->query("SELECT district_name FROM districts WHERE district_id = $district_id");
                if ($d_res) $d_name = $d_res->fetch_assoc()['district_name'] ?? '';
            }
            if ($upazila_id != "NULL") {
                $u_res = $conn->query("SELECT upazila_name FROM upazilas WHERE upazila_id = $upazila_id");
                if ($u_res) $u_name = $u_res->fetch_assoc()['upazila_name'] ?? '';
            }

            $s_sql = "SELECT user_id FROM job_seeker_profiles WHERE 
                (preferred_district != '' AND preferred_district LIKE '%".$conn->real_escape_string($d_name)."%') OR 
                (preferred_upazila != '' AND preferred_upazila LIKE '%".$conn->real_escape_string($u_name)."%') OR 
                (preferred_job_category != '' AND preferred_job_category LIKE '%".$conn->real_escape_string($job_category)."%')";

            $s_res = $conn->query($s_sql);
            if ($s_res) {
                $t_en = "New job posted in your preferred area";
                $t_bn = "আপনার পছন্দের এলাকায় নতুন চাকরি প্রকাশ করা হয়েছে";
                $m_en = $conn->real_escape_string($title);
                $m_bn = $conn->real_escape_string($title);
                
                while ($s = $s_res->fetch_assoc()) {
                    $uid = $s['user_id'];
                    $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, is_read) VALUES ($uid, $new_job_id, '$t_en', '$t_bn', '$m_en', '$m_bn', 0)");
                }
            }

            echo "<script>
                alert('" . addslashes($ct['alert_success']) . "');
                window.location='manage_jobs.php';
            </script>";
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "danger";
        }
    }
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<style>
/* Premium Stepper Styling */
body { background-color: #f4f7f6; }
.card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.step-pane { display: none; opacity: 0; transform: translateY(10px); transition: all 0.4s ease-in-out; }
.step-pane.active { display: block; opacity: 1; transform: translateY(0); }
.stepper-header { display: flex; justify-content: space-between; margin-bottom: 2.5rem; position: relative; padding: 0 10px; }
.stepper-header::before {
    content: ''; position: absolute; top: 50%; left: 0; width: 100%; height: 4px; background: #e9ecef; z-index: 0; transform: translateY(-50%); border-radius: 2px;
}
.progress-line {
    position: absolute; top: 50%; left: 0; height: 4px; background: #006a4e; z-index: 0; transform: translateY(-50%); transition: width 0.4s ease; border-radius: 2px;
}
.step-indicator {
    background: #fff; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    border: 4px solid #e9ecef; z-index: 1; font-weight: bold; color: #adb5bd; position: relative; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 0 0 5px #fff;
}
.step-indicator.active { border-color: #006a4e; color: #006a4e; box-shadow: 0 0 0 5px #e6f0ed; }
.step-indicator.completed { background: #006a4e; border-color: #006a4e; color: #fff; }
.form-control, .form-select { padding: 0.8rem 1rem; border-radius: 12px; border: 1px solid #dee2e6; background-color: #f8f9fa; transition: all 0.2s; }
.form-control:focus, .form-select:focus { background-color: #fff; border-color: #006a4e; box-shadow: 0 0 0 0.25rem rgba(0, 106, 78, 0.15); }
.btn-success { background-color: #006a4e; border-color: #006a4e; border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; }
.btn-success:hover { background-color: #00563f; border-color: #00563f; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0,106,78,0.2); }
.btn-secondary { background-color: #6c757d; border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0"><?php echo $ct['subtitle']; ?> <?php echo htmlspecialchars($company_profile['company_name']); ?>.</p>
        </div>
        <a href="dashboard.php" class="btn btn-dark"><?php echo $ct['back_btn']; ?></a>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?> shadow-sm"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm p-4">
        <div class="stepper-header" id="stepperHeader">
            <div class="progress-line" id="progressLine" style="width: 0%;"></div>
            <div class="step-indicator active" id="ind-1">1</div>
            <div class="step-indicator" id="ind-2">2</div>
            <div class="step-indicator" id="ind-3">3</div>
            <div class="step-indicator" id="ind-4">4</div>
            <div class="step-indicator" id="ind-5">5</div>
            <div class="step-indicator" id="ind-6"><i class="fa-solid fa-check"></i></div>
        </div>

        <h5 id="stepTitle" class="text-center mb-4 text-success fw-bold"></h5>

        <form method="POST" id="wizardForm">
            <!-- STEP 1 -->
            <div class="step-pane active" id="step-1">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['job_title']; ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="inp_title" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['category']; ?> <span class="text-danger">*</span></label>
                        <select name="job_category" id="inp_cat" class="form-select" required>
                            <option value=""><?php echo $ct['select_category']; ?></option>
                            <option value="IT & Computer"><?php echo $ct['cat_it']; ?></option>
                            <option value="Garments"><?php echo $ct['cat_garments']; ?></option>
                            <option value="Driving"><?php echo $ct['cat_driving']; ?></option>
                            <option value="Sales & Marketing"><?php echo $ct['cat_sales']; ?></option>
                            <option value="Office Support"><?php echo $ct['cat_office']; ?></option>
                            <option value="Healthcare"><?php echo $ct['cat_health']; ?></option>
                            <option value="Education"><?php echo $ct['cat_edu']; ?></option>
                            <option value="Small Business"><?php echo $ct['cat_biz']; ?></option>
                            <option value="Other"><?php echo $ct['cat_other']; ?></option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['type']; ?> <span class="text-danger">*</span></label>
                        <select name="job_type" id="inp_type" class="form-select" required>
                            <option value=""><?php echo $ct['select_type']; ?></option>
                            <option value="Full-time"><?php echo $ct['type_ft']; ?></option>
                            <option value="Part-time"><?php echo $ct['type_pt']; ?></option>
                            <option value="Part-time (Student)"><?php echo $ct['type_pts']; ?></option>
                            <option value="Day Labor"><?php echo $ct['type_dl']; ?></option>
                            <option value="Internship"><?php echo $ct['type_intern']; ?></option>
                            <option value="Contract"><?php echo $ct['type_contract']; ?></option>
                            <option value="Remote"><?php echo $ct['type_remote']; ?></option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['vacancy']; ?></label>
                        <input type="number" name="vacancy" id="inp_vac" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['deadline']; ?> <span class="text-danger">*</span></label>
                        <input type="date" name="application_deadline" id="inp_dead" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-success px-4" onclick="nextStep(1)"><?php echo $ct['btn_next']; ?></button>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="step-pane" id="step-2">
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['job_desc']; ?> <span class="text-danger">*</span></label>
                    <textarea name="description" id="inp_desc" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['responsibilities']; ?></label>
                    <textarea name="responsibilities" id="inp_resp" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['requirements_desc']; ?></label>
                    <textarea name="requirements_desc" id="inp_req" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevStep(2)"><?php echo $ct['btn_back']; ?></button>
                    <button type="button" class="btn btn-success px-4" onclick="nextStep(2)"><?php echo $ct['btn_next']; ?></button>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="step-pane" id="step-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['district']; ?> <span class="text-danger">*</span></label>
                        <select name="district_id" id="inp_dist" class="form-select" required>
                            <option value=""><?php echo $ct['select_district']; ?></option>
                            <?php if ($districts && $districts->num_rows > 0) { while ($r = $districts->fetch_assoc()) echo "<option value='{$r['district_id']}'>{$r['district_name']}</option>"; } ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['upazila']; ?></label>
                        <select name="upazila_id" id="inp_upa" class="form-select">
                            <option value=""><?php echo $ct['select_upazila']; ?></option>
                            <?php if ($upazilas && $upazilas->num_rows > 0) { while ($r = $upazilas->fetch_assoc()) echo "<option value='{$r['upazila_id']}'>{$r['upazila_name']}</option>"; } ?>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['location_details']; ?></label>
                        <input type="text" name="location" id="inp_loc" class="form-control">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevStep(3)"><?php echo $ct['btn_back']; ?></button>
                    <button type="button" class="btn btn-success px-4" onclick="nextStep(3)"><?php echo $ct['btn_next']; ?></button>
                </div>
            </div>

            <!-- STEP 4 -->
            <div class="step-pane" id="step-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['salary_min']; ?></label>
                        <input type="text" name="salary_min" id="inp_smin" class="form-control" placeholder="e.g. 15000">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['salary_max']; ?></label>
                        <input type="text" name="salary_max" id="inp_smax" class="form-control" placeholder="e.g. 25000">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['benefits']; ?></label>
                        <textarea name="benefits" id="inp_ben" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevStep(4)"><?php echo $ct['btn_back']; ?></button>
                    <button type="button" class="btn btn-success px-4" onclick="nextStep(4)"><?php echo $ct['btn_next']; ?></button>
                </div>
            </div>

            <!-- STEP 5 -->
            <div class="step-pane" id="step-5">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['exp_req']; ?></label>
                        <select name="experience_required" id="inp_exp" class="form-select">
                            <option value="">Any Experience</option>
                            <option value="Freshers Allowed">Freshers Allowed</option>
                            <option value="1-2 Years">1-2 Years</option>
                            <option value="3-5 Years">3-5 Years</option>
                            <option value="5-10 Years">5-10 Years</option>
                            <option value="10+ Years">10+ Years</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['edu_req']; ?></label>
                        <select name="education_required" id="inp_edu" class="form-select">
                            <option value="">Any Education</option>
                            <option value="JSC / JDC">JSC / JDC</option>
                            <option value="SSC / Dakhil">SSC / Dakhil</option>
                            <option value="HSC / Alim">HSC / Alim</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Bachelor/Honors">Bachelor / Honors</option>
                            <option value="Masters">Masters</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold"><?php echo $ct['skills_req']; ?></label>
                        <input type="text" name="skills_req" id="inp_skills" class="form-control" placeholder="e.g. PHP, MySQL, CSS">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevStep(5)"><i class="fa-solid fa-arrow-left me-2"></i><?php echo $ct['btn_back']; ?></button>
                    <button type="button" class="btn btn-success px-4" onclick="nextStep(5); populateReview();"><?php echo $ct['btn_next']; ?> <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- STEP 6 -->
            <div class="step-pane" id="step-6">
                <h5 class="mb-3 text-secondary"><?php echo $ct['review_title']; ?></h5>
                <div class="card bg-light border-0 p-3 mb-4">
                    <p><strong><?php echo $ct['job_title']; ?>:</strong> <span id="rev_title"></span></p>
                    <p><strong><?php echo $ct['category']; ?>:</strong> <span id="rev_cat"></span></p>
                    <p><strong><?php echo $ct['type']; ?>:</strong> <span id="rev_type"></span></p>
                    <hr>
                    <p><strong><?php echo $ct['job_desc']; ?>:</strong><br><span id="rev_desc"></span></p>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary px-4" onclick="prevStep(6)"><?php echo $ct['btn_back']; ?></button>
                    <button type="submit" name="post_job" class="btn btn-primary px-5 fw-bold"><?php echo $ct['publish_btn']; ?></button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 6;
const stepTitles = [
    "<?php echo $ct['step1']; ?>", "<?php echo $ct['step2']; ?>", "<?php echo $ct['step3']; ?>",
    "<?php echo $ct['step4']; ?>", "<?php echo $ct['step5']; ?>", "<?php echo $ct['step6']; ?>"
];

function updateUI() {
    for (let i = 1; i <= totalSteps; i++) {
        document.getElementById('step-' + i).classList.remove('active');
        const ind = document.getElementById('ind-' + i);
        ind.classList.remove('active', 'completed');
        if (i < currentStep) ind.classList.add('completed');
        if (i === currentStep) ind.classList.add('active');
    }
    document.getElementById('step-' + currentStep).classList.add('active');
    document.getElementById('stepTitle').innerText = stepTitles[currentStep - 1];
    
    // Update progress bar
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressLine').style.width = progress + '%';
}

function validateStep(step) {
    let valid = true;
    if (step === 1) {
        if (!document.getElementById('inp_title').value) valid = false;
        if (!document.getElementById('inp_cat').value) valid = false;
        if (!document.getElementById('inp_type').value) valid = false;
        if (!document.getElementById('inp_dead').value) valid = false;
    } else if (step === 2) {
        if (!document.getElementById('inp_desc').value) valid = false;
    } else if (step === 3) {
        if (!document.getElementById('inp_dist').value) valid = false;
    }
    if (!valid) alert("<?php echo $ct['err_fill']; ?>");
    return valid;
}

function nextStep(step) {
    if (validateStep(step)) {
        currentStep++;
        updateUI();
    }
}

function prevStep(step) {
    currentStep--;
    updateUI();
}

function populateReview() {
    document.getElementById('rev_title').innerText = document.getElementById('inp_title').value;
    document.getElementById('rev_cat').innerText = document.getElementById('inp_cat').options[document.getElementById('inp_cat').selectedIndex].text;
    document.getElementById('rev_type').innerText = document.getElementById('inp_type').options[document.getElementById('inp_type').selectedIndex].text;
    document.getElementById('rev_desc').innerText = document.getElementById('inp_desc').value;
}

updateUI();
</script>

<?php include('../includes/footer.php'); ?>