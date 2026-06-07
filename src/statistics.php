<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';



$stats_en = [
    'title' => 'National Employment Intelligence',
    'subtitle' => 'Real-time macro analytics and workforce distribution across Bangladesh.',
    'global_filters' => 'Global Filters',
    'all_div' => 'All Divisions',
    'all_dist' => 'All Districts',
    'all_ind' => 'All Industries',
    'all_type' => 'All Job Types',
    'kpi1' => 'Registered Seekers',
    'kpi2' => 'Verified Employers',
    'kpi3' => 'Active Job Postings',
    'kpi4' => 'Total Placements',
    'pulse' => 'Employment Pulse',
    'p1' => 'Today\'s Active Jobs',
    'p2' => 'New Jobs This Month',
    'p3' => 'Most In-Demand',
    'p4' => 'Most Active District',
    'p5' => 'Placement Rate',
    'trends' => 'Employment Trends',
    '6m' => '6M',
    '12m' => '12M',
    'all_time' => 'All Time',
    'geo' => 'Geographic Insights (Division Wise)',
    'map' => 'Map Coming Soon',
    'type' => 'Job Type Distribution',
    'skills' => 'Top 10 In-Demand Skills',
    'ind' => 'Industry Insights',
    'export' => 'Export',
    'th1' => 'Industry Name',
    'th2' => 'Active Employers',
    'th3' => 'Total Hires',
    'th4' => 'Avg. Salary',
    'th5' => 'Growth Trend',
    'imp1' => 'Jibika Platform Impact',
    'imp2' => 'People Employed',
    'imp3' => 'Districts Covered',
    'imp4' => 'Applications Processed',
    'd_dhaka' => 'Dhaka',
    'd_ctg' => 'Chittagong',
    'd_raj' => 'Rajshahi',
    'd_khu' => 'Khulna',
    'd_syl' => 'Sylhet',
    'd_bar' => 'Barishal',
    'd_rng' => 'Rangpur',
    'd_mym' => 'Mymensingh',
    'i_it' => 'IT & Software',
    'i_gar' => 'Garments & Textile',
    'i_con' => 'Construction',
    'i_agr' => 'Agriculture',
    'i_hea' => 'Healthcare',
    't_ft' => 'Full-Time',
    't_pt' => 'Part-Time',
    't_in' => 'Internship',
    't_rm' => 'Remote',
    't_dl' => 'Day Labor',
    'c_post' => 'Job Postings',
    'c_place' => 'Placements',
    'c_act' => 'Active Jobs',
    'c_seek' => 'Job Seekers',
    'c_dem' => 'Demand Index',
    'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'sk_list' => ['Python', 'Excel', 'Digital Mkt.', 'Graphic Design', 'Java', 'Accounting', 'Data Entry', 'Driving', 'Nursing', 'Plumbing']
];

