<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$arText = [
    'bn' => [
        'title' => 'কর্মসংস্থান বিশ্লেষণ ড্যাশবোর্ড',
        'subtitle' => 'এলাকাভিত্তিক বেকারত্ব, কর্মসংস্থান, দক্ষতা এবং চাকরির চাহিদা পর্যবেক্ষণ।',
        'details_btn' => 'বেকারদের বিবরণ',
        'back_btn' => 'ফিরে যান',
        'total_users' => 'মোট ব্যবহারকারী',
        'job_seekers' => 'চাকরিপ্রার্থী',
        'employers' => 'নিয়োগকর্তা',
        'total_jobs' => 'মোট চাকরি',
        'unemployed' => 'বেকার',
        'employed' => 'নিযুক্ত',
        'training' => 'প্রশিক্ষণরত',
        'self_employed' => 'স্বনির্ভর',
        'status_overview' => 'কর্মসংস্থানের অবস্থার সংক্ষিপ্ত বিবরণ',
        'district_unemployment' => 'জেলাওয়ারী বেকারত্ব',
        'jobs_by_district' => 'জেলা অনুযায়ী পোস্ট করা চাকরি',
        'top_skills' => 'শীর্ষ দক্ষতা বিশ্লেষণ',
        'table_title' => 'জেলাওয়ারী কর্মসংস্থান পর্যবেক্ষণ টেবিল',
        'th_district' => 'জেলা',
        'th_job_seekers' => 'চাকরিপ্রার্থী',
        'th_unemployed' => 'বেকার',
        'th_employed' => 'নিযুক্ত',
        'th_training' => 'প্রশিক্ষণরত',
        'th_self_employed' => 'স্বনির্ভর',
        'th_jobs_posted' => 'পোস্ট করা চাকরি',
        'no_district_data' => 'কোনো জেলা বিশ্লেষণ তথ্য পাওয়া যায়নি।',
        'recent_changes' => 'সাম্প্রতিক কর্মসংস্থানের পরিবর্তন',
        'th_name' => 'নাম',
        'th_change' => 'পরিবর্তন',
        'th_date' => 'তারিখ',
        'no_changes' => 'এখনও কোনো কর্মসংস্থানের অবস্থা পরিবর্তন নেই।',
        'recent_jobs' => 'সাম্প্রতিক চাকরি',
        'th_title' => 'শিরোনাম',
        'th_salary' => 'বেতন',
        'no_recent_jobs' => 'কোনো সাম্প্রতিক চাকরি পাওয়া যায়নি।',
        'negotiable' => 'আলোচনা সাপেক্ষে',
        'na' => 'প্রযোজ্য নয়',
        // Chart labels (JavaScript)
        'chart_label_unemployed' => 'বেকার',
        'chart_label_employed' => 'নিযুক্ত',
        'chart_label_training' => 'প্রশিক্ষণরত',
        'chart_label_self_employed' => 'স্বনির্ভর',
        'chart_label_jobs_posted' => 'পোস্ট করা চাকরি',
        'chart_label_users' => 'ব্যবহারকারী',
    ],
    'en' => [
        'title' => 'Employment Analytics Dashboard',
        'subtitle' => 'Area-based unemployment, employment, skill and job demand monitoring.',
        'details_btn' => 'Unemployed Details',
        'back_btn' => 'Back',
        'total_users' => 'Total Users',
        'job_seekers' => 'Job Seekers',
        'employers' => 'Employers',
        'total_jobs' => 'Total Jobs',
        'unemployed' => 'Unemployed',
        'employed' => 'Employed',
        'training' => 'Training',
        'self_employed' => 'Self Employed',
        'status_overview' => 'Employment Status Overview',
        'district_unemployment' => 'District-wise Unemployment',
        'jobs_by_district' => 'Jobs Posted by District',
        'top_skills' => 'Top Skills Analytics',
        'table_title' => 'District-wise Employment Monitoring Table',
        'th_district' => 'District',
        'th_job_seekers' => 'Job Seekers',
        'th_unemployed' => 'Unemployed',
        'th_employed' => 'Employed',
        'th_training' => 'Training',
        'th_self_employed' => 'Self Employed',
        'th_jobs_posted' => 'Jobs Posted',
        'no_district_data' => 'No district analytics data found.',
        'recent_changes' => 'Recent Employment Changes',
        'th_name' => 'Name',
        'th_change' => 'Change',
        'th_date' => 'Date',
        'no_changes' => 'No employment status changes yet.',
        'recent_jobs' => 'Recent Jobs',
        'th_title' => 'Title',
        'th_salary' => 'Salary',
        'no_recent_jobs' => 'No recent jobs found.',
        'negotiable' => 'Negotiable',
        'na' => 'N/A',
        // Chart labels (JavaScript)
        'chart_label_unemployed' => 'Unemployed',
        'chart_label_employed' => 'Employed',
        'chart_label_training' => 'Training',
        'chart_label_self_employed' => 'Self Employed',
        'chart_label_jobs_posted' => 'Jobs Posted',
        'chart_label_users' => 'Users',
    ]
];
$ct = $arText[$lang];

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
            <h2 class="mb-1 fw-bold"><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0">
                <?php echo $ct['subtitle']; ?>
            </p>
        </div>
        <div>
            <a href="unemployed_details.php" class="btn btn-danger me-2"><?php echo $ct['details_btn']; ?></a>
            <a href="dashboard.php" class="btn btn-secondary"><?php echo $ct['back_btn']; ?></a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['total_users']; ?></h6><h3><?php echo $total_users; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['job_seekers']; ?></h6><h3><?php echo $total_job_seekers; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['employers']; ?></h6><h3><?php echo $total_employers; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['total_jobs']; ?></h6><h3><?php echo $total_jobs; ?></h3></div></div>

        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['unemployed']; ?></h6><h3 class="text-danger"><?php echo $total_unemployed; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['employed']; ?></h6><h3 class="text-success"><?php echo $total_employed; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['training']; ?></h6><h3 class="text-warning"><?php echo $total_training; ?></h3></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0 p-3 h-100"><h6><?php echo $ct['self_employed']; ?></h6><h3 class="text-info"><?php echo $total_self_employed; ?></h3></div></div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['status_overview']; ?></h4>
                <canvas id="statusPieChart" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['district_unemployment']; ?></h4>
                <canvas id="districtBarChart" height="220"></canvas>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['jobs_by_district']; ?></h4>
                <canvas id="jobsDistrictChart" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['top_skills']; ?></h4>
                <canvas id="skillsChart" height="220"></canvas>
            </div>
        </div>

    </div>

    <div class="card shadow p-4 mb-4">
        <h4 class="mb-3"><?php echo $ct['table_title']; ?></h4>

        <?php if(!empty($district_rows)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo $ct['th_district']; ?></th>
                            <th><?php echo $ct['th_job_seekers']; ?></th>
                            <th><?php echo $ct['th_unemployed']; ?></th>
                            <th><?php echo $ct['th_employed']; ?></th>
                            <th><?php echo $ct['th_training']; ?></th>
                            <th><?php echo $ct['th_self_employed']; ?></th>
                            <th><?php echo $ct['th_jobs_posted']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($district_rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(translateDistrict($row['district_name'] ?? '', $lang)); ?></td>
                                <td><?php echo htmlspecialchars(translateNumber($row['total_job_seekers'] ?? 0, $lang)); ?></td>
                                <td><span class="badge bg-danger"><?php echo htmlspecialchars(translateNumber($row['unemployed'] ?? 0, $lang)); ?></span></td>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars(translateNumber($row['employed'] ?? 0, $lang)); ?></span></td>
                                <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars(translateNumber($row['training'] ?? 0, $lang)); ?></span></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars(translateNumber($row['self_employed'] ?? 0, $lang)); ?></span></td>
                                <td><?php echo htmlspecialchars(translateNumber($row['jobs_posted'] ?? 0, $lang)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mb-0"><?php echo $ct['no_district_data']; ?></div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['recent_changes']; ?></h4>

                <?php if($recent_employment_changes && $recent_employment_changes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th><?php echo $ct['th_name']; ?></th>
                                    <th><?php echo $ct['th_change']; ?></th>
                                    <th><?php echo $ct['th_date']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_employment_changes->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars(translateEmployerName($row['full_name'] ?? '', $lang)); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($ct[$row['old_status']] ?? $row['old_status'] ?? $ct['na']); ?>
                                            →
                                            <strong><?php echo htmlspecialchars($ct[$row['new_status']] ?? $row['new_status']); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars(translateDate($row['created_at'] ?? '', $lang)); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0"><?php echo $ct['no_changes']; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow p-4 h-100">
                <h4 class="mb-3"><?php echo $ct['recent_jobs']; ?></h4>

                <?php if($recent_jobs && $recent_jobs->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th><?php echo $ct['th_title']; ?></th>
                                    <th><?php echo $ct['th_district']; ?></th>
                                    <th><?php echo $ct['th_salary']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_jobs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(translateJobTitle($row['title'] ?? '', $lang)); ?></td>
                                        <td><?php echo htmlspecialchars(translateDistrict($row['district_name'] ?? '', $lang) ?: $ct['na']); ?></td>
                                        <td><?php echo (empty($row['salary']) || strtolower($row['salary']) === 'negotiable') ? $ct['negotiable'] : '৳ ' . translateSalary($row['salary'], $lang); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0"><?php echo $ct['no_recent_jobs']; ?></div>
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

const chartLabels = {
    unemployed: <?php echo json_encode($ct['chart_label_unemployed']); ?>,
    employed: <?php echo json_encode($ct['chart_label_employed']); ?>,
    training: <?php echo json_encode($ct['chart_label_training']); ?>,
    self_employed: <?php echo json_encode($ct['chart_label_self_employed']); ?>,
    jobs_posted: <?php echo json_encode($ct['chart_label_jobs_posted']); ?>,
    users: <?php echo json_encode($ct['chart_label_users']); ?>
};

new Chart(document.getElementById('statusPieChart'), {
    type: 'doughnut',
    data: {
        labels: [chartLabels.unemployed, chartLabels.employed, chartLabels.training, chartLabels.self_employed],
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
            { label: chartLabels.unemployed, data: unemployedData, backgroundColor: '#dc3545' },
            { label: chartLabels.employed, data: employedData, backgroundColor: '#198754' },
            { label: chartLabels.training, data: trainingData, backgroundColor: '#ffc107' },
            { label: chartLabels.self_employed, data: selfEmployedData, backgroundColor: '#0dcaf0' }
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
            label: chartLabels.jobs_posted,
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
            label: chartLabels.users,
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