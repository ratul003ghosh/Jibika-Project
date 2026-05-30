<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$profile_check = $conn->query("SELECT employer_profile_id FROM employer_profiles WHERE user_id='$employer_id' LIMIT 1");

if (!$profile_check || $profile_check->num_rows == 0) {
    header("Location: profile.php");
    exit();
}

$full_name = $_SESSION['full_name'] ?? 'Employer';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? '';

$total_jobs = 0;
$total_applicants = 0;
$pending_applicants = 0;
$accepted_applicants = 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE employer_id='$employer_id'");
if ($q) $total_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
");
if ($q) $total_applicants = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
    AND applications.status='Pending'
");
if ($q) $pending_applicants = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("
    SELECT COUNT(*) AS total 
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    WHERE jobs.employer_id='$employer_id'
    AND applications.status='Accepted'
");
if ($q) $accepted_applicants = $q->fetch_assoc()['total'] ?? 0;

$recent_jobs = $conn->query("
    SELECT jobs.*, 
           d.district_name, 
           u.upazila_name, 
           w.ward_name
    FROM jobs
    LEFT JOIN districts d ON jobs.district_id = d.district_id
    LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
    LEFT JOIN wards w ON jobs.ward_id = w.ward_id
    WHERE jobs.employer_id='$employer_id'
    ORDER BY jobs.job_id DESC
    LIMIT 5
");

$recent_applicants = $conn->query("
    SELECT 
        applications.application_id,
        applications.status,
        applications.applied_at,
        jobs.title AS job_title,
        users.full_name,
        users.email
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    JOIN users ON applications.user_id = users.user_id
    WHERE jobs.employer_id='$employer_id'
    ORDER BY applications.application_id DESC
    LIMIT 5
");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Employer Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome, <?php echo htmlspecialchars($full_name); ?>. Manage your jobs and applicants from here.
            </p>
        </div>

        <div>
            <a href="post_job.php" class="btn btn-success me-2">Post a Job</a>
           
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Posted Jobs</h6>
                <h2><?php echo $total_jobs; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Total Applicants</h6>
                <h2><?php echo $total_applicants; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Pending Applicants</h6>
                <h2><?php echo $pending_applicants; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Accepted Applicants</h6>
                <h2><?php echo $accepted_applicants; ?></h2>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0 p-4 mb-4">
        <h5 class="mb-3">Quick Actions</h5>

        <a href="post_job.php" class="btn btn-success me-2 mb-2">Post Job</a>
        <a href="manage_jobs.php" class="btn btn-warning me-2 mb-2">Manage Jobs</a>
        <a href="applicants.php" class="btn btn-primary me-2 mb-2">View Applicants</a>
        <a href="profile.php" class="btn btn-dark me-2 mb-2">Company Profile</a>
    </div>

    <div class="row g-4">

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent Posted Jobs</h5>
                    <a href="manage_jobs.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>

                <?php if ($recent_jobs && $recent_jobs->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Area</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'Negotiable'; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        No jobs posted yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent Applicants</h5>
                    <a href="applicants.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>

                <?php if ($recent_applicants && $recent_applicants->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Applicant</th>
                                    <th>Job</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($app = $recent_applicants->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($app['full_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                        <td>
                                            <?php
                                            if ($app['status'] == 'Accepted') {
                                                echo "<span class='badge bg-success'>Accepted</span>";
                                            } elseif ($app['status'] == 'Rejected') {
                                                echo "<span class='badge bg-danger'>Rejected</span>";
                                            } else {
                                                echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        No applicants yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>