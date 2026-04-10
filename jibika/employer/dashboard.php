<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer'){
    header("Location: ../login.php");
    exit();
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2>Employer Dashboard</h2>
        <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
        <p>Role: <?php echo $_SESSION['role']; ?></p>

        <div class="mt-3">
            <a href="post_job.php" class="btn btn-success me-2">Post a Job</a>
            <a href="applicants.php" class="btn btn-primary me-2">View Applicants</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>