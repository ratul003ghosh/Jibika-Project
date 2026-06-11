<?php
include_once('admin_bootstrap.php');

$districtId = isset($_GET['district_id']) ? (int)$_GET['district_id'] : 0;
$upazilaId = isset($_GET['upazila_id']) ? (int)$_GET['upazila_id'] : 0;
$wardId = isset($_GET['ward_id']) ? (int)$_GET['ward_id'] : 0;
$skill = trim((string)($_GET['skill'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$startDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_GET['start_date'] ?? '')) ? $_GET['start_date'] : '';
$endDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_GET['end_date'] ?? '')) ? $_GET['end_date'] : '';
$allowedStatuses = ['unemployed', 'employed', 'training', 'self_employed'];
$status = in_array($status, $allowedStatuses, true) ? $status : '';
$skillSql = $conn->real_escape_string($skill);

$districts = admin_rows($conn, "SELECT district_id, district_name FROM districts ORDER BY district_name");
$upazilas = admin_rows($conn, "SELECT upazila_id, upazila_name FROM upazilas ORDER BY upazila_name");
$wards = admin_rows($conn, "SELECT ward_id, ward_name FROM wards ORDER BY ward_name");
$skills = admin_rows($conn, "SELECT skill_name FROM dic_skills ORDER BY skill_name LIMIT 250");

function report_profile_where($districtId, $upazilaId, $wardId, $status, $skillSql) {
    $conditions = ["1=1"];
    if ($districtId > 0) $conditions[] = "jsp.district_id = $districtId";
    if ($upazilaId > 0) $conditions[] = "jsp.upazila_id = $upazilaId";
    if ($wardId > 0) $conditions[] = "jsp.ward_id = $wardId";
    if ($status !== '') $conditions[] = "es.current_status = '$status'";
    if ($skillSql !== '') {
        $conditions[] = "EXISTS (
            SELECT 1 FROM job_seeker_skills jss2
            JOIN dic_skills ds2 ON jss2.skill_id = ds2.skill_id
            WHERE jss2.user_id = jsp.user_id AND ds2.skill_name = '$skillSql'
        )";
    }
    return implode(' AND ', $conditions);
}

function report_job_where($districtId, $upazilaId, $wardId, $skillSql, $startDate, $endDate) {
    $conditions = ["1=1"];
    if ($districtId > 0) $conditions[] = "j.district_id = $districtId";
    if ($upazilaId > 0) $conditions[] = "j.upazila_id = $upazilaId";
    if ($wardId > 0) $conditions[] = "j.ward_id = $wardId";
    if ($startDate !== '') $conditions[] = "DATE(j.created_at) >= '$startDate'";
    if ($endDate !== '') $conditions[] = "DATE(j.created_at) <= '$endDate'";
    if ($skillSql !== '') {
        $conditions[] = "EXISTS (
            SELECT 1 FROM job_required_skills jrs2
            LEFT JOIN dic_skills ds2 ON jrs2.skill_id = ds2.skill_id
            WHERE jrs2.job_id = j.job_id
              AND (ds2.skill_name = '$skillSql' OR jrs2.skill_name = '$skillSql')
        )";
    }
    return implode(' AND ', $conditions);
}

function report_app_date_where($startDate, $endDate) {
    $conditions = [];
    if ($startDate !== '') $conditions[] = "DATE(a.applied_at) >= '$startDate'";
    if ($endDate !== '') $conditions[] = "DATE(a.applied_at) <= '$endDate'";
    return $conditions;
}

$profileWhere = report_profile_where($districtId, $upazilaId, $wardId, $status, $skillSql);
$jobWhere = report_job_where($districtId, $upazilaId, $wardId, $skillSql, $startDate, $endDate);
$appConditions = array_merge(["$jobWhere"], report_app_date_where($startDate, $endDate));
$appWhere = implode(' AND ', $appConditions);

