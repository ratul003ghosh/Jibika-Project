<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';
$application_id = isset($_GET['application_id']) ? intval($_GET['application_id']) : 0;

// ── Translations ──
$siText = [
    'bn' => [
        'page_title' => 'সাক্ষাৎকার নির্ধারণ - জীবিকা',
        'heading' => 'সাক্ষাৎকার নির্ধারণ করুন',
        'candidate' => 'প্রার্থী:',
        'role' => 'পদ:',
        'lbl_title' => 'সাক্ষাৎকারের শিরোনাম',
        'lbl_type' => 'ধরন',
        'type_online' => 'অনলাইন',
        'type_offline' => 'অফলাইন / সশরীর',
        'lbl_datetime' => 'তারিখ ও সময়',
        'lbl_location' => 'স্থান (অফলাইন হলে)',
        'ph_location' => 'যেমন: প্রধান কার্যালয়, ৫ম তলা',
        'lbl_link' => 'মিটিং লিঙ্ক (অনলাইন হলে)',
        'ph_link' => 'https://zoom.us/j/...',
        'lbl_notes' => 'প্রার্থীর জন্য নির্দেশনা / মন্তব্য',
        'ph_notes' => 'যেমন: আপনার সিভির একটি কপি সাথে আনুন...',
        'btn_cancel' => 'বাতিল',
        'btn_schedule' => 'সাক্ষাৎকার নির্ধারণ করুন',
        'initial_title' => 'প্রাথমিক সাক্ষাৎকার - ',
        'err_invalid' => 'অবৈধ আবেদন অথবা আপনার অনুমতি নেই।',
        'err_state_locked' => '<strong>অবস্থা লক করা আছে:</strong> এই আবেদনের জন্য ইতিমধ্যে একটি সক্রিয় সাক্ষাৎকার প্রক্রিয়া চলছে। পুনরায় নির্ধারণ নিষিদ্ধ।',
        'err_conflict' => '<strong>সময়সূচি সংঘর্ষ:</strong> আপনার ইতিমধ্যে একটি সাক্ষাৎকার নির্ধারিত আছে',
        'err_conflict_at' => 'সময়ে। অনুগ্রহ করে আপনার',
        'err_conflict_cal' => 'ক্যালেন্ডার',
        'err_conflict_end' => 'দেখুন এবং অন্য সময় নির্বাচন করুন।',
        'err_db' => 'সাক্ষাৎকার নির্ধারণে ত্রুটি: ',
        'notif_title' => 'সাক্ষাৎকার প্রস্তাবিত',
        'notif_msg' => 'নিয়োগকর্তা আপনার জন্য একটি সাক্ষাৎকার প্রস্তাব করেছেন। অনুগ্রহ করে পর্যালোচনা করে গ্রহণ করুন।',
        'month_names' => ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'],
    ],
    'en' => [
        'page_title' => 'Schedule Interview - Jibika',
        'heading' => 'Schedule Interview',
        'candidate' => 'Candidate:',
        'role' => 'Role:',
        'lbl_title' => 'Interview Title',
        'lbl_type' => 'Type',
        'type_online' => 'Online',
        'type_offline' => 'Offline / In-person',
        'lbl_datetime' => 'Date & Time',
        'lbl_location' => 'Location (If Offline)',
        'ph_location' => 'e.g. Head Office, Floor 5',
        'lbl_link' => 'Meeting Link (If Online)',
        'ph_link' => 'https://zoom.us/j/...',
        'lbl_notes' => 'Instructions / Notes to Candidate',
        'ph_notes' => 'e.g. Please bring a copy of your CV...',
        'btn_cancel' => 'Cancel',
        'btn_schedule' => 'Schedule Interview',
        'initial_title' => 'Initial Interview for ',
        'err_invalid' => 'Invalid application or you do not have permission.',
        'err_state_locked' => '<strong>STATE LOCKED:</strong> This application already has an active interview scheduling process. Duplicate scheduling is prohibited.',
        'err_conflict' => '<strong>CRITICAL CONFLICT DETECTED:</strong> You already have an interview scheduled',
        'err_conflict_at' => 'at',
        'err_conflict_cal' => 'Calendar',
        'err_conflict_end' => 'and select an alternative time.',
        'err_db' => 'Error scheduling interview: ',
        'notif_title' => 'Interview Proposed',
        'notif_msg' => 'The employer has proposed an interview. Please review and accept.',
        'month_names' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    ]
];
$ct = $siText[$lang];

