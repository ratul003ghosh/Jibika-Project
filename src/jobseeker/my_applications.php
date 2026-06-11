<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            $num = str_replace($eng_nums, $bng_nums, (string)$num);
            
            $en_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'AM', 'PM'];
            $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'এএম', 'পিএম'];
            $num = str_replace($en_months, $bn_months, $num);
        }
        return $num;
    }
}

$district_translations = [
    'bn' => [
        'Dhaka' => 'ঢাকা', 'Chattogram' => 'চট্টগ্রাম', 'Khulna' => 'খুলনা', 'Rajshahi' => 'রাজশাহী',
        'Barishal' => 'বরিশাল', 'Sylhet' => 'সিলেট', 'Rangpur' => 'রংপুর', 'Mymensingh' => 'ময়মনসিংহ'
    ],
    'en' => [
        'Dhaka' => 'Dhaka', 'Chattogram' => 'Chattogram', 'Khulna' => 'Khulna', 'Rajshahi' => 'Rajshahi',
        'Barishal' => 'Barishal', 'Sylhet' => 'Sylhet', 'Rangpur' => 'Rangpur', 'Mymensingh' => 'Mymensingh'
    ]
];

$job_type_translations = [
    'bn' => [
        'Full-time' => 'পূর্ণকালীন',
        'Part-time' => 'খণ্ডকালীন',
        'Contract' => 'চুক্তিভিত্তিক',
        'Part-time (Student)' => 'পার্ট-টাইম (ছাত্র)',
        'Day Labor' => 'দৈনিক শ্রমিক'
    ],
    'en' => [
        'Full-time' => 'Full-time',
        'Part-time' => 'Part-time',
        'Contract' => 'Contract',
        'Part-time (Student)' => 'Part-time (Student)',
        'Day Labor' => 'Day Labor'
    ]
];

$sql = "SELECT 
            applications.status,
            applications.applied_at,
            jobs.job_id,
            jobs.title,
            jobs.salary,
            jobs.job_type,
            jobs.job_category,
            jobs.application_deadline,
            jobs.status AS job_status,
            d.district_name,
            u.upazila_name,
            users.full_name AS company_name
        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN users ON jobs.employer_id = users.user_id
        WHERE applications.user_id = '$user_id'
        ORDER BY applications.application_id DESC";

$result = $conn->query($sql);
$total = $result ? $result->num_rows : 0;

$logo_colors = [
    ['bg'=>'#EEF2FF','color'=>'#4F46E5','grad'=>'linear-gradient(135deg,#EEF2FF,#C7D2FE)'],
    ['bg'=>'#FEF3C7','color'=>'#D97706','grad'=>'linear-gradient(135deg,#FEF3C7,#FDE68A)'],
    ['bg'=>'#ECFDF5','color'=>'#059669','grad'=>'linear-gradient(135deg,#ECFDF5,#A7F3D0)'],
    ['bg'=>'#FFF1F2','color'=>'#E11D48','grad'=>'linear-gradient(135deg,#FFF1F2,#FECDD3)'],
    ['bg'=>'#F0F9FF','color'=>'#0284C7','grad'=>'linear-gradient(135deg,#F0F9FF,#BAE6FD)'],
    ['bg'=>'#FDF4FF','color'=>'#9333EA','grad'=>'linear-gradient(135deg,#FDF4FF,#E9D5FF)'],
];

include('../includes/header.php');
include('../includes/navbar.php');
?>

<link rel="stylesheet" href="../assets/css/jobseeker_my_applications.css">

<!-- Hero -->
<div class="app-hero text-center">
    <div class="container px-4">
        <span style="background:rgba(255,255,255,0.15); border-radius:50px; padding:5px 18px; font-size:0.82rem; font-weight:700; color:#fff; letter-spacing:1px; text-transform:uppercase; border:1px solid rgba(255,255,255,0.25); display:inline-block; margin-bottom:16px;">
            <i class="fa-solid fa-file-circle-check me-2" style="color:#a7f3d0;"></i>
            <?php echo $lang=='bn' ? 'আমার আবেদন' : 'My Applications'; ?>
        </span>
        <h1><?php echo $lang=='bn' ? 'আবেদনের তালিকা' : 'Application History'; ?></h1>
        <p><?php echo $lang=='bn' ? 'আপনার সমস্ত চাকরির আবেদনের ট্র্যাক রাখুন' : 'Track all your job applications and their statuses'; ?></p>
    </div>
</div>