$areaReport = admin_rows($conn, "
    SELECT d.district_name,
           COUNT(DISTINCT jsp.user_id) AS job_seekers,
           COUNT(DISTINCT CASE WHEN es.current_status = 'unemployed' THEN jsp.user_id END) AS unemployed,
           COUNT(DISTINCT CASE WHEN es.current_status = 'employed' THEN jsp.user_id END) AS employed,
           COUNT(DISTINCT CASE WHEN es.current_status = 'training' THEN jsp.user_id END) AS training,
           COUNT(DISTINCT CASE WHEN es.current_status = 'self_employed' THEN jsp.user_id END) AS self_employed,
           COUNT(DISTINCT j.job_id) AS job_posts
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    LEFT JOIN jobs j ON d.district_id = j.district_id
    WHERE " . ($districtId > 0 ? "d.district_id = $districtId" : "1=1") . "
    GROUP BY d.district_id, d.district_name
    ORDER BY unemployed DESC, d.district_name ASC
");

$skillDistribution = admin_rows($conn, "
    SELECT ds.skill_name, COUNT(DISTINCT jss.user_id) AS total
    FROM job_seeker_profiles jsp
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    JOIN job_seeker_skills jss ON jsp.user_id = jss.user_id
    JOIN dic_skills ds ON jss.skill_id = ds.skill_id
    WHERE $profileWhere
    GROUP BY ds.skill_id, ds.skill_name
    ORDER BY total DESC
    LIMIT 12
");

$jobDemand = admin_rows($conn, "
    SELECT COALESCE(ds.skill_name, jrs.skill_name) AS skill_name, COUNT(DISTINCT j.job_id) AS total_jobs
    FROM jobs j
    JOIN job_required_skills jrs ON j.job_id = jrs.job_id
    LEFT JOIN dic_skills ds ON jrs.skill_id = ds.skill_id
    WHERE $jobWhere
    GROUP BY skill_name
    ORDER BY total_jobs DESC
    LIMIT 12
");

$skillGap = admin_rows($conn, "
    SELECT skill_name, supply_count, demand_count, gap_count
    FROM vw_skill_gap
    " . ($skillSql !== '' ? "WHERE skill_name = '$skillSql'" : "") . "
    ORDER BY gap_count DESC, demand_count DESC
    LIMIT 12
");

$trainingNeed = admin_rows($conn, "
    SELECT d.district_name,
           COUNT(DISTINCT jsp.user_id) AS job_seekers,
           COUNT(DISTINCT CASE WHEN es.current_status = 'unemployed' THEN jsp.user_id END) AS unemployed,
           COUNT(DISTINCT j.job_id) AS jobs_available
    FROM districts d
    LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    LEFT JOIN jobs j ON d.district_id = j.district_id
    WHERE " . ($districtId > 0 ? "d.district_id = $districtId" : "1=1") . "
    GROUP BY d.district_id, d.district_name
    ORDER BY unemployed DESC
");

$employerActivity = admin_rows($conn, "
    SELECT employer_id, full_name, company_name, total_jobs, total_applications
    FROM vw_employer_activity
    ORDER BY total_applications DESC, total_jobs DESC
    LIMIT 12
");

$applicationStatus = admin_rows($conn, "
    SELECT a.status, COUNT(*) AS total
    FROM applications a
    JOIN jobs j ON a.job_id = j.job_id
    WHERE $appWhere
    GROUP BY a.status
    ORDER BY total DESC
");

$statusChanges = admin_rows($conn, "
    SELECT u.full_name, esl.old_status, esl.new_status, esl.remarks, esl.created_at
    FROM employment_status_logs esl
    JOIN users u ON esl.user_id = u.user_id
    LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
    LEFT JOIN employment_status es ON u.user_id = es.user_id
    WHERE $profileWhere
    ORDER BY esl.created_at DESC
    LIMIT 12
");

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=jibika_reports.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Jibika Area-wise Unemployment And Skill Matching Report']);
    fputcsv($out, []);
    fputcsv($out, ['District', 'Job Seekers', 'Unemployed', 'Employed', 'Training', 'Self Employed', 'Job Posts']);
    foreach ($areaReport as $row) {
        fputcsv($out, [$row['district_name'], $row['job_seekers'], $row['unemployed'], $row['employed'], $row['training'], $row['self_employed'], $row['job_posts']]);
    }
    fputcsv($out, []);
    fputcsv($out, ['Skill Gap', 'Supply', 'Demand', 'Gap']);
    foreach ($skillGap as $row) {
        fputcsv($out, [$row['skill_name'], $row['supply_count'], $row['demand_count'], $row['gap_count']]);
    }
    fputcsv($out, []);
    fputcsv($out, ['Employer', 'Company', 'Jobs', 'Applications']);
    foreach ($employerActivity as $row) {
        fputcsv($out, [$row['full_name'], $row['company_name'], $row['total_jobs'], $row['total_applications']]);
    }
    fclose($out);
    exit();
}

admin_header('Reports');
?>
<style>
@media print { .side, .top .btn, .filters, .report-actions { display:none !important; } .layout{display:block} main{padding:0} body{background:#fff} }
.report-note { background:#eef8f2; border:1px solid #cfe9d8; border-radius:14px; padding:16px; margin-bottom:16px; color:#154734; }
</style>

<div class="report-note">
    These reports help government, NGO and admin teams identify high-unemployment areas, skill gaps, weak job demand, and employer activity so training and employment programs can be planned with evidence.
</div>

<form class="filters" method="GET">
    <select name="district_id"><option value="0">All Districts</option><?php foreach ($districts as $d): ?><option value="<?php echo admin_e($d['district_id']); ?>" <?php echo $districtId === (int)$d['district_id'] ? 'selected' : ''; ?>><?php echo admin_e($d['district_name']); ?></option><?php endforeach; ?></select>
    <select name="upazila_id"><option value="0">All Upazilas</option><?php foreach ($upazilas as $u): ?><option value="<?php echo admin_e($u['upazila_id']); ?>" <?php echo $upazilaId === (int)$u['upazila_id'] ? 'selected' : ''; ?>><?php echo admin_e($u['upazila_name']); ?></option><?php endforeach; ?></select>
    <select name="ward_id"><option value="0">All Wards</option><?php foreach ($wards as $w): ?><option value="<?php echo admin_e($w['ward_id']); ?>" <?php echo $wardId === (int)$w['ward_id'] ? 'selected' : ''; ?>><?php echo admin_e($w['ward_name']); ?></option><?php endforeach; ?></select>
    <select name="skill"><option value="">All Skills</option><?php foreach ($skills as $s): ?><option value="<?php echo admin_e($s['skill_name']); ?>" <?php echo $skill === $s['skill_name'] ? 'selected' : ''; ?>><?php echo admin_e($s['skill_name']); ?></option><?php endforeach; ?></select>
    <select name="status"><option value="">All Status</option><?php foreach ($allowedStatuses as $s): ?><option value="<?php echo admin_e($s); ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo admin_e(ucwords(str_replace('_', ' ', $s))); ?></option><?php endforeach; ?></select>
    <input type="date" name="start_date" value="<?php echo admin_e($startDate); ?>">
    <input type="date" name="end_date" value="<?php echo admin_e($endDate); ?>">
    <button class="btn" type="submit">Apply</button>
    <a class="btn light" href="reports.php">Reset</a>
</form>

<div class="report-actions" style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
    <button class="btn" type="button" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Report</button>
    <a class="btn light" href="reports.php?<?php echo admin_e(http_build_query(array_merge($_GET, ['export' => 'csv']))); ?>"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
</div>

<div class="grid">
    <div class="card"><h3>Area-wise Unemployment Report</h3><p class="muted">Compares unemployment, employment, training and job posts by district.</p></div>
    <div class="card"><h3>Skill Gap Report</h3><p class="muted">Shows where training is needed because demand is higher than supply.</p></div>
    <div class="card"><h3>Employer Activity Report</h3><p class="muted">Shows which employers generate jobs and applications.</p></div>
</div>

<h2>Area-wise Unemployment Report</h2>
<table><thead><tr><th>District</th><th>Job Seekers</th><th>Unemployed</th><th>Employed</th><th>Training</th><th>Self-employed</th><th>Jobs</th></tr></thead><tbody>
<?php foreach ($areaReport as $r): ?><tr><td><?php echo admin_e($r['district_name']); ?></td><td><?php echo admin_e($r['job_seekers']); ?></td><td><?php echo admin_e($r['unemployed']); ?></td><td><?php echo admin_e($r['employed']); ?></td><td><?php echo admin_e($r['training']); ?></td><td><?php echo admin_e($r['self_employed']); ?></td><td><?php echo admin_e($r['job_posts']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Skill Distribution Report</h2>
<table><thead><tr><th>Available Skill</th><th>Job Seekers</th></tr></thead><tbody>
<?php foreach ($skillDistribution as $r): ?><tr><td><?php echo admin_e($r['skill_name']); ?></td><td><?php echo admin_e($r['total']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Job Demand Report</h2>
<table><thead><tr><th>Demanded Skill / Job Skill</th><th>Active Job Posts</th></tr></thead><tbody>
<?php foreach ($jobDemand as $r): ?><tr><td><?php echo admin_e($r['skill_name']); ?></td><td><?php echo admin_e($r['total_jobs']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Skill Gap Report</h2>
<table><thead><tr><th>Skill</th><th>Supply</th><th>Demand</th><th>Gap</th><th>Suggested Action</th></tr></thead><tbody>
<?php foreach ($skillGap as $r): $gap = (int)$r['gap_count']; ?><tr><td><?php echo admin_e($r['skill_name']); ?></td><td><?php echo admin_e($r['supply_count']); ?></td><td><?php echo admin_e($r['demand_count']); ?></td><td><span class="badge"><?php echo admin_e($gap); ?></span></td><td><?php echo admin_e($gap > 0 ? 'Training program recommended' : 'Job matching or SME support recommended'); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Training Need Report</h2>
<table><thead><tr><th>District</th><th>Unemployed</th><th>Jobs Available</th><th>Training Need Score</th><th>Recommendation</th></tr></thead><tbody>
<?php foreach ($trainingNeed as $r): $seekers = max(1, (int)$r['job_seekers']); $rate = ((int)$r['unemployed'] / $seekers) * 100; $score = min(100, round($rate + (((int)$r['jobs_available'] < max(1, (int)$r['unemployed'] / 10)) ? 20 : 0))); ?><tr><td><?php echo admin_e($r['district_name']); ?></td><td><?php echo admin_e($r['unemployed']); ?></td><td><?php echo admin_e($r['jobs_available']); ?></td><td><?php echo admin_e($score); ?>/100</td><td><?php echo admin_e($score >= 60 ? 'Government/NGO training and employer outreach recommended' : 'Job matching campaign recommended'); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Employer Activity Report</h2>
<table><thead><tr><th>Employer</th><th>Company</th><th>Jobs</th><th>Applications</th></tr></thead><tbody>
<?php foreach ($employerActivity as $r): ?><tr><td><?php echo admin_e($r['full_name']); ?></td><td><?php echo admin_e($r['company_name']); ?></td><td><?php echo admin_e($r['total_jobs']); ?></td><td><?php echo admin_e($r['total_applications']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Application Status Report</h2>
<table><thead><tr><th>Status</th><th>Total Applications</th></tr></thead><tbody>
<?php foreach ($applicationStatus as $r): ?><tr><td><?php echo admin_e($r['status']); ?></td><td><?php echo admin_e($r['total']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<br><h2>Employment Status Change Report</h2>
<table><thead><tr><th>User</th><th>Old Status</th><th>New Status</th><th>Remarks</th><th>Date</th></tr></thead><tbody>
<?php foreach ($statusChanges as $r): ?><tr><td><?php echo admin_e($r['full_name']); ?></td><td><?php echo admin_e($r['old_status']); ?></td><td><?php echo admin_e($r['new_status']); ?></td><td><?php echo admin_e($r['remarks']); ?></td><td><?php echo admin_e($r['created_at']); ?></td></tr><?php endforeach; ?>
</tbody></table>

<?php admin_footer(); ?>
