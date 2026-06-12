<?php
include_once('admin_bootstrap.php');

$applicationStatuses = ['Pending','Under Review','Shortlisted','Interview Proposed','Interview Scheduled','Accepted','Rejected'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    $application_id = (int)$_POST['application_id'];
    $status = $_POST['status'];
    if (in_array($status, $applicationStatuses, true)) {
        $stmt = $conn->prepare("UPDATE applications SET status=? WHERE application_id=?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $application_id);
            $stmt->execute();
        }
    }
}

$statusFilter = $conn->real_escape_string($_GET['status'] ?? '');
$where = $statusFilter !== '' ? "WHERE a.status='$statusFilter'" : '';
$applications = admin_rows($conn, "
    SELECT a.application_id, a.status, a.applied_at, u.full_name AS applicant, j.title AS job_title, ep.company_name
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    JOIN jobs j ON a.job_id = j.job_id
    LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
    $where
    ORDER BY a.applied_at DESC
    LIMIT 100
");

admin_header('Applications');
?>
<form class="filters" method="GET">
    <select name="status">
        <option value="">All Statuses</option>
        <?php foreach ($applicationStatuses as $s): ?>
            <option value="<?php echo admin_e($s); ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo admin_e($s); ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filter</button>
    <a class="btn light" href="applications.php">Reset</a>
</form>
<table>
    <thead><tr><th>ID</th><th>Applicant</th><th>Job</th><th>Company</th><th>Status</th><th>Applied</th><th>Update</th></tr></thead>
    <tbody>
    <?php foreach ($applications as $a): ?>
        <tr>
            <td><?php echo admin_e($a['application_id']); ?></td>
            <td><?php echo admin_e($a['applicant']); ?></td>
            <td><?php echo admin_e($a['job_title']); ?></td>
            <td><?php echo admin_e($a['company_name'] ?: 'N/A'); ?></td>
            <td><span class="badge"><?php echo admin_e($a['status']); ?></span></td>
            <td><?php echo admin_e($a['applied_at']); ?></td>
            <td>
                <form method="POST" class="filters" style="margin:0">
                    <input type="hidden" name="application_id" value="<?php echo admin_e($a['application_id']); ?>">
                    <select name="status">
                        <?php foreach ($applicationStatuses as $s): ?>
                            <option value="<?php echo admin_e($s); ?>" <?php echo ($a['status'] ?? '') === $s ? 'selected' : ''; ?>><?php echo admin_e($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn light" type="submit">Save</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php admin_footer(); ?>
