<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

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

    $check = $conn->query("SELECT * FROM job_seeker_profiles WHERE user_id='$user_id'");

    if ($check && $check->num_rows > 0) {
        $sql = "UPDATE job_seeker_profiles 
                SET 
                    nid='$nid',
                    district_id=$district_id,
                    upazila_id=$upazila_id,
                    ward_id=$ward_id,
                    education='$education',
                    about='$about'
                WHERE user_id='$user_id'";
    } else {
        $sql = "INSERT INTO job_seeker_profiles
                (user_id, nid, district_id, upazila_id, ward_id, education, about)
                VALUES
                ('$user_id', '$nid', $district_id, $upazila_id, $ward_id, '$education', '$about')";
    }

    if ($conn->query($sql)) {
        echo "<script>
            alert('Profile completed successfully!');
            window.location='dashboard.php';
        </script>";
        exit();
    } else {
        $message = "Error: " . $conn->error;
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

if (!empty($profile['nid'])) $profile_completion += 20;
if (!empty($profile['education'])) $profile_completion += 20;
if ($total_skills > 0) $profile_completion += 20;
if (!empty($profile['about'])) $profile_completion += 20;
if (!empty($profile['district_id'])) $profile_completion += 20;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">My Profile</h2>
            <p class="text-muted mb-0">Manage your personal information and improve your job matching.</p>
        </div>

        <a href="dashboard.php" class="btn btn-dark">Back Dashboard</a>
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
                    <div class="rounded-circle bg-success text-white d-inline-flex justify-content-center align-items-center"
                         style="width:90px;height:90px;font-size:32px;font-weight:bold;">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                </div>

                <h4 class="fw-bold mb-1">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </h4>

                <p class="text-muted mb-3">
                    <?php echo htmlspecialchars($_SESSION['email']); ?>
                </p>

                <div class="mb-2">
                    <small class="fw-bold">Profile Completion</small>
                </div>

                <div class="progress mb-2" style="height:12px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $profile_completion; ?>%;"></div>
                </div>

                <small class="text-success fw-bold">
                    <?php echo $profile_completion; ?>% Completed
                </small>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4">
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

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-2">Skills</h5>
                <p class="text-muted mb-3">
                    Total skills added: <strong><?php echo $total_skills; ?></strong>
                </p>
                <a href="skills.php" class="btn btn-dark w-100">Manage Skills</a>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm p-4">

                <form method="POST">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NID Number</label>
                            <input type="text"
                                   name="nid"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($profile['nid'] ?? ''); ?>"
                                   <?php echo !empty($profile['nid']) ? 'readonly' : ''; ?>>
                            <?php if (!empty($profile['nid'])): ?>
                                <small class="text-muted">NID is locked after first save.</small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Education</label>
                            <input type="text"
                                   name="education"
                                   class="form-control"
                                   placeholder="Example: BSc in CSE"
                                   value="<?php echo htmlspecialchars($profile['education'] ?? ''); ?>">
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
                            <label class="form-label fw-semibold">About Me</label>
                            <textarea name="about"
                                      class="form-control"
                                      rows="5"
                                      placeholder="Write something about yourself..."><?php echo htmlspecialchars($profile['about'] ?? ''); ?></textarea>
                        </div>

                    </div>

                    <button type="submit" name="save_profile" class="btn btn-success w-100 py-2 fw-semibold">
                        Save Profile
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>