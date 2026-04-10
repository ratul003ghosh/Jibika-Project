<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT jobs.title, jobs.location, jobs.salary, applications.status, applications.applied_at
        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
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
                <p class="text-muted mb-0">Track all the jobs you have applied for.</p>
            </div>
            <a href="jobs.php" class="btn btn-warning">Browse More Jobs</a>
        </div>

        <?php if($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Job Title</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['salary']); ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];

                                        if($status == 'Accepted'){
                                            echo "<span class='badge bg-success'>Accepted</span>";
                                        } elseif($status == 'Rejected'){
                                            echo "<span class='badge bg-danger'>Rejected</span>";
                                        } else {
                                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['applied_at']); ?></td>
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