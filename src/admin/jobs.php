<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$ajText = [
    'bn' => [
        'page_title'        => 'চাকরি পরিচালনা — জীবিকা পোর্টাল',
        'title'             => 'চাকরি পরিচালনা',
        'subtitle'          => 'প্ল্যাটফর্মে পোস্ট করা সকল চাকরি পর্যালোচনা ও পরিচালনা করুন।',
        'back_btn'          => 'ড্যাশবোর্ডে ফিরুন',
        'search_ph'         => 'চাকরি বা নিয়োগকর্তা অনুসন্ধান করুন...',
        'filter_all'        => 'সব চাকরি',
        'total_jobs'        => 'মোট চাকরি',
        'th_serial'         => '#',
        'th_title'          => 'চাকরির শিরোনাম',
        'th_location'       => 'অবস্থান',
        'th_salary'         => 'বেতন',
        'th_employer'       => 'নিয়োগকর্তা',
        'th_created'        => 'তৈরির তারিখ',
        'th_action'         => 'পদক্ষেপ',
        'btn_view'          => 'দেখুন',
        'btn_delete'        => 'মুছুন',
        'confirm_delete'    => 'আপনি কি এই চাকরিটি মুছে ফেলতে চান?',
        'no_jobs'           => 'কোনো চাকরি পাওয়া যায়নি',
        'no_jobs_sub'       => 'নিয়োগকর্তারা কোনো চাকরি পোস্ট করেননি।',
        'negotiable'        => 'আলোচনা সাপেক্ষে',
        'na'                => 'প্রযোজ্য নয়',
        'desc_label'        => 'বিবরণ',
        'salary_label'      => 'বেতন',
        'employer_label'    => 'নিয়োগকর্তা',
        'posted_label'      => 'পোস্টের তারিখ',
        'modal_title'       => 'চাকরির বিবরণ',
        'close'             => 'বন্ধ করুন',
        'showing'           => 'দেখানো হচ্ছে',
        'of'                => 'এর মধ্যে',
        'jobs_lbl'          => 'টি চাকরি',
    ],
    'en' => [
        'page_title'        => 'Manage Jobs — Jibika Portal',
        'title'             => 'Manage Jobs',
        'subtitle'          => 'Review and manage all jobs posted on the platform.',
        'back_btn'          => 'Back to Dashboard',
        'search_ph'         => 'Search jobs or employers...',
        'filter_all'        => 'All Jobs',
        'total_jobs'        => 'Total Jobs',
        'th_serial'         => '#',
        'th_title'          => 'Job Title',
        'th_location'       => 'Location',
        'th_salary'         => 'Salary',
        'th_employer'       => 'Employer',
        'th_created'        => 'Posted On',
        'th_action'         => 'Action',
        'btn_view'          => 'View',
        'btn_delete'        => 'Delete',
        'confirm_delete'    => 'Delete this job posting?',
        'no_jobs'           => 'No Jobs Found',
        'no_jobs_sub'       => 'No employers have posted any jobs yet.',
        'negotiable'        => 'Negotiable',
        'na'                => 'N/A',
        'desc_label'        => 'Description',
        'salary_label'      => 'Salary',
        'employer_label'    => 'Employer',
        'posted_label'      => 'Posted On',
        'modal_title'       => 'Job Details',
        'close'             => 'Close',
        'showing'           => 'Showing',
        'of'                => 'of',
        'jobs_lbl'          => 'jobs',
    ]
];
$ct = $ajText[$lang];

// ─── Bengali helper functions ───────────────────────────────────────────────
// Convert Arabic digits to Bengali digits
function toBnNum(string $n): string {
    return str_replace(
        ['0','1','2','3','4','5','6','7','8','9'],
        ['০','১','২','৩','৪','৫','৬','৭','৮','৯'],
        $n
    );
}

