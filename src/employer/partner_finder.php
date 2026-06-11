<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

// Handle Search & Filter
$where = ["u.role = 'job_seeker'"];
if (!empty($_GET['skills'])) {
    // Array of skills
    $skills = is_array($_GET['skills']) ? $_GET['skills'] : [$_GET['skills']];
    $skill_cond = [];
    foreach($skills as $s) {
        if (!empty($s)) {
            $es = $conn->real_escape_string($s);
            $skill_cond[] = "u.user_id IN (SELECT user_id FROM job_seeker_skills jss JOIN dic_skills ds ON jss.skill_id = ds.skill_id WHERE ds.skill_name LIKE '%$es%')";
        }
    }
    if(count($skill_cond) > 0) {
        $where[] = "(" . implode(' OR ', $skill_cond) . ")";
    }
}
if (!empty($_GET['education'])) {
    $e = $conn->real_escape_string($_GET['education']);
    $where[] = "u.user_id IN (SELECT user_id FROM seeker_education se JOIN dic_education_levels del ON se.level_id = del.level_id WHERE del.level_name LIKE '%$e%')";
}
if (!empty($_GET['location'])) {
    $l = $conn->real_escape_string($_GET['location']);
    $where[] = "d.district_name LIKE '%$l%'";
}
if (!empty($_GET['is_remote'])) {
    $where[] = "jsp.is_remote = 1";
}
if (!empty($_GET['availability_status'])) {
    $av = $conn->real_escape_string($_GET['availability_status']);
    $where[] = "jsp.availability_status = '$av'";
}
if (!empty($_GET['partner_type'])) {
    $pt = $conn->real_escape_string($_GET['partner_type']);
    $where[] = "jsp.partner_type = '$pt'";
}

$int_join = "";
if (!empty($_GET['interview_status'])) {
    $ist = $conn->real_escape_string($_GET['interview_status']);
    $int_join = "LEFT JOIN interviews i ON u.user_id = (SELECT a.user_id FROM applications a WHERE a.application_id = i.application_id LIMIT 1)";
    if ($ist == 'Not Interviewed') {
        $where[] = "(i.interview_id IS NULL OR i.employer_id != $employer_id)";
    } else {
        $where[] = "i.status = '$ist' AND i.employer_id = $employer_id";
    }
}

$where_clause = implode(' AND ', $where);

// Sorting logic
$order_by = "u.created_at DESC";
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] == 'experience_high') $order_by = "jsp.experience_years DESC";
    if ($_GET['sort'] == 'experience_low') $order_by = "jsp.experience_years ASC";
}

