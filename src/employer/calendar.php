<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

// Fetch all interviews for this employer
$q = $conn->query("SELECT i.interview_id, i.interview_title, i.interview_datetime, i.status, u.full_name, j.title as job_title 
                   FROM interviews i 
                   JOIN users u ON i.candidate_id = u.user_id 
                   LEFT JOIN jobs j ON i.job_id = j.job_id 
                   WHERE i.employer_id = $employer_id");

$events = [];
while ($row = $q->fetch_assoc()) {
    $color = '#f39c12'; // Yellow for proposed/pending
    if ($row['status'] == 'scheduled') $color = '#27ae60'; // Green
    if ($row['status'] == 'cancelled' || $row['status'] == 'rejected') $color = '#e74c3c'; // Red
    if ($row['status'] == 'completed' || $row['status'] == 'selected') $color = '#3498db'; // Blue
    
    $events[] = [
        'id' => $row['interview_id'],
        'title' => $row['full_name'] . ' - ' . ($row['job_title'] ?: $row['interview_title']),
        'start' => date('Y-m-d\TH:i:s', strtotime($row['interview_datetime'])),
        'color' => $color,
        'extendedProps' => [
            'status' => ucfirst($row['status']),
            'job' => $row['job_title'] ?: $row['interview_title'],
            'candidate' => $row['full_name']
        ]
    ];
}
$events_json = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Interview Calendar - Jibika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <style>
        #calendar {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .fc-event { cursor: pointer; }
    </style>
</head>
<body class="bg-light">
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa fa-calendar"></i> <?php echo $lang == 'bn' ? 'সাক্ষাৎকার ক্যালেন্ডার' : 'Interview Calendar'; ?></h2>
        <div>
            <span class="badge bg-success me-2">Scheduled</span>
            <span class="badge bg-warning text-dark me-2">Proposed/Pending</span>
            <span class="badge bg-danger">Cancelled</span>
        </div>
    </div>
    
    <div id="calendar"></div>
</div>

<!-- Modal for Event Details -->
<div class="modal fade" id="eventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Interview Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h5 id="modalCandidate" class="text-primary"></h5>
        <p class="mb-1"><strong>Job:</strong> <span id="modalJob"></span></p>
        <p class="mb-1"><strong>Date & Time:</strong> <span id="modalTime"></span></p>
        <p class="mb-3"><strong>Status:</strong> <span id="modalStatus" class="badge bg-secondary"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="#" id="modalLink" class="btn btn-primary">View Applicant</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var events = <?php echo $events_json; ?>;
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: events,
        eventClick: function(info) {
            var props = info.event.extendedProps;
            document.getElementById('modalCandidate').innerText = props.candidate;
            document.getElementById('modalJob').innerText = props.job;
            document.getElementById('modalStatus').innerText = props.status;
            
            // Format time nicely
            var d = info.event.start;
            document.getElementById('modalTime').innerText = d.toLocaleString();
            
            // Link to applicant
            document.getElementById('modalLink').href = "applicants.php"; 
            
            var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
            myModal.show();
        }
    });
    calendar.render();
});
</script>

<?php include('../includes/footer.php'); ?>
</body>
</html>
