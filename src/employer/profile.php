<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$epText = [
    'bn' => [
        'alert_success' => 'কোম্পানি প্রোফাইল সফলভাবে সংরক্ষণ করা হয়েছে!',
        'msg_error' => 'ত্রুটি: ',
        'title' => 'কোম্পানি প্রোফাইল',
        'subtitle' => 'চাকরি পোস্ট ও পরিচালনার আগে আপনার কোম্পানির তথ্য সম্পূর্ণ করুন।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'not_set' => 'সেট করা হয়নি',
        'completion' => 'প্রোফাইল সম্পন্নতা',
        'location_info' => 'অবস্থানের তথ্য',
        'district' => 'জেলা',
        'upazila' => 'উপজেলা',
        'ward' => 'ওয়ার্ড',
        'edit_details' => 'কোম্পানির বিবরণ সম্পাদনা করুন',
        'company_name' => 'কোম্পানি / ব্যবসার নাম',
        'placeholder_company_name' => 'উদাহরণ: ডিজিটাল সার্ভিস লিমিটেড',
        'company_type' => 'কোম্পানির ধরন',
        'select_type' => 'ধরন নির্বাচন করুন',
        'trade_license' => 'ট্রেড লাইসেন্স / রেজিস্ট্রেশন নম্বর',
        'placeholder_trade' => 'ঐচ্ছিক কিন্তু প্রস্তাবিত',
        'trade_warning' => 'সংরক্ষণ করার পর এডমিনের অনুমোদন ছাড়া এটি পরিবর্তন করা যাবে না।',
        'address' => 'অফিসের ঠিকানা',
        'placeholder_address' => 'উদাহরণ: রোড ২, মিরপুর, ঢাকা',
        'select_district' => 'জেলা নির্বাচন করুন',
        'select_upazila' => 'উপজেলা নির্বাচন করুন',
        'select_ward' => 'ওয়ার্ড নির্বাচন করুন',
        'description' => 'কোম্পানির বিবরণ',
        'placeholder_desc' => 'আপনার কোম্পানি/ব্যবসা সম্পর্কে লিখুন...',
        'save_btn' => 'কোম্পানি প্রোফাইল সংরক্ষণ করুন',
        // Company Types translation
        'IT Company' => 'আইটি কোম্পানি',
        'Garments' => 'গার্মেন্টস',
        'Shop/Small Business' => 'দোকান/ক্ষুদ্র ব্যবসা',
        'Factory' => 'কারখানা',
        'NGO' => 'এনজিও',
        'Education' => 'শিক্ষা',
        'Healthcare' => 'স্বাস্থ্যসেবা',
        'Other' => 'অন্যান্য',
    ],
    'en' => [
        'alert_success' => 'Company profile saved successfully!',
        'msg_error' => 'Error: ',
        'title' => 'Company Profile',
        'subtitle' => 'Complete your company information before posting and managing jobs.',
        'back_btn' => 'Back to Dashboard',
        'not_set' => 'Not Set',
        'completion' => 'Profile Completion',
        'location_info' => 'Location Info',
        'district' => 'District',
        'upazila' => 'Upazila',
        'ward' => 'Ward',
        'edit_details' => 'Edit Company Details',
        'company_name' => 'Company / Business Name',
        'placeholder_company_name' => 'Example: Digital Service Ltd.',
        'company_type' => 'Company Type',
        'select_type' => 'Select Type',
        'trade_license' => 'Trade License / Registration No.',
        'placeholder_trade' => 'Optional but recommended',
        'trade_warning' => 'Cannot be changed once saved without admin approval.',
        'address' => 'Office Address',
        'placeholder_address' => 'Example: Road 2, Mirpur, Dhaka',
        'select_district' => 'Select District',
        'select_upazila' => 'Select Upazila',
        'select_ward' => 'Select Ward',
        'description' => 'Company Description',
        'placeholder_desc' => 'Write about your company/business...',
        'save_btn' => 'Save Company Profile',
        // Company Types translation
        'IT Company' => 'IT Company',
        'Garments' => 'Garments',
        'Shop/Small Business' => 'Shop/Small Business',
        'Factory' => 'Factory',
        'NGO' => 'NGO',
        'Education' => 'Education',
        'Healthcare' => 'Healthcare',
        'Other' => 'Other',
    ]
];
$ct = $epText[$lang];