// Produce a Bengali date string: e.g. "০৬ এপ্রিল ২০২৬"
function toBnDate(string $dateStr): string {
    $months = [
        'January'   => 'জানুয়ারি',
        'February'  => 'ফেব্রুয়ারি',
        'March'     => 'মার্চ',
        'April'     => 'এপ্রিল',
        'May'       => 'মে',
        'June'      => 'জুন',
        'July'      => 'জুলাই',
        'August'    => 'আগস্ট',
        'September' => 'সেপ্টেম্বর',
        'October'   => 'অক্টোবর',
        'November'  => 'নভেম্বর',
        'December'  => 'ডিসেম্বর',
        // Short forms used by date('d M Y')
        'Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ',
        'Apr' => 'এপ্রিল',   'Jun' => 'জুন',          'Jul' => 'জুলাই',
        'Aug' => 'আগস্ট',    'Sep' => 'সেপ্টেম্বর',  'Oct' => 'অক্টোবর',
        'Nov' => 'নভেম্বর',  'Dec' => 'ডিসেম্বর',
    ];
    // Replace month name
    foreach ($months as $en => $bn) {
        $dateStr = str_replace($en, $bn, $dateStr);
    }
    // Convert digits
    return toBnNum($dateStr);
}

// Location/district name → Bengali
$locationBn = [
    'Dhaka'        => 'ঢাকা',        'dhaka'        => 'ঢাকা',
    'Chattogram'   => 'চট্টগ্রাম',  'chattogram'   => 'চট্টগ্রাম',
    'Chittagong'   => 'চট্টগ্রাম',  'chittagong'   => 'চট্টগ্রাম',
    'Khulna'       => 'খুলনা',       'khulna'       => 'খুলনা',
    'Rajshahi'     => 'রাজশাহী',    'rajshahi'     => 'রাজশাহী',
    'Barishal'     => 'বরিশাল',     'barishal'     => 'বরিশাল',
    'Barisal'      => 'বরিশাল',     'barisal'      => 'বরিশাল',
    'Sylhet'       => 'সিলেট',       'sylhet'       => 'সিলেট',
    'Rangpur'      => 'রংপুর',       'rangpur'      => 'রংপুর',
    'Mymensingh'   => 'ময়মনসিংহ',  'mymensingh'   => 'ময়মনসিংহ',
    'Comilla'      => 'কুমিল্লা',   'comilla'      => 'কুমিল্লা',
    'Narayanganj'  => 'নারায়ণগঞ্জ','narayanganj'  => 'নারায়ণগঞ্জ',
    'Gazipur'      => 'গাজীপুর',    'gazipur'      => 'গাজীপুর',
    'Bogura'       => 'বগুড়া',      'bogura'       => 'বগুড়া',
    'Bogra'        => 'বগুড়া',      'bogra'        => 'বগুড়া',
    'Narsingdi'    => 'নরসিংদী',    'narsingdi'    => 'নরসিংদী',
    'Tangail'      => 'টাঙ্গাইল',   'tangail'      => 'টাঙ্গাইল',
    'Jessore'      => 'যশোর',        'jessore'      => 'যশোর',
    'Jashore'      => 'যশোর',        'jashore'      => 'যশোর',
    "Cox's Bazar" => 'কক্সবাজার',   "cox's bazar" => 'কক্সবাজার',
    'Coxsbazar'    => 'কক্সবাজার',  'coxsbazar'    => 'কক্সবাজার',
    'Dinajpur'     => 'দিনাজপুর',   'dinajpur'     => 'দিনাজপুর',
    'Noakhali'     => 'নোয়াখালী',   'noakhali'     => 'নোয়াখালী',
    'Feni'         => 'ফেনী',         'feni'         => 'ফেনী',
    'Brahmanbaria' => 'ব্রাহ্মণবাড়িয়া','brahmanbaria'=> 'ব্রাহ্মণবাড়িয়া',
    'Kishoreganj'  => 'কিশোরগঞ্জ',  'kishoreganj'  => 'কিশোরগঞ্জ',
    'Netrokona'    => 'নেত্রকোণা',  'netrokona'    => 'নেত্রকোণা',
    'Jamalpur'     => 'জামালপুর',   'jamalpur'     => 'জামালপুর',
    'Sherpur'      => 'শেরপুর',      'sherpur'      => 'শেরপুর',
    'Sunamganj'    => 'সুনামগঞ্জ',  'sunamganj'    => 'সুনামগঞ্জ',
    'Habiganj'     => 'হবিগঞ্জ',     'habiganj'     => 'হবিগঞ্জ',
    'Moulvibazar'  => 'মৌলভীবাজার', 'moulvibazar'  => 'মৌলভীবাজার',
    'Bagerhat'     => 'বাগেরহাট',   'bagerhat'     => 'বাগেরহাট',
    'Satkhira'     => 'সাতক্ষীরা',  'satkhira'     => 'সাতক্ষীরা',
    'Kushtia'      => 'কুষ্টিয়া',  'kushtia'      => 'কুষ্টিয়া',
    'Pabna'        => 'পাবনা',       'pabna'        => 'পাবনা',
    'Sirajganj'    => 'সিরাজগঞ্জ',  'sirajganj'    => 'সিরাজগঞ্জ',
    'Natore'       => 'নাটোর',       'natore'       => 'নাটোর',
    'Naogaon'      => 'নওগাঁ',       'naogaon'      => 'নওগাঁ',
    'Joypurhat'    => 'জয়পুরহাট',   'joypurhat'    => 'জয়পুরহাট',
    'Gaibandha'    => 'গাইবান্ধা',  'gaibandha'    => 'গাইবান্ধা',
    'Kurigram'     => 'কুড়িগ্রাম',  'kurigram'     => 'কুড়িগ্রাম',
    'Lalmonirhat'  => 'লালমনিরহাট', 'lalmonirhat'  => 'লালমনিরহাট',
    'Nilphamari'   => 'নীলফামারী',  'nilphamari'   => 'নীলফামারী',
    'Thakurgaon'   => 'ঠাকুরগাঁও',  'thakurgaon'   => 'ঠাকুরগাঁও',
    'Panchagarh'   => 'পঞ্চগড়',     'panchagarh'   => 'পঞ্চগড়',
    'Barguna'      => 'বরগুনা',      'barguna'      => 'বরগুনা',
    'Bhola'        => 'ভোলা',         'bhola'        => 'ভোলা',
    'Patuakhali'   => 'পটুয়াখালী',  'patuakhali'   => 'পটুয়াখালী',
    'Pirojpur'     => 'পিরোজপুর',    'pirojpur'     => 'পিরোজপুর',
    'Jhalokathi'   => 'ঝালকাঠি',    'jhalokathi'   => 'ঝালকাঠি',
    'Shariatpur'   => 'শরীয়তপুর',  'shariatpur'   => 'শরীয়তপুর',
    'Madaripur'    => 'মাদারীপুর',  'madaripur'    => 'মাদারীপুর',
    'Gopalganj'    => 'গোপালগঞ্জ',  'gopalganj'    => 'গোপালগঞ্জ',
    'Faridpur'     => 'ফরিদপুর',    'faridpur'     => 'ফরিদপুর',
    'Rajbari'      => 'রাজবাড়ী',   'rajbari'      => 'রাজবাড়ী',
    'Manikganj'    => 'মানিকগঞ্জ',  'manikganj'    => 'মানিকগঞ্জ',
    'Munshiganj'   => 'মুন্সীগঞ্জ', 'munshiganj'   => 'মুন্সীগঞ্জ',
    'Chandpur'     => 'চাঁদপুর',     'chandpur'     => 'চাঁদপুর',
    'Lakshmipur'   => 'লক্ষ্মীপুর', 'lakshmipur'   => 'লক্ষ্মীপুর',
    'Khagrachhari' => 'খাগড়াছড়ি', 'khagrachhari' => 'খাগড়াছড়ি',
    'Rangamati'    => 'রাঙামাটি',   'rangamati'    => 'রাঙামাটি',
    'Bandarban'    => 'বান্দরবান',  'bandarban'    => 'বান্দরবান',
    'Narail'       => 'নড়াইল',      'narail'       => 'নড়াইল',
    'Magura'       => 'মাগুরা',      'magura'       => 'মাগুরা',
    'Jhenaidah'    => 'ঝিনাইদহ',    'jhenaidah'    => 'ঝিনাইদহ',
    'Chuadanga'    => 'চুয়াডাঙ্গা', 'chuadanga'    => 'চুয়াডাঙ্গা',
    'Meherpur'     => 'মেহেরপুর',   'meherpur'     => 'মেহেরপুর',
    'Chapainawabganj' => 'চাঁপাইনবাবগঞ্জ', 'chapainawabganj' => 'চাঁপাইনবাবগঞ্জ',
    'Nawabganj'    => 'নবাবগঞ্জ',   'nawabganj'    => 'নবাবগঞ্জ',
];
// ─────────────────────────────────────────────────────────────────────────────


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jobs WHERE job_id = '$id'");
    header("Location: jobs.php");
    exit();
}