$stats_bn = [
    'title' => 'জাতীয় কর্মসংস্থান বুদ্ধিমত্তা',
    'subtitle' => 'সারা বাংলাদেশে রিয়েল-টাইম ম্যাক্রো বিশ্লেষণ এবং কর্মশক্তি বন্টন।',
    'global_filters' => 'গ্লোবাল ফিল্টার',
    'all_div' => 'সকল বিভাগ',
    'all_dist' => 'সকল জেলা',
    'all_ind' => 'সকল শিল্প',
    'all_type' => 'সকল কাজের ধরন',
    'kpi1' => 'নিবন্ধিত চাকরিপ্রার্থী',
    'kpi2' => 'যাচাইকৃত নিয়োগকর্তা',
    'kpi3' => 'সক্রিয় চাকরির পোস্টিং',
    'kpi4' => 'মোট নিয়োগ',
    'pulse' => 'কর্মসংস্থান পালস',
    'p1' => 'আজকের সক্রিয় চাকরি',
    'p2' => 'এই মাসে নতুন চাকরি',
    'p3' => 'সবচেয়ে বেশি চাহিদা',
    'p4' => 'সবচেয়ে সক্রিয় জেলা',
    'p5' => 'নিয়োগের হার',
    'trends' => 'কর্মসংস্থানের প্রবণতা',
    '6m' => '৬ মাস',
    '12m' => '১২ মাস',
    'all_time' => 'সব সময়',
    'geo' => 'ভৌগলিক অন্তর্দৃষ্টি (বিভাগ অনুযায়ী)',
    'map' => 'ম্যাপ শীঘ্রই আসছে',
    'type' => 'কাজের ধরন বন্টন',
    'skills' => 'শীর্ষ ১০টি চাহিদাসম্পন্ন দক্ষতা',
    'ind' => 'শিল্পের অন্তর্দৃষ্টি',
    'export' => 'এক্সপোর্ট',
    'th1' => 'শিল্পের নাম',
    'th2' => 'সক্রিয় নিয়োগকর্তা',
    'th3' => 'মোট নিয়োগ',
    'th4' => 'গড় বেতন',
    'th5' => 'বৃদ্ধির ধারা',
    'imp1' => 'জীবিকা প্ল্যাটফর্মের প্রভাব',
    'imp2' => 'কর্মসংস্থানপ্রাপ্ত মানুষ',
    'imp3' => 'অন্তর্ভুক্ত জেলা',
    'imp4' => 'আবেদন প্রক্রিয়া সম্পন্ন',
    'd_dhaka' => 'ঢাকা',
    'd_ctg' => 'চট্টগ্রাম',
    'd_raj' => 'রাজশাহী',
    'd_khu' => 'খুলনা',
    'd_syl' => 'সিলেট',
    'd_bar' => 'বরিশাল',
    'd_rng' => 'রংপুর',
    'd_mym' => 'ময়মনসিংহ',
    'i_it' => 'আইটি এবং সফটওয়্যার',
    'i_gar' => 'গার্মেন্টস ও টেক্সটাইল',
    'i_con' => 'নির্মাণ',
    'i_agr' => 'কৃষি',
    'i_hea' => 'স্বাস্থ্যসেবা',
    't_ft' => 'ফুল-টাইম',
    't_pt' => 'পার্ট-টাইম',
    't_in' => 'ইন্টার্নশিপ',
    't_rm' => 'রিমোট',
    't_dl' => 'দিনমজুর',
    'c_post' => 'চাকরির পোস্টিং',
    'c_place' => 'নিয়োগ',
    'c_act' => 'সক্রিয় চাকরি',
    'c_seek' => 'চাকরিপ্রার্থী',
    'c_dem' => 'চাহিদা সূচক',
    'months' => ['জানু', 'ফেব্রু', 'মার্চ', 'এপ্রিল', 'মে', 'জুন'],
    'sk_list' => ['পাইথন', 'এক্সেল', 'ডিজিটাল মার্কেটিং', 'গ্রাফিক ডিজাইন', 'জাভা', 'অ্যাকাউন্টিং', 'ডেটা এন্ট্রি', 'ড্রাইভিং', 'নার্সিং', 'প্লাম্বিং']
];

