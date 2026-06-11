<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
$employer_id = $_SESSION['user_id'];
$application_id = isset($_GET['application_id']) ? intval($_GET['application_id']) : 0;

// Verify ownership
$q = $conn->query("SELECT a.job_id, a.status as app_status, u.user_id as candidate_id, u.full_name, j.title 
                   FROM applications a 
                   JOIN jobs j ON a.job_id = j.job_id 
                   JOIN users u ON a.user_id = u.user_id
                   WHERE a.application_id = $application_id AND j.employer_id = $employer_id");

if ($q->num_rows == 0) {
    die("Invalid application or you do not have permission.");
}
$app_data = $q->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $conn->real_escape_string($_POST['type']);
    $title = $conn->real_escape_string($_POST['title']);
    $datetime = $conn->real_escape_string($_POST['datetime']);
    $location = $conn->real_escape_string($_POST['location']);
    $link = $conn->real_escape_string($_POST['link']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    $job_id = $app_data['job_id'];
    $candidate_id = $app_data['candidate_id'];
    
    // STRICT STATE LOCKING (Phase 5)
    if ($app_data['app_status'] == 'Interview Scheduled' || $app_data['app_status'] == 'Interview Proposed') {
        $error = "<strong>STATE LOCKED:</strong> This application already has an active interview scheduling process. Duplicate scheduling is prohibited.";
    } else {
    
    // CALENDAR CONFLICT PREVENTION (Phase 12)
    // Check if employer has any interview within 1 hour of this time
    $conflict_q = $conn->query("SELECT interview_id, interview_title, interview_datetime FROM interviews 
                                WHERE employer_id = $employer_id 
                                AND status IN ('scheduled', 'proposed')
                                AND ABS(TIMESTAMPDIFF(MINUTE, interview_datetime, '$datetime')) < 60");
    
    if ($conflict_q->num_rows > 0) {
        $conflict = $conflict_q->fetch_assoc();
        $error = "<strong>CRITICAL CONFLICT DETECTED:</strong> You already have an interview ('{$conflict['interview_title']}') scheduled at " . date('d M Y, h:i A', strtotime($conflict['interview_datetime'])) . ". Please check your <a href='calendar.php' class='alert-link'>Calendar</a> and select an alternative time.";
    } else {
        $sql = "INSERT INTO interviews (job_id, application_id, employer_id, candidate_id, interview_type, interview_title, interview_datetime, interview_location, meeting_link, notes, status) 
                VALUES ($job_id, $application_id, $employer_id, $candidate_id, '$type', '$title', '$datetime', '$location', '$link', '$notes', 'proposed')";
                
        if ($conn->query($sql)) {
            // Update application status
            $conn->query("UPDATE applications SET status='Interview Proposed' WHERE application_id=$application_id");
            
            // Notify Candidate
            $note_message = $conn->real_escape_string("The employer has proposed an interview for " . ($app_data['title'] ?? 'your application') . ". Please review and accept.");
            $conn->query("INSERT INTO notifications (user_id, job_id, message, title_en, message_en, type, link) VALUES ($candidate_id, $job_id, '$note_message', 'Interview Proposed', '$note_message', 'info', 'jobseeker/application_tracking.php')");
            
            header("Location: calendar.php?msg=interview_proposed");
            exit();
        } else {
            $error = "Error scheduling interview: " . $conn->error;
        }
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Schedule Interview - Jibika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Schedule Interview</h4>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                    
                    <h5 class="mb-3">Candidate: <span class="text-primary"><?php echo htmlspecialchars($app_data['full_name']); ?></span></h5>
                    <h6 class="mb-4 text-muted">Role: <?php echo htmlspecialchars($app_data['title']); ?></h6>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Interview Title</label>
                            <input type="text" name="title" class="form-control" required value="Initial Interview for <?php echo htmlspecialchars($app_data['title']); ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline / In-person</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date & Time</label>
                                <input type="datetime-local" name="datetime" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Location (If Offline)</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Head Office, Floor 5">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meeting Link (If Online)</label>
                            <input type="url" name="link" class="form-control" placeholder="https://zoom.us/j/...">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Instructions / Notes to Candidate</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Please bring a copy of your CV..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="applicants.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Schedule Interview</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