$sql = "SELECT jobs.*, users.full_name AS employer_name, users.email AS employer_email
        FROM jobs
        LEFT JOIN users ON jobs.employer_id = users.user_id
        ORDER BY jobs.job_id DESC";

$result = $conn->query($sql);
$jobs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
}
$total = count($jobs);
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<style>
    body { background: #f0f4f8; }

    .admin-jobs-hero {
        padding: 30px 0 10px 0;
        position: relative;
    }

    .stat-badge {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 24px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .stat-badge i {
        color: #006a4e;
    }

    .jobs-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        overflow: hidden;
        border: none;
    }

    .jobs-toolbar {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 220px;
        position: relative;
    }
    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.92rem;
        transition: border-color 0.2s;
        outline: none;
        background: white;
    }
    .search-box input:focus { border-color: #006a4e; }
    .search-box .search-icon {
        position: absolute;
        left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .table thead th {
        background: #f8fafc;
        color: #374151;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 2px solid #e5e7eb;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table tbody tr {
        transition: background 0.15s;
    }
    .table tbody tr:hover { background: #f8fafc; }
    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #374151;
    }

    .job-title-cell .job-name {
        font-weight: 600;
        color: #0a4f32;
        font-size: 0.95rem;
        display: block;
    }
    .job-title-cell .job-id {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .employer-cell .emp-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
        display: block;
    }
    .employer-cell .emp-email {
        font-size: 0.75rem;
        color: #64748b;
    }

    .salary-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #dcfce7;
        color: #166534;
    }
    .salary-badge.negotiable {
        background: #fef3c7;
        color: #92400e;
    }

    .location-text {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #64748b;
        font-size: 0.88rem;
    }

    .btn-view-job {
        background: linear-gradient(135deg, #006a4e, #0a4f32);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-view-job:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,106,78,0.35);
        color: white;
    }

    .btn-del-job {
        background: white;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-del-job:hover {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
        transform: translateY(-1px);
    }

    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
    .empty-state-icon {
        width: 90px; height: 90px;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.2rem;
        color: #16a34a;
    }

    .table-footer-bar {
        padding: 14px 24px;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Modal */
    .job-detail-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .job-detail-modal .modal-header {
        background: linear-gradient(135deg, #006a4e, #0a4f32);
        color: white;
        border: none;
        padding: 20px 24px;
    }
    .job-detail-modal .modal-header .btn-close { filter: invert(1); }
    .job-detail-modal .modal-body { padding: 24px; }
    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .detail-label { font-size: 0.78rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .detail-value { font-size: 0.95rem; color: #1e293b; font-weight: 500; margin-top: 2px; }

    .hidden-row { display: none !important; }
</style>

<!-- Hero Section -->
<div class="admin-jobs-hero">
    <div class="container-fluid px-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1 fs-3 text-dark">
                    <i class="fa-solid fa-briefcase me-2 text-success"></i><?php echo $ct['title']; ?>
                </h2>
                <p class="mb-0 text-muted"><?php echo $ct['subtitle']; ?></p>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <div class="stat-badge">
                    <i class="fa-solid fa-briefcase fs-5"></i>
                    <div>
                        <div style="font-size:0.75rem; color:#64748b;"><?php echo $ct['total_jobs']; ?></div>
                        <div style="font-size:1.4rem; font-weight:800; color:#1e293b;" id="totalCount"><?php echo $total; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 py-4">
    <div class="jobs-table-card">

        <!-- Toolbar -->
        <div class="jobs-toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="jobSearch" placeholder="<?php echo htmlspecialchars($ct['search_ph']); ?>" oninput="filterJobs()">
            </div>
            <div id="countDisplay" class="text-muted" style="font-size:0.88rem; white-space:nowrap;">
                <?php echo $ct['showing']; ?> <strong id="visibleCount"><?php echo $total; ?></strong>
                <?php echo $ct['of']; ?> <strong><?php echo $total; ?></strong> <?php echo $ct['jobs_lbl']; ?>
            </div>
        </div>

        <!-- Table -->
        <?php if (count($jobs) > 0): ?>
        <div class="table-responsive">
            <table class="table mb-0" id="jobsTable">
                <thead>
                    <tr>
                        <th style="width:50px;"><?php echo $ct['th_serial']; ?></th>
                        <th><?php echo $ct['th_title']; ?></th>
                        <th><?php echo $ct['th_location']; ?></th>
                        <th><?php echo $ct['th_salary']; ?></th>
                        <th><?php echo $ct['th_employer']; ?></th>
                        <th><?php echo $ct['th_created']; ?></th>
                        <th style="width:140px;"><?php echo $ct['th_action']; ?></th>
                    </tr>
                </thead>
                <tbody id="jobsBody">
                    <?php foreach ($jobs as $i => $row):
                        $salary = (!empty($row['salary']) && strtolower($row['salary']) !== 'negotiable')
                            ? htmlspecialchars($row['salary'])
                            : $ct['negotiable'];
                        $isSalaryFixed = (!empty($row['salary']) && strtolower($row['salary']) !== 'negotiable');

                        // Date formatting
                        $rawDate = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '';
                        $createdDate = ($lang === 'bn' && $rawDate) ? toBnDate($rawDate) : ($rawDate ?: $ct['na']);

                        // Serial number and Job ID
                        $serialDisplay = ($lang === 'bn') ? toBnNum((string)($i + 1)) : (string)($i + 1);
                        $jobIdDisplay  = ($lang === 'bn') ? '#' . toBnNum((string)$row['job_id']) : '#' . $row['job_id'];

                        // Location
                        $rawLoc = $row['location'] ?? '';
                        $locDisplay = ($lang === 'bn' && $rawLoc)
                            ? ($locationBn[$rawLoc] ?? $locationBn[strtolower($rawLoc)] ?? $rawLoc)
                            : ($rawLoc ?: $ct['na']);

                        // Salary display with Bengali numerals
                        if ($isSalaryFixed) {
                            $salaryDisplay = '৳ ' . translateSalary($row['salary'], $lang);
                        } else {
                            $salaryDisplay = $ct['negotiable'];
                        }

                        $translatedTitle = translateJobTitle($row['title'] ?? '', $lang);
                        $translatedEmpName = translateEmployerName($row['employer_name'] ?? '', $lang);

                        $searchData = strtolower(
                            ($row['title'] ?? '') . ' ' .
                            ($row['employer_name'] ?? '') . ' ' .
                            ($row['location'] ?? '')
                        );
                    ?>
                    <tr data-search="<?php echo htmlspecialchars($searchData); ?>"
                        data-job-id="job_<?php echo $row['job_id']; ?>">
                        <td class="text-muted fw-bold"><?php echo $serialDisplay; ?></td>
                        <td>
                            <div class="job-title-cell">
                                <span class="job-name"><?php echo htmlspecialchars($translatedTitle); ?></span>
                                <span class="job-id"><?php echo $jobIdDisplay; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="location-text">
                                <i class="fa-solid fa-location-dot text-danger" style="font-size:0.8rem;"></i>
                                <?php echo htmlspecialchars($locDisplay); ?>
                            </div>
                        </td>
                        <td>
                            <span class="salary-badge <?php echo $isSalaryFixed ? '' : 'negotiable'; ?>">
                                <?php echo $salaryDisplay; ?>
                            </span>
                        </td>
                        <td>
                            <div class="employer-cell">
                                <span class="emp-name"><?php echo htmlspecialchars($translatedEmpName ?: $ct['na']); ?></span>
                                <?php if ($lang === 'en'): ?>
                                <span class="emp-email"><?php echo htmlspecialchars($row['employer_email'] ?? ''); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; font-size:0.85rem; color:#64748b;">
                                <i class="fa-regular fa-calendar" style="font-size:0.78rem;"></i>
                                <?php echo $createdDate; ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn-view-job"
                                    onclick="showJobModal(<?php echo $row['job_id']; ?>,
                                        '<?php echo addslashes(htmlspecialchars($translatedTitle)); ?>',
                                        '<?php echo addslashes(htmlspecialchars($row['description'] ?? '')); ?>',
                                        '<?php echo addslashes(htmlspecialchars($row['location'] ?? '')); ?>',
                                        '<?php echo addslashes($salaryDisplay); ?>',
                                        '<?php echo addslashes(htmlspecialchars($translatedEmpName)); ?>',
                                        '<?php echo $createdDate; ?>'
                                    )">
                                    <i class="fa-solid fa-eye me-1"></i><?php echo $ct['btn_view']; ?>
                                </button>
                                <a href="jobs.php?delete=<?php echo $row['job_id']; ?>"
                                   class="btn-del-job"
                                   onclick="return confirm('<?php echo addslashes($ct['confirm_delete']); ?>')">
                                    <i class="fa-solid fa-trash me-1"></i><?php echo $ct['btn_delete']; ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="table-footer-bar d-flex justify-content-between align-items-center">
            <span><?php echo $ct['showing']; ?> <span id="footerVisible"><?php echo $total; ?></span> <?php echo $ct['of']; ?> <?php echo $total; ?> <?php echo $ct['jobs_lbl']; ?></span>
            <span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i><?php echo $ct['title']; ?></span>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fa-solid fa-briefcase-blank"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2"><?php echo $ct['no_jobs']; ?></h5>
            <p class="text-muted mb-0"><?php echo $ct['no_jobs_sub']; ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Job Detail Modal -->
<div class="modal fade job-detail-modal" id="jobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-briefcase me-2"></i><?php echo $ct['modal_title']; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-icon" style="background:#eff6ff; color:#2563eb;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <div class="detail-label"><?php echo $ct['th_title']; ?></div>
                        <div class="detail-value fw-bold fs-5" id="modal-title">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon" style="background:#fef3c7; color:#d97706;">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <div class="detail-label"><?php echo $ct['th_location']; ?></div>
                        <div class="detail-value" id="modal-location">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon" style="background:#f0fdf4; color:#16a34a;">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="detail-label"><?php echo $ct['salary_label']; ?></div>
                        <div class="detail-value" id="modal-salary">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon" style="background:#f5f3ff; color:#7c3aed;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <div class="detail-label"><?php echo $ct['employer_label']; ?></div>
                        <div class="detail-value" id="modal-employer">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon" style="background:#fdf2f8; color:#be185d;">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                    <div>
                        <div class="detail-label"><?php echo $ct['posted_label']; ?></div>
                        <div class="detail-value" id="modal-date">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon" style="background:#f0f9ff; color:#0284c7;">
                        <i class="fa-solid fa-align-left"></i>
                    </div>
                    <div style="flex:1;">
                        <div class="detail-label"><?php echo $ct['desc_label']; ?></div>
                        <div class="detail-value" id="modal-desc" style="line-height:1.7; color:#475569;">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0" style="padding:16px 24px;">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i><?php echo $ct['close']; ?>
                </button>
                <a href="#" id="modal-delete-btn" class="btn btn-danger rounded-pill px-4"
                   onclick="return confirm('<?php echo addslashes($ct['confirm_delete']); ?>')">
                    <i class="fa-solid fa-trash me-1"></i><?php echo $ct['btn_delete']; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showJobModal(id, title, desc, location, salary, employer, date) {
    document.getElementById('modal-title').textContent    = title;
    document.getElementById('modal-desc').textContent     = desc || '—';
    document.getElementById('modal-location').textContent = location || '—';
    document.getElementById('modal-salary').textContent   = salary || '—';
    document.getElementById('modal-employer').textContent = employer || '—';
    document.getElementById('modal-date').textContent     = date || '—';
    document.getElementById('modal-delete-btn').href = 'jobs.php?delete=' + id;
    var modal = new bootstrap.Modal(document.getElementById('jobModal'));
    modal.show();
}

function filterJobs() {
    const query = document.getElementById('jobSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#jobsBody tr');
    let visible = 0;
    rows.forEach(function(row) {
        const data = row.getAttribute('data-search') || '';
        if (!query || data.includes(query)) {
            row.classList.remove('hidden-row');
            visible++;
        } else {
            row.classList.add('hidden-row');
        }
    });
    const vc = document.getElementById('visibleCount');
    const fv = document.getElementById('footerVisible');
    if (vc) vc.textContent = visible;
    if (fv) fv.textContent = visible;
}
</script>

<?php include('../includes/footer.php'); ?>