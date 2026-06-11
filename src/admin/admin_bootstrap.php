<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

include_once('../assets/config/db.php');

function admin_e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function admin_rows($conn, $sql) {
    $rows = [];
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function admin_count($conn, $sql) {
    $row = admin_rows($conn, $sql);
    return (int)($row[0]['total'] ?? 0);
}

function admin_header($title) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo admin_e($title); ?> - Jibika Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --green:#087443; --soft:#eef8f2; --line:#dbe7df; --text:#172033; --muted:#667085; }
        body { margin:0; font-family:Arial, sans-serif; background:#f6faf8; color:var(--text); }
        .layout { display:grid; grid-template-columns:250px 1fr; min-height:100vh; }
        .side { background:#fff; border-right:1px solid var(--line); padding:22px 16px; }
        .brand { color:var(--green); font-size:26px; font-weight:800; text-decoration:none; display:block; margin-bottom:6px; }
        .sub { color:var(--muted); font-size:12px; margin-bottom:20px; }
        .nav a { display:flex; gap:10px; align-items:center; padding:11px 12px; border-radius:10px; color:#145235; text-decoration:none; font-size:14px; margin-bottom:4px; }
        .nav a:hover, .nav a.active { background:var(--soft); color:var(--green); }
        main { padding:24px; }
        .top { display:flex; justify-content:space-between; gap:14px; align-items:center; margin-bottom:20px; }
        h1 { margin:0; font-size:28px; }
        .btn { border:0; background:var(--green); color:#fff; padding:10px 14px; border-radius:8px; text-decoration:none; display:inline-flex; gap:8px; align-items:center; cursor:pointer; }
        .btn.light { background:#fff; color:var(--green); border:1px solid var(--line); }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:14px; margin-bottom:18px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:18px; box-shadow:0 10px 24px rgba(16,24,40,.05); }
        .card h3 { margin:0 0 8px; font-size:14px; color:var(--muted); }
        .num { font-size:26px; font-weight:800; color:var(--green); }
        table { width:100%; border-collapse:collapse; background:#fff; border-radius:14px; overflow:hidden; border:1px solid var(--line); }
        th, td { padding:12px 14px; border-bottom:1px solid var(--line); text-align:left; font-size:14px; }
        th { background:var(--soft); color:#154734; }
        .muted { color:var(--muted); }
        .badge { display:inline-block; padding:5px 8px; border-radius:999px; background:var(--soft); color:var(--green); font-size:12px; }
        .filters { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
        .filters input, .filters select { padding:10px; border:1px solid var(--line); border-radius:8px; background:#fff; }
        @media (max-width:820px){ .layout{grid-template-columns:1fr}.side{position:static}.top{align-items:flex-start;flex-direction:column} }
    </style>
</head>
<body>
<div class="layout">
    <aside class="side">
        <a class="brand" href="dashboard.php">Jibika</a>
        <div class="sub">Connecting Skills, Creating Opportunities</div>
        <nav class="nav">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
            <a href="unemployed_details.php"><i class="fa-solid fa-user-graduate"></i> Job Seekers</a>
            <a href="users.php?role=employer"><i class="fa-solid fa-building"></i> Employers</a>
            <a href="jobs.php"><i class="fa-solid fa-briefcase"></i> Job Posts</a>
            <a href="applications.php"><i class="fa-solid fa-file-lines"></i> Applications</a>
            <a href="skills_training.php"><i class="fa-solid fa-medal"></i> Skills & Training</a>
            <a href="area_monitor.php"><i class="fa-solid fa-location-dot"></i> Area Monitor</a>
            <a href="job_matching.php"><i class="fa-solid fa-link"></i> Job Matching</a>
            <a href="reports.php"><i class="fa-solid fa-chart-simple"></i> Reports</a>
            <a href="users.php?verify=1"><i class="fa-solid fa-shield-halved"></i> Verifications</a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../index.php"><i class="fa-solid fa-globe"></i> Visit Main Site</a>
            <a href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </aside>
    <main>
        <div class="top">
            <div>
                <h1><?php echo admin_e($title); ?></h1>
                <div class="muted">Jibika admin management and DBMS reporting view</div>
            </div>
            <a class="btn light" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
        </div>
<?php
}

function admin_footer() {
?>
    </main>
</div>
</body>
</html>
<?php
}
?>
