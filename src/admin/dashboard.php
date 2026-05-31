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

<div class="container-fluid py-5 px-xl-5">

    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="mb-2 fw-bold" style="color: #0a4f32;"><i class="fa-solid fa-chart-line me-2"></i>Admin Monitoring Dashboard</h2>
            <p class="text-muted mb-0 fs-5">Monitor area-based unemployment, job activity, and employment changes.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="reports.php" class="btn btn-warning px-4 py-2 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-chart-pie me-2"></i>Analytics Reports</a>
            <a href="unemployed_details.php" class="btn btn-danger px-4 py-2 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-tower-observation me-2"></i>Workforce Monitor</a>
        </div>
    </div>

    <!-- Platform Stats -->
    <h5 class="fw-bold mb-3 text-muted text-uppercase" style="letter-spacing:1px; font-size:0.9rem;">Platform Overview</h5>
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #0d6efd !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Total Users</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_users; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(13,110,253,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#0d6efd; font-size:2rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #198754 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Job Seekers</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_job_seekers; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(25,135,84,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#198754; font-size:2rem;">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #6f42c1 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Employers</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_employers; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(111,66,193,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#6f42c1; font-size:2rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #fd7e14 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Total Jobs</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_jobs; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(253,126,20,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fd7e14; font-size:2rem;">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employment Status -->
    <h5 class="fw-bold mb-3 text-muted text-uppercase" style="letter-spacing:1px; font-size:0.9rem;">Workforce Status</h5>
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #dc3545 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Unemployed</h6>
                        <h2 class="mb-0 fw-bold text-danger"><?php echo $total_unemployed; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(220,53,69,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#dc3545; font-size:2rem;">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #20c997 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Employed</h6>
                        <h2 class="mb-0 fw-bold text-success"><?php echo $total_employed; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(32,201,151,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#20c997; font-size:2rem;">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #ffc107 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Training</h6>
                        <h2 class="mb-0 fw-bold text-warning"><?php echo $total_training; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(255,193,7,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#ffc107; font-size:2rem;">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; border-left: 6px solid #0dcaf0 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size:0.85rem; letter-spacing:1px;">Self Employed</h6>
                        <h2 class="mb-0 fw-bold text-info"><?php echo $total_self_employed; ?></h2>
                    </div>
                    <div class="icon-box" style="width:65px; height:65px; background:rgba(13,202,240,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#0dcaf0; font-size:2rem;">
                        <i class="fa-solid fa-shop"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-muted text-uppercase" style="letter-spacing:1px; font-size:0.9rem;">Quick Navigation</h5>
    <div class="row g-3 mb-5">
        <div class="col-xl-3 col-md-6">
            <a href="users.php" class="btn btn-light shadow-sm w-100 py-3 text-start fw-bold border-0" style="border-radius:15px; color:#0a4f32;"><i class="fa-solid fa-users-gear fs-4 me-3 align-middle text-primary"></i> Manage Users</a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="jobs.php" class="btn btn-light shadow-sm w-100 py-3 text-start fw-bold border-0" style="border-radius:15px; color:#0a4f32;"><i class="fa-solid fa-briefcase fs-4 me-3 align-middle text-success"></i> Manage Jobs</a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="reports.php" class="btn btn-light shadow-sm w-100 py-3 text-start fw-bold border-0" style="border-radius:15px; color:#0a4f32;"><i class="fa-solid fa-chart-line fs-4 me-3 align-middle text-warning"></i> Analytics Reports</a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="unemployed_details.php" class="btn btn-light shadow-sm w-100 py-3 text-start fw-bold border-0" style="border-radius:15px; color:#0a4f32;"><i class="fa-solid fa-tower-observation fs-4 me-3 align-middle text-danger"></i> Workforce Monitor</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Recent Employment Status Changes</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($recent_changes && $recent_changes->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 rounded-start">User Info</th>
                                        <th class="border-0">Status Transition</th>
                                        <th class="border-0 rounded-end">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $recent_changes->fetch_assoc()): ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width:45px; height:45px; font-size:1.2rem;">
                                                        <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></h6>
                                                        <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-secondary opacity-75"><?php echo htmlspecialchars($row['old_status'] ?? 'None'); ?></span>
                                                    <i class="fa-solid fa-arrow-right mx-2 text-muted"></i>
                                                    <span class="badge bg-success"><?php echo htmlspecialchars($row['new_status']); ?></span>
                                                </div>
                                                <?php if(!empty($row['remarks'])): ?>
                                                    <small class="d-block mt-1 text-muted"><i class="fa-regular fa-comment-dots me-1"></i> <?php echo htmlspecialchars($row['remarks']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 text-muted">
                                                <i class="fa-regular fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?><br>
                                                <small><i class="fa-regular fa-clock me-1"></i> <?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size:3rem;"><i class="fa-solid fa-folder-open"></i></div>
                            <h5 class="text-muted fw-bold">No Status Changes Yet</h5>
                            <p class="text-muted mb-0">Employment updates will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius:20px; background:linear-gradient(145deg, #1e293b, #0f172a); color:white;">
                <div class="card-header border-0 pt-4 pb-2 px-4 bg-transparent">
                    <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-location-dot text-danger me-2"></i> High Unemployment Areas</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($top_districts && $top_districts->num_rows > 0): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php 
                            $max_count = 0;
                            $districts_data = [];
                            while ($row = $top_districts->fetch_assoc()) {
                                $districts_data[] = $row;
                                if ($row['unemployed_count'] > $max_count) {
                                    $max_count = $row['unemployed_count'];
                                }
                            }
                            ?>
                            <?php foreach ($districts_data as $row): ?>
                                <?php 
                                    $percentage = $max_count > 0 ? ($row['unemployed_count'] / $max_count) * 100 : 0; 
                                ?>
                                <div class="area-stat-item">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold text-white"><?php echo htmlspecialchars($row['district_name']); ?></span>
                                        <span class="badge bg-danger rounded-pill px-3"><?php echo htmlspecialchars($row['unemployed_count']); ?> Unemployed</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $percentage; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-white-50 mb-3" style="font-size:3rem;"><i class="fa-solid fa-map-location-dot"></i></div>
                            <p class="text-white-50 mb-0">No area data available.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center">
                        <a href="unemployed_details.php" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold">View Full Map &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include('../includes/footer.php'); ?>