$message = "";

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

$upazilas_json = [];
if ($upazilas && $upazilas->num_rows > 0) {
    while($u = $upazilas->fetch_assoc()) $upazilas_json[] = $u;
    $upazilas->data_seek(0);
}
$wards_json = [];
if ($wards && $wards->num_rows > 0) {
    while($w = $wards->fetch_assoc()) $wards_json[] = $w;
    $wards->data_seek(0);
}

if (isset($_POST['save_profile'])) {
    $company_name = trim($_POST['company_name']);
    $company_type = trim($_POST['company_type']);
    $trade_license = trim($_POST['trade_license']);
    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $ward_id = !empty($_POST['ward_id']) ? intval($_POST['ward_id']) : "NULL";
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);

    $check = $conn->query("SELECT * FROM employer_profiles WHERE user_id='$employer_id' LIMIT 1");

    if ($check && $check->num_rows > 0) {
        $sql = "UPDATE employer_profiles
                SET company_name='$company_name',
                    company_type='$company_type',
                    trade_license='$trade_license',
                    district_id=$district_id,
                    upazila_id=$upazila_id,
                    ward_id=$ward_id,
                    company_address='$address',
                    company_description='$description'
                WHERE user_id='$employer_id'";
    } else {
        $sql = "INSERT INTO employer_profiles
                (user_id, company_name, company_type, trade_license, district_id, upazila_id, ward_id, company_address, company_description)
                VALUES
                ('$employer_id', '$company_name', '$company_type', '$trade_license', $district_id, $upazila_id, $ward_id, '$address', '$description')";
    }

    if ($conn->query($sql)) {
        $alert_msg = addslashes($ct['alert_success']);
        echo "<script>
            alert('$alert_msg');
            window.location='dashboard.php';
        </script>";
        exit();
    } else {
        $message = $ct['msg_error'] . $conn->error;
    }
}

$result = $conn->query("
    SELECT 
        ep.*,
        d.district_name,
        u.upazila_name,
        w.ward_name
    FROM employer_profiles ep
    LEFT JOIN districts d ON ep.district_id = d.district_id
    LEFT JOIN upazilas u ON ep.upazila_id = u.upazila_id
    LEFT JOIN wards w ON ep.ward_id = w.ward_id
    WHERE ep.user_id='$employer_id'
    LIMIT 1
");

$profile = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];

