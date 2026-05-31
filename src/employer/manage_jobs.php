<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$message = "";

// Delete job
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);

    $delete_sql = "DELETE FROM jobs WHERE job_id='$job_id' AND employer_id='$employer_id'";

    if ($conn->query($delete_sql)) {
        $message = "Job deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Toggle job status
if (isset($_GET['status']) && isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $status = ($_GET['status'] == 'closed') ? 'closed' : 'active';

    $update_sql = "UPDATE jobs 
                   SET status='$status' 
                   WHERE job_id='$job_id' AND employer_id='$employer_id'";

    if ($conn->query($update_sql)) {
        $message = "Job status updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch employer jobs
$sql = "SELECT 
            jobs.*,
            d.district_name,
            u.upazila_name,
            w.ward_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN wards w ON jobs.ward_id = w.ward_id
        WHERE jobs.employer_id='$employer_id'
        ORDER BY jobs.job_id DESC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Manage Jobs</h2>
                <p class="text-muted mb-0">View, close, reopen, or delete your posted jobs.</p>
            </div>

            <div>
                <a href="post_job.php" class="btn btn-success me-2">Post New Job</a>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Job Info</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Area</th>
                            <th>Salary</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $count = 1; ?>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($job['description'] ?? '', 0, 80)); ?>...
                                    </small>
                                    <br>
                                    <small>
                                        <strong>Vacancy:</strong>
                                        <?php echo htmlspecialchars($job['vacancy'] ?? 'N/A'); ?>
                                    </small>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($job['job_category'] ?? 'N/A'); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($job['job_type'] ?? 'N/A'); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars($job['upazila_name'] ?? 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars($job['ward_name'] ?? 'N/A'); ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($job['location'] ?? ''); ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($job['salary_type'] ?? 'Negotiable'); ?>
                                    </span>
                                    <br>
                                    <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'Negotiable'; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($job['application_deadline'] ?? 'N/A'); ?>
                                </td>

                                <td>
                                    <?php if (($job['status'] ?? 'active') == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Closed</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="applicants.php" class="btn btn-primary btn-sm mb-1">
                                        Applicants
                                    </a>

                                    <?php if (($job['status'] ?? 'active') == 'active'): ?>
                                        <a href="manage_jobs.php?status=closed&job_id=<?php echo $job['job_id']; ?>"
                                           class="btn btn-warning btn-sm mb-1"
                                           onclick="return confirm('Close this job?')">
                                            Close
                                        </a>
                                    <?php else: ?>
                                        <a href="manage_jobs.php?status=active&job_id=<?php echo $job['job_id']; ?>"
                                           class="btn btn-success btn-sm mb-1"
                                           onclick="return confirm('Reopen this job?')">
                                            Reopen
                                        </a>
                                    <?php endif; ?>

                                    <a href="manage_jobs.php?delete=<?php echo $job['job_id']; ?>"
                                       class="btn btn-danger btn-sm mb-1"
                                       onclick="return confirm('Are you sure you want to delete this job?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        <?php else: ?>

            <div class="alert alert-warning text-center mb-0">
                <h5 class="mb-2">No Jobs Posted Yet</h5>
                <p class="mb-3">You have not posted any job yet.</p>
                <a href="post_job.php" class="btn btn-success">Post Your First Job</a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>