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

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Company Profile</h2>
            <p class="text-muted mb-0">
                Complete your company information before posting and managing jobs.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-dark">
            Back Dashboard
        </a>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-danger shadow-sm">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center"
                         style="width:90px;height:90px;font-size:32px;font-weight:bold;">
                        <?php
                        $display_name = !empty($profile['company_name']) ? $profile['company_name'] : ($_SESSION['full_name'] ?? 'E');
                        echo strtoupper(substr($display_name, 0, 1));
                        ?>
                    </div>
                </div>

                <h4 class="fw-bold mb-1">
                    <?php echo htmlspecialchars($profile['company_name'] ?? 'Company Name Not Set'); ?>
                </h4>

                <p class="text-muted mb-3">
                    <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>
                </p>

                <div class="mb-2">
                    <small class="fw-bold">Profile Completion</small>
                </div>

                <div class="progress mb-2" style="height:12px;">
                    <div class="progress-bar bg-primary" style="width: <?php echo $completion; ?>%;"></div>
                </div>

                <small class="text-primary fw-bold">
                    <?php echo $completion; ?>% Completed
                </small>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Saved Area Information</h5>

                <p class="mb-2">
                    <strong>District:</strong>
                    <?php echo htmlspecialchars($profile['district_name'] ?? 'Not Set'); ?>
                </p>

                <p class="mb-2">
                    <strong>Upazila:</strong>
                    <?php echo htmlspecialchars($profile['upazila_name'] ?? 'Not Set'); ?>
                </p>

                <p class="mb-0">
                    <strong>Ward:</strong>
                    <?php echo htmlspecialchars($profile['ward_name'] ?? 'Not Set'); ?>
                </p>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm p-4">

                <form method="POST">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Company / Business Name</label>
                            <input type="text"
                                   name="company_name"
                                   class="form-control"
                                   required
                                   placeholder="Example: Digital Service Ltd."
                                   value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Company Type</label>
                            <select name="company_type" class="form-select">
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

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Trade License / Registration No.</label>
                            <input type="text"
                                   name="trade_license"
                                   class="form-control"
                                   placeholder="Optional but recommended"
                                   value="<?php echo htmlspecialchars($profile['trade_license'] ?? ''); ?>"
                                   <?php echo !empty($profile['trade_license']) ? 'readonly' : ''; ?>>
                            <small class="text-muted">
                                Once saved, trade license should not be changed without admin approval.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Office Address</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   placeholder="Example: Road 2, Mirpur, Dhaka"
                                   value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">District</label>
                            <select name="district_id" class="form-select">
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

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Upazila</label>
                            <select name="upazila_id" class="form-select">
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

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Ward</label>
                            <select name="ward_id" class="form-select">
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

                        <div class="col-12 mb-4">
                            <label class="form-label fw-semibold">Company Description</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="5"
                                      placeholder="Write about your company/business..."><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                        </div>

                    </div>

                    <button type="submit" name="save_profile" class="btn btn-primary w-100 py-2 fw-semibold">
                        Save Company Profile
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>