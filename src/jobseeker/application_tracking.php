<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: ../auth/login.php");
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
    $verify = $conn->query("SELECT a.status, a.rejection_count, j.employer_id, j.title, j.job_id FROM applications a JOIN jobs j ON a.job_id=j.job_id WHERE a.application_id=$app_id AND a.user_id=$user_id");
    if ($verify && $verify->num_rows > 0) {
        $app = $verify->fetch_assoc();
        
        $emp_id = $app['employer_id'];
        $job_id = $app['job_id'];
        $job_title = $conn->real_escape_string($app['title']);
        $seeker_name = 'Candidate';
        $name_q = $conn->query("SELECT full_name FROM users WHERE user_id=$user_id");
        if ($name_q && $name_q->num_rows > 0) {
            $seeker_name = $conn->real_escape_string($name_q->fetch_assoc()['full_name']);
        }
        
        if ($_POST['action'] == 'accept_interview') {
            $conn->query("UPDATE applications SET status='Interview Scheduled' WHERE application_id=$app_id");
            $conn->query("UPDATE interviews SET status='scheduled' WHERE application_id=$app_id");
            $msg = "Interview officially accepted and scheduled.";
            
            // Notify Employer
            $notif_title_en = "Interview Invitation Accepted";
            $notif_title_bn = "সাক্ষাৎকার গ্রহণের আমন্ত্রণ গৃহীত";
            $notif_msg_en = "$seeker_name has accepted the interview for the role '$job_title'.";
            $notif_msg_bn = "$seeker_name '$job_title' পদের জন্য সাক্ষাৎকার গ্রহণের আমন্ত্রণ স্বীকার করেছেন।";
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($emp_id, $job_id, '$notif_title_en', '$notif_title_bn', '$notif_msg_en', '$notif_msg_bn', 'success', 'employer/calendar.php')");
            
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
            
            // Notify Employer
            $notif_title_en = "Interview Invitation Declined";
            $notif_title_bn = "সাক্ষাৎকার গ্রহণের আমন্ত্রণ প্রত্যাখ্যাত";
            $notif_msg_en = "$seeker_name has declined the interview for the role '$job_title'.";
            $notif_msg_bn = "$seeker_name '$job_title' পদের জন্য সাক্ষাৎকার গ্রহণের আমন্ত্রণ প্রত্যাখ্যান করেছেন।";
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($emp_id, $job_id, '$notif_title_en', '$notif_title_bn', '$notif_msg_en', '$notif_msg_bn', 'danger', 'employer/calendar.php')");
            
        } elseif ($_POST['action'] == 'suggest_time') {
            $suggested_time = $conn->real_escape_string($_POST['suggested_time']);
            $new_count = $app['rejection_count'] + 1;
            
            if ($new_count >= 2) {
                 $conn->query("UPDATE applications SET status='Rejected', rejection_count=$new_count WHERE application_id=$app_id");
                 $conn->query("UPDATE interviews SET status='rejected' WHERE application_id=$app_id");
                 $msg = "Maximum negotiations reached. Application rejected.";
                 
                 // Notify Employer
                 $notif_title_en = "Interview Invitation Declined (Max Negotiations)";
                 $notif_title_bn = "সাক্ষাৎকার প্রত্যাখ্যাত (সর্বোচ্চ আলোচনা সীমা অতিক্রম)";
                 $notif_msg_en = "$seeker_name has declined the interview for the role '$job_title'.";
                 $notif_msg_bn = "$seeker_name '$job_title' পদের জন্য সাক্ষাৎকার প্রত্যাখ্যাত করেছেন।";
            } else {
                 $conn->query("UPDATE applications SET status='Pending', rejection_count=$new_count, suggested_datetime='$suggested_time' WHERE application_id=$app_id");
                 $conn->query("UPDATE interviews SET status='reschedule_requested' WHERE application_id=$app_id");
                 $msg = "Alternative time suggested to the employer.";
                 
                 // Notify Employer of proposed reschedule
                 $notif_title_en = "Reschedule Requested";
                 $notif_title_bn = "সাক্ষাৎকার পুনঃনির্ধারণ অনুরোধ";
                 $notif_msg_en = "$seeker_name has proposed an alternative time for the role '$job_title': $suggested_time.";
                 $notif_msg_bn = "$seeker_name '$job_title' পদের সাক্ষাৎকারের জন্য একটি নতুন সময় প্রস্তাব করেছেন: $suggested_time।";
            }
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($emp_id, $job_id, '$notif_title_en', '$notif_title_bn', '$notif_msg_en', '$notif_msg_bn', 'warning', 'employer/calendar.php')");
        }
    }
}

