<?php
include_once('admin_bootstrap.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setting_key'])) {
    $key = $conn->real_escape_string($_POST['setting_key']);
    $value = $conn->real_escape_string($_POST['setting_value'] ?? '');
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
}

$settings = admin_rows($conn, "SELECT setting_key, setting_value, updated_at FROM settings ORDER BY setting_key");

admin_header('Settings');
?>
<div class="card">
    <h2>Add / Update Setting</h2>
    <form class="filters" method="POST">
        <input name="setting_key" placeholder="Setting key" required>
        <input name="setting_value" placeholder="Setting value">
        <button class="btn" type="submit">Save</button>
    </form>
</div>
<br>
<table>
    <thead><tr><th>Key</th><th>Value</th><th>Updated</th></tr></thead>
    <tbody><?php foreach ($settings as $s): ?><tr><td><?php echo admin_e($s['setting_key']); ?></td><td><?php echo admin_e($s['setting_value']); ?></td><td><?php echo admin_e($s['updated_at']); ?></td></tr><?php endforeach; ?></tbody>
</table>
<?php admin_footer(); ?>
