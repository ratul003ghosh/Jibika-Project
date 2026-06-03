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
        'title' => 'চাকরি পোস্ট করুন',
        'subtitle' => 'এর জন্য একটি পেশাদার চাকরির সার্কুলার তৈরি করুন।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'err_fill' => 'অনুগ্রহ করে সব প্রয়োজনীয় ক্ষেত্র পূরণ করুন।',
        'job_title' => 'চাকরির শিরোনাম',
        'vacancy' => 'খালি পদ সংখ্যা',
        'category' => 'চাকরির ক্যাটাগরি',
        'select_category' => 'ক্যাটাগরি নির্বাচন করুন',
        'type' => 'কাজের ধরন',
        'select_type' => 'ধরন নির্বাচন করুন',
        'deadline' => 'আবেদনের শেষ সময়',
        'edu_req' => 'শিক্ষাগত যোগ্যতা',
        'exp_req' => 'অভিজ্ঞতার যোগ্যতা',
        'salary_type' => 'বেতনের ধরন',
        'salary' => 'বেতন',
        'district' => 'জেলা',
        'select_district' => 'জেলা নির্বাচন করুন',
        'upazila' => 'উপজেলা',
        'select_upazila' => 'উপজেলা নির্বাচন করুন',
        'ward' => 'ওয়ার্ড',
        'select_ward' => 'ওয়ার্ড নির্বাচন করুন',
        'location_details' => 'অবস্থানের বিবরণ',
        'job_desc' => 'কাজের বিবরণ',
        'publish_btn' => 'চাকরি প্রকাশ করুন',
        'alert_success' => 'চাকরি সফলভাবে পোস্ট করা হয়েছে!',
        'fixed' => 'নির্দিষ্ট',
        'range' => 'সীমা',
        'negotiable' => 'আলোচনা সাপেক্ষে',
        // Categories
        'cat_it' => 'আইটি ও কম্পিউটার', 'cat_garments' => 'গার্মেন্টস', 'cat_driving' => 'ড্রাইভিং',
        'cat_sales' => 'বিক্রয় ও বিপণন', 'cat_office' => 'অফিস সাপোর্ট', 'cat_health' => 'স্বাস্থ্যসেবা',
        'cat_edu' => 'শিক্ষা', 'cat_biz' => 'ক্ষুদ্র ব্যবসা', 'cat_other' => 'অন্যান্য',
        // Types
        'type_ft' => 'পূর্ণকালীন', 'type_pt' => 'খণ্ডকালীন', 'type_pts' => 'খণ্ডকালীন (শিক্ষার্থী)',
        'type_dl' => 'দৈনিক শ্রমিক', 'type_intern' => 'ইন্টার্নশিপ', 'type_contract' => 'চুক্তিভিত্তিক', 'type_remote' => 'রিমোট',
    ],
    'en' => [
        'title' => 'Post a Job',
        'subtitle' => 'Create a professional job post for ',
        'back_btn' => 'Back Dashboard',
        'err_fill' => 'Please fill all required fields.',
        'job_title' => 'Job Title',
        'vacancy' => 'Vacancy',
        'category' => 'Job Category',
        'select_category' => 'Select Category',
        'type' => 'Job Type',
        'select_type' => 'Select Type',
        'deadline' => 'Application Deadline',
        'edu_req' => 'Education Requirement',
        'exp_req' => 'Experience Requirement',
        'salary_type' => 'Salary Type',
        'salary' => 'Salary',
        'district' => 'District',
        'select_district' => 'Select District',
        'upazila' => 'Upazila',
        'select_upazila' => 'Select Upazila',
        'ward' => 'Ward',
        'select_ward' => 'Select Ward',
        'location_details' => 'Location Details',
        'job_desc' => 'Job Description',
        'publish_btn' => 'Publish Job',
        'alert_success' => 'Job posted successfully!',
        'fixed' => 'Fixed',
        'range' => 'Range',
        'negotiable' => 'Negotiable',
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
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

if (isset($_POST['post_job'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $job_category = trim($_POST['job_category']);
    $job_type = trim($_POST['job_type']);
    $vacancy = !empty($_POST['vacancy']) ? intval($_POST['vacancy']) : 1;
    $experience_required = trim($_POST['experience_required']);
    $education_required = trim($_POST['education_required']);
    $salary = trim($_POST['salary']);
    $salary_type = trim($_POST['salary_type']);
    $application_deadline = $_POST['application_deadline'];
    $location = trim($_POST['location']);

    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $ward_id = !empty($_POST['ward_id']) ? intval($_POST['ward_id']) : "NULL";

    if ($title == "" || $description == "" || $job_category == "" || $job_type == "" || $application_deadline == "" || $district_id == "NULL") {
        $message = $ct['err_fill'];
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO jobs 
                (employer_id, title, description, location, salary, district_id, upazila_id, ward_id, job_type, job_category, vacancy, experience_required, education_required, application_deadline, salary_type, status)
                VALUES
                ('$employer_id', '$title', '$description', '$location', '$salary', $district_id, $upazila_id, $ward_id, '$job_type', '$job_category', '$vacancy', '$experience_required', '$education_required', '$application_deadline', '$salary_type', 'active')";

        if ($conn->query($sql)) {
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

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0">
                <?php echo $ct['subtitle']; ?> <?php echo htmlspecialchars($company_profile['company_name']); ?>.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-dark"><?php echo $ct['back_btn']; ?></a>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?> shadow-sm">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm p-4">

        <form method="POST">

            <div class="row">

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['job_title']; ?> <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Example: Computer Operator" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['vacancy']; ?></label>
                    <input type="number" name="vacancy" class="form-control" min="1" value="1">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['category']; ?> <span class="text-danger">*</span></label>
                    <select name="job_category" class="form-select" required>
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

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['type']; ?> <span class="text-danger">*</span></label>
                    <select name="job_type" class="form-select" required>
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

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['deadline']; ?> <span class="text-danger">*</span></label>
                    <input type="date" name="application_deadline" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['edu_req']; ?></label>
                    <input type="text" name="education_required" class="form-control" placeholder="Example: HSC / Diploma / Any">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['exp_req']; ?></label>
                    <input type="text" name="experience_required" class="form-control" placeholder="Example: 1 year / Freshers allowed">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['salary_type']; ?></label>
                    <select name="salary_type" class="form-select">
                        <option value="Negotiable"><?php echo $ct['negotiable']; ?></option>
                        <option value="Fixed"><?php echo $ct['fixed']; ?></option>
                        <option value="Range"><?php echo $ct['range']; ?></option>
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['salary']; ?></label>
                    <input type="text" name="salary" class="form-control" placeholder="Example: 15000-20000 BDT">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['district']; ?> <span class="text-danger">*</span></label>
                    <select name="district_id" class="form-select" required>
                        <option value=""><?php echo $ct['select_district']; ?></option>
                        <?php
                        if ($districts && $districts->num_rows > 0) {
                            while ($row = $districts->fetch_assoc()) {
                                echo "<option value='" . $row['district_id'] . "'>" . htmlspecialchars($row['district_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['upazila']; ?></label>
                    <select name="upazila_id" class="form-select">
                        <option value=""><?php echo $ct['select_upazila']; ?></option>
                        <?php
                        if ($upazilas && $upazilas->num_rows > 0) {
                            while ($row = $upazilas->fetch_assoc()) {
                                echo "<option value='" . $row['upazila_id'] . "'>" . htmlspecialchars($row['upazila_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['ward']; ?></label>
                    <select name="ward_id" class="form-select">
                        <option value=""><?php echo $ct['select_ward']; ?></option>
                        <?php
                        if ($wards && $wards->num_rows > 0) {
                            while ($row = $wards->fetch_assoc()) {
                                echo "<option value='" . $row['ward_id'] . "'>" . htmlspecialchars($row['ward_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label fw-semibold"><?php echo $ct['location_details']; ?></label>
                    <input type="text" name="location" class="form-control" placeholder="Example: Near Bazar, Road 2">
                </div>

                <div class="col-12 mb-4">
                    <label class="form-label fw-semibold"><?php echo $ct['job_desc']; ?> <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="6" placeholder="Write job responsibilities, required skills, working time..." required></textarea>
                </div>

            </div>

            <button type="submit" name="post_job" class="btn btn-success w-100 py-2 fw-semibold">
                <?php echo $ct['publish_btn']; ?>
            </button>

        </form>

    </div>
</div>

<?php include('../includes/footer.php'); ?>