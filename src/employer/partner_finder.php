<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');
include('../includes/header.php');
include('../includes/navbar.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return str_replace($eng_nums, $bng_nums, (string)$num);
        }
        return $num;
    }
}

$district_translations = [
    'bn' => [
        'Dhaka' => 'ঢাকা', 'Chattogram' => 'চট্টগ্রাম', 'Khulna' => 'খুলনা', 'Rajshahi' => 'রাজশাহী',
        'Barishal' => 'বরিশাল', 'Sylhet' => 'সিলেট', 'Rangpur' => 'রংপুর', 'Mymensingh' => 'ময়মনসিংহ'
    ]
];

$pfText = [
    'bn' => [
        'page_title' => 'পার্টনার ফাইন্ডার - জীবিকা',
        'heading' => 'পার্টনার ফাইন্ডার ও ট্যালেন্ট সার্চ',
        'lbl_skills' => 'দক্ষতা / বিশেষত্ব (একাধিক)',
        'lbl_education' => 'শিক্ষা',
        'any_education' => 'যেকোনো শিক্ষা',
        'lbl_location' => 'অবস্থান ও রিমোট',
        'any_location' => 'যেকোনো অবস্থান',
        'remote_only' => 'শুধুমাত্র রিমোট',
        'lbl_availability' => 'উপলব্ধতা',
        'any' => 'যেকোনো',
        'lbl_interview' => 'সাক্ষাৎকারের অবস্থা',
        'all' => 'সব',
        'not_interviewed' => 'সাক্ষাৎকার নেওয়া হয়নি',
        'proposed' => 'সাক্ষাৎকার প্রস্তাবিত',
        'scheduled' => 'সাক্ষাৎকার নির্ধারিত',
        'lbl_partner_type' => 'অংশীদারের ধরন',
        'any_type' => 'যেকোনো ধরন',
        'job_candidate' => 'চাকরি প্রার্থী',
        'business_partner' => 'ব্যবসা অংশীদার',
        'freelancer' => 'ফ্রিল্যান্সার',
        'intern' => 'ইন্টার্ন',
        'lbl_sort' => 'সাজানোর ইঞ্জিন',
        'sort_relevance' => 'প্রাসঙ্গিকতা (ডিফল্ট)',
        'sort_recommended' => 'মিল স্কোর (উচ্চ প্রস্তাবিত)',
        'sort_exp_high' => 'অভিজ্ঞতা: বেশি থেকে কম',
        'sort_exp_low' => 'সম্প্রতি সক্রিয় (কম অভিজ্ঞতা)',
        'btn_apply' => 'ফিল্টার প্রয়োগ করুন',
        'highly_recommended' => 'আপনার কোম্পানির জন্য অত্যন্ত প্রস্তাবিত',
        'no_partners' => 'আপনার মানদণ্ডের সাথে মিলে এমন কোনো অংশীদার পাওয়া যায়নি।',
        'education_lbl' => 'শিক্ষা:',
        'experience_lbl' => 'অভিজ্ঞতা:',
        'location_lbl' => 'অবস্থান:',
        'btn_profile' => 'প্রোফাইল',
        'btn_chat' => 'চ্যাট',
        'chat_with' => 'চ্যাট করুন',
        'chat_placeholder' => 'একটি বার্তা লিখুন...',
        'years' => 'বছর',
        'remote' => 'রিমোট',
        'match_score' => 'মিল'
    ],
    'en' => [
        'page_title' => 'Partner Finder - Jibika',
        'heading' => 'Partner Finder & Talent Search',
        'lbl_skills' => 'Skills / Expertise (Multi)',
        'lbl_education' => 'Education',
        'any_education' => 'Any Education',
        'lbl_location' => 'Location & Remote',
        'any_location' => 'Any Location',
        'remote_only' => 'Remote Only',
        'lbl_availability' => 'Availability',
        'any' => 'Any',
        'lbl_interview' => 'Interview Status',
        'all' => 'All',
        'not_interviewed' => 'Not Interviewed',
        'proposed' => 'Interview Proposed',
        'scheduled' => 'Interview Scheduled',
        'lbl_partner_type' => 'Partner Type',
        'any_type' => 'Any Type',
        'job_candidate' => 'Job Candidate',
        'business_partner' => 'Business Partner',
        'freelancer' => 'Freelancer',
        'intern' => 'Intern',
        'lbl_sort' => 'Sort Engine',
        'sort_relevance' => 'Relevance (Default)',
        'sort_recommended' => 'Match Score (Highly Recommended)',
        'sort_exp_high' => 'Experience (High to Low)',
        'sort_exp_low' => 'Recently Active (Low to High Exp)',
        'btn_apply' => 'Apply Filters',
        'highly_recommended' => 'Highly Recommended for Your Company',
        'no_partners' => 'No partners found matching your criteria.',
        'education_lbl' => 'Education:',
        'experience_lbl' => 'Experience:',
        'location_lbl' => 'Location:',
        'btn_profile' => 'Profile',
        'btn_chat' => 'Chat',
        'chat_with' => 'Chat with',
        'chat_placeholder' => 'Type a message...',
        'years' => 'Years',
        'remote' => 'Remote',
        'match_score' => 'Match'
    ]
];
$ct = $pfText[$lang];

