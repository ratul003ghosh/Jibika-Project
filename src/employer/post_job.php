<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
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
        $message = "Please fill all required fields.";
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO jobs 
                (employer_id, title, description, location, salary, district_id, upazila_id, ward_id, job_type, job_category, vacancy, experience_required, education_required, application_deadline, salary_type, status)
                VALUES
                ('$employer_id', '$title', '$description', '$location', '$salary', $district_id, $upazila_id, $ward_id, '$job_type', '$job_category', '$vacancy', '$experience_required', '$education_required', '$application_deadline', '$salary_type', 'active')";

        if ($conn->query($sql)) {
            echo "<script>
                alert('Job posted successfully!');
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
            <h2 class="fw-bold mb-1">Post a Job</h2>
            <p class="text-muted mb-0">
                Create a professional job post for <?php echo htmlspecialchars($company_profile['company_name']); ?>.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-dark">Back Dashboard</a>
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
                    <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Example: Computer Operator" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Vacancy</label>
                    <input type="number" name="vacancy" class="form-control" min="1" value="1">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Job Category <span class="text-danger">*</span></label>
                    <select name="job_category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="IT & Computer">IT & Computer</option>
                        <option value="Garments">Garments</option>
                        <option value="Driving">Driving</option>
                        <option value="Sales & Marketing">Sales & Marketing</option>
                        <option value="Office Support">Office Support</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Education">Education</option>
                        <option value="Small Business">Small Business</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Job Type <span class="text-danger">*</span></label>
                    <select name="job_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Part-time (Student)">Part-time (Student)</option>
                        <option value="Day Labor">Day Labor</option>
                        <option value="Internship">Internship</option>
                        <option value="Contract">Contract</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Application Deadline <span class="text-danger">*</span></label>
                    <input type="date" name="application_deadline" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Education Requirement</label>
                    <input type="text" name="education_required" class="form-control" placeholder="Example: HSC / Diploma / Any">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Experience Requirement</label>
                    <input type="text" name="experience_required" class="form-control" placeholder="Example: 1 year / Freshers allowed">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Salary Type</label>
                    <select name="salary_type" class="form-select">
                        <option value="Negotiable">Negotiable</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Range">Range</option>
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label fw-semibold">Salary</label>
                    <input type="text" name="salary" class="form-control" placeholder="Example: 15000-20000 BDT">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                    <select name="district_id" class="form-select" required>
                        <option value="">Select District</option>
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
                    <label class="form-label fw-semibold">Upazila</label>
                    <select name="upazila_id" class="form-select">
                        <option value="">Select Upazila</option>
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
                    <label class="form-label fw-semibold">Ward</label>
                    <select name="ward_id" class="form-select">
                        <option value="">Select Ward</option>
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
                    <label class="form-label fw-semibold">Location Details</label>
                    <input type="text" name="location" class="form-control" placeholder="Example: Near Bazar, Road 2">
                </div>

                <div class="col-12 mb-4">
                    <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="6" placeholder="Write job responsibilities, required skills, working time..." required></textarea>
                </div>

            </div>

            <button type="submit" name="post_job" class="btn btn-success w-100 py-2 fw-semibold">
                Publish Job
            </button>

        </form>

    </div>
</div>

<?php include('../includes/footer.php'); ?>