// Handle Partner Interview Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['partner_action'])) {
    $int_id = intval($_POST['interview_id']);
    
    // Verify ownership
    $verify_partner = $conn->query("SELECT i.*, u.full_name as employer_name FROM interviews i JOIN users u ON i.employer_id=u.user_id WHERE i.interview_id=$int_id AND i.candidate_id=$user_id");
    if ($verify_partner && $verify_partner->num_rows > 0) {
        $partner_int = $verify_partner->fetch_assoc();
        $emp_id = $partner_int['employer_id'];
        $int_title = $conn->real_escape_string($partner_int['interview_title']);
        
        $seeker_name = 'Candidate';
        $name_q = $conn->query("SELECT full_name FROM users WHERE user_id=$user_id");
        if ($name_q && $name_q->num_rows > 0) {
            $seeker_name = $conn->real_escape_string($name_q->fetch_assoc()['full_name']);
        }

        if ($_POST['partner_action'] == 'accept_partner_interview') {
            $conn->query("UPDATE interviews SET status='scheduled' WHERE interview_id=$int_id");
            $msg = "Partner interview invitation accepted successfully.";
            
            // Notify Employer
            $notif_title_en = "Partner Interview Accepted";
            $notif_title_bn = "অংশীদার সাক্ষাৎকার আমন্ত্রণ গৃহীত";
            $notif_msg_en = "$seeker_name has accepted the partner interview for '$int_title'.";
            $notif_msg_bn = "$seeker_name '$int_title' এর জন্য অংশীদার সাক্ষাৎকার আমন্ত্রণ স্বীকার করেছেন।";
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($emp_id, NULL, '$notif_title_en', '$notif_title_bn', '$notif_msg_en', '$notif_msg_bn', 'success', 'employer/calendar.php')");
                          
        } elseif ($_POST['partner_action'] == 'reject_partner_interview') {
            $conn->query("UPDATE interviews SET status='rejected' WHERE interview_id=$int_id");
            $msg = "Partner interview invitation declined.";
            
            // Notify Employer
            $notif_title_en = "Partner Interview Declined";
            $notif_title_bn = "অংশীদার সাক্ষাৎকার আমন্ত্রণ প্রত্যাখ্যাত";
            $notif_msg_en = "$seeker_name has declined the partner interview for '$int_title'.";
            $notif_msg_bn = "$seeker_name '$int_title' এর জন্য অংশীদার সাক্ষাৎকার আমন্ত্রণ প্রত্যাখ্যান করেছেন।";
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($emp_id, NULL, '$notif_title_en', '$notif_title_bn', '$notif_msg_en', '$notif_msg_bn', 'danger', 'employer/calendar.php')");
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

// 2.1 Fetch Direct/Partner Finder Interviews
$partner_int_q = $conn->query("SELECT 
    i.interview_id, i.interview_title, i.interview_datetime, i.interview_type, i.status as int_status, i.interview_location, i.meeting_link, i.notes,
    ep.company_name, ep.user_id as employer_id
    FROM interviews i
    JOIN employer_profiles ep ON i.employer_id = ep.user_id
    WHERE i.candidate_id = $user_id AND (i.application_id IS NULL OR i.application_id = 0)
    ORDER BY i.interview_datetime DESC");

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

    <h4 class="mt-5">Partner Finder & Direct Interview Invitations</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-primary">
                <tr>
                    <th>Interview Title</th>
                    <th>Employer Company</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Details & Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($partner_int_q && $partner_int_q->num_rows > 0): ?>
                    <?php while($p_row = $partner_int_q->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($p_row['interview_title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p_row['company_name']); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($p_row['interview_datetime'])); ?> (<span class="text-uppercase"><?php echo htmlspecialchars($p_row['interview_type']); ?></span>)</td>
                        <td>
                            <?php 
                            $status_badge = 'bg-secondary';
                            if ($p_row['int_status'] == 'scheduled') $status_badge = 'bg-success';
                            elseif ($p_row['int_status'] == 'rejected') $status_badge = 'bg-danger';
                            elseif ($p_row['int_status'] == 'proposed') $status_badge = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?php echo $status_badge; ?>"><?php echo ucfirst(htmlspecialchars($p_row['int_status'])); ?></span>
                        </td>
                        <td>
                            <div class="small mb-2">
                                <?php if(!empty($p_row['interview_location'])): ?>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($p_row['interview_location']); ?><br>
                                <?php endif; ?>
                                <?php if(!empty($p_row['meeting_link'])): ?>
                                    <strong>Meeting Link:</strong> <a href="<?php echo htmlspecialchars($p_row['meeting_link']); ?>" target="_blank"><?php echo htmlspecialchars($p_row['meeting_link']); ?></a><br>
                                <?php endif; ?>
                                <?php if(!empty($p_row['notes'])): ?>
                                    <strong>Notes:</strong> <?php echo htmlspecialchars($p_row['notes']); ?><br>
                                <?php endif; ?>
                            </div>
                            <?php if($p_row['int_status'] == 'proposed'): ?>
                                <div class="mt-2 p-2 border rounded bg-white" style="max-width: 250px;">
                                    <p class="mb-1 fw-bold small">Respond to this Invitation:</p>
                                    <form method="POST">
                                        <input type="hidden" name="interview_id" value="<?php echo $p_row['interview_id']; ?>">
                                        <button type="submit" name="partner_action" value="accept_partner_interview" class="btn btn-sm btn-success w-100 mb-1">Accept</button>
                                        <button type="submit" name="partner_action" value="reject_partner_interview" class="btn btn-sm btn-danger w-100" onclick="return confirm('Decline this interview invitation?');">Decline</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No direct or partner interview invitations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
