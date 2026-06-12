<?php
session_start();
include('assets/config/db.php');
$lang = $_SESSION['lang'] ?? 'bn';
$message = "";
$message_type = "";

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

if (isset($_GET['applied']) && $_GET['applied'] === '1') {
    $message = "Applied successfully!";
    $message_type = "success";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'job_seeker') {
        header("Location: auth/login.php");
        exit();
    }

    $user_id = (int)$_SESSION['user_id'];
    $is_closed = (($job['status'] ?? 'active') === 'closed');
    $is_deadline_over = (!empty($job['application_deadline']) && $job['application_deadline'] < date('Y-m-d'));

    if ($is_closed) {
        $message = "This job is closed.";
        $message_type = "warning";
    } elseif ($is_deadline_over) {
        $message = "Application deadline is over.";
        $message_type = "warning";
    } else {
        $check = $conn->prepare("SELECT application_id FROM applications WHERE job_id=? AND user_id=? LIMIT 1");
        if ($check) {
            $check->bind_param("ii", $job_id, $user_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $message = "You already applied for this job.";
                $message_type = "warning";
            } else {
                $apply = $conn->prepare("INSERT INTO applications (job_id, user_id) VALUES (?, ?)");
                if ($apply) {
                    $apply->bind_param("ii", $job_id, $user_id);
                    if ($apply->execute()) {
                        header("Location: job_details.php?id=$job_id&applied=1");
                        exit();
                    }
                    $message = "Error applying for this job: " . $conn->error;
                    $message_type = "danger";
                } else {
                    $message = "Error preparing application.";
                    $message_type = "danger";
                }
            }
        } else {
            $message = "Error checking existing application.";
            $message_type = "danger";
        }
    }
}
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
        <?php if ($message !== ""): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
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
        
        <div class="mt-4 d-flex flex-wrap gap-2">
            <form method="POST" action="job_details.php?id=<?php echo $job_id; ?>" style="display:inline;">
                <button type="submit" name="apply_job" class="btn btn-success btn-lg px-5">Apply Now</button>
            </form>
            <a href="jobseeker/jobs.php" class="btn btn-outline-secondary btn-lg ms-2">Back to Jobs</a>
        </div>
    </div>
</div>
</body>
</html>
