<?php
include_once('admin_bootstrap.php');

$districtId = isset($_GET['district_id']) ? (int)$_GET['district_id'] : 0;
$upazilaId = isset($_GET['upazila_id']) ? (int)$_GET['upazila_id'] : 0;
$wardId = isset($_GET['ward_id']) ? (int)$_GET['ward_id'] : 0;
$skill = trim((string)($_GET['skill'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$allowedStatuses = ['unemployed', 'employed', 'training', 'self_employed'];
$status = in_array($status, $allowedStatuses, true) ? $status : '';
$skillSql = $conn->real_escape_string($skill);

$districts = admin_rows($conn, "SELECT district_id, district_name FROM districts ORDER BY district_name");
$upazilas = admin_rows($conn, "SELECT upazila_id, district_id, upazila_name FROM upazilas ORDER BY upazila_name");
$wards = admin_rows($conn, "SELECT ward_id, upazila_id, ward_name FROM wards ORDER BY ward_name");
$skills = admin_rows($conn, "SELECT skill_name FROM dic_skills ORDER BY skill_name LIMIT 250");

function area_profile_conditions($districtId, $upazilaId, $wardId, $status, $skillSql) {
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

function area_job_conditions($districtId, $upazilaId, $wardId, $skillSql) {
    $conditions = ["1=1"];
    if ($districtId > 0) $conditions[] = "j.district_id = $districtId";
    if ($upazilaId > 0) $conditions[] = "j.upazila_id = $upazilaId";
    if ($wardId > 0) $conditions[] = "j.ward_id = $wardId";
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

$profileWhere = area_profile_conditions($districtId, $upazilaId, $wardId, $status, $skillSql);
$jobWhere = area_job_conditions($districtId, $upazilaId, $wardId, $skillSql);

$summary = admin_rows($conn, "
    SELECT
        COUNT(DISTINCT jsp.user_id) AS job_seekers,
        COUNT(DISTINCT CASE WHEN es.current_status = 'unemployed' THEN jsp.user_id END) AS unemployed,
        COUNT(DISTINCT CASE WHEN es.current_status = 'employed' THEN jsp.user_id END) AS employed,
        COUNT(DISTINCT CASE WHEN es.current_status = 'training' THEN jsp.user_id END) AS training,
        COUNT(DISTINCT CASE WHEN es.current_status = 'self_employed' THEN jsp.user_id END) AS self_employed
    FROM job_seeker_profiles jsp
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    WHERE $profileWhere
");
$summary = $summary[0] ?? [];
$jobSeekers = (int)($summary['job_seekers'] ?? 0);
$unemployed = (int)($summary['unemployed'] ?? 0);
$employed = (int)($summary['employed'] ?? 0);
$training = (int)($summary['training'] ?? 0);
$selfEmployed = (int)($summary['self_employed'] ?? 0);
$jobPosts = admin_count($conn, "SELECT COUNT(*) AS total FROM jobs j WHERE $jobWhere");
$applications = admin_count($conn, "
    SELECT COUNT(DISTINCT a.application_id) AS total
    FROM applications a
    JOIN jobs j ON a.job_id = j.job_id
    WHERE $jobWhere
");

$availableSkills = admin_rows($conn, "
    SELECT ds.skill_name, COUNT(DISTINCT jss.user_id) AS total
    FROM job_seeker_profiles jsp
    LEFT JOIN employment_status es ON jsp.user_id = es.user_id
    JOIN job_seeker_skills jss ON jsp.user_id = jss.user_id
    JOIN dic_skills ds ON jss.skill_id = ds.skill_id
    WHERE $profileWhere
    GROUP BY ds.skill_id, ds.skill_name
    ORDER BY total DESC, ds.skill_name ASC
    LIMIT 8
");

$demandedSkills = admin_rows($conn, "
    SELECT COALESCE(ds.skill_name, jrs.skill_name) AS skill_name, COUNT(DISTINCT j.job_id) AS total
    FROM jobs j
    JOIN job_required_skills jrs ON j.job_id = jrs.job_id
    LEFT JOIN dic_skills ds ON jrs.skill_id = ds.skill_id
    WHERE $jobWhere
    GROUP BY skill_name
    ORDER BY total DESC, skill_name ASC
    LIMIT 8
");

$supplyBySkill = [];
foreach ($availableSkills as $row) $supplyBySkill[$row['skill_name']] = (int)$row['total'];
$demandBySkill = [];
foreach ($demandedSkills as $row) $demandBySkill[$row['skill_name']] = (int)$row['total'];
$allSkillNames = array_slice(array_values(array_unique(array_merge(array_keys($supplyBySkill), array_keys($demandBySkill)))), 0, 10);

$unemploymentRate = $jobSeekers > 0 ? round(($unemployed / $jobSeekers) * 100, 1) : 0;
$opportunityRatio = $jobPosts > 0 ? round($unemployed / $jobPosts, 1) : $unemployed;
$supplyTotal = array_sum($supplyBySkill);
$demandTotal = array_sum($demandBySkill);
$gapTotal = $demandTotal - $supplyTotal;
$trainingNeedScore = min(100, round(($unemploymentRate * 0.65) + (max(0, $gapTotal) * 8) + (($jobPosts < max(1, $unemployed / 10)) ? 15 : 0)));

$recommendations = [];
if ($unemployed > 0 && $jobPosts < max(1, round($unemployed / 10))) {
    $recommendations[] = "High unemployment but low job posts: employer outreach and NGO support recommended.";
}
if ($gapTotal > 0) {
    $recommendations[] = "High demand but low skill supply: training program recommended.";
}
if ($supplyTotal > 0 && $demandTotal < max(1, round($supplyTotal / 3))) {
    $recommendations[] = "High skill supply but low job demand: entrepreneurship/SME support recommended.";
}
if (empty($recommendations)) {
    $recommendations[] = "Balanced supply and demand: job matching campaign recommended.";
}

admin_header('Area Monitor');
?>
<p class="muted" style="max-width:900px;margin-top:-8px">
    Area Monitor shows where people are unemployed, what skills they have, what jobs exist nearby, and what action government or NGO partners should take.
</p>

<form class="filters" method="GET">
    <select name="district_id">
        <option value="0">All Districts</option>
        <?php foreach ($districts as $d): ?>
            <option value="<?php echo admin_e($d['district_id']); ?>" <?php echo $districtId === (int)$d['district_id'] ? 'selected' : ''; ?>><?php echo admin_e($d['district_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="upazila_id">
        <option value="0">All Upazilas</option>
        <?php foreach ($upazilas as $u): ?>
            <option value="<?php echo admin_e($u['upazila_id']); ?>" <?php echo $upazilaId === (int)$u['upazila_id'] ? 'selected' : ''; ?>><?php echo admin_e($u['upazila_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="ward_id">
        <option value="0">All Wards</option>
        <?php foreach ($wards as $w): ?>
            <option value="<?php echo admin_e($w['ward_id']); ?>" <?php echo $wardId === (int)$w['ward_id'] ? 'selected' : ''; ?>><?php echo admin_e($w['ward_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="skill">
        <option value="">All Skills</option>
        <?php foreach ($skills as $s): ?>
            <option value="<?php echo admin_e($s['skill_name']); ?>" <?php echo $skill === $s['skill_name'] ? 'selected' : ''; ?>><?php echo admin_e($s['skill_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status">
        <option value="">All Employment Status</option>
        <?php foreach ($allowedStatuses as $s): ?>
            <option value="<?php echo admin_e($s); ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo admin_e(ucwords(str_replace('_', ' ', $s))); ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Apply Filters</button>
    <a class="btn light" href="area_monitor.php">Reset</a>
</form>

<div class="grid">
    <div class="card"><h3>Total Job Seekers</h3><div class="num"><?php echo admin_e($jobSeekers); ?></div></div>
    <div class="card"><h3>Unemployed</h3><div class="num"><?php echo admin_e($unemployed); ?></div><span class="muted"><?php echo admin_e($unemploymentRate); ?>% of seekers</span></div>
    <div class="card"><h3>Employed</h3><div class="num"><?php echo admin_e($employed); ?></div></div>
    <div class="card"><h3>Training</h3><div class="num"><?php echo admin_e($training); ?></div></div>
    <div class="card"><h3>Self-employed</h3><div class="num"><?php echo admin_e($selfEmployed); ?></div></div>
    <div class="card"><h3>Job Posts</h3><div class="num"><?php echo admin_e($jobPosts); ?></div></div>
    <div class="card"><h3>Applications</h3><div class="num"><?php echo admin_e($applications); ?></div></div>
    <div class="card"><h3>Training Need Score</h3><div class="num"><?php echo admin_e($trainingNeedScore); ?>/100</div><span class="muted">Opportunity ratio: <?php echo admin_e($opportunityRatio); ?></span></div>
</div>

<div class="grid">
    <div class="card">
        <h2>Government / NGO Suggested Action</h2>
        <?php foreach ($recommendations as $rec): ?>
            <p><span class="badge">Action</span> <?php echo admin_e($rec); ?></p>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h2>Top Available Skills</h2>
        <?php if ($availableSkills): foreach ($availableSkills as $row): ?>
            <p><span class="badge"><?php echo admin_e($row['total']); ?></span> <?php echo admin_e($row['skill_name']); ?></p>
        <?php endforeach; else: ?><p class="muted">No skill supply found for this filter.</p><?php endif; ?>
    </div>
    <div class="card">
        <h2>Top Demanded Skills / Jobs</h2>
        <?php if ($demandedSkills): foreach ($demandedSkills as $row): ?>
            <p><span class="badge"><?php echo admin_e($row['total']); ?></span> <?php echo admin_e($row['skill_name']); ?></p>
        <?php endforeach; else: ?><p class="muted">No skill demand found for this filter.</p><?php endif; ?>
    </div>
</div>

<table>
    <thead><tr><th>Skill</th><th>Supply</th><th>Demand</th><th>Gap</th><th>Recommendation</th></tr></thead>
    <tbody>
    <?php if ($allSkillNames): foreach ($allSkillNames as $name):
        $supply = $supplyBySkill[$name] ?? 0;
        $demand = $demandBySkill[$name] ?? 0;
        $gap = $demand - $supply;
        $message = $gap > 0 ? 'Training program recommended' : ($supply > $demand ? 'Employer/SME support recommended' : 'Job matching campaign recommended');
    ?>
        <tr>
            <td><?php echo admin_e($name); ?></td>
            <td><?php echo admin_e($supply); ?></td>
            <td><?php echo admin_e($demand); ?></td>
            <td><span class="badge"><?php echo admin_e($gap); ?></span></td>
            <td><?php echo admin_e($message); ?></td>
        </tr>
    <?php endforeach; else: ?>
        <tr><td colspan="5" class="muted">No supply/demand comparison available for this filter.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php admin_footer(); ?>