$candidate_id = isset($_GET['candidate_id']) ? intval($_GET['candidate_id']) : 0;
$app_data = null;

if ($application_id > 0) {
    // Verify ownership for applicant interview
    $q = $conn->query("SELECT a.job_id, a.status as app_status, u.user_id as candidate_id, u.full_name, j.title 
                       FROM applications a 
                       JOIN jobs j ON a.job_id = j.job_id 
                       JOIN users u ON a.user_id = u.user_id
                       WHERE a.application_id = $application_id AND j.employer_id = $employer_id");
    if ($q && $q->num_rows > 0) {
        $app_data = $q->fetch_assoc();
    }
} elseif ($candidate_id > 0) {
    // Verify candidate exists for partner finder / direct invite interview
    $q = $conn->query("SELECT user_id as candidate_id, full_name FROM users WHERE user_id = $candidate_id AND role = 'job_seeker'");
    if ($q && $q->num_rows > 0) {
        $cand = $q->fetch_assoc();
        $app_data = [
            'job_id' => 0,
            'app_status' => 'Pending',
            'candidate_id' => $cand['candidate_id'],
            'full_name' => $cand['full_name'],
            'title' => $lang == 'bn' ? 'অংশীদার বা সরাসরি আমন্ত্রণ' : 'Partner Finder / Direct Invitation'
        ];
    }
}

if (!$app_data) {
    die($ct['err_invalid']);
}
$cancel_url = ($application_id > 0) ? 'applicants.php' : 'partner_finder.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $conn->real_escape_string($_POST['type']);
    $title = $conn->real_escape_string($_POST['title']);
    $datetime = $conn->real_escape_string($_POST['datetime']);
    $location = $conn->real_escape_string($_POST['location']);
    $link = $conn->real_escape_string($_POST['link']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    $job_id = $app_data['job_id'];
    $candidate_id = $app_data['candidate_id'];
    
    // STRICT STATE LOCKING
    if ($app_data['app_status'] == 'Interview Scheduled' || $app_data['app_status'] == 'Interview Proposed') {
        $error = $ct['err_state_locked'];
    } else {
    
    // CALENDAR CONFLICT PREVENTION
    $conflict_q = $conn->query("SELECT interview_id, interview_title, interview_datetime FROM interviews 
                                WHERE employer_id = $employer_id 
                                AND status IN ('scheduled', 'proposed')
                                AND ABS(TIMESTAMPDIFF(MINUTE, interview_datetime, '$datetime')) < 60");
    
    if ($conflict_q && $conflict_q->num_rows > 0) {
        $conflict = $conflict_q->fetch_assoc();
        $conf_date = date('d', strtotime($conflict['interview_datetime']));
        $conf_month = $ct['month_names'][intval(date('n', strtotime($conflict['interview_datetime']))) - 1];
        $conf_year = date('Y', strtotime($conflict['interview_datetime']));
        $conf_time = date('h:i A', strtotime($conflict['interview_datetime']));
        if ($lang == 'bn') {
            $conf_date = translateNumber($conf_date, 'bn');
            $conf_year = translateNumber($conf_year, 'bn');
            $conf_time = str_replace(['AM', 'PM'], ['পূর্বাহ্ন', 'অপরাহ্ন'], $conf_time);
            $conf_time = translateNumber($conf_time, 'bn');
        }
        $formatted_dt = "$conf_date $conf_month $conf_year, $conf_time";
        $error = "{$ct['err_conflict']} ('{$conflict['interview_title']}') {$ct['err_conflict_at']} $formatted_dt। <a href='calendar.php' class='alert-link'>{$ct['err_conflict_cal']}</a> {$ct['err_conflict_end']}";
    } else {
        $job_val = $job_id > 0 ? $job_id : "NULL";
        $app_val = $application_id > 0 ? $application_id : "NULL";

        $sql = "INSERT INTO interviews (job_id, application_id, employer_id, candidate_id, interview_type, interview_title, interview_datetime, interview_location, meeting_link, notes, status) 
                VALUES ($job_val, $app_val, $employer_id, $candidate_id, '$type', '$title', '$datetime', '$location', '$link', '$notes', 'proposed')";
                
        if ($conn->query($sql)) {
            if ($application_id > 0) {
                // Update application status
                $conn->query("UPDATE applications SET status='Interview Proposed' WHERE application_id=$application_id");
            }
            
            // Notify Candidate
            $notif_title_esc = $conn->real_escape_string($ct['notif_title']);
            $notif_msg_esc = $conn->real_escape_string($ct['notif_msg']);
            $job_notif_val = $job_id > 0 ? $job_id : "NULL";
            
            $conn->query("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, type, link) 
                          VALUES ($candidate_id, $job_notif_val, '$notif_title_esc', '$notif_title_esc', '$notif_msg_esc', '$notif_msg_esc', 'info', 'jobseeker/application_tracking.php')");
            
            header("Location: calendar.php?msg=interview_proposed");
            exit();
        } else {
            $error = $ct['err_db'] . $conn->error;
        }
    }
}
}
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<style>
    .schedule-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .schedule-card .card-header {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        padding: 24px 30px;
        border: none;
    }
    .schedule-card .card-body {
        padding: 30px;
    }
    .schedule-card .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .schedule-card .form-control,
    .schedule-card .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .schedule-card .form-control:focus,
    .schedule-card .form-select:focus {
        border-color: #006a4e;
        box-shadow: 0 0 0 3px rgba(0, 106, 78, 0.12);
    }
    .candidate-badge {
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }
    .btn-schedule {
        background: linear-gradient(135deg, #00563f, #006a4e);
        border: none;
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-schedule:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 106, 78, 0.25);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card schedule-card">
                <div class="card-header text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-calendar-check me-2"></i><?php echo $ct['heading']; ?></h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                    
                    <div class="candidate-badge d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark"><?php echo $ct['candidate']; ?> <span class="text-primary"><?php echo htmlspecialchars($app_data['full_name']); ?></span></h5>
                            <p class="mb-0 text-muted"><?php echo $ct['role']; ?> <?php echo htmlspecialchars(translateJobTitle($app_data['title'], $lang)); ?></p>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $ct['lbl_title']; ?></label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($ct['initial_title'] . translateJobTitle($app_data['title'], $lang)); ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><?php echo $ct['lbl_type']; ?></label>
                                <select name="type" class="form-select" required>
                                    <option value="online"><?php echo $ct['type_online']; ?></option>
                                    <option value="offline"><?php echo $ct['type_offline']; ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo $ct['lbl_datetime']; ?></label>
                                <input type="datetime-local" name="datetime" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo $ct['lbl_location']; ?></label>
                            <input type="text" name="location" class="form-control" placeholder="<?php echo $ct['ph_location']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo $ct['lbl_link']; ?></label>
                            <input type="url" name="link" class="form-control" placeholder="<?php echo $ct['ph_link']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><?php echo $ct['lbl_notes']; ?></label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="<?php echo $ct['ph_notes']; ?>"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo $cancel_url; ?>" class="btn btn-outline-secondary rounded-pill px-4"><?php echo $ct['btn_cancel']; ?></a>
                            <button type="submit" class="btn btn-schedule text-white px-4"><i class="fa-solid fa-calendar-plus me-2"></i><?php echo $ct['btn_schedule']; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
