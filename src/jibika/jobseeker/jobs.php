<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";
// SAVE JOB
if (isset($_GET['save'])) {

    $job_id = intval($_GET['save']);

    $check_saved = $conn->query("
        SELECT * 
        FROM saved_jobs 
        WHERE user_id='$user_id' 
        AND job_id='$job_id'
    ");

    if ($check_saved && $check_saved->num_rows > 0) {

        $message = "Job already saved.";
        $message_type = "warning";

    } else {

        $save_sql = "
            INSERT INTO saved_jobs (user_id, job_id)
            VALUES ('$user_id', '$job_id')
        ";

        if ($conn->query($save_sql)) {

            $message = "Job saved successfully!";
            $message_type = "success";

        } else {

            $message = "Error saving job.";
            $message_type = "danger";
        }
    }
}

// UNSAVE JOB
if (isset($_GET['unsave'])) {

    $job_id = intval($_GET['unsave']);

    $conn->query("
        DELETE FROM saved_jobs
        WHERE user_id='$user_id'
        AND job_id='$job_id'
    ");

    $message = "Saved job removed.";
    $message_type = "info";
}

if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);

    $job_check = $conn->query("SELECT status, application_deadline FROM jobs WHERE job_id='$job_id' LIMIT 1");

    if (!$job_check || $job_check->num_rows == 0) {
        $message = "Job not found.";
        $message_type = "danger";
    } else {
        $job_data = $job_check->fetch_assoc();

        $is_closed = (($job_data['status'] ?? 'active') == 'closed');
        $is_deadline_over = (!empty($job_data['application_deadline']) && $job_data['application_deadline'] < date('Y-m-d'));

        if ($is_closed) {
            $message = "This job is closed. You cannot apply.";
            $message_type = "warning";
        } elseif ($is_deadline_over) {
            $message = "Application deadline is over.";
            $message_type = "warning";
        } else {
            $check_sql = "SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'";
            $check_result = $conn->query($check_sql);

            if ($check_result && $check_result->num_rows > 0) {
                $message = "You already applied for this job.";
                $message_type = "warning";
            } else {
                $apply_sql = "INSERT INTO applications (job_id, user_id) VALUES ('$job_id', '$user_id')";

                if ($conn->query($apply_sql)) {
                    $message = "Job applied successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error: " . $conn->error;
                    $message_type = "danger";
                }
            }
        }
    }
}

$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;
$job_type = isset($_GET['job_type']) ? trim($_GET['job_type']) : '';

$sql = "SELECT 
            jobs.*,
            d.district_name,
            u.upazila_name,
            w.ward_name,
            users.full_name AS company_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN wards w ON jobs.ward_id = w.ward_id
        LEFT JOIN users ON jobs.employer_id = users.user_id
        WHERE jobs.status='active'";

if ($search != "") {
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND (
        jobs.title LIKE '%$search_safe%' 
        OR jobs.description LIKE '%$search_safe%' 
        OR jobs.job_category LIKE '%$search_safe%'
    )";
}

if ($district_id > 0) {
    $sql .= " AND jobs.district_id = '$district_id'";
}

if ($job_type != "") {
    $job_type_safe = $conn->real_escape_string($job_type);
    $sql .= " AND jobs.job_type = '$job_type_safe'";
}

$sql .= " ORDER BY jobs.job_id DESC";