// Fetch Employer Profile Data for Recommendation
$emp_q = $conn->query("SELECT e.company_description, e.district_id, d.district_name as emp_location 
                       FROM employer_profiles e 
                       LEFT JOIN districts d ON e.district_id = d.district_id 
                       WHERE e.user_id = $employer_id");
$emp_data = $emp_q->fetch_assoc();

// Fetch Candidates
$q = $conn->query("SELECT DISTINCT u.user_id, u.full_name, u.email, jsp.experience_years, d.district_name as location, jsp.availability_status, jsp.is_remote,
                   (SELECT GROUP_CONCAT(ds.skill_name) FROM job_seeker_skills jss JOIN dic_skills ds ON jss.skill_id = ds.skill_id WHERE jss.user_id = u.user_id) as skills,
                   (SELECT del.level_name FROM seeker_education se JOIN dic_education_levels del ON se.level_id = del.level_id WHERE se.user_id = u.user_id LIMIT 1) as education
                   FROM users u 
                   LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id 
                   LEFT JOIN districts d ON jsp.district_id = d.district_id
                   $int_join
                   WHERE $where_clause 
                   ORDER BY $order_by LIMIT 100");

$candidates = [];
$is_ai_sort = (($_GET['sort'] ?? '') == 'highly_recommended');

while($row = $q->fetch_assoc()) {
    $score = 0;
    if ($is_ai_sort && $emp_data) {
        // Highly Recommended Scoring based on Employer's Company Data
        $emp_text = strtolower($emp_data['company_description'] ?? '');
        $cand_skills = array_map('strtolower', array_map('trim', explode(',', $row['skills'])));
        
        $match_count = 0;
        foreach ($cand_skills as $cs) {
            if ($cs != '' && strpos($emp_text, $cs) !== false) {
                $match_count++;
            }
        }
        $skill_score = count($cand_skills) > 0 ? min(70, ($match_count / max(1, count($cand_skills))) * 100) : 0;
        
        // Location matching
        $loc_score = 0;
        if (!empty($row['location']) && !empty($emp_data['emp_location']) && $row['location'] == $emp_data['emp_location']) {
            $loc_score = 30; // 30% boost for being in the same district
        }
        
        // Experience boost
        $exp_score = min(20, intval($row['experience_years']) * 4); // Max 20 points for 5+ years
        
        $score = min(100, round($skill_score + $loc_score + $exp_score, 1));
    }
    $row['ai_score'] = $score;
    $candidates[] = $row;
}

if ($is_ai_sort) {
    // Sort by AI Score descending
    usort($candidates, function($a, $b) {
        return $b['ai_score'] <=> $a['ai_score'];
    });
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Partner Finder - Jibika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .partner-card { transition: transform 0.2s, box-shadow 0.2s; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .partner-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .skill-badge { background: #e0f2fe; color: #0284c7; font-weight: 600; font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; margin-right: 4px; margin-bottom: 4px; display: inline-block; }
    </style>
</head>
<body class="bg-light">
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4 text-primary"><i class="fa-solid fa-users-viewfinder"></i> Partner Finder & Talent Search</h2>
    
    <!-- Advanced Search & Filter -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Skills / Expertise (Multi)</label>
                    <select name="skills[]" class="form-select form-select-sm" multiple style="height: 60px;">
                        <option value="PHP" <?php echo (in_array('PHP', $_GET['skills']??[]))?'selected':''; ?>>PHP</option>
                        <option value="Laravel" <?php echo (in_array('Laravel', $_GET['skills']??[]))?'selected':''; ?>>Laravel</option>
                        <option value="React" <?php echo (in_array('React', $_GET['skills']??[]))?'selected':''; ?>>React</option>
                        <option value="HTML" <?php echo (in_array('HTML', $_GET['skills']??[]))?'selected':''; ?>>HTML / CSS</option>
                        <option value="Sales" <?php echo (in_array('Sales', $_GET['skills']??[]))?'selected':''; ?>>Sales / Marketing</option>
                        <option value="Design" <?php echo (in_array('Design', $_GET['skills']??[]))?'selected':''; ?>>Graphic Design</option>
                        <option value="Communication" <?php echo (in_array('Communication', $_GET['skills']??[]))?'selected':''; ?>>Communication</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Education</label>
                    <select name="education" class="form-select form-select-sm">
                        <option value="">Any Education</option>
                        <option value="Bachelor" <?php echo ($_GET['education']??'')=='Bachelor'?'selected':''; ?>>Bachelor</option>
                        <option value="Masters" <?php echo ($_GET['education']??'')=='Masters'?'selected':''; ?>>Master</option>
                        <option value="Diploma" <?php echo ($_GET['education']??'')=='Diploma'?'selected':''; ?>>Diploma</option>
                        <option value="PhD" <?php echo ($_GET['education']??'')=='PhD'?'selected':''; ?>>PhD</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Location & Remote</label>
                    <select name="location" class="form-select form-select-sm mb-1">
                        <option value="">Any Location</option>
                        <option value="Dhaka" <?php echo ($_GET['location']??'')=='Dhaka'?'selected':''; ?>>Dhaka</option>
                        <option value="Chattogram" <?php echo ($_GET['location']??'')=='Chattogram'?'selected':''; ?>>Chattogram</option>
                        <option value="Sylhet" <?php echo ($_GET['location']??'')=='Sylhet'?'selected':''; ?>>Sylhet</option>
                        <option value="Rajshahi" <?php echo ($_GET['location']??'')=='Rajshahi'?'selected':''; ?>>Rajshahi</option>
                    </select>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" <?php echo (!empty($_GET['is_remote']))?'checked':''; ?>>
                        <label class="form-check-label small" for="is_remote">Remote Only</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Availability</label>
                    <select name="availability_status" class="form-select form-select-sm mb-1">
                        <option value="">Any</option>
                        <option value="Available Now" <?php echo ($_GET['availability_status']??'')=='Available Now'?'selected':''; ?>>Available Now</option>
                        <option value="Available This Week" <?php echo ($_GET['availability_status']??'')=='Available This Week'?'selected':''; ?>>Available This Week</option>
                        <option value="Busy" <?php echo ($_GET['availability_status']??'')=='Busy'?'selected':''; ?>>Busy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Interview Status</label>
                    <select name="interview_status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="Not Interviewed" <?php echo ($_GET['interview_status']??'')=='Not Interviewed'?'selected':''; ?>>Not Interviewed</option>
                        <option value="proposed" <?php echo ($_GET['interview_status']??'')=='proposed'?'selected':''; ?>>Interview Proposed</option>
                        <option value="scheduled" <?php echo ($_GET['interview_status']??'')=='scheduled'?'selected':''; ?>>Interview Scheduled</option>
                    </select>
                </div>
                
                <div class="col-md-3 mt-2">
                    <label class="form-label text-muted small fw-bold">Partner Type</label>
                    <select name="partner_type" class="form-select form-select-sm">
                        <option value="">Any Type</option>
                        <option value="Job Candidate" <?php echo ($_GET['partner_type']??'')=='Job Candidate'?'selected':''; ?>>Job Candidate</option>
                        <option value="Business Partner" <?php echo ($_GET['partner_type']??'')=='Business Partner'?'selected':''; ?>>Business Partner</option>
                        <option value="Freelancer" <?php echo ($_GET['partner_type']??'')=='Freelancer'?'selected':''; ?>>Freelancer</option>
                        <option value="Intern" <?php echo ($_GET['partner_type']??'')=='Intern'?'selected':''; ?>>Intern</option>
                    </select>
                </div>
                <div class="col-md-7 mt-2">
                    <label class="form-label text-muted small fw-bold">Sort Engine</label>
                    <select name="sort" class="form-select form-select-sm border-warning">
                        <option value="relevance" <?php echo ($_GET['sort']??'')=='relevance'?'selected':''; ?>>Relevance (Default)</option>
                        <option value="highly_recommended" <?php echo ($_GET['sort']??'')=='highly_recommended'?'selected':''; ?>>🔥 Match Score (Highly Recommended)</option>
                        <option value="experience_high" <?php echo ($_GET['sort']??'')=='experience_high'?'selected':''; ?>>📈 Experience (High to Low)</option>
                        <option value="experience_low" <?php echo ($_GET['sort']??'')=='experience_low'?'selected':''; ?>>⏱ Recently Active (Low to High Exp)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mt-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-search"></i> Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results -->
    <?php if ($is_ai_sort): ?>
    <h4 class="mb-3 text-warning"><i class="fa fa-star"></i> Highly Recommended for Your Company</h4>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if(count($candidates) == 0): ?>
            <div class="col-12"><div class="alert alert-warning">No partners found matching your criteria.</div></div>
        <?php else: ?>
            <?php foreach($candidates as $row): ?>
            <div class="col">
                <div class="card partner-card h-100 <?php echo ($is_ai_sort && $row['ai_score'] > 0) ? 'border-warning shadow-sm' : ''; ?>" style="<?php echo ($is_ai_sort && $row['ai_score'] > 0) ? 'border-width: 2px !important;' : ''; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-dark mb-0">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                                <?php if($row['is_remote']): ?>
                                    <span class="badge bg-secondary ms-1"><i class="fa fa-laptop-house"></i> Remote</span>
                                <?php endif; ?>
                            </h5>
                            <?php if ($is_ai_sort && $row['ai_score'] > 0): ?>
                                <span class="badge bg-warning text-dark"><i class="fa fa-bolt"></i> Match: <?php echo $row['ai_score']; ?>%</span>
                            <?php else: ?>
                                <?php 
                                    $av_status = $row['availability_status'] ?? 'Available Now';
                                    $bg = 'success';
                                    if($av_status == 'Busy') $bg = 'danger';
                                    if($av_status == 'Available This Week') $bg = 'info';
                                ?>
                                <span class="badge bg-<?php echo $bg; ?> bg-opacity-10 text-<?php echo $bg; ?>"><i class="fa fa-check-circle"></i> <?php echo $av_status; ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-3"><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                        
                        <div class="mb-2">
                            <strong><i class="fa fa-graduation-cap"></i> Education:</strong> <?php echo htmlspecialchars($row['education'] ?? 'Not specified'); ?>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fa fa-briefcase"></i> Experience:</strong> <?php echo intval($row['experience_years']); ?> Years
                        </div>
                        <div class="mb-3">
                            <strong><i class="fa fa-map-marker-alt"></i> Location:</strong> <?php echo htmlspecialchars($row['location'] ?? 'Not specified'); ?>
                        </div>
                        
                        <div class="mb-4">
                            <?php 
                            $skills = explode(',', $row['skills']);
                            foreach($skills as $sk) {
                                $sk = trim($sk);
                                if($sk) echo "<span class='skill-badge'>".htmlspecialchars($sk)."</span>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 d-flex gap-2">
                        <a href="../candidate_biodata.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary btn-sm w-50"><i class="fa fa-user"></i> Profile</a>
                        <button class="btn btn-outline-secondary btn-sm w-50" onclick="openChat(<?php echo $row['user_id']; ?>, '<?php echo addslashes($row['full_name']); ?>')"><i class="fa fa-comment-dots"></i> Chat</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Chat Modal (In-Tab Real-Time Messaging) -->
<div class="modal fade" id="chatModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable modal-md">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-comments"></i> Chat with <span id="chatPartnerName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="chatBox" style="height: 400px; background: #f8fafc; overflow-y: auto;">
          <!-- Messages will load here via AJAX -->
      </div>
      <div class="modal-footer p-2 bg-light">
          <input type="hidden" id="chatPartnerId">
          <div class="input-group">
              <input type="text" id="chatInput" class="form-control" placeholder="Type a message...">
              <button class="btn btn-primary" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
          </div>
      </div>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let chatInterval;

function openChat(partnerId, partnerName) {
    document.getElementById('chatPartnerId').value = partnerId;
    document.getElementById('chatPartnerName').innerText = partnerName;
    
    var myModal = new bootstrap.Modal(document.getElementById('chatModal'));
    myModal.show();
    
    fetchMessages();
    if(chatInterval) clearInterval(chatInterval);
    chatInterval = setInterval(fetchMessages, 3000); // Polling every 3s
}

function fetchMessages() {
    let pid = document.getElementById('chatPartnerId').value;
    if(!pid) return;
    
    fetch('chat_api.php?action=fetch&partner_id=' + pid)
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById('chatBox');
        box.innerHTML = '';
        data.messages.forEach(msg => {
            let align = msg.is_mine ? 'text-end' : 'text-start';
            let bg = msg.is_mine ? 'bg-primary text-white' : 'bg-white border';
            box.innerHTML += `<div class="mb-2 ${align}">
                                <div class="d-inline-block p-2 rounded shadow-sm ${bg}" style="max-width: 80%;">
                                    ${msg.message_text}
                                </div>
                              </div>`;
        });
        box.scrollTop = box.scrollHeight;
    });
}

function sendMessage() {
    let pid = document.getElementById('chatPartnerId').value;
    let txt = document.getElementById('chatInput').value;
    if(!txt.trim()) return;
    
    let formData = new FormData();
    formData.append('action', 'send');
    formData.append('receiver_id', pid);
    formData.append('message', txt);
    
    fetch('chat_api.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('chatInput').value = '';
            fetchMessages();
        }
    });
}

// Clear interval on modal close
document.getElementById('chatModal').addEventListener('hidden.bs.modal', function () {
    clearInterval(chatInterval);
});
</script>
</body>
</html>
