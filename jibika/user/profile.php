<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$message = "";
$user_id = $_SESSION['user_id'];

// Dropdown data
$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

// Save / Update profile
if(isset($_POST['save_profile'])){
    $nid         = $_POST['nid'];
    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id  = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $ward_id     = !empty($_POST['ward_id']) ? intval($_POST['ward_id']) : "NULL";
    $education   = $_POST['education'];
    $skills      = $_POST['skills'];
    $about       = $_POST['about'];

    $check = $conn->query("SELECT * FROM job_seeker_profiles WHERE user_id='$user_id'");

    if($check->num_rows > 0){
        $sql = "UPDATE job_seeker_profiles 
                SET nid='$nid',
                    district_id=$district_id,
                    upazila_id=$upazila_id,
                    ward_id=$ward_id,
                    education='$education',
                    skills='$skills',
                    about='$about'
                WHERE user_id='$user_id'";
    } else {
        $sql = "INSERT INTO job_seeker_profiles 
                (user_id, nid, district_id, upazila_id, ward_id, education, skills, about)
                VALUES 
                ('$user_id', '$nid', $district_id, $upazila_id, $ward_id, '$education', '$skills', '$about')";
    }

    if($conn->query($sql)){
        $message = "Profile saved successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch profile with joined location names
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

$profile = $result->fetch_assoc();
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">My Profile</h2>
                <p class="text-muted mb-0">Update your personal and area-based information.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if($message != ""): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>NID</label>
                <input type="text" name="nid" class="form-control" value="<?php echo htmlspecialchars($profile['nid'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>District</label>
                <select name="district_id" class="form-control">
                    <option value="">Select District</option>
                    <?php
                    if($districts && $districts->num_rows > 0){
                        while($row = $districts->fetch_assoc()){
                            $selected = (($profile['district_id'] ?? '') == $row['district_id']) ? 'selected' : '';
                            echo "<option value='".$row['district_id']."' $selected>".htmlspecialchars($row['district_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Upazila</label>
                <select name="upazila_id" class="form-control">
                    <option value="">Select Upazila</option>
                    <?php
                    if($upazilas && $upazilas->num_rows > 0){
                        while($row = $upazilas->fetch_assoc()){
                            $selected = (($profile['upazila_id'] ?? '') == $row['upazila_id']) ? 'selected' : '';
                            echo "<option value='".$row['upazila_id']."' $selected>".htmlspecialchars($row['upazila_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Ward</label>
                <select name="ward_id" class="form-control">
                    <option value="">Select Ward</option>
                    <?php
                    if($wards && $wards->num_rows > 0){
                        while($row = $wards->fetch_assoc()){
                            $selected = (($profile['ward_id'] ?? '') == $row['ward_id']) ? 'selected' : '';
                            echo "<option value='".$row['ward_id']."' $selected>".htmlspecialchars($row['ward_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Education</label>
                <input type="text" name="education" class="form-control" value="<?php echo htmlspecialchars($profile['education'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Skills</label>
                <textarea name="skills" class="form-control" rows="3"><?php echo htmlspecialchars($profile['skills'] ?? ''); ?></textarea>
            </div>

            <div class="mb-3">
                <label>About</label>
                <textarea name="about" class="form-control" rows="4"><?php echo htmlspecialchars($profile['about'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="save_profile" class="btn btn-success w-100">
                Save Profile
            </button>
        </form>

        <?php if(!empty($profile)): ?>
            <div class="mt-4 p-3 bg-light rounded">
                <h5 class="mb-3">Saved Area Information</h5>
                <p class="mb-1"><strong>District:</strong> <?php echo htmlspecialchars($profile['district_name'] ?? ''); ?></p>
                <p class="mb-1"><strong>Upazila:</strong> <?php echo htmlspecialchars($profile['upazila_name'] ?? ''); ?></p>
                <p class="mb-0"><strong>Ward:</strong> <?php echo htmlspecialchars($profile['ward_name'] ?? ''); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>