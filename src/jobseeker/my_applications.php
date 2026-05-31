<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            applications.status,
            applications.applied_at,
            jobs.title,
            jobs.location,
            jobs.salary,
            jobs.salary_type,
            jobs.job_type,
            jobs.job_category,
            jobs.application_deadline,
            jobs.status AS job_status,
            users.full_name AS company_name
        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
        LEFT JOIN users ON jobs.employer_id = users.user_id
        WHERE applications.user_id = '$user_id'
        ORDER BY applications.application_id DESC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">My Applications</h2>
                <p class="text-muted mb-0">Track your applied jobs and application status.</p>
            </div>

            <a href="jobs.php" class="btn btn-warning">Browse More Jobs</a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Job Details</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Salary</th>
                            <th>Deadline</th>
                            <th>Application Status</th>
                            <th>Applied At</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $count = 1; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $deadline = $row['application_deadline'] ?? '';
                            $deadline_over = (!empty($deadline) && $deadline < date('Y-m-d'));
                            ?>

                            <tr>
                                <td><?php echo $count++; ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($row['job_category'] ?? 'General'); ?>
                                    </small>
                                    <br>
                                    <small>
                                        <strong>Location:</strong>
                                        <?php echo htmlspecialchars($row['location'] ?? 'N/A'); ?>
                                    </small>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['company_name'] ?? 'Unknown Employer'); ?>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($row['job_type'] ?? 'N/A'); ?>
                                    </span>

                                    <?php if (($row['job_status'] ?? 'active') == 'closed'): ?>
                                        <br>
                                        <span class="badge bg-secondary mt-1">Job Closed</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($row['salary_type'] ?? 'Negotiable'); ?>
                                    </span>
                                    <br>
                                    <?php echo !empty($row['salary']) ? htmlspecialchars($row['salary']) : 'Negotiable'; ?>
                                </td>

                                <td>
                                    <?php if (!empty($deadline)): ?>
                                        <?php echo htmlspecialchars($deadline); ?>

                                        <?php if ($deadline_over): ?>
                                            <br>
                                            <span class="badge bg-danger mt-1">Expired</span>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $row['status'];

                                    if ($status == 'Accepted') {
                                        echo "<span class='badge bg-success'>Accepted</span>";
                                    } elseif ($status == 'Rejected') {
                                        echo "<span class='badge bg-danger'>Rejected</span>";
                                    } else {
                                        echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['applied_at']); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="alert alert-warning text-center mb-0">
                <h5 class="mb-2">No Applications Yet</h5>
                <p class="mb-3">You have not applied to any jobs yet.</p>
                <a href="jobs.php" class="btn btn-success">Apply for Jobs</a>
            </div>

        <?php endif; ?>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

    </div>
</div>

<?php include('../includes/footer.php'); ?>