$completion = 0;
if (!empty($profile['company_name'])) $completion += 25;
if (!empty($profile['company_type'])) $completion += 15;
if (!empty($profile['trade_license'])) $completion += 20;
if (!empty($profile['district_id'])) $completion += 20;
if (!empty($profile['company_address'])) $completion += 10;
if (!empty($profile['company_description'])) $completion += 10;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container-fluid py-5 px-xl-5">

    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-2" style="color: #006a4e;"><i class="fa-solid fa-building me-2"></i><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0 fs-5"><?php echo $ct['subtitle']; ?></p>
        </div>

        <a href="dashboard.php" class="btn btn-outline-dark px-4 py-2 rounded-pill shadow-sm fw-bold"><i class="fa-solid fa-arrow-left me-2"></i><?php echo $ct['back_btn']; ?></a>
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
                         style="width:110px;height:110px;font-size:40px;font-weight:bold; background:linear-gradient(135deg, #00563f, #006a4e);">
                        <?php
                        $display_name = !empty($profile['company_name']) ? $profile['company_name'] : ($_SESSION['full_name'] ?? 'E');
                        echo strtoupper(substr($display_name, 0, 1));
                        ?>
                    </div>
                </div>

                <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($profile['company_name'] ?? $ct['not_set']); ?></h4>
                <p class="text-muted mb-4"><i class="fa-solid fa-envelope me-2 text-success"></i><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>

                <div class="mb-2 text-start d-flex justify-content-between align-items-center">
                    <small class="fw-bold text-muted text-uppercase" style="letter-spacing:1px;"><?php echo $ct['completion']; ?></small>
                    <small class="fw-bold fs-6" style="color: #f42a41;"><?php echo $completion; ?>%</small>
                </div>

                <div class="progress mb-2 rounded-pill shadow-sm" style="height:12px; background:#e9ecef;">
                    <div class="progress-bar rounded-pill" style="width: <?php echo $completion; ?>%; background-color: #f42a41;"></div>
                </div>
            </div>

            <!-- Saved Area -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-map-location-dot me-2 text-primary"></i><?php echo $ct['location_info']; ?></h5>
                
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-city"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['district']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['district_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-map"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['upazila']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['upazila_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;"><?php echo $ct['ward']; ?></small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['ward_name'] ?? $ct['not_set']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-5 h-100" style="border-radius:20px;">
                <h4 class="fw-bold mb-4 text-dark border-bottom pb-3"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i><?php echo $ct['edit_details']; ?></h4>

                <style>
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
                </style>

                <div class="stepper-header" id="stepperHeader">
                    <div class="progress-line" id="progressLine" style="width: 0%;"></div>
                    <div class="step-indicator active" id="ind-1">1</div>
                    <div class="step-indicator" id="ind-2">2</div>
                    <div class="step-indicator" id="ind-3">3</div>
                </div>

                <form method="POST" id="profileForm">
                    <!-- STEP 1: Basic Info -->
                    <div class="step-pane active" id="step-1">
                        <h5 class="mb-4 text-success fw-bold">Step 1: Basic Information</h5>
                        <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['company_name']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-building text-primary"></i></span>
                                <input type="text" name="company_name" class="form-control border-0 bg-light" required placeholder="<?php echo $ct['placeholder_company_name']; ?>" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['company_type']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-industry text-primary"></i></span>
                                <select name="company_type" class="form-select border-0 bg-light">
                                    <option value=""><?php echo $ct['select_type']; ?></option>
                                    <?php
                                    $types = ['IT Company', 'Garments', 'Shop/Small Business', 'Factory', 'NGO', 'Education', 'Healthcare', 'Other'];
                                    foreach ($types as $type) {
                                        $selected = (($profile['company_type'] ?? '') == $type) ? 'selected' : '';
                                        $translated_type = $ct[$type] ?? $type;
                                        echo "<option value='$type' $selected>$translated_type</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['trade_license']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-certificate text-primary"></i></span>
                                <input type="text" name="trade_license" class="form-control border-0 bg-light" placeholder="<?php echo $ct['placeholder_trade']; ?>" value="<?php echo htmlspecialchars($profile['trade_license'] ?? ''); ?>" <?php echo !empty($profile['trade_license']) ? 'readonly' : ''; ?>>
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="fa-solid fa-lock me-1"></i><?php echo $ct['trade_warning']; ?></small>
                        </div>

                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-success px-4 py-2 rounded-pill fw-bold" onclick="nextStep(1)">Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2: Location Info -->
                    <div class="step-pane" id="step-2">
                        <h5 class="mb-4 text-success fw-bold">Step 2: Location Details</h5>
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted"><?php echo $ct['address']; ?></label>
                                <div class="input-group input-group-lg shadow-sm rounded-3">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-map-pin text-primary"></i></span>
                                    <input type="text" name="address" class="form-control border-0 bg-light" placeholder="<?php echo $ct['placeholder_address']; ?>" value="<?php echo htmlspecialchars($profile['company_address'] ?? ''); ?>">
                                </div>
                            </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['district']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-city text-primary"></i></span>
                                <select name="district_id" id="district_id" class="form-select border-0 bg-light" onchange="filterUpazilas()">
                                    <option value=""><?php echo $ct['select_district']; ?></option>
                                    <?php
                                    if ($districts && $districts->num_rows > 0) {
                                        $districts->data_seek(0);
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
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-road text-primary"></i></span>
                                <select name="upazila_id" id="upazila_id" class="form-select border-0 bg-light" onchange="filterWards()">
                                    <option value=""><?php echo $ct['select_upazila']; ?></option>
                                    <!-- Options populated by JS -->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['ward']; ?></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-house-flag text-primary"></i></span>
                                <select name="ward_id" id="ward_id" class="form-select border-0 bg-light">
                                    <option value=""><?php echo $ct['select_ward']; ?></option>
                                    <!-- Options populated by JS -->
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary px-4 py-2 rounded-pill fw-bold" onclick="prevStep(2)"><i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                            <button type="button" class="btn btn-success px-4 py-2 rounded-pill fw-bold" onclick="nextStep(2)">Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 3: Description -->
                    <div class="step-pane" id="step-3">
                        <h5 class="mb-4 text-success fw-bold">Step 3: Company Description</h5>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['description']; ?></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 align-items-start pt-3"><i class="fa-solid fa-align-left text-primary"></i></span>
                                <textarea name="description" class="form-control border-0 bg-light" rows="5" placeholder="<?php echo $ct['placeholder_desc']; ?>"><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary px-4 py-2 rounded-pill fw-bold" onclick="prevStep(3)"><i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                            <button type="submit" name="save_profile" class="btn btn-success px-5 py-2 rounded-pill fw-bold shadow-sm" style="background-color: #006a4e; border: none;">
                                <i class="fa-solid fa-floppy-disk me-2"></i><?php echo $ct['save_btn']; ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

</div>

<script>
const upazilas = <?php echo json_encode($upazilas_json); ?>;
const wards = <?php echo json_encode($wards_json); ?>;
const currentUpazila = "<?php echo $profile['upazila_id'] ?? ''; ?>";
const currentWard = "<?php echo $profile['ward_id'] ?? ''; ?>";

function filterUpazilas() {
    const d_id = document.getElementById('district_id').value;
    const u_sel = document.getElementById('upazila_id');
    u_sel.innerHTML = '<option value=""><?php echo $ct['select_upazila']; ?></option>';
    
    if (d_id) {
        upazilas.filter(u => u.district_id == d_id).forEach(u => {
            let selected = (u.upazila_id == currentUpazila) ? 'selected' : '';
            u_sel.innerHTML += `<option value="${u.upazila_id}" ${selected}>${u.upazila_name}</option>`;
        });
    }
    filterWards();
}

function filterWards() {
    const u_id = document.getElementById('upazila_id').value;
    const w_sel = document.getElementById('ward_id');
    w_sel.innerHTML = '<option value=""><?php echo $ct['select_ward']; ?></option>';
    
    if (u_id) {
        wards.filter(w => w.upazila_id == u_id).forEach(w => {
            let selected = (w.ward_id == currentWard) ? 'selected' : '';
            w_sel.innerHTML += `<option value="${w.ward_id}" ${selected}>${w.ward_name}</option>`;
        });
    }
}

// Init dropdowns on load
window.onload = function() {
    filterUpazilas();
};

let currentStep = 1;
const totalSteps = 3;

function updateUI() {
    for (let i = 1; i <= totalSteps; i++) {
        document.getElementById('step-' + i).classList.remove('active');
        const ind = document.getElementById('ind-' + i);
        ind.classList.remove('active', 'completed');
        if (i < currentStep) ind.classList.add('completed');
        if (i === currentStep) ind.classList.add('active');
    }
    document.getElementById('step-' + currentStep).classList.add('active');
    
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressLine').style.width = progress + '%';
}

function nextStep(step) {
    if (step === 1) {
        if (!document.querySelector('input[name="company_name"]').value) {
            alert('Company Name is required.'); return;
        }
    }
    currentStep++;
    updateUI();
}

function prevStep(step) {
    currentStep--;
    updateUI();
}
</script>

<?php include('../includes/footer.php'); ?>