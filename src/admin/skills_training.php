<?php
include_once('admin_bootstrap.php');

$topSupply = admin_rows($conn, "SELECT skill_name, supply_count FROM vw_skill_supply ORDER BY supply_count DESC LIMIT 20");
$topDemand = admin_rows($conn, "SELECT skill_name, demand_count FROM vw_skill_demand ORDER BY demand_count DESC LIMIT 20");
$courses = admin_rows($conn, "SELECT title, provider_name, mode, status FROM training_courses ORDER BY course_id DESC LIMIT 20");

admin_header('Skills & Training');
?>
<div class="grid">
    <div class="card"><h3>Dictionary Skills</h3><div class="num"><?php echo admin_count($conn, "SELECT COUNT(*) AS total FROM dic_skills"); ?></div></div>
    <div class="card"><h3>User Skill Links</h3><div class="num"><?php echo admin_count($conn, "SELECT COUNT(*) AS total FROM job_seeker_skills"); ?></div></div>
    <div class="card"><h3>Training Courses</h3><div class="num"><?php echo admin_count($conn, "SELECT COUNT(*) AS total FROM training_courses"); ?></div></div>
</div>
<div class="grid">
    <div class="card">
        <h2>Top Available Skills</h2>
        <?php foreach ($topSupply as $row): ?><p><span class="badge"><?php echo admin_e($row['supply_count']); ?></span> <?php echo admin_e($row['skill_name']); ?></p><?php endforeach; ?>
    </div>
    <div class="card">
        <h2>Top Demanded Skills</h2>
        <?php foreach ($topDemand as $row): ?><p><span class="badge"><?php echo admin_e($row['demand_count']); ?></span> <?php echo admin_e($row['skill_name']); ?></p><?php endforeach; ?>
    </div>
</div>
<table>
    <thead><tr><th>Course</th><th>Provider</th><th>Mode</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($courses as $c): ?><tr><td><?php echo admin_e($c['title']); ?></td><td><?php echo admin_e($c['provider_name']); ?></td><td><?php echo admin_e($c['mode']); ?></td><td><span class="badge"><?php echo admin_e($c['status']); ?></span></td></tr><?php endforeach; ?></tbody>
</table>
<?php admin_footer(); ?>
