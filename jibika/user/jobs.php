<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Apply job
if(isset($_GET['apply'])){
    $job_id = intval($_GET['apply']);

    $check_sql = "SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'";
    $check_result = $conn->query($check_sql);

    if($check_result->num_rows > 0){
        $message = "You already applied for this job.";
        $message_type = "warning";
    } else {
        $apply_sql = "INSERT INTO applications (job_id, user_id) VALUES ('$job_id', '$user_id')";
        if($conn->query($apply_sql)){
            $message = "Job applied successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "danger";
        }
    }
}

// Dropdown data
$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");

// Search & filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;

$sql = "SELECT 
            jobs.*,
            d.district_name,
            u.upazila_name,
            w.ward_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN wards w ON jobs.ward_id = w.ward_id
        WHERE 1";

if($search != ""){
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND jobs.title LIKE '%$search_safe%'";
}

if($district_id > 0){
    $sql .= " AND jobs.district_id = '$district_id'";
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
                <p class="text-muted mb-0">Browse area-based job opportunities and apply easily.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if($message != ""): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by job title"
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </div>

            <div class="col-md-4">
                <select name="district_id" class="form-control">
                    <option value="">All Districts</option>
                    <?php
                    if($districts && $districts->num_rows > 0){
                        while($row = $districts->fetch_assoc()){
                            $selected = ($district_id == $row['district_id']) ? 'selected' : '';
                            echo "<option value='".$row['district_id']."' $selected>".htmlspecialchars($row['district_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <?php if($jobs_result && $jobs_result->num_rows > 0): ?>
            <div class="row">
                <?php while($job = $jobs_result->fetch_assoc()): ?>
                    <?php
                        $job_id = $job['job_id'];
                        $check_applied = $conn->query("SELECT * FROM applications WHERE job_id='$job_id' AND user_id='$user_id'");
                        $already_applied = ($check_applied->num_rows > 0);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo htmlspecialchars($job['title']); ?></h4>

                                <p class="mb-2">
                                    <strong>Description:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>District:</strong>
                                    <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Upazila:</strong>
                                    <?php echo htmlspecialchars($job['upazila_name'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Ward:</strong>
                                    <?php echo htmlspecialchars($job['ward_name'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Location Details:</strong>
                                    <?php echo htmlspecialchars($job['location'] ?? 'N/A'); ?>
                                </p>

                                <p class="mb-3">
                                    <strong>Salary:</strong>
                                    <?php echo htmlspecialchars($job['salary']); ?>
                                </p>

                                <?php if($already_applied): ?>
                                    <button class="btn btn-outline-secondary" disabled>Already Applied</button>
                                <?php else: ?>
                                    <a href="jobs.php?apply=<?php echo $job['job_id']; ?>&search=<?php echo urlencode($search); ?>&district_id=<?php echo urlencode($district_id); ?>"
                                       class="btn btn-success"
                                       onclick="return confirm('Apply for this job?')">
                                       Apply Now
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center mb-0">
                <h5 class="mb-2">No Jobs Found</h5>
                <p class="mb-0">Try another title or district.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>