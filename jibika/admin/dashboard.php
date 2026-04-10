<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_job_seekers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='job_seeker'")->fetch_assoc()['total'];
$total_employers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='employer'")->fetch_assoc()['total'];
$total_jobs = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$total_applications = $conn->query("SELECT COUNT(*) AS total FROM applications")->fetch_assoc()['total'];
$total_skills = $conn->query("SELECT COUNT(*) AS total FROM skills")->fetch_assoc()['total'];
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Admin Dashboard</h2>
            <p class="text-muted mb-0">Monitor the complete Jibika system from here.</p>
        </div>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Total Users</h5>
                <h2><?php echo $total_users; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Job Seekers</h5>
                <h2><?php echo $total_job_seekers; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Employers</h5>
                <h2><?php echo $total_employers; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Total Jobs</h5>
                <h2><?php echo $total_jobs; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Total Applications</h5>
                <h2><?php echo $total_applications; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5>Total Skills Added</h5>
                <h2><?php echo $total_skills; ?></h2>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="users.php" class="btn btn-primary me-2">Manage Users</a>
        <a href="jobs.php" class="btn btn-success me-2">Manage Jobs</a>
        <a href="reports.php" class="btn btn-warning">View Reports</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>