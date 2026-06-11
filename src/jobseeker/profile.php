<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$profText = [
    'bn' => [
        'title' => 'আমার প্রোফাইল',
        'subtitle' => 'আপনার ব্যক্তিগত তথ্য পরিচালনা করুন এবং চাকরির মিল উন্নত করুন।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'profile_completion' => 'প্রোফাইল সম্পন্নতা',
        'location_info' => 'অবস্থানের তথ্য',
        'district' => 'জেলা',
        'upazila' => 'উপজেলা',
        'ward' => 'ওয়ার্ড',
        'not_set' => 'সেট করা হয়নি',
        'skills' => 'দক্ষতাসমূহ',
        'total_skills' => 'মোট দক্ষতাসমূহ',
        'manage_skills' => 'দক্ষতা পরিচালনা করুন',
        'cv' => 'জীবনবৃত্তান্ত (CV)',
        'cv_uploaded' => 'সিভি আপলোড করা হয়েছে',
        'view_cv' => 'বর্তমান সিভি দেখুন',
        'no_cv' => 'কোনো সিভি আপলোড করা হয়নি',
        'incomplete' => 'প্রোফাইল অসম্পূর্ণ',
        'edit_details' => 'ব্যক্তিগত বিবরণ সম্পাদনা করুন',
        'nid' => 'এনআইডি নম্বর',
        'nid_locked' => 'প্রথমবার সেভ করার পর এনআইডি পরিবর্তন করা যাবে না।',
        'education' => 'শিক্ষা',
        'education_placeholder' => 'উদাহরণ: সিএসইতে বিএসসি',
        'select_district' => 'জেলা নির্বাচন করুন',
        'select_upazila' => 'উপজেলা নির্বাচন করুন',
        'select_ward' => 'ওয়ার্ড নির্বাচন করুন',
        'upload_cv' => 'পেশাদার সিভি আপলোড করুন',
        'upload_new' => 'নতুন',
        'drag_drop' => 'টেনে আনুন এবং ছেড়ে দিন অথবা আপলোড করতে ক্লিক করুন',
        'formats' => 'সমর্থিত ফরম্যাট: PDF, DOC, DOCX (সর্বোচ্চ ৫ মেগাবাইট)',
        'about_me' => 'আমার সম্পর্কে',
        'about_me_placeholder' => 'নিজের সম্পর্কে কিছু লিখুন...',
        'save_btn' => 'প্রোফাইল এবং সিভি সংরক্ষণ করুন',
        'alert_success' => 'প্রোফাইল সফলভাবে সম্পন্ন হয়েছে!',
        'upload_failed' => 'সিভি আপলোড করতে ব্যর্থ হয়েছে।',
        'invalid_format' => 'অবৈধ সিভি ফরম্যাট। কেবল PDF, DOC, DOCX অনুমোদিত।',
    ],
    'en' => [
        'title' => 'My Profile',
        'subtitle' => 'Manage your personal information and improve your job matching.',
        'back_btn' => 'Back to Dashboard',
        'profile_completion' => 'Profile Completion',
        'location_info' => 'Location Info',
        'district' => 'District',
        'upazila' => 'Upazila',
        'ward' => 'Ward',
        'not_set' => 'Not Set',
        'skills' => 'Skills',
        'total_skills' => 'Total Skills',
        'manage_skills' => 'Manage Skills',
        'cv' => 'Curriculum Vitae',
        'cv_uploaded' => 'CV Uploaded',
        'view_cv' => 'View Current CV',
        'no_cv' => 'No CV Uploaded',
        'incomplete' => 'Profile Incomplete',
        'edit_details' => 'Edit Personal Details',
        'nid' => 'NID Number',
        'nid_locked' => 'NID is locked after first save.',
        'education' => 'Education',
        'education_placeholder' => 'Example: BSc in CSE',
        'select_district' => 'Select District',
        'select_upazila' => 'Select Upazila',
        'select_ward' => 'Select Ward',
        'upload_cv' => 'Upload Professional CV',
        'upload_new' => 'New',
        'drag_drop' => 'Drag and drop or click to upload',
        'formats' => 'Supported formats: PDF, DOC, DOCX (Max 5MB)',
        'about_me' => 'About Me',
        'about_me_placeholder' => 'Write something about yourself...',
        'save_btn' => 'Save Profile & CV',
        'alert_success' => 'Profile completed successfully!',
        'upload_failed' => 'Failed to upload CV.',
        'invalid_format' => 'Invalid CV format. Only PDF, DOC, DOCX are allowed.',
    ]
];
$ct = $profText[$lang];

