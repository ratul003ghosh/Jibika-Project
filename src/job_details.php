<?php
session_start();
include('assets/config/db.php');
$lang = $_SESSION['lang'] ?? 'bn';

if (!isset($_GET['id'])) {
    header("Location: jobseeker/jobs.php");
    exit();
}

$job_id = intval($_GET['id']);
$job_query = $conn->query("SELECT j.*, ep.company_name, d.district_name 
                           FROM jobs j 
                           LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id 
                           LEFT JOIN districts d ON j.district_id = d.district_id 
                           WHERE j.job_id = $job_id");

if ($job_query->num_rows == 0) {
    die("Job not found.");
}
$job = $job_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<?php include('includes/navbar.php'); ?>

<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="fw-bold text-dark"><?php echo htmlspecialchars($job['title']); ?></h2>
        <h5 class="text-muted"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($job['company_name'] ?? 'Unknown Company'); ?></h5>
        <hr>
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong><i class="fa-solid fa-briefcase"></i> Category:</strong> <?php echo htmlspecialchars($job['job_category']); ?><br>
                <strong><i class="fa-solid fa-clock"></i> Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?><br>
                <strong><i class="fa-solid fa-wallet"></i> Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?>
            </div>
            <div class="col-md-6 mb-3">
                <strong><i class="fa-solid fa-location-dot"></i> Location:</strong> <?php echo htmlspecialchars($job['district_name'] ?? 'Multiple'); ?><br>
                <strong><i class="fa-solid fa-layer-group"></i> Experience Required:</strong> <?php echo htmlspecialchars($job['experience_required']); ?><br>
                <strong><i class="fa-regular fa-calendar-xmark"></i> Deadline:</strong> <?php echo htmlspecialchars($job['application_deadline']); ?>
            </div>
        </div>
        <hr>
        <h5 class="fw-bold mt-3">Job Description</h5>
        <p class="mt-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($job['description']); ?></p>
        
        <div class="mt-4">
            <a href="jobseeker/jobs.php?apply=<?php echo $job_id; ?>" class="btn btn-success btn-lg px-5">Apply Now</a>
            <a href="jobseeker/jobs.php" class="btn btn-outline-secondary btn-lg ms-2">Back to Jobs</a>
        </div>
    </div>
</div>
</body>
</html>
