<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: ../login.php");
    exit();
}

include('../assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';
$user_id = $_SESSION['user_id'];

// Translation Dictionary
$ui = [
    'title' => ['en' => 'Application Tracking Dashboard', 'bn' => 'আবেদন ট্র্যাকিং ড্যাশবোর্ড'],
    'total_applied' => ['en' => 'Total Applied', 'bn' => 'মোট আবেদন'],
    'pending' => ['en' => 'Pending', 'bn' => 'অপেক্ষমাণ'],
    'interviews' => ['en' => 'Interviews', 'bn' => 'সাক্ষাৎকার'],
    'selected' => ['en' => 'Selected', 'bn' => 'নির্বাচিত'],
    'success_rate' => ['en' => 'Success Rate:', 'bn' => 'সাফল্যের হার:'],
    'interview_rate' => ['en' => 'Interview Rate:', 'bn' => 'সাক্ষাৎকারের হার:'],
    'app_history' => ['en' => 'Application History', 'bn' => 'আবেদনের ইতিহাস'],
    'th_job' => ['en' => 'Job Title', 'bn' => 'চাকরির পদ'],
    'th_comp' => ['en' => 'Company', 'bn' => 'কোম্পানি'],
    'th_date' => ['en' => 'Applied Date', 'bn' => 'আবেদনের তারিখ'],
    'th_status' => ['en' => 'Status', 'bn' => 'অবস্থা'],
    'th_int' => ['en' => 'Interview Details', 'bn' => 'সাক্ষাৎকারের বিস্তারিত'],
    'not_sched' => ['en' => 'Not Scheduled', 'bn' => 'নির্ধারিত নয়'],
    'emp_proposed' => ['en' => 'Employer proposed this time. Respond:', 'bn' => 'নিয়োগকর্তা এই সময় প্রস্তাব করেছেন। উত্তর দিন:'],
    'btn_acc' => ['en' => 'Accept', 'bn' => 'গ্রহণ করুন'],
    'btn_rej' => ['en' => 'Reject', 'bn' => 'প্রত্যাখ্যান করুন'],
    'btn_sug' => ['en' => 'Suggest', 'bn' => 'প্রস্তাব করুন']
];


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
    a.application_id, a.rejection_count, j.job_id, j.title, ep.company_name, a.applied_at, a.status as app_status,
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
    <title><?php echo $ui['title'][$lang]; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <h2><?php echo $ui['title'][$lang]; ?></h2>
    
    <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-primary"><h4><?php echo translateNumber($stats['total_applied'], $lang); ?></h4><?php echo $ui['total_applied'][$lang]; ?></div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-warning"><h4><?php echo translateNumber($stats['pending'], $lang); ?></h4><?php echo $ui['pending'][$lang]; ?></div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-info"><h4><?php echo translateNumber($stats['interview'], $lang); ?></h4><?php echo $ui['interviews'][$lang]; ?></div></div>
        <div class="col-md-3"><div class="card text-center p-3 text-white bg-success"><h4><?php echo translateNumber($stats['selected'], $lang); ?></h4><?php echo $ui['selected'][$lang]; ?></div></div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5><?php echo $ui['success_rate'][$lang]; ?> <span class="text-success"><?php echo translateNumber($success_rate, $lang); ?>%</span></h5>
                <h5><?php echo $ui['interview_rate'][$lang]; ?> <span class="text-info"><?php echo translateNumber($interview_rate, $lang); ?>%</span></h5>
            </div>
        </div>
    </div>

    <h4 class="mt-5"><?php echo $ui['app_history'][$lang]; ?></h4>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th><?php echo $ui['th_job'][$lang]; ?></th>
                <th><?php echo $ui['th_comp'][$lang]; ?></th>
                <th><?php echo $ui['th_date'][$lang]; ?></th>
                <th><?php echo $ui['th_status'][$lang]; ?></th>
                <th><?php echo $ui['th_int'][$lang]; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $history_q->fetch_assoc()): ?>
            <tr>
                <td><a href="../job_details.php?id=<?php echo $row['job_id']; ?>" class="text-decoration-none fw-bold"><?php echo htmlspecialchars($row['title']); ?></a></td>
                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                <td><?php echo date('d M Y', strtotime($row['applied_at'])); ?></td>
                <td>
                    <?php 
                        $status_class = 'bg-secondary';
                        if ($row['app_status'] == 'Pending') $status_class = 'bg-warning text-dark';
                        elseif ($row['app_status'] == 'Under Review') $status_class = 'bg-info text-dark';
                        elseif ($row['app_status'] == 'Selected') $status_class = 'bg-success';
                        elseif ($row['app_status'] == 'Rejected') $status_class = 'bg-danger';
                        elseif ($row['app_status'] == 'Interview Scheduled') $status_class = 'bg-primary';
                        elseif ($row['app_status'] == 'Interview Cancelled') $status_class = 'bg-dark';
                    ?>
                    <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['app_status']); ?></span>
                </td>
                <td>
                    <?php if($row['int_status']): ?>
                        <span class="badge bg-info"><?php echo htmlspecialchars($row['int_status']); ?></span>
                        <br><small><?php echo translateNumber(date('d M Y, h:i A', strtotime($row['interview_datetime'])), $lang); ?> (<?php echo htmlspecialchars($row['interview_type']); ?>)</small>
                        
                        <?php if($row['app_status'] == 'Interview Proposed' || $row['app_status'] == 'Interview Scheduled'): ?>
                            <div class="mt-2 p-2 border rounded bg-white">
                                <p class="mb-1 fw-bold small"><?php echo $ui['emp_proposed'][$lang]; ?></p>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                    <button type="submit" name="action" value="accept_interview" class="btn btn-sm btn-success w-100 mb-1"><?php echo $ui['btn_acc'][$lang]; ?></button>
                                    <button type="submit" name="action" value="reject_interview" class="btn btn-sm btn-danger w-100 mb-1" onclick="return confirm('Reject this interview?');"><?php echo $ui['btn_rej'][$lang]; ?></button>
                                </form>
                                <?php if($row['rejection_count'] < 1): ?>
                                    <form method="POST" class="mt-1">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <input type="hidden" name="action" value="suggest_time">
                                        <div class="input-group input-group-sm">
                                            <input type="datetime-local" name="suggested_time" class="form-control" required>
                                            <button class="btn btn-outline-primary" type="submit"><?php echo $ui['btn_sug'][$lang]; ?></button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <small class="text-muted"><?php echo $ui['not_sched'][$lang]; ?></small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