$message = "";
$user_id = $_SESSION['user_id'];

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

if (isset($_POST['save_profile'])) {

    $nid = trim($_POST['nid']);
    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $ward_id = !empty($_POST['ward_id']) ? intval($_POST['ward_id']) : "NULL";
    $education = trim($_POST['education']);
    $about = trim($_POST['about']);

    $cv_path = "";
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == 0) {
        $allowed_exts = ['pdf', 'doc', 'docx'];
        $file_name = $_FILES['cv_file']['name'];
        $file_tmp = $_FILES['cv_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_exts)) {
            $new_name = "cv_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_dir = "../uploads/cv/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                $cv_path = $upload_dir . $new_name;
            } else {
                $message = $ct['upload_failed'];
            }
        } else {
            $message = $ct['invalid_format'];
        }
    }

    $pref_district = trim($_POST['preferred_district'] ?? '');
    $pref_upazila = trim($_POST['preferred_upazila'] ?? '');
    $pref_category = trim($_POST['preferred_job_category'] ?? '');

    $check = $conn->query("SELECT * FROM job_seeker_profiles WHERE user_id='$user_id'");

    if ($check && $check->num_rows > 0) {
        // Only update cv_path if a new one was uploaded
        $cv_update = ($cv_path != "") ? ", cv_path='$cv_path'" : "";
        $sql = "UPDATE job_seeker_profiles 
                SET 
                     nid='$nid',
                     district_id=$district_id,
                     upazila_id=$upazila_id,
                     ward_id=$ward_id,
                     degree='$education',
                     about='$about',
                     preferred_district='$pref_district',
                     preferred_upazila='$pref_upazila',
                     preferred_job_category='$pref_category'
                     $cv_update
                WHERE user_id='$user_id'";
    } else {
        $sql = "INSERT INTO job_seeker_profiles
                (user_id, nid, district_id, upazila_id, ward_id, degree, about, cv_path, preferred_district, preferred_upazila, preferred_job_category)
                VALUES
                ('$user_id', '$nid', $district_id, $upazila_id, $ward_id, '$education', '$about', '$cv_path', '$pref_district', '$pref_upazila', '$pref_category')";
    }

    if ($message == "") {
        if ($conn->query($sql)) {
            echo "<script>
                alert('" . addslashes($ct['alert_success']) . "');
                window.location.href = 'profile.php';
            </script>";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$result = $conn->query("
    SELECT 
        jsp.*,
        d.district_name,
        u.upazila_name,
        w.ward_name
    FROM job_seeker_profiles jsp
    LEFT JOIN districts d ON jsp.district_id = d.district_id
    LEFT JOIN upazilas u ON jsp.upazila_id = u.upazila_id
    LEFT JOIN wards w ON jsp.ward_id = w.ward_id
    WHERE jsp.user_id='$user_id'
");

$profile = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];

$skills_result = $conn->query("SELECT * FROM skills WHERE user_id='$user_id'");
$total_skills = ($skills_result) ? $skills_result->num_rows : 0;

$profile_completion = 0;

if (!empty($profile['nid'])) $profile_completion += 15;
if (!empty($profile['degree'])) $profile_completion += 15;
if ($total_skills > 0) $profile_completion += 15;
if (!empty($profile['about'])) $profile_completion += 15;
if (!empty($profile['district_id'])) $profile_completion += 20;
if (!empty($profile['cv_path'])) $profile_completion += 20;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container-fluid py-5 px-xl-5">

    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-2" style="color: #0a4f32;"><i class="fa-solid fa-id-card me-2"></i><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0 fs-5"> <?php echo $ct['subtitle']; ?></p>
        </div>

        <a href="dashboard.php" class="btn btn-dark px-4 py-2 rounded-pill shadow-sm fw-bold"><i class="fa-solid fa-arrow-left me-2"></i><?php echo $ct['back_btn']; ?></a>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-danger shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <!-- Avatar Card -->
            <div class="card border-0 shadow-sm p-4 mb-4 text-center" style="border-radius:20px; background:linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="mb-4 d-flex justify-content-center">
                    <div class="rounded-circle text-white d-flex justify-content-center align-items-center shadow-sm"
                         style="width:110px;height:110px;font-size:40px;font-weight:bold; background:linear-gradient(135deg, #198754, #0a4f32);">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                </div>

                <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h4>
                <p class="text-muted mb-4"><i class="fa-solid fa-envelope me-2 text-success"></i><?php echo htmlspecialchars($_SESSION['email']); ?></p>

                <div class="mb-2 text-start d-flex justify-content-between align-items-center">
                    <small class="fw-bold text-muted text-uppercase" style="letter-spacing:1px;"><?php echo $ct['profile_completion']; ?></small>
                    <small class="text-success fw-bold fs-6"><?php echo $profile_completion; ?>%</small>
                </div>

                <div class="progress mb-2 rounded-pill shadow-sm" style="height:12px; background:#e9ecef;">
                    <div class="progress-bar bg-success rounded-pill" style="width: <?php echo $profile_completion; ?>%;"></div>
                </div>
            </div>

            <!-- Saved Area -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-map-location-dot me-2 text-success"></i><?php echo $ct['location_info']; ?></h5>
                
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-success fs-4"><i class="fa-solid fa-city"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['district']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['district_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-success fs-4"><i class="fa-solid fa-map"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['upazila']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['upazila_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                    <div class="me-3 text-success fs-4"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['ward']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['ward_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:20px;">
                <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-screwdriver-wrench me-2 text-success"></i><?php echo $ct['skills']; ?></h5>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted fw-bold"><?php echo $ct['total_skills']; ?></span>
                    <span class="badge bg-success rounded-pill px-3 py-2 fs-6 shadow-sm"><?php echo $total_skills; ?></span>
                </div>
                <a href="skills.php" class="btn btn-outline-success w-100 rounded-pill fw-bold border-2"><?php echo $ct['manage_skills']; ?></a>
            </div>

            <!-- CV Status -->
            <div class="card border-0 shadow-sm p-4 text-center" style="border-radius:20px; background:linear-gradient(135deg, #198754, #0a4f32); color:#fff;">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-pdf me-2"></i><?php echo $ct['cv']; ?></h5>
                <?php if (!empty($profile['cv_path'])): ?>
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-check text-white fs-1 mb-2 d-block"></i>
                        <span class="fw-bold"><?php echo $ct['cv_uploaded']; ?></span>
                    </div>
                    <a href="<?php echo htmlspecialchars($profile['cv_path']); ?>" target="_blank" class="btn btn-light rounded-pill fw-bold w-100 shadow-sm text-success">
                        <i class="fa-solid fa-eye me-2"></i><?php echo $ct['view_cv']; ?>
                    </a>
                <?php else: ?>
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-xmark text-white-50 fs-1 mb-2 d-block"></i>
                        <span class="fw-bold text-white-50"><?php echo $ct['no_cv']; ?></span>
                    </div>
                    <span class="badge bg-danger rounded-pill px-3 py-2 border border-white"><?php echo $ct['incomplete']; ?></span>
                <?php endif; ?>
            </div>

        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-5 h-100" style="border-radius:20px;">
                <h4 class="fw-bold mb-4 text-dark border-bottom pb-3"><i class="fa-solid fa-user-pen me-2 text-success"></i><?php echo $ct['edit_details']; ?></h4>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['nid']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-regular fa-id-card text-success"></i></span>
                                <input type="text" name="nid" class="form-control border-0 bg-light" value="<?php echo htmlspecialchars($profile['nid'] ?? ''); ?>" <?php echo !empty($profile['nid']) ? 'readonly' : ''; ?>>
                            </div>
                            <?php if (!empty($profile['nid'])): ?>
                                <small class="text-muted mt-1 d-block"><i class="fa-solid fa-lock me-1"></i><?php echo $ct['nid_locked']; ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['education']; ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-graduation-cap text-muted"></i></span>
                                <input type="text" name="education" class="form-control border-0 bg-light" placeholder="<?php echo $ct['education_placeholder']; ?>" value="<?php echo htmlspecialchars($profile['degree'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['district']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-building text-success"></i></span>
                                <select name="district_id" class="form-select border-0 bg-light">
                                    <option value=""><?php echo $ct['select_district']; ?></option>
                                    <?php
                                    if ($districts && $districts->num_rows > 0) {
                                        while ($row = $districts->fetch_assoc()) {
                                            $selected = (($profile['district_id'] ?? '') == $row['district_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['district_id'] . "' $selected>" . htmlspecialchars($row['district_name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['upazila']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-road text-success"></i></span>
                                <select name="upazila_id" class="form-select border-0 bg-light">
                                    <option value=""><?php echo $ct['select_upazila']; ?></option>
                                    <?php
                                    if ($upazilas && $upazilas->num_rows > 0) {
                                        while ($row = $upazilas->fetch_assoc()) {
                                            $selected = (($profile['upazila_id'] ?? '') == $row['upazila_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['upazila_id'] . "' $selected>" . htmlspecialchars($row['upazila_name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['ward']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-house-chimney text-success"></i></span>
                                <select name="ward_id" class="form-select border-0 bg-light">
                                    <option value=""><?php echo $ct['select_ward']; ?></option>
                                    <?php
                                    if ($wards && $wards->num_rows > 0) {
                                        while ($row = $wards->fetch_assoc()) {
                                            $selected = (($profile['ward_id'] ?? '') == $row['ward_id']) ? 'selected' : '';
                                            echo "<option value='" . $row['ward_id'] . "' $selected>" . htmlspecialchars($row['ward_name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- PREFERRED AREA SETTINGS -->
                        <div class="col-12 mt-4"><h5 class="border-bottom pb-2 text-primary">Job Preferences / কাজের পছন্দ</h5></div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Preferred District / পছন্দের জেলা</label>
                            <input type="text" name="preferred_district" class="form-control bg-light border-0 shadow-sm" value="<?php echo htmlspecialchars($profile['preferred_district'] ?? ''); ?>" placeholder="e.g. Dhaka">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Preferred Upazila / পছন্দের উপজেলা</label>
                            <input type="text" name="preferred_upazila" class="form-control bg-light border-0 shadow-sm" value="<?php echo htmlspecialchars($profile['preferred_upazila'] ?? ''); ?>" placeholder="e.g. Savar">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Preferred Job Category / পছন্দের ক্যাটাগরি</label>
                            <input type="text" name="preferred_job_category" class="form-control bg-light border-0 shadow-sm" value="<?php echo htmlspecialchars($profile['preferred_job_category'] ?? ''); ?>" placeholder="e.g. IT & Computer">
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['upload_cv']; ?> <span class="badge bg-success rounded-pill ms-2"><?php echo $ct['upload_new']; ?></span></label>
                            <div class="p-4 border border-2 border-dashed rounded-3 bg-light text-center" style="border-color: #198754 !important;">
                                <i class="fa-solid fa-cloud-arrow-up fs-1 text-success mb-3"></i>
                                <h6 class="fw-bold text-dark mb-1"><?php echo $ct['drag_drop']; ?></h6>
                                <p class="text-muted small mb-3"><?php echo $ct['formats']; ?></p>
                                <input type="file" name="cv_file" class="form-control form-control-lg bg-white shadow-sm rounded-pill" accept=".pdf,.doc,.docx">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['about_me']; ?></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 align-items-start pt-3"><i class="fa-solid fa-pen-clip text-success"></i></span>
                                <textarea name="about" class="form-control border-0 bg-light" rows="5" placeholder="<?php echo $ct['about_me_placeholder']; ?>"><?php echo htmlspecialchars($profile['about'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex justify-content-end align-items-center border-top pt-4">
                        <button type="submit" name="save_profile" class="btn btn-success px-5 py-3 rounded-pill fw-bold shadow-lg" style="font-size:1.1rem; background: linear-gradient(135deg, #198754, #0a4f32); border: none;">
                            <i class="fa-solid fa-floppy-disk me-2"></i><?php echo $ct['save_btn']; ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>