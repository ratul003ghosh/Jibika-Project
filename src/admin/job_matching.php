<?php
include_once('admin_bootstrap.php');

$skillGap = admin_rows($conn, "SELECT skill_name, supply_count, demand_count, gap_count FROM vw_skill_gap ORDER BY gap_count DESC, demand_count DESC LIMIT 30");
$matches = admin_rows($conn, "
    SELECT DISTINCT u.full_name, j.title, d.district_name, ds.skill_name
    FROM job_seeker_skills jss
    JOIN dic_skills ds ON jss.skill_id = ds.skill_id
    JOIN job_required_skills jrs ON ds.skill_id = jrs.skill_id
    JOIN jobs j ON jrs.job_id = j.job_id
    JOIN users u ON jss.user_id = u.user_id
    LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
    LEFT JOIN districts d ON jsp.district_id = d.district_id
    WHERE j.status = 'active'
    ORDER BY j.created_at DESC
    LIMIT 50
");

admin_header('Job Matching');
?>
<div class="grid">
    <div class="card"><h3>Skill Gap Rows</h3><div class="num"><?php echo count($skillGap); ?></div></div>
    <div class="card"><h3>Recent Skill Matches</h3><div class="num"><?php echo count($matches); ?></div></div>
</div>
<table>
    <thead><tr><th>Skill</th><th>Supply</th><th>Demand</th><th>Gap</th></tr></thead>
    <tbody><?php foreach ($skillGap as $s): ?><tr><td><?php echo admin_e($s['skill_name']); ?></td><td><?php echo admin_e($s['supply_count']); ?></td><td><?php echo admin_e($s['demand_count']); ?></td><td><span class="badge"><?php echo admin_e($s['gap_count']); ?></span></td></tr><?php endforeach; ?></tbody>
</table>
<br>
<table>
    <thead><tr><th>Job Seeker</th><th>Matched Job</th><th>District</th><th>Matching Skill</th></tr></thead>
    <tbody><?php foreach ($matches as $m): ?><tr><td><?php echo admin_e($m['full_name']); ?></td><td><?php echo admin_e($m['title']); ?></td><td><?php echo admin_e($m['district_name']); ?></td><td><?php echo admin_e($m['skill_name']); ?></td></tr><?php endforeach; ?></tbody>
</table>
<?php admin_footer(); ?>
