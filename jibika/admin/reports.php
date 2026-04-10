<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

// Summary counts
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_job_seekers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='job_seeker'")->fetch_assoc()['total'];
$total_employers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='employer'")->fetch_assoc()['total'];
$total_jobs = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$total_applications = $conn->query("SELECT COUNT(*) AS total FROM applications")->fetch_assoc()['total'];
$total_skills = $conn->query("SELECT COUNT(*) AS total FROM skills")->fetch_assoc()['total'];
$total_profiles = $conn->query("SELECT COUNT(*) AS total FROM job_seeker_profiles")->fetch_assoc()['total'];
$total_unemployed = $conn->query("SELECT COUNT(*) AS total FROM employment_status WHERE current_status='unemployed'")->fetch_assoc()['total'];

// Recent users
$recent_users = $conn->query("SELECT full_name, email, role, created_at FROM users ORDER BY user_id DESC LIMIT 5");

// Recent jobs
$recent_jobs = $conn->query("
    SELECT jobs.title, jobs.location, jobs.salary, jobs.created_at, districts.district_name
    FROM jobs
    LEFT JOIN districts ON jobs.district_id = districts.district_id
    ORDER BY jobs.job_id DESC
    LIMIT 5
");

// District-wise job seekers
$district_wise_users = $conn->query("
    SELECT 
        d.district_name,
        COUNT(jsp.profile_id) AS total_job_seekers
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    GROUP BY d.district_id, d.district_name
    ORDER BY total_job_seekers DESC, d.district_name ASC
");

// District-wise jobs
$district_wise_jobs = $conn->query("
    SELECT 
        d.district_name,
        COUNT(j.job_id) AS total_jobs
    FROM districts d
    LEFT JOIN jobs j ON d.district_id = j.district_id
    GROUP BY d.district_id, d.district_name
    ORDER BY total_jobs DESC, d.district_name ASC
");

// District-wise unemployed
$district_wise_unemployed = $conn->query("
    SELECT 
        d.district_name,
        COUNT(es.status_id) AS total_unemployed
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    LEFT JOIN employment_status es 
        ON jsp.user_id = es.user_id 
        AND es.current_status = 'unemployed'
    GROUP BY d.district_id, d.district_name
    ORDER BY total_unemployed DESC, d.district_name ASC
");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">System Reports</h2>
            <p class="text-muted mb-0">Overview and area-based unemployment analytics of the Jibika platform.</p>
        </div>
        <a href="unemployed_details.php" class="btn btn-danger">View Unemployed Details</a>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Total Users</h6>
                <h3><?php echo $total_users; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Job Seekers</h6>
                <h3><?php echo $total_job_seekers; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Employers</h6>
                <h3><?php echo $total_employers; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Total Jobs</h6>
                <h3><?php echo $total_jobs; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Applications</h6>
                <h3><?php echo $total_applications; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Skills Added</h6>
                <h3><?php echo $total_skills; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Profiles Created</h6>
                <h3><?php echo $total_profiles; ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Total Unemployed</h6>
                <h3><?php echo $total_unemployed; ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">Recent Users</h4>
                <?php if($recent_users->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No recent users found.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">Recent Jobs</h4>
                <?php if($recent_jobs->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>District</th>
                                    <th>Salary</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_jobs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['district_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['salary']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No recent jobs found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">District-wise Job Seekers</h4>
                <?php if($district_wise_users->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th>Total Job Seekers</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $district_wise_users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total_job_seekers']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No district-wise job seeker data found.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">District-wise Jobs Posted</h4>
                <?php if($district_wise_jobs->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th>Total Jobs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $district_wise_jobs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total_jobs']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No district-wise job data found.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">District-wise Unemployed</h4>
                <?php if($district_wise_unemployed->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th>Total Unemployed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $district_wise_unemployed->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total_unemployed']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No district-wise unemployment data found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>