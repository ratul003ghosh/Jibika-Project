<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
$lang = $_SESSION['lang'] ?? 'bn';
$user_id = $_SESSION['user_id'];

// Handle Applicant Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $app_id = intval($_POST['application_id']);
    
    // Verify ownership
    $verify = $conn->query("SELECT status, rejection_count FROM applications WHERE application_id=$app_id AND user_id=$user_id");
    if ($verify->num_rows > 0) {
        $app = $verify->fetch_assoc();
        
        if ($_POST['action'] == 'accept_interview') {
            $conn->query("UPDATE applications SET status='Interview Scheduled' WHERE application_id=$app_id");
            $conn->query("UPDATE interviews SET status='scheduled' WHERE application_id=$app_id");
            $msg = "Interview officially accepted and scheduled.";
            
        } elseif ($_POST['action'] == 'reject_interview') {
            $new_count = $app['rejection_count'] + 1;
            if ($new_count >= 2) {
                // Permanent Rejection
                $conn->query("UPDATE applications SET status='Rejected', rejection_count=$new_count WHERE application_id=$app_id");
                $conn->query("UPDATE interviews SET status='rejected' WHERE application_id=$app_id");
                $msg = "You have permanently rejected this job application.";
            } else {
                // First Rejection
                $conn->query("UPDATE applications SET status='Interview Cancelled', rejection_count=$new_count WHERE application_id=$app_id");
                $conn->query("UPDATE interviews SET status='cancelled' WHERE application_id=$app_id");
                $msg = "Interview rejected. You have 1 rejection left before permanent disqualification.";
            }
            
        } elseif ($_POST['action'] == 'suggest_time') {
            $suggested_time = $conn->real_escape_string($_POST['suggested_time']);
            $new_count = $app['rejection_count'] + 1;
            
            if ($new_count >= 2) {
                 $conn->query("UPDATE applications SET status='Rejected', rejection_count=$new_count WHERE application_id=$app_id");
                 $conn->query("UPDATE interviews SET status='rejected' WHERE application_id=$app_id");
                 $msg = "Maximum negotiations reached. Application rejected.";
            } else {
                 $conn->query("UPDATE applications SET status='Pending', rejection_count=$new_count, suggested_datetime='$suggested_time' WHERE application_id=$app_id");
                 $conn->query("UPDATE interviews SET status='reschedule_requested' WHERE application_id=$app_id");
                 $msg = "Alternative time suggested to the employer.";
            }
        }
    }
}

// 1. Dashboard Statistics
$stats_q = $conn->query("SELECT 
    COUNT(*) as total_applied,
    SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='Under Review' THEN 1 ELSE 0 END) as under_review,
    SUM(CASE WHEN status='Interview Scheduled' THEN 1 ELSE 0 END) as interview,
    SUM(CASE WHEN status='Selected' OR status='Accepted' THEN 1 ELSE 0 END) as selected,
    SUM(CASE WHEN status='Rejected' THEN 1 ELSE 0 END) as rejected
    FROM applications WHERE user_id = $user_id");
$stats = $stats_q->fetch_assoc();

// 2. Application History (Join with Interviews)
$history_q = $conn->query("SELECT 
    a.application_id, a.rejection_count, j.title, ep.company_name, a.applied_at, a.status as app_status,
    i.status as int_status, i.interview_datetime, i.interview_type
    FROM applications a
    JOIN jobs j ON a.job_id = j.job_id
    JOIN employer_profiles ep ON j.employer_id = ep.user_id
    LEFT JOIN interviews i ON a.application_id = i.application_id
    WHERE a.user_id = $user_id
    ORDER BY a.applied_at DESC");

// 3. Analytics (Success Rate)
$success_rate = ($stats['total_applied'] > 0) ? round(($stats['selected'] / $stats['total_applied']) * 100, 1) : 0;
$interview_rate = ($stats['total_applied'] > 0) ? round(($stats['interview'] / $stats['total_applied']) * 100, 1) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Application Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <h2>Application Tracking Dashboard</h2>
    
    <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-primary"><h4><?php echo $stats['total_applied']; ?></h4>Total Applied</div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-warning"><h4><?php echo $stats['pending']; ?></h4>Pending</div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-info"><h4><?php echo $stats['interview']; ?></h4>Interviews</div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-success"><h4><?php echo $stats['selected']; ?></h4>Selected</div></div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Success Rate: <span class="text-success"><?php echo $success_rate; ?>%</span></h5>
                <h5>Interview Rate: <span class="text-info"><?php echo $interview_rate; ?>%</span></h5>
            </div>
        </div>
    </div>

    <h4 class="mt-5">Application History</h4>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Applied Date</th>
                <th>Status</th>
                <th>Interview Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $history_q->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                <td><?php echo date('d M Y', strtotime($row['applied_at'])); ?></td>
                <td><span class="badge bg-secondary"><?php echo $row['app_status']; ?></span></td>
                <td>
                    <?php if($row['int_status']): ?>
                        <span class="badge bg-info"><?php echo $row['int_status']; ?></span>
                        <br><small><?php echo date('d M Y, h:i A', strtotime($row['interview_datetime'])); ?> (<?php echo $row['interview_type']; ?>)</small>
                        
                        <?php if($row['app_status'] == 'Interview Proposed'): ?>
                            <div class="mt-2 p-2 border rounded bg-white">
                                <p class="mb-1 fw-bold small">Employer proposed this time. Respond:</p>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                    <button type="submit" name="action" value="accept_interview" class="btn btn-sm btn-success w-100 mb-1">Accept</button>
                                    <button type="submit" name="action" value="reject_interview" class="btn btn-sm btn-danger w-100 mb-1" onclick="return confirm('Reject this interview?');">Reject</button>
                                </form>
                                <?php if($row['rejection_count'] < 1): ?>
                                    <form method="POST" class="mt-1">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <input type="hidden" name="action" value="suggest_time">
                                        <div class="input-group input-group-sm">
                                            <input type="datetime-local" name="suggested_time" class="form-control" required>
                                            <button class="btn btn-outline-primary" type="submit">Suggest</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <small class="text-muted">Not Scheduled</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
