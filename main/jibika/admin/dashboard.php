<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

function getCount($conn, $sql) {
    $q = $conn->query($sql);
    return ($q) ? ($q->fetch_assoc()['total'] ?? 0) : 0;
}

$total_users = getCount($conn, "SELECT COUNT(*) AS total FROM users");
$total_job_seekers = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='job_seeker'");
$total_employers = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='employer'");
$total_jobs = getCount($conn, "SELECT COUNT(*) AS total FROM jobs");
$total_applications = getCount($conn, "SELECT COUNT(*) AS total FROM applications");
$total_skills = getCount($conn, "SELECT COUNT(*) AS total FROM skills");

$total_unemployed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='unemployed'");
$total_employed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='employed'");
$total_training = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='training'");
$total_self_employed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='self_employed'");

$recent_changes = $conn->query("
    SELECT 
        esl.old_status,
        esl.new_status,
        esl.remarks,
        esl.created_at,
        u.full_name,
        u.email
    FROM employment_status_logs esl
    JOIN users u ON esl.user_id = u.user_id
    ORDER BY esl.log_id DESC
    LIMIT 6
");

$top_districts = $conn->query("
    SELECT 
        d.district_name,
        COUNT(CASE WHEN es.current_status='unemployed' THEN 1 END) AS unemployed_count
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    GROUP BY d.district_id, d.district_name
    ORDER BY unemployed_count DESC
    LIMIT 5
");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1 fw-bold">Admin Monitoring Dashboard</h2>
            <p class="text-muted mb-0">
                Monitor area-based unemployment, job activity, and employment changes.
            </p>
        </div>

        <div>
            <a href="reports.php" class="btn btn-warning me-2">Analytics Reports</a>
            <a href="unemployed_details.php" class="btn btn-danger">Workforce Monitor</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Total Users</h6>
                <h2><?php echo $total_users; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Job Seekers</h6>
                <h2><?php echo $total_job_seekers; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Employers</h6>
                <h2><?php echo $total_employers; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h6 class="text-muted">Total Jobs</h6>
                <h2><?php echo $total_jobs; ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100 border-start border-danger border-4">
                <h6 class="text-muted">Unemployed</h6>
                <h2 class="text-danger"><?php echo $total_unemployed; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100 border-start border-success border-4">
                <h6 class="text-muted">Employed</h6>
                <h2 class="text-success"><?php echo $total_employed; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100 border-start border-warning border-4">
                <h6 class="text-muted">Training</h6>
                <h2 class="text-warning"><?php echo $total_training; ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-4 h-100 border-start border-info border-4">
                <h6 class="text-muted">Self Employed</h6>
                <h2 class="text-info"><?php echo $total_self_employed; ?></h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4 mb-4">
        <h5 class="mb-3">Quick Actions</h5>

        <a href="users.php" class="btn btn-primary me-2 mb-2">Manage Users</a>
        <a href="jobs.php" class="btn btn-success me-2 mb-2">Manage Jobs</a>
        <a href="reports.php" class="btn btn-warning me-2 mb-2">Analytics Reports</a>
        <a href="unemployed_details.php" class="btn btn-danger me-2 mb-2">Workforce Monitor</a>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="mb-3">Recent Employment Status Changes</h5>

                <?php if ($recent_changes && $recent_changes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Status Change</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = $recent_changes->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['old_status'] ?? 'N/A'); ?>
                                            →
                                            <strong><?php echo htmlspecialchars($row['new_status']); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></small>
                                        </td>

                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        No employment status changes yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="mb-3">Top Unemployment Areas</h5>

                <?php if ($top_districts && $top_districts->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>District</th>
                                    <th>Unemployed</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = $top_districts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?php echo htmlspecialchars($row['unemployed_count']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        No area data found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>