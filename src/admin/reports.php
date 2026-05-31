<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

function getCount($conn, $sql){
    $q = $conn->query($sql);
    return ($q) ? ($q->fetch_assoc()['total'] ?? 0) : 0;
}

$total_users = getCount($conn, "SELECT COUNT(*) AS total FROM users");
$total_job_seekers = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='job_seeker'");
$total_employers = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='employer'");
$total_jobs = getCount($conn, "SELECT COUNT(*) AS total FROM jobs");
$total_applications = getCount($conn, "SELECT COUNT(*) AS total FROM applications");
$total_skills = getCount($conn, "SELECT COUNT(*) AS total FROM skills");
$total_profiles = getCount($conn, "SELECT COUNT(*) AS total FROM job_seeker_profiles");
$total_unemployed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='unemployed'");
$total_employed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='employed'");
$total_training = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='training'");
$total_self_employed = getCount($conn, "SELECT COUNT(*) AS total FROM employment_status WHERE current_status='self_employed'");

$district_analytics = $conn->query("
    SELECT 
        d.district_name,
        COUNT(DISTINCT jsp.user_id) AS total_job_seekers,
        COUNT(DISTINCT CASE WHEN es.current_status='unemployed' THEN es.user_id END) AS unemployed,
        COUNT(DISTINCT CASE WHEN es.current_status='employed' THEN es.user_id END) AS employed,
        COUNT(DISTINCT CASE WHEN es.current_status='training' THEN es.user_id END) AS training,
        COUNT(DISTINCT CASE WHEN es.current_status='self_employed' THEN es.user_id END) AS self_employed,
        COUNT(DISTINCT j.job_id) AS jobs_posted
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    LEFT JOIN jobs j ON d.district_id = j.district_id
    GROUP BY d.district_id, d.district_name
    ORDER BY unemployed DESC, d.district_name ASC
");

$district_labels = [];
$unemployed_data = [];
$employed_data = [];
$training_data = [];
$self_employed_data = [];
$jobs_data = [];

$district_rows = [];

if($district_analytics && $district_analytics->num_rows > 0){
    while($row = $district_analytics->fetch_assoc()){
        $district_rows[] = $row;
        $district_labels[] = $row['district_name'];
        $unemployed_data[] = (int)$row['unemployed'];
        $employed_data[] = (int)$row['employed'];
        $training_data[] = (int)$row['training'];
        $self_employed_data[] = (int)$row['self_employed'];
        $jobs_data[] = (int)$row['jobs_posted'];
    }
}

$top_skills = $conn->query("
    SELECT skill_name, COUNT(*) AS total
    FROM skills
    GROUP BY skill_name
    ORDER BY total DESC
    LIMIT 10
");

$skill_labels = [];
$skill_data = [];
$skill_rows = [];

if($top_skills && $top_skills->num_rows > 0){
    while($row = $top_skills->fetch_assoc()){
        $skill_rows[] = $row;
        $skill_labels[] = $row['skill_name'];
        $skill_data[] = (int)$row['total'];
    }
}

$recent_employment_changes = $conn->query("
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
    LIMIT 8
");

$recent_jobs = $conn->query("
    SELECT jobs.title, jobs.salary, jobs.created_at, districts.district_name
    FROM jobs
    LEFT JOIN districts ON jobs.district_id = districts.district_id
    ORDER BY jobs.job_id DESC
    LIMIT 5
");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1 fw-bold">Employment Analytics Dashboard</h2>
            <p class="text-muted mb-0">
                Area-based unemployment, employment, skill and job demand monitoring.
            </p>
        </div>
        <div>
            <a href="unemployed_details.php" class="btn btn-danger me-2">Unemployed Details</a>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Total Users</h6><h3><?php echo $total_users; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Job Seekers</h6><h3><?php echo $total_job_seekers; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Employers</h6><h3><?php echo $total_employers; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Total Jobs</h6><h3><?php echo $total_jobs; ?></h3></div></div>

        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Unemployed</h6><h3 class="text-danger"><?php echo $total_unemployed; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Employed</h6><h3 class="text-success"><?php echo $total_employed; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Training</h6><h3 class="text-warning"><?php echo $total_training; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6>Self Employed</h6><h3 class="text-info"><?php echo $total_self_employed; ?></h3></div></div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3">Employment Status Overview</h4>
                <canvas id="statusPieChart" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3">District-wise Unemployment</h4>
                <canvas id="districtBarChart" height="220"></canvas>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3">Jobs Posted by District</h4>
                <canvas id="jobsDistrictChart" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3">Top Skills Analytics</h4>
                <canvas id="skillsChart" height="220"></canvas>
            </div>
        </div>

    </div>

    <div class="card shadow p-4 mb-4">
        <h4 class="mb-3">District-wise Employment Monitoring Table</h4>

        <?php if(!empty($district_rows)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>District</th>
                            <th>Job Seekers</th>
                            <th>Unemployed</th>
                            <th>Employed</th>
                            <th>Training</th>
                            <th>Self Employed</th>
                            <th>Jobs Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($district_rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_job_seekers']); ?></td>
                                <td><span class="badge bg-danger"><?php echo htmlspecialchars($row['unemployed']); ?></span></td>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars($row['employed']); ?></span></td>
                                <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['training']); ?></span></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['self_employed']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['jobs_posted']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mb-0">No district analytics data found.</div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">Recent Employment Changes</h4>

                <?php if($recent_employment_changes && $recent_employment_changes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Change</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_employment_changes->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($row['full_name']); ?><br>
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
                    <div class="alert alert-warning mb-0">No employment status changes yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3">Recent Jobs</h4>

                <?php if($recent_jobs && $recent_jobs->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>District</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_jobs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['district_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['salary'] ?? 'Negotiable'); ?></td>
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

</div>

<script>
const districtLabels = <?php echo json_encode($district_labels); ?>;
const unemployedData = <?php echo json_encode($unemployed_data); ?>;
const employedData = <?php echo json_encode($employed_data); ?>;
const trainingData = <?php echo json_encode($training_data); ?>;
const selfEmployedData = <?php echo json_encode($self_employed_data); ?>;
const jobsData = <?php echo json_encode($jobs_data); ?>;

const skillLabels = <?php echo json_encode($skill_labels); ?>;
const skillData = <?php echo json_encode($skill_data); ?>;

new Chart(document.getElementById('statusPieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Unemployed', 'Employed', 'Training', 'Self Employed'],
        datasets: [{
            data: [
                <?php echo $total_unemployed; ?>,
                <?php echo $total_employed; ?>,
                <?php echo $total_training; ?>,
                <?php echo $total_self_employed; ?>
            ],
            backgroundColor: ['#dc3545', '#198754', '#ffc107', '#0dcaf0']
        }]
    }
});

new Chart(document.getElementById('districtBarChart'), {
    type: 'bar',
    data: {
        labels: districtLabels,
        datasets: [
            { label: 'Unemployed', data: unemployedData, backgroundColor: '#dc3545' },
            { label: 'Employed', data: employedData, backgroundColor: '#198754' },
            { label: 'Training', data: trainingData, backgroundColor: '#ffc107' },
            { label: 'Self Employed', data: selfEmployedData, backgroundColor: '#0dcaf0' }
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('jobsDistrictChart'), {
    type: 'bar',
    data: {
        labels: districtLabels,
        datasets: [{
            label: 'Jobs Posted',
            data: jobsData,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('skillsChart'), {
    type: 'bar',
    data: {
        labels: skillLabels,
        datasets: [{
            label: 'Users',
            data: skillData,
            backgroundColor: '#6f42c1'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: { x: { beginAtZero: true } }
    }
});
</script>

<?php include('../includes/footer.php'); ?>