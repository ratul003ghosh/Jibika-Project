<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

// DELETE JOB
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM jobs WHERE job_id = '$id'");
    header("Location: jobs.php");
    exit();
}

// FETCH JOBS WITH EMPLOYER INFO
$sql = "SELECT jobs.*, users.full_name AS employer_name, users.email AS employer_email
        FROM jobs
        LEFT JOIN users ON jobs.employer_id = users.user_id
        ORDER BY jobs.job_id DESC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Jobs</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow p-4">
        <?php if($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Job Title</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Employer</th>
                            <th>Employer Email</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['salary']); ?></td>
                                <td><?php echo htmlspecialchars($row['employer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['employer_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['job_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this job?')">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No jobs found</div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>