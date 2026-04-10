<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];

// Fetch current employment status
$status_result = $conn->query("SELECT current_status, remarks, updated_at FROM employment_status WHERE user_id='$user_id' LIMIT 1");
$status_data = $status_result ? $status_result->fetch_assoc() : null;

$current_status = $status_data['current_status'] ?? 'unemployed';
$status_remarks = $status_data['remarks'] ?? '';
$status_updated = $status_data['updated_at'] ?? '';
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2>Job Seeker Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

        <div class="mt-4 p-3 bg-light rounded">
            <h5 class="mb-3">Current Employment Status</h5>
            <p class="mb-2">
                <?php
                    if($current_status == 'employed'){
                        echo "<span class='badge bg-success'>Employed</span>";
                    } elseif($current_status == 'training'){
                        echo "<span class='badge bg-warning text-dark'>Training</span>";
                    } elseif($current_status == 'self_employed'){
                        echo "<span class='badge bg-info text-dark'>Self Employed</span>";
                    } else {
                        echo "<span class='badge bg-danger'>Unemployed</span>";
                    }
                ?>
            </p>

            <?php if($status_remarks != ""): ?>
                <p class="mb-1"><strong>Remarks:</strong> <?php echo htmlspecialchars($status_remarks); ?></p>
            <?php endif; ?>

            <?php if($status_updated != ""): ?>
                <p class="mb-0"><strong>Last Updated:</strong> <?php echo htmlspecialchars($status_updated); ?></p>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <a href="profile.php" class="btn btn-success me-2">My Profile</a>
            <a href="skills.php" class="btn btn-primary me-2">My Skills</a>
            <a href="jobs.php" class="btn btn-warning me-2">Browse Jobs</a>
            <a href="my_applications.php" class="btn btn-info me-2">My Applications</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>