<div class="container px-3 px-lg-4 app-wrap">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <span style="font-size:0.95rem; color:#64748b; font-weight:500;">
            <strong style="color:#006a4e; font-size:1.1rem;"><?php echo translateNumber($total, $lang); ?></strong>
            <?php echo $lang=='bn' ? ' টি আবেদন' : ' application(s)'; ?>
        </span>
        <div class="d-flex gap-2">
            <a href="jobs.php" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-plus me-1"></i> <?php echo $lang=='bn' ? 'আরো আবেদন করুন' : 'Apply More'; ?>
            </a>
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> <?php echo $lang=='bn' ? 'ড্যাশবোর্ড' : 'Dashboard'; ?>
            </a>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
    
    <!-- DESKTOP TABLE -->
    <div class="app-card">
        <table class="app-table">
            <thead>
                <tr>
                    <th width="35%"><?php echo $lang=='bn' ? 'চাকরির বিবরণ' : 'Job Details'; ?></th>
                    <th width="20%"><?php echo $lang=='bn' ? 'আবেদনের তারিখ' : 'Applied Date'; ?></th>
                    <th width="25%"><?php echo $lang=='bn' ? 'বেতন ও অবস্থান' : 'Salary & Location'; ?></th>
                    <th width="20%"><?php echo $lang=='bn' ? 'অবস্থা' : 'Status'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result->data_seek(0);
                $idx = 0;
                while ($row = $result->fetch_assoc()): 
                    $lc = $logo_colors[$idx % count($logo_colors)];
                    $company = translateEmployerName($row['company_name'] ?? ($lang=='bn'?'অজ্ঞাত নিয়োগকর্তা':'Unknown Employer'), $lang);
                    $initial = strtoupper(mb_substr($company, 0, 1, 'UTF-8'));
                    $d_name = translateDistrict($row['district_name'] ?? '', $lang) ?: ($district_translations[$lang][$row['district_name']] ?? $row['district_name']);
                    $loc = htmlspecialchars($d_name ?? ($row['location'] ?? ($lang=='bn'?'একাধিক স্থান':'Multiple Locations')));
                    $sal = (empty($row['salary']) || strtolower($row['salary']) === 'negotiable') ? ($lang=='bn'?'আলোচনা সাপেক্ষে':'Negotiable') : '৳' . translateSalary($row['salary'], $lang);
                    
                    $status = $row['status'];
                    $stat_class = 'stat-pending'; $stat_icon = 'fa-clock'; $stat_text = $lang=='bn'?'অপেক্ষমান':'Pending';
                    if ($status == 'Accepted') { $stat_class = 'stat-accepted'; $stat_icon = 'fa-circle-check'; $stat_text = $lang=='bn'?'গৃহীত':'Accepted'; }
                    elseif ($status == 'Rejected') { $stat_class = 'stat-rejected'; $stat_icon = 'fa-circle-xmark'; $stat_text = $lang=='bn'?'বাতিল':'Rejected'; }

                    $j_type_raw = $row['job_type'] ?? 'Full-time';
                    $j_type_disp = $job_type_translations[$lang][$j_type_raw] ?? $j_type_raw;
                ?>
                <tr>
                    <td>
                        <div class="job-cell">
                            <div class="job-logo" style="background:<?php echo $lc['grad']; ?>; color:<?php echo $lc['color']; ?>;">
                                <?php echo $initial; ?>
                            </div>
                            <div>
                                <div class="job-title"><?php echo htmlspecialchars(translateJobTitle($row['title'] ?? '', $lang)); ?></div>
                                <div class="job-company"><?php echo htmlspecialchars($company); ?></div>
                                <div style="margin-top:4px;">
                                    <span class="badge bg-light text-secondary border fw-semibold" style="font-size:0.7rem;"><?php echo htmlspecialchars($j_type_disp); ?></span>
                                    <?php if(($row['job_status'] ?? 'active') == 'closed'): ?>
                                    <span class="badge bg-danger fw-semibold" style="font-size:0.7rem;"><i class="fa-solid fa-lock me-1"></i><?php echo $lang=='bn'?'বন্ধ':'Closed'; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="meta-text"><i class="fa-regular fa-calendar meta-icon"></i> <?php echo translateNumber(date('d M Y', strtotime($row['applied_at'])), $lang); ?></div>
                        <div class="meta-text" style="font-size:0.75rem; margin-top:2px; opacity:0.8;">
                            <?php echo translateNumber(date('h:i A', strtotime($row['applied_at'])), $lang); ?>
                        </div>
                    </td>
                    <td>
                        <div class="meta-text" style="color:#006a4e; font-weight:700;"><i class="fa-solid fa-bangladeshi-taka-sign meta-icon" style="color:#006a4e;"></i> <?php echo $sal; ?></div>
                        <div class="meta-text" style="margin-top:4px;"><i class="fa-solid fa-location-dot meta-icon"></i> <?php echo $loc; ?></div>
                    </td>
                    <td>
                        <span class="stat-badge <?php echo $stat_class; ?>">
                            <i class="fa-solid <?php echo $stat_icon; ?>"></i> <?php echo $stat_text; ?>
                        </span>
                    </td>
                </tr>
                <?php $idx++; endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- MOBILE CARDS -->
    <div class="mobile-app-list">
        <?php 
        $result->data_seek(0);
        $idx = 0;
        while ($row = $result->fetch_assoc()): 
            $lc = $logo_colors[$idx % count($logo_colors)];
            $company = $row['company_name'] ?? ($lang=='bn'?'অজ্ঞাত':'Unknown');
            $initial = strtoupper(mb_substr($company, 0, 1, 'UTF-8'));
            $d_name = $district_translations[$lang][$row['district_name']] ?? $row['district_name'];
            $loc = htmlspecialchars($d_name ?? ($row['location'] ?? ($lang=='bn'?'একাধিক স্থান':'Multiple Locations')));
            $sal = !empty($row['salary']) ? '৳' . translateNumber(number_format((float)$row['salary']), $lang) : ($lang=='bn'?'আলোচনা সাপেক্ষে':'Negotiable');
            
            $status = $row['status'];
            $stat_class = 'stat-pending'; $stat_icon = 'fa-clock'; $stat_text = $lang=='bn'?'অপেক্ষমান':'Pending';
            if ($status == 'Accepted') { $stat_class = 'stat-accepted'; $stat_icon = 'fa-circle-check'; $stat_text = $lang=='bn'?'গৃহীত':'Accepted'; }
            elseif ($status == 'Rejected') { $stat_class = 'stat-rejected'; $stat_icon = 'fa-circle-xmark'; $stat_text = $lang=='bn'?'বাতিল':'Rejected'; }

            $j_type_raw = $row['job_type'] ?? 'Full-time';
            $j_type_disp = $job_type_translations[$lang][$j_type_raw] ?? $j_type_raw;
        ?>
        <div class="ma-card">
            <div class="ma-status">
                <span class="stat-badge <?php echo $stat_class; ?>" style="padding:4px 10px; font-size:0.75rem;">
                    <i class="fa-solid <?php echo $stat_icon; ?>"></i> <?php echo $stat_text; ?>
                </span>
            </div>
            
            <div class="ma-top">
                <div class="ma-logo" style="background:<?php echo $lc['grad']; ?>; color:<?php echo $lc['color']; ?>;">
                    <?php echo $initial; ?>
                </div>
                <div style="flex:1;">
                    <div class="ma-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    <div class="ma-company"><?php echo htmlspecialchars($company); ?></div>
                    <?php if(($row['job_status'] ?? 'active') == 'closed'): ?>
                    <span class="badge bg-danger mt-1" style="font-size:0.65rem;"><?php echo $lang=='bn' ? 'বন্ধ' : 'Closed'; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="ma-meta">
                <div class="ma-meta-row">
                    <span class="ma-meta-label"><?php echo $lang=='bn' ? 'আবেদনকৃত:' : 'Applied:'; ?></span>
                    <span class="ma-meta-val"><?php echo translateNumber(date('d M Y, h:i A', strtotime($row['applied_at'])), $lang); ?></span>
                </div>
                <div class="ma-meta-row">
                    <span class="ma-meta-label"><?php echo $lang=='bn' ? 'বেতন:' : 'Salary:'; ?></span>
                    <span class="ma-meta-val text-success"><?php echo $sal; ?></span>
                </div>
                <div class="ma-meta-row">
                    <span class="ma-meta-label"><?php echo $lang=='bn' ? 'অবস্থান:' : 'Location:'; ?></span>
                    <span class="ma-meta-val"><?php echo $loc; ?></span>
                </div>
            </div>
        </div>
        <?php $idx++; endwhile; ?>
    </div>

    <?php else: ?>
    <div class="empty-app">
        <i class="fa-solid fa-file-circle-xmark" style="font-size:3.5rem; color:#cbd5e1; display:block; margin-bottom:16px;"></i>
        <h4 class="fw-bold text-dark mb-2"><?php echo $lang=='bn' ? 'কোনো আবেদন নেই' : 'No Applications Yet'; ?></h4>
        <p class="text-muted mb-4"><?php echo $lang=='bn' ? 'আপনি এখনও কোনো চাকরিতে আবেদন করেননি।' : 'You have not applied to any jobs yet.'; ?></p>
        <a href="jobs.php" class="btn btn-success rounded-pill px-5 py-2 fw-bold">
            <i class="fa-solid fa-briefcase me-2"></i>
            <?php echo $lang=='bn' ? 'চাকরি খুঁজুন' : 'Browse Jobs'; ?>
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>