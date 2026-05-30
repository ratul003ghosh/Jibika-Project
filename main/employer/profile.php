<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$message = "";

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

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
                    address='$address',
                    description='$description'
                WHERE user_id='$employer_id'";
    } else {
        $sql = "INSERT INTO employer_profiles
                (user_id, company_name, company_type, trade_license, district_id, upazila_id, ward_id, address, description)
                VALUES
                ('$employer_id', '$company_name', '$company_type', '$trade_license', $district_id, $upazila_id, $ward_id, '$address', '$description')";
    }

    if ($conn->query($sql)) {
        echo "<script>
            alert('Company profile saved successfully!');
            window.location='dashboard.php';
        </script>";
        exit();
    } else {
        $message = "Error: " . $conn->error;
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
if (!empty($profile['address'])) $completion += 10;
if (!empty($profile['description'])) $completion += 10;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container-fluid py-5 px-xl-5">

    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-2" style="color: #006a4e;"><i class="fa-solid fa-building me-2"></i>Company Profile</h2>
            <p class="text-muted mb-0 fs-5">Complete your company information before posting and managing jobs.</p>
        </div>

        <a href="dashboard.php" class="btn btn-outline-dark px-4 py-2 rounded-pill shadow-sm fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard</a>
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

                <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($profile['company_name'] ?? 'Company Name Not Set'); ?></h4>
                <p class="text-muted mb-4"><i class="fa-solid fa-envelope me-2 text-success"></i><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>

                <div class="mb-2 text-start d-flex justify-content-between align-items-center">
                    <small class="fw-bold text-muted text-uppercase" style="letter-spacing:1px;">Profile Completion</small>
                    <small class="fw-bold fs-6" style="color: #f42a41;"><?php echo $completion; ?>%</small>
                </div>

                <div class="progress mb-2 rounded-pill shadow-sm" style="height:12px; background:#e9ecef;">
                    <div class="progress-bar rounded-pill" style="width: <?php echo $completion; ?>%; background-color: #f42a41;"></div>
                </div>
            </div>

            <!-- Saved Area -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-map-location-dot me-2 text-primary"></i>Location Info</h5>
                
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-city"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">District</small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['district_name'] ?? 'Not Set'); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-map"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">Upazila</small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['upazila_name'] ?? 'Not Set'); ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                    <div class="me-3 text-primary fs-4"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">Ward</small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($profile['ward_name'] ?? 'Not Set'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-5 h-100" style="border-radius:20px;">
                <h4 class="fw-bold mb-4 text-dark border-bottom pb-3"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Company Details</h4>

                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Company / Business Name</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-building text-primary"></i></span>
                                <input type="text" name="company_name" class="form-control border-0 bg-light" required placeholder="Example: Digital Service Ltd." value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Company Type</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-industry text-primary"></i></span>
                                <select name="company_type" class="form-select border-0 bg-light">
                                    <option value="">Select Type</option>
                                    <?php
                                    $types = ['IT Company', 'Garments', 'Shop/Small Business', 'Factory', 'NGO', 'Education', 'Healthcare', 'Other'];
                                    foreach ($types as $type) {
                                        $selected = (($profile['company_type'] ?? '') == $type) ? 'selected' : '';
                                        echo "<option value='$type' $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Trade License / Registration No.</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-certificate text-primary"></i></span>
                                <input type="text" name="trade_license" class="form-control border-0 bg-light" placeholder="Optional but recommended" value="<?php echo htmlspecialchars($profile['trade_license'] ?? ''); ?>" <?php echo !empty($profile['trade_license']) ? 'readonly' : ''; ?>>
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="fa-solid fa-lock me-1"></i>Cannot be changed once saved without admin approval.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Office Address</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-map-pin text-primary"></i></span>
                                <input type="text" name="address" class="form-control border-0 bg-light" placeholder="Example: Road 2, Mirpur, Dhaka" value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">District</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-city text-primary"></i></span>
                                <select name="district_id" class="form-select border-0 bg-light">
                                    <option value="">Select District</option>
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
                            <label class="form-label fw-bold text-muted">Upazila</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-road text-primary"></i></span>
                                <select name="upazila_id" class="form-select border-0 bg-light">
                                    <option value="">Select Upazila</option>
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
                            <label class="form-label fw-bold text-muted">Ward</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-house-flag text-primary"></i></span>
                                <select name="ward_id" class="form-select border-0 bg-light">
                                    <option value="">Select Ward</option>
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

                        <div class="col-12">
                            <label class="form-label fw-bold text-muted">Company Description</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-light border-0 align-items-start pt-3"><i class="fa-solid fa-align-left text-primary"></i></span>
                                <textarea name="description" class="form-control border-0 bg-light" rows="5" placeholder="Write about your company/business..."><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" name="save_profile" class="btn btn-success px-5 py-3 rounded-pill fw-bold shadow-sm" style="font-size:1.1rem; background-color: #006a4e; border: none;">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Save Company Profile
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>