$jobs_result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h2 class="mb-1">Available Jobs</h2>
                <p class="text-muted mb-0">Browse active area-based job opportunities and apply easily.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by title, skill, category"
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </div>

            <div class="col-md-3">
                <select name="district_id" class="form-control">
                    <option value="">All Districts</option>
                    <?php
                    if ($districts && $districts->num_rows > 0) {
                        while ($row = $districts->fetch_assoc()) {
                            $selected = ($district_id == $row['district_id']) ? 'selected' : '';
                            echo "<option value='" . $row['district_id'] . "' $selected>" . htmlspecialchars($row['district_name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="job_type" class="form-control">
                    <option value="">All Job Types</option>
                    <?php
                    $types = ['Full-time', 'Part-time', 'Internship', 'Contract', 'Remote'];
                    foreach ($types as $type) {
                        $selected = ($job_type == $type) ? 'selected' : '';
                        echo "<option value='$type' $selected>$type</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <?php if ($jobs_result && $jobs_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($job = $jobs_result->fetch_assoc()): ?>
                    <?php
                    $job_id = $job['job_id'];

                    $check_applied = $conn->query("SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'");
                    $already_applied = ($check_applied && $check_applied->num_rows > 0);
                    $check_saved = $conn->query("
    SELECT * 
    FROM saved_jobs 
    WHERE user_id='$user_id'
    AND job_id='$job_id'
");

$already_saved = ($check_saved && $check_saved->num_rows > 0);
                    

                    $deadline = $job['application_deadline'] ?? '';
                    $deadline_over = (!empty($deadline) && $deadline < date('Y-m-d'));
                    ?>

                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h4 class="mb-0"><?php echo htmlspecialchars($job['title']); ?></h4>

                                    <?php if ($deadline_over): ?>
                                        <span class="badge bg-danger">Deadline Over</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-muted mb-2">
                                    <?php echo htmlspecialchars($job['company_name'] ?? 'Unknown Employer'); ?>
                                </p>

                                <div class="mb-3">
                                    <span class="badge bg-primary me-1">
                                        <?php echo htmlspecialchars($job['job_category'] ?? 'General'); ?>
                                    </span>
                                    <span class="badge bg-info text-dark me-1">
                                        <?php echo htmlspecialchars($job['job_type'] ?? 'N/A'); ?>
                                    </span>
                                    <span class="badge bg-secondary">
                                        Vacancy: <?php echo htmlspecialchars($job['vacancy'] ?? 'N/A'); ?>
                                    </span>
                                </div>

                                <p class="mb-2">
                                    <strong>Description:</strong><br>
                                    <?php echo nl2br(htmlspecialchars(substr($job['description'] ?? '', 0, 180))); ?>...
                                </p>

                                <p class="mb-2">
                                    <strong>Area:</strong>
                                    <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars($job['upazila_name'] ?? 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars($job['ward_name'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Location:</strong>
                                    <?php echo htmlspecialchars($job['location'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Education:</strong>
                                    <?php echo htmlspecialchars($job['education_required'] ?? 'Not specified'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Experience:</strong>
                                    <?php echo htmlspecialchars($job['experience_required'] ?? 'Not specified'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Salary:</strong>
                                    <?php echo htmlspecialchars($job['salary_type'] ?? 'Negotiable'); ?>
                                    -
                                    <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'Negotiable'; ?>
                                </p>

                                <p class="mb-3">
                                    <strong>Deadline:</strong>
                                    <?php echo !empty($deadline) ? htmlspecialchars($deadline) : 'Not specified'; ?>
                                </p>

                             <div class="d-flex flex-wrap gap-2">

    <?php if ($already_applied): ?>

        <button class="btn btn-outline-secondary" disabled>
            Already Applied
        </button>

    <?php elseif ($deadline_over): ?>

        <button class="btn btn-outline-danger" disabled>
            Deadline Over
        </button>

    <?php else: ?>

        <a href="jobs.php?apply=<?php echo $job['job_id']; ?>&search=<?php echo urlencode($search); ?>&district_id=<?php echo urlencode($district_id); ?>&job_type=<?php echo urlencode($job_type); ?>"
           class="btn btn-success"
           onclick="return confirm('Apply for this job?')">

            Apply Now

        </a>

    <?php endif; ?>

    <?php if ($already_saved): ?>

        <a href="jobs.php?unsave=<?php echo $job['job_id']; ?>"
           class="btn btn-outline-danger">

            Unsave

        </a>

    <?php else: ?>

        <a href="jobs.php?save=<?php echo $job['job_id']; ?>"
           class="btn btn-outline-primary">

            Save Job

        </a>

    <?php endif; ?>

</div>

                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center mb-0">
                <h5 class="mb-2">No Active Jobs Found</h5>
                <p class="mb-0">Try another title, district, or job type.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>