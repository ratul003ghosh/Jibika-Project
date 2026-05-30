<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$profile_check = $conn->query("SELECT employer_profile_id FROM employer_profiles WHERE user_id='$employer_id' LIMIT 1");
$has_profile = ($profile_check && $profile_check->num_rows > 0);

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

<style>
    body { background-color: #f4f7f6; }
    .dash-hero {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        border-radius: 16px;
        color: white;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 106, 78, 0.15);
        position: relative;
        overflow: hidden;
    }
    .dash-hero::after {
        content: '\f2b5'; /* fa-handshake */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        right: -20px;
        bottom: -40px;
        font-size: 15rem;
        color: rgba(255,255,255,0.05);
        transform: rotate(-15deg);
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        border: none;
        padding: 25px;
        transition: transform 0.2s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    .action-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #edf2f7;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #2c3e50;
        transition: all 0.2s;
        display: block;
    }
    .action-card:hover {
        background: #006a4e;
        color: white !important;
        border-color: #006a4e;
    }
    .action-card:hover .action-icon { color: white !important; }
    .custom-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #edf2f7;
        padding: 15px;
    }
    .custom-table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
    }
</style>

<div class="container py-4">

    <!-- Hero Section -->
    <div class="dash-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="z-index: 1;">
            <h2 class="fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($full_name); ?>!</h2>
            <p class="mb-0 fs-5 opacity-75">Here is what's happening with your job listings today.</p>
        </div>
        <div style="z-index: 1;">
            <?php if ($has_profile): ?>
            <a href="post_job.php" class="btn btn-light text-success fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> Post a New Job
            </a>
            <?php else: ?>
            <a href="profile.php" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Complete Profile to Post Jobs
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$has_profile): ?>
    <div class="alert shadow-sm rounded-4 mb-4 border-0 border-start border-danger border-4" style="background-color: #fff5f5; color: #c53030;">
        <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Action Required: Profile Incomplete</h5>
        <p class="mb-0">You cannot post jobs or review applicants until your official company profile is completed and verified. <a href="profile.php" class="fw-bold text-danger text-decoration-underline ms-2">Set up Company Profile now <i class="fa-solid fa-arrow-right"></i></a></p>
    </div>
    <?php endif; ?>

    <!-- KPI Stats -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Active Jobs</p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo $total_jobs; ?></h2>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Total Applications</p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo $total_applicants; ?></h2>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Pending Review</p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo $pending_applicants; ?></h2>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Hired Candidates</p>
                        <h2 class="fw-bold text-dark mb-0"><?php echo $accepted_applicants; ?></h2>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <h5 class="fw-bold text-dark mb-3">Quick Actions</h5>
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="post_job.php" class="action-card shadow-sm">
                <i class="fa-solid fa-file-circle-plus action-icon text-success fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0">Create Job</h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="manage_jobs.php" class="action-card shadow-sm">
                <i class="fa-solid fa-list-check action-icon text-primary fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0">Manage Listings</h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="applicants.php" class="action-card shadow-sm">
                <i class="fa-solid fa-user-tie action-icon text-warning fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0">Review Candidates</h6>
            </a>
        </div>
        <div class="col-md-3">
            <a href="profile.php" class="action-card shadow-sm">
                <i class="fa-solid fa-building action-icon text-secondary fs-3 mb-2"></i>
                <h6 class="fw-bold mb-0">Company Profile</h6>
            </a>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent Jobs -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Recently Posted Jobs</h5>
                    <a href="manage_jobs.php" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-3">View All</a>
                </div>
                <div class="card-body p-4">
                    <?php if ($recent_jobs && $recent_jobs->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table custom-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Location</th>
                                        <th>Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                                        <tr>
                                            <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($job['title']); ?></span></td>
                                            <td class="text-secondary"><i class="fa-solid fa-location-dot me-1 text-muted small"></i> <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-light text-success border px-2 py-1">
                                                    <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'Negotiable'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-folder-open text-muted fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Jobs Posted Yet</h6>
                            <p class="text-muted small">Create your first job listing to attract talent.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Applicants -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Recent Applicants</h5>
                    <a href="applicants.php" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-3">View All</a>
                </div>
                <div class="card-body p-4">
                    <?php if ($recent_applicants && $recent_applicants->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table custom-table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Applied For</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = $recent_applicants->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($app['email']); ?></div>
                                            </td>
                                            <td class="text-secondary"><?php echo htmlspecialchars($app['job_title']); ?></td>
                                            <td>
                                                <?php
                                                if ($app['status'] == 'Accepted') {
                                                    echo "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1'>Accepted</span>";
                                                } elseif ($app['status'] == 'Rejected') {
                                                    echo "<span class='badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1'>Rejected</span>";
                                                } else {
                                                    echo "<span class='badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 px-2 py-1'>Pending</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-user-xmark text-muted fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Applications Yet</h6>
                            <p class="text-muted small">Wait for candidates to discover your postings.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>