$stats_t = $lang === 'en' ? $stats_en : $stats_bn;
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<link rel="stylesheet" href="assets/css/statistics.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<div class="dashboard-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold mb-2"><?= $stats_t['title'] ?></h1>
        <p class="fs-5 opacity-75 mb-0"><?= $stats_t['subtitle'] ?></p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">

    <div class="filter-bar mb-5">
        <div class="row g-3 align-items-center">
            <div class="col-md-2 fw-bold text-muted">
                <i class="fa-solid fa-filter me-2"></i> <?= $stats_t['global_filters'] ?>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm border-0 bg-light">
                    <option><?= $stats_t['all_div'] ?></option>
                    <option><?= $stats_t['d_dhaka'] ?></option>
                    <option><?= $stats_t['d_ctg'] ?></option>
                    <option><?= $stats_t['d_raj'] ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm border-0 bg-light">
                    <option><?= $stats_t['all_dist'] ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm border-0 bg-light">
                    <option><?= $stats_t['all_ind'] ?></option>
                    <option><?= $stats_t['i_it'] ?></option>
                    <option><?= $stats_t['i_gar'] ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm border-0 bg-light">
                    <option><?= $stats_t['all_type'] ?></option>
                    <option><?= $stats_t['t_ft'] ?></option>
                    <option><?= $stats_t['t_pt'] ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm border-0 bg-light">
                    <option><?= translateNumber('2026', $lang) ?></option>
                    <option><?= translateNumber('2025', $lang) ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="kpi-card border-top border-4 border-success rounded-top">
                    <div class="d-flex justify-content-between">
                        <div class="kpi-icon bg-emerald-light"><i class="fa-solid fa-users"></i></div>
                        <div class="kpi-growth bg-success-subtle text-success"><i class="fa-solid fa-arrow-trend-up"></i> <?= translateNumber('+12.5%', $lang) ?></div>
                    </div>
                    <div class="kpi-value mt-3"><?= translateNumber('125,430', $lang) ?></div>
                    <div class="kpi-label"><?= $stats_t['kpi1'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="kpi-card border-top border-4 border-primary rounded-top">
                    <div class="d-flex justify-content-between">
                        <div class="kpi-icon bg-navy-light"><i class="fa-solid fa-building"></i></div>
                        <div class="kpi-growth bg-success-subtle text-success"><i class="fa-solid fa-arrow-trend-up"></i> <?= translateNumber('+5.2%', $lang) ?></div>
                    </div>
                    <div class="kpi-value mt-3"><?= translateNumber('8,450', $lang) ?></div>
                    <div class="kpi-label"><?= $stats_t['kpi2'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="kpi-card border-top border-4 border-warning rounded-top">
                    <div class="d-flex justify-content-between">
                        <div class="kpi-icon bg-warning-light"><i class="fa-solid fa-briefcase"></i></div>
                        <div class="kpi-growth bg-success-subtle text-success"><i class="fa-solid fa-arrow-trend-up"></i> <?= translateNumber('+18.1%', $lang) ?></div>
                    </div>
                    <div class="kpi-value mt-3"><?= translateNumber('14,200', $lang) ?></div>
                    <div class="kpi-label"><?= $stats_t['kpi3'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="kpi-card border-top border-4 border-info rounded-top">
                    <div class="d-flex justify-content-between">
                        <div class="kpi-icon bg-info-light"><i class="fa-solid fa-handshake"></i></div>
                        <div class="kpi-growth bg-success-subtle text-success"><i class="fa-solid fa-arrow-trend-up"></i> <?= translateNumber('+22.4%', $lang) ?></div>
                    </div>
                    <div class="kpi-value mt-3"><?= translateNumber('62,890', $lang) ?></div>
                    <div class="kpi-label"><?= $stats_t['kpi4'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['pulse'] ?></span>
                    <i class="fa-solid fa-heart-pulse text-danger"></i>
                </div>
                <div class="dash-card-body">
                    <div class="pulse-item d-flex justify-content-between align-items-center">
                        <span class="pulse-label"><i class="fa-solid fa-clock me-2 text-muted"></i><?= $stats_t['p1'] ?></span>
                        <span class="pulse-value fs-5"><?= translateNumber('1,245', $lang) ?></span>
                    </div>
                    <div class="pulse-item d-flex justify-content-between align-items-center">
                        <span class="pulse-label"><i class="fa-solid fa-calendar-plus me-2 text-muted"></i><?= $stats_t['p2'] ?></span>
                        <span class="pulse-value fs-5 text-success"><?= translateNumber('+4,820', $lang) ?></span>
                    </div>
                    <div class="pulse-item d-flex justify-content-between align-items-center">
                        <span class="pulse-label"><i class="fa-solid fa-industry me-2 text-muted"></i><?= $stats_t['p3'] ?></span>
                        <span class="pulse-value"><span class="badge bg-navy-light text-dark"><?= $stats_t['i_it'] ?></span></span>
                    </div>
                    <div class="pulse-item d-flex justify-content-between align-items-center">
                        <span class="pulse-label"><i class="fa-solid fa-location-dot me-2 text-muted"></i><?= $stats_t['p4'] ?></span>
                        <span class="pulse-value"><?= $stats_t['d_dhaka'] ?></span>
                    </div>
                    <div class="pulse-item d-flex justify-content-between align-items-center">
                        <span class="pulse-label"><i class="fa-solid fa-chart-line me-2 text-muted"></i><?= $stats_t['p5'] ?></span>
                        <span class="pulse-value text-emerald"><?= translateNumber('78.4%', $lang) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['trends'] ?></span>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active"><?= $stats_t['6m'] ?></button>
                        <button class="btn btn-outline-secondary"><?= $stats_t['12m'] ?></button>
                        <button class="btn btn-outline-secondary"><?= $stats_t['all_time'] ?></button>
                    </div>
                </div>
                <div class="dash-card-body" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['geo'] ?></span>
                    <span class="badge bg-success-subtle text-success"><?= $stats_t['map'] ?></span>
                </div>
                <div class="dash-card-body" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="geoChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['type'] ?></span>
                </div>
                <div class="dash-card-body d-flex justify-content-center align-items-center" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="jobTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['skills'] ?></span>
                </div>
                <div class="dash-card-body" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="skillsChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="dash-card">
                <div class="dash-card-header">
                    <span><?= $stats_t['ind'] ?></span>
                    <button class="btn btn-sm btn-light border"><i class="fa-solid fa-download"></i> <?= $stats_t['export'] ?></button>
                </div>
                <div class="dash-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                                <tr>
                                    <th class="ps-4 py-3"><?= $stats_t['th1'] ?></th>
                                    <th class="py-3"><?= $stats_t['th2'] ?></th>
                                    <th class="py-3"><?= $stats_t['th3'] ?></th>
                                    <th class="py-3"><?= $stats_t['th4'] ?></th>
                                    <th class="pe-4 py-3" style="width: 150px;"><?= $stats_t['th5'] ?></th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fa-solid fa-shirt text-warning me-2"></i><?= $stats_t['i_gar'] ?></td>
                                    <td><?= translateNumber('1,240', $lang) ?></td>
                                    <td class="text-success fw-bold"><?= translateNumber('25,430', $lang) ?></td>
                                    <td class="text-muted"><?= translateNumber('৳18,500', $lang) ?></td>
                                    <td class="pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small text-muted"><?= translateNumber('85%', $lang) ?></span>
                                            <div class="table-progress flex-grow-1"><div class="table-progress-bar" style="width: 85%; background: #129B6F;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fa-solid fa-laptop-code text-primary me-2"></i><?= $stats_t['i_it'] ?></td>
                                    <td><?= translateNumber('850', $lang) ?></td>
                                    <td class="text-success fw-bold"><?= translateNumber('15,200', $lang) ?></td>
                                    <td class="text-muted"><?= translateNumber('৳45,000', $lang) ?></td>
                                    <td class="pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small text-muted"><?= translateNumber('92%', $lang) ?></span>
                                            <div class="table-progress flex-grow-1"><div class="table-progress-bar" style="width: 92%; background: #129B6F;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fa-solid fa-trowel-bricks text-secondary me-2"></i><?= $stats_t['i_con'] ?></td>
                                    <td><?= translateNumber('420', $lang) ?></td>
                                    <td class="text-success fw-bold"><?= translateNumber('12,100', $lang) ?></td>
                                    <td class="text-muted"><?= translateNumber('৳22,000', $lang) ?></td>
                                    <td class="pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small text-muted"><?= translateNumber('65%', $lang) ?></span>
                                            <div class="table-progress flex-grow-1"><div class="table-progress-bar" style="width: 65%; background: #152334;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fa-solid fa-tractor text-success me-2"></i><?= $stats_t['i_agr'] ?></td>
                                    <td><?= translateNumber('310', $lang) ?></td>
                                    <td class="text-success fw-bold"><?= translateNumber('8,050', $lang) ?></td>
                                    <td class="text-muted"><?= translateNumber('৳15,000', $lang) ?></td>
                                    <td class="pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small text-muted"><?= translateNumber('45%', $lang) ?></span>
                                            <div class="table-progress flex-grow-1"><div class="table-progress-bar" style="width: 45%; background: #f59e0b;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="fa-solid fa-stethoscope text-danger me-2"></i><?= $stats_t['i_hea'] ?></td>
                                    <td><?= translateNumber('280', $lang) ?></td>
                                    <td class="text-success fw-bold"><?= translateNumber('6,120', $lang) ?></td>
                                    <td class="text-muted"><?= translateNumber('৳35,000', $lang) ?></td>
                                    <td class="pe-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small text-muted"><?= translateNumber('78%', $lang) ?></span>
                                            <div class="table-progress flex-grow-1"><div class="table-progress-bar" style="width: 78%; background: #129B6F;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="impact-section text-center shadow-lg">
        <h2 class="fw-bold mb-5"><?= $stats_t['imp1'] ?></h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="impact-counter text-warning"><?= translateNumber('62K+', $lang) ?></div>
                <div class="text-uppercase fw-bold letter-spacing-1 opacity-75"><?= $stats_t['imp2'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="impact-counter text-white"><?= translateNumber('64', $lang) ?></div>
                <div class="text-uppercase fw-bold letter-spacing-1 opacity-75"><?= $stats_t['imp3'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="impact-counter text-white"><?= translateNumber('8.4K', $lang) ?></div>
                <div class="text-uppercase fw-bold letter-spacing-1 opacity-75"><?= $stats_t['th2'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="impact-counter text-warning"><?= translateNumber('350K+', $lang) ?></div>
                <div class="text-uppercase fw-bold letter-spacing-1 opacity-75"><?= $stats_t['imp4'] ?></div>
            </div>
        </div>
    </div>

</div>

<script>
window.statsTranslations = <?php echo json_encode($stats_t); ?>;
</script>
<script src="assets/js/statistics.js"></script>

<?php include('includes/footer.php'); ?>