// Handle Search & Filter
$where = ["u.role = 'job_seeker'"];
if (!empty($_GET['skills'])) {
    $skills = is_array($_GET['skills']) ? $_GET['skills'] : [$_GET['skills']];
    $skill_cond = [];
    foreach($skills as $s) {
        if (!empty($s)) {
            $es = $conn->real_escape_string($s);
            $skill_cond[] = "u.user_id IN (SELECT user_id FROM skills WHERE skill_name LIKE '%$es%')";
        }
    }
    if(count($skill_cond) > 0) {
        $where[] = "(" . implode(' OR ', $skill_cond) . ")";
    }
}
if (!empty($_GET['education'])) {
    $e = $conn->real_escape_string($_GET['education']);
    $where[] = "jsp.education LIKE '%$e%'";
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

$q = $conn->query("SELECT DISTINCT u.user_id, u.full_name, u.email, jsp.experience_years, d.district_name as location, jsp.availability_status, jsp.is_remote,
                   (SELECT GROUP_CONCAT(s.skill_name) FROM skills s WHERE s.user_id = u.user_id) as skills,
                   jsp.education as education
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
        $emp_text = strtolower($emp_data['company_description'] ?? '');
        $cand_skills = array_map('strtolower', array_map('trim', explode(',', $row['skills'])));
        
        $match_count = 0;
        foreach ($cand_skills as $cs) {
            if ($cs != '' && strpos($emp_text, $cs) !== false) {
                $match_count++;
            }
        }
        $skill_score = count($cand_skills) > 0 ? min(70, ($match_count / max(1, count($cand_skills))) * 100) : 0;
        
        $loc_score = 0;
        if (!empty($row['location']) && !empty($emp_data['emp_location']) && $row['location'] == $emp_data['emp_location']) {
            $loc_score = 30;
        }
        
        $exp_score = min(20, intval($row['experience_years']) * 4);
        $score = min(100, round($skill_score + $loc_score + $exp_score, 1));
    }
    $row['ai_score'] = $score;
    $candidates[] = $row;
}

if ($is_ai_sort) {
    usort($candidates, function($a, $b) {
        return $b['ai_score'] <=> $a['ai_score'];
    });
}
?>

<style>
    .partner-card { transition: transform 0.2s, box-shadow 0.2s; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .partner-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .skill-badge { background: #e0f2fe; color: #0284c7; font-weight: 600; font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; margin-right: 4px; margin-bottom: 4px; display: inline-block; }
</style>

<div class="container mt-5 mb-5">
    <h2 class="mb-4 text-primary"><i class="fa-solid fa-users-viewfinder"></i> <?php echo $ct['heading']; ?></h2>
    
    <!-- Advanced Search & Filter -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_skills']; ?></label>
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
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_education']; ?></label>
                    <select name="education" class="form-select form-select-sm">
                        <option value=""><?php echo $ct['any_education']; ?></option>
                        <option value="Bachelor" <?php echo ($_GET['education']??'')=='Bachelor'?'selected':''; ?>><?php echo $lang == 'bn' ? 'ব্যাচেলর' : 'Bachelor'; ?></option>
                        <option value="Masters" <?php echo ($_GET['education']??'')=='Masters'?'selected':''; ?>><?php echo $lang == 'bn' ? 'মাস্টার্স' : 'Masters'; ?></option>
                        <option value="Diploma" <?php echo ($_GET['education']??'')=='Diploma'?'selected':''; ?>><?php echo $lang == 'bn' ? 'ডিপ্লোমা' : 'Diploma'; ?></option>
                        <option value="PhD" <?php echo ($_GET['education']??'')=='PhD'?'selected':''; ?>><?php echo $lang == 'bn' ? 'পিএইচডি' : 'PhD'; ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_location']; ?></label>
                    <select name="location" class="form-select form-select-sm mb-1">
                        <option value=""><?php echo $ct['any_location']; ?></option>
                        <option value="Dhaka" <?php echo ($_GET['location']??'')=='Dhaka'?'selected':''; ?>><?php echo $lang == 'bn' ? 'ঢাকা' : 'Dhaka'; ?></option>
                        <option value="Chattogram" <?php echo ($_GET['location']??'')=='Chattogram'?'selected':''; ?>><?php echo $lang == 'bn' ? 'চট্টগ্রাম' : 'Chattogram'; ?></option>
                        <option value="Sylhet" <?php echo ($_GET['location']??'')=='Sylhet'?'selected':''; ?>><?php echo $lang == 'bn' ? 'সিলেট' : 'Sylhet'; ?></option>
                        <option value="Rajshahi" <?php echo ($_GET['location']??'')=='Rajshahi'?'selected':''; ?>><?php echo $lang == 'bn' ? 'রাজশাহী' : 'Rajshahi'; ?></option>
                    </select>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" <?php echo (!empty($_GET['is_remote']))?'checked':''; ?>>
                        <label class="form-check-label small" for="is_remote"><?php echo $ct['remote_only']; ?></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_availability']; ?></label>
                    <select name="availability_status" class="form-select form-select-sm mb-1">
                        <option value=""><?php echo $ct['any']; ?></option>
                        <option value="Available Now" <?php echo ($_GET['availability_status']??'')=='Available Now'?'selected':''; ?>><?php echo $lang == 'bn' ? 'এখন উপলব্ধ' : 'Available Now'; ?></option>
                        <option value="Available This Week" <?php echo ($_GET['availability_status']??'')=='Available This Week'?'selected':''; ?>><?php echo $lang == 'bn' ? 'এই সপ্তাহে উপলব্ধ' : 'Available This Week'; ?></option>
                        <option value="Busy" <?php echo ($_GET['availability_status']??'')=='Busy'?'selected':''; ?>><?php echo $lang == 'bn' ? 'ব্যস্ত' : 'Busy'; ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_interview']; ?></label>
                    <select name="interview_status" class="form-select form-select-sm">
                        <option value=""><?php echo $ct['all']; ?></option>
                        <option value="Not Interviewed" <?php echo ($_GET['interview_status']??'')=='Not Interviewed'?'selected':''; ?>><?php echo $ct['not_interviewed']; ?></option>
                        <option value="proposed" <?php echo ($_GET['interview_status']??'')=='proposed'?'selected':''; ?>><?php echo $ct['proposed']; ?></option>
                        <option value="scheduled" <?php echo ($_GET['interview_status']??'')=='scheduled'?'selected':''; ?>><?php echo $ct['scheduled']; ?></option>
                    </select>
                </div>
                
                <div class="col-md-3 mt-2">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_partner_type']; ?></label>
                    <select name="partner_type" class="form-select form-select-sm">
                        <option value=""><?php echo $ct['any_type']; ?></option>
                        <option value="Job Candidate" <?php echo ($_GET['partner_type']??'')=='Job Candidate'?'selected':''; ?>><?php echo $ct['job_candidate']; ?></option>
                        <option value="Business Partner" <?php echo ($_GET['partner_type']??'')=='Business Partner'?'selected':''; ?>><?php echo $ct['business_partner']; ?></option>
                        <option value="Freelancer" <?php echo ($_GET['partner_type']??'')=='Freelancer'?'selected':''; ?>><?php echo $ct['freelancer']; ?></option>
                        <option value="Intern" <?php echo ($_GET['partner_type']??'')=='Intern'?'selected':''; ?>><?php echo $ct['intern']; ?></option>
                    </select>
                </div>
                <div class="col-md-7 mt-2">
                    <label class="form-label text-muted small fw-bold"><?php echo $ct['lbl_sort']; ?></label>
                    <select name="sort" class="form-select form-select-sm border-warning">
                        <option value="relevance" <?php echo ($_GET['sort']??'')=='relevance'?'selected':''; ?>>
                            <?php echo $ct['sort_relevance']; ?>
                        </option>
                        <option value="highly_recommended" <?php echo ($_GET['sort']??'')=='highly_recommended'?'selected':''; ?>>
                            🔥 <?php echo $ct['sort_recommended']; ?>
                        </option>
                        <option value="experience_high" <?php echo ($_GET['sort']??'')=='experience_high'?'selected':''; ?>>
                            📈 <?php echo $ct['sort_exp_high']; ?>
                        </option>
                        <option value="experience_low" <?php echo ($_GET['sort']??'')=='experience_low'?'selected':''; ?>>
                            ⏱ <?php echo $ct['sort_exp_low']; ?>
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mt-2">
                    <button type="submit" class="btn btn-success btn-sm w-100" style="background-color:#09372a; border-color:#09372a;"><i class="fa fa-search"></i> <?php echo $ct['btn_apply']; ?></button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results -->
    <?php if ($is_ai_sort): ?>
    <h4 class="mb-3 text-warning"><i class="fa fa-star"></i> <?php echo $ct['highly_recommended']; ?></h4>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if(count($candidates) == 0): ?>
            <div class="col-12"><div class="alert alert-warning"><?php echo $ct['no_partners']; ?></div></div>
        <?php else: ?>
            <?php foreach($candidates as $row): ?>
            <div class="col">
                <div class="card partner-card h-100 <?php echo ($is_ai_sort && $row['ai_score'] > 0) ? 'border-warning shadow-sm' : ''; ?>" style="<?php echo ($is_ai_sort && $row['ai_score'] > 0) ? 'border-width: 2px !important;' : ''; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-dark mb-0">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                                <?php if($row['is_remote']): ?>
                                    <span class="badge bg-secondary ms-1"><i class="fa fa-laptop-house"></i> <?php echo $ct['remote']; ?></span>
                                <?php endif; ?>
                            </h5>
                            <?php if ($is_ai_sort && $row['ai_score'] > 0): ?>
                                <span class="badge bg-warning text-dark"><i class="fa fa-bolt"></i> <?php echo $ct['match_score']; ?>: <?php echo translateNumber($row['ai_score'], $lang); ?>%</span>
                            <?php else: ?>
                                <?php 
                                    $av_status = $row['availability_status'] ?? 'Available Now';
                                    $bg = 'success';
                                    if($av_status == 'Busy') $bg = 'danger';
                                    if($av_status == 'Available This Week') $bg = 'info';

                                    $av_disp = $av_status;
                                    if ($av_status == 'Available Now') $av_disp = $lang == 'bn' ? 'এখন উপলব্ধ' : 'Available Now';
                                    elseif ($av_status == 'Available This Week') $av_disp = $lang == 'bn' ? 'এই সপ্তাহে উপলব্ধ' : 'Available This Week';
                                    elseif ($av_status == 'Busy') $av_disp = $lang == 'bn' ? 'ব্যস্ত' : 'Busy';
                                ?>
                                <span class="badge bg-<?php echo $bg; ?> bg-opacity-10 text-<?php echo $bg; ?>"><i class="fa fa-check-circle"></i> <?php echo $av_disp; ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-3"><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                        
                        <div class="mb-2">
                            <?php
                                $edu_disp = $row['education'] ?? ($lang == 'bn' ? 'উল্লেখ নেই' : 'Not specified');
                                if ($lang == 'bn') {
                                    $edu_disp = str_replace(['Bachelor', 'Masters', 'Diploma', 'PhD'], ['ব্যাচেলর', 'মাস্টার্স', 'ডিপ্লোমা', 'পিএইচডি'], $edu_disp);
                                }
                            ?>
                            <strong><i class="fa fa-graduation-cap"></i> <?php echo $ct['education_lbl']; ?></strong> <?php echo htmlspecialchars($edu_disp); ?>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fa fa-briefcase"></i> <?php echo $ct['experience_lbl']; ?></strong> <?php echo translateNumber(intval($row['experience_years']), $lang); ?> <?php echo $ct['years']; ?>
                        </div>
                        <div class="mb-3">
                            <?php
                                $loc_disp = $row['location'] ?? ($lang == 'bn' ? 'উল্লেখ নেই' : 'Not specified');
                                if ($lang == 'bn') {
                                    $loc_disp = $district_translations['bn'][$loc_disp] ?? $loc_disp;
                                }
                            ?>
                            <strong><i class="fa fa-map-marker-alt"></i> <?php echo $ct['location_lbl']; ?></strong> <?php echo htmlspecialchars($loc_disp); ?>
                        </div>
                        
                        <div class="mb-4">
                            <?php 
                            $skills = explode(',', $row['skills'] ?? '');
                            foreach($skills as $sk) {
                                $sk = trim($sk);
                                if($sk) echo "<span class='skill-badge'>".htmlspecialchars($sk)."</span>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 d-flex flex-column gap-2">
                        <div class="d-flex gap-2">
                            <a href="../candidate_biodata.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-success btn-sm w-50" style="background-color:#09372a; border-color:#09372a;"><i class="fa fa-user"></i> <?php echo $ct['btn_profile']; ?></a>
                            <button class="btn btn-outline-secondary btn-sm w-50" onclick="openChat(<?php echo $row['user_id']; ?>, '<?php echo addslashes($row['full_name']); ?>')"><i class="fa fa-comment-dots"></i> <?php echo $ct['btn_chat']; ?></button>
                        </div>
                        <a href="schedule_interview.php?candidate_id=<?php echo $row['user_id']; ?>" class="btn btn-primary btn-sm w-100" style="background-color:#006a4e; border-color:#006a4e;"><i class="fa-solid fa-calendar-check"></i> <?php echo $lang == 'bn' ? 'সাক্ষাৎকার নির্ধারণ' : 'Schedule Interview'; ?></a>
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
      <div class="modal-header bg-success text-white" style="background-color:#09372a !important;">
        <h5 class="modal-title"><i class="fa fa-comments"></i> <?php echo $ct['chat_with']; ?> <span id="chatPartnerName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="chatBox" style="height: 400px; background: #f8fafc; overflow-y: auto;">
          <!-- Messages will load here via AJAX -->
      </div>
      <div class="modal-footer p-2 bg-light">
          <input type="hidden" id="chatPartnerId">
          <div class="input-group">
              <input type="text" id="chatInput" class="form-control" placeholder="<?php echo $ct['chat_placeholder']; ?>">
              <button class="btn btn-success" style="background-color:#09372a; border-color:#09372a;" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
          </div>
      </div>
    </div>
  </div>
</div>

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
            let bg = msg.is_mine ? 'bg-success text-white' : 'bg-white border';
            let customBgStyle = msg.is_mine ? 'style="background-color:#09372a !important;"' : '';
            box.innerHTML += `<div class="mb-2 ${align}">
                                <div class="d-inline-block p-2 rounded shadow-sm ${bg}" ${customBgStyle} style="max-width: 80%;">
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

document.getElementById('chatModal').addEventListener('hidden.bs.modal', function () {
    clearInterval(chatInterval);
});
</script>

<?php include('../includes/footer.php'); ?>
