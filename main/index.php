<?php
session_start();
include('assets/config/db.php');
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<?php
$role_route = $_SESSION['role'] ?? 'guest';
$jobs_link = "/jobseeker/jobs.php";
$skills_link = "/jobseeker/skills.php";
$dashboard_link = "/auth/login.php";

if ($role_route == 'admin') {
    $jobs_link = "/admin/jobs.php";
    $skills_link = "/admin/reports.php";
    $dashboard_link = "/admin/dashboard.php";
} elseif ($role_route == 'employer') {
    $jobs_link = "/employer/manage_jobs.php";
    $skills_link = "/employer/dashboard.php";
    $dashboard_link = "/employer/dashboard.php";
} elseif ($role_route == 'job_seeker') {
    $dashboard_link = "/jobseeker/dashboard.php";
}

$total_districts = 0;
$total_jobs = 0;
$total_job_seekers = 0;
$total_applications = 0;

$district_count_query = mysqli_query($conn, "SELECT COUNT(DISTINCT location) AS total FROM jobs WHERE location IS NOT NULL AND location != ''");
if ($district_count_query) {
    $district_count_data = mysqli_fetch_assoc($district_count_query);
    $total_districts = $district_count_data['total'] ?? 0;
}

$jobs_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jobs");
if ($jobs_count_query) {
    $jobs_count_data = mysqli_fetch_assoc($jobs_count_query);
    $total_jobs = $jobs_count_data['total'] ?? 0;
}

$job_seekers_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'job_seeker'");
if ($job_seekers_count_query) {
    $job_seekers_count_data = mysqli_fetch_assoc($job_seekers_count_query);
    $total_job_seekers = $job_seekers_count_data['total'] ?? 0;
}

$applications_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications");
if ($applications_count_query) {
    $applications_count_data = mysqli_fetch_assoc($applications_count_query);
    $total_applications = $applications_count_data['total'] ?? 0;
}

$locations_result = mysqli_query($conn, "SELECT DISTINCT location FROM jobs WHERE location IS NOT NULL AND location != '' ORDER BY location ASC");

$latest_jobs_result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY id DESC LIMIT 6");

$top_areas_result = mysqli_query($conn, "
    SELECT location, COUNT(*) AS total_jobs
    FROM jobs
    WHERE location IS NOT NULL AND location != ''
    GROUP BY location
    ORDER BY total_jobs DESC, location ASC
    LIMIT 4
");
?>

<div class="container-fluid p-0">

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container-fluid px-4 px-xl-5 hero-content">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <p class="hero-quote">প্রতিটি দক্ষতার পেছনে লুকিয়ে আছে একটি সম্ভাবনার গল্প</p>
                    <h1 class="hero-title">
                        একটি দক্ষতা, একটি সুযোগ —<br>
                        বদলে দিন আপনার গল্প
                    </h1>
                    <p class="hero-subtitle">
                        Jibika is an area-based unemployment monitoring and smart job matching system.
                    </p>
                    <div class="hero-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] == 'job_seeker'): ?>
                                <a href="/jobseeker/dashboard.php" class="btn btn-warning btn-lg me-2 mb-2">My Dashboard</a>
                            <?php elseif ($_SESSION['role'] == 'employer'): ?>
                                <a href="/employer/dashboard.php" class="btn btn-warning btn-lg me-2 mb-2">Employer Dashboard</a>
                            <?php else: ?>
                                <a href="/auth/login.php" class="btn btn-warning btn-lg me-2 mb-2">Go to Panel</a>
                            <?php endif; ?>
                            <a href="/jobseeker/jobs.php" class="btn btn-outline-light btn-lg mb-2">Browse Jobs</a>
                        <?php else: ?>
                            <a href="/auth/register.php" class="btn btn-warning btn-lg me-2 mb-2">Get Started</a>
                            <a href="/auth/login.php" class="btn btn-outline-light btn-lg mb-2">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-info-box">
                        <a href="<?php echo $dashboard_link; ?>" class="info-card d-block text-decoration-none">
                            <h5>এলাকা ভিত্তিক মনিটরিং</h5>
                            <p>জেলা, উপজেলা ও ওয়ার্ডভিত্তিক বেকারত্ব পর্যবেক্ষণ</p>
                        </a>
                        <a href="<?php echo $skills_link; ?>" class="info-card d-block text-decoration-none">
                            <h5>স্কিল ম্যাপিং</h5>
                            <p>কার কী দক্ষতা আছে এবং কোন এলাকায় আছে তা সহজে জানা</p>
                        </a>
                        <a href="<?php echo $jobs_link; ?>" class="info-card d-block text-decoration-none">
                            <h5>স্মার্ট জব ম্যাচিং</h5>
                            <p>সঠিক দক্ষতাকে সঠিক চাকরির সাথে দ্রুত যুক্ত করা</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- START JOURNEY -->
    <section class="journey-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Start Your Journey with Jibika</h2>
                <p class="section-subtitle">
                    Whether you are a job seeker, employer, policymaker, or entrepreneur, Jibika helps you move forward.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('/assets/image/journey_jobseeker.png');">
                        <div class="journey-overlay-content">
                            <h5>Job Seekers</h5>
                            <p>Find jobs based on your skills, district, and local opportunities.</p>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'job_seeker'): ?>
                                <a href="/jobseeker/dashboard.php" class="btn btn-sm journey-btn">Explore</a>
                            <?php else: ?>
                                <a href="/auth/login.php" class="btn btn-sm journey-btn">Explore</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('/assets/image/journey_employer.png');">
                        <div class="journey-overlay-content">
                            <h5>Employers</h5>
                            <p>Hire suitable workers easily by skill, area, and job profile.</p>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'employer'): ?>
                                <a href="/employer/dashboard.php" class="btn btn-sm journey-btn">Hire Now</a>
                            <?php else: ?>
                                <a href="/auth/register.php" class="btn btn-sm journey-btn">Hire Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('/assets/image/journey_government.png');">
                        <div class="journey-overlay-content">
                            <h5>Government</h5>
                            <p>Monitor unemployment trends area-wise for better planning and policy.</p>
                            <a href="/admin_login.php" class="btn btn-sm journey-btn">View Access</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('/assets/image/journey_entrepreneur.png');">
                        <div class="journey-overlay-content">
                            <h5>Entrepreneurs</h5>
                            <p>Find potential partners and build small businesses with shared skills.</p>
                            <a href="/jobseeker/partner_finder.php" class="btn btn-sm journey-btn">Start Building</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEARCH SECTION -->
    <section class="search-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-4">
                <h2 class="section-title">আপনি কী খুঁজছেন?</h2>
                <p class="section-subtitle">চাকরির শিরোনাম, জেলা, উপজেলা বা দক্ষতা অনুযায়ী খুঁজুন।</p>
            </div>
            <div class="search-box">
                <form action="/jobseeker/jobs.php" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="keyword" class="form-control custom-input" placeholder="চাকরির নাম বা দক্ষতা দিয়ে খুঁজুন">
                        </div>
                        <div class="col-md-3">
                            <select name="district" class="form-control custom-input">
                                <option value="">জেলা নির্বাচন করুন</option>
                                <?php if ($locations_result && mysqli_num_rows($locations_result) > 0): ?>
                                    <?php while ($location_row = mysqli_fetch_assoc($locations_result)): ?>
                                        <option value="<?php echo htmlspecialchars($location_row['location']); ?>">
                                            <?php echo htmlspecialchars($location_row['location']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="upazila" class="form-control custom-input" placeholder="উপজেলা">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-warning btn-lg">চাকরি খুঁজুন</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- SECTORS with icons -->
    <section class="sector-section py-5" style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title">বাংলাদেশের গুরুত্বপূর্ণ চাকরির খাতসমূহ</h2>
                <p class="section-subtitle">আপনার পছন্দের খাতে চাকরি খুঁজুন</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=আইটি" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-laptop-code"></i></div><h5>আইটি ও ফ্রিল্যান্সিং</h5><span class="sector-count">ওয়েব, অ্যাপ, ডাটা</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=গার্মেন্টস" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-industry"></i></div><h5>গার্মেন্টস ও টেক্সটাইল</h5><span class="sector-count">উৎপাদন, পোশাক</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=ড্রাইভিং" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-truck-fast"></i></div><h5>ড্রাইভিং ও ট্রান্সপোর্ট</h5><span class="sector-count">পরিবহন, লজিস্টিক</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=স্বাস্থ্যসেবা" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-user-doctor"></i></div><h5>স্বাস্থ্যসেবা</h5><span class="sector-count">চিকিৎসা, নার্সিং</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=কৃষি" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-wheat-awn"></i></div><h5>কৃষি</h5><span class="sector-count">চাষাবাদ, মৎস্য</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=বিক্রয়" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-chart-line"></i></div><h5>বিক্রয় ও মার্কেটিং</h5><span class="sector-count">বিপণন, সেলস</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=শিক্ষা" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-chalkboard-user"></i></div><h5>শিক্ষা ও প্রশিক্ষণ</h5><span class="sector-count">টিউশন, কোচিং</span></a></div>
                <div class="col-lg-3 col-md-4 col-6"><a href="<?php echo $jobs_link; ?>?search=ব্যবসা" class="sector-card-v2 d-block text-decoration-none"><div class="sector-icon"><i class="fa-solid fa-store"></i></div><h5>ক্ষুদ্র ব্যবসা</h5><span class="sector-count">উদ্যোক্তা, SME</span></a></div>
            </div>
        </div>
    </section>

    <!-- SPECIAL CATEGORIES (STUDENT & DAY LABOR) -->
    <section class="py-5" style="background-color: #fff;">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title">বিশেষায়িত ক্যাটাগরি</h2>
                <p class="section-subtitle">শিক্ষার্থী এবং দৈনিক শ্রমিকদের জন্য দ্রুত চাকরির সুযোগ</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Part-time+(Student)" class="d-flex align-items-center bg-white border rounded-4 p-4 shadow-sm text-decoration-none hover-lift" style="border-left: 5px solid #f59e0b !important; transition: transform 0.2s;">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex justify-content-center align-items-center me-4" style="width:60px; height:60px; font-size:1.8rem;">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Student Jobs</h4>
                            <p class="text-muted mb-0">Part-time & Flexible Hours</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Day+Labor" class="d-flex align-items-center bg-white border rounded-4 p-4 shadow-sm text-decoration-none hover-lift" style="border-left: 5px solid #10b981 !important; transition: transform 0.2s;">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center me-4" style="width:60px; height:60px; font-size:1.8rem;">
                            <i class="fa-solid fa-person-digging"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Day Labor</h4>
                            <p class="text-muted mb-0">Daily & Weekly wages</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Internship" class="d-flex align-items-center bg-white border rounded-4 p-4 shadow-sm text-decoration-none hover-lift" style="border-left: 5px solid #3b82f6 !important; transition: transform 0.2s;">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-4" style="width:60px; height:60px; font-size:1.8rem;">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Internships</h4>
                            <p class="text-muted mb-0">Kickstart your career</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- LATEST JOBS -->
    <section class="latest-jobs-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="mb-5 text-center">
                <h2 class="section-title">সাম্প্রতিক চাকরির সুযোগ</h2>
                <p class="section-subtitle">নতুন পোস্ট হওয়া চাকরিগুলো এক নজরে দেখুন</p>
            </div>
            <div class="row g-4">
                <?php if ($latest_jobs_result && mysqli_num_rows($latest_jobs_result) > 0): ?>
                    <?php $job_icons = ['💼','🏢','📋','🎯','⚡','🔧']; $ji=0; ?>
                    <?php while ($job = mysqli_fetch_assoc($latest_jobs_result)): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="job-card-v2">
                                <div class="job-card-header">
                                    <span class="job-icon-circle"><?php echo $job_icons[$ji % 6]; ?></span>
                                    <span class="job-badge">নতুন</span>
                                </div>
                                <h5 class="job-card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                <div class="job-card-meta">
                                    <span>📍 <?php echo htmlspecialchars($job['location']); ?></span>
                                    <span>💰 <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'আলোচনা সাপেক্ষে'; ?></span>
                                </div>
                                <a href="/jobseeker/jobs.php" class="btn btn-apply">আবেদন করুন →</a>
                            </div>
                        </div>
                    <?php $ji++; endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state-v2">
                            <div class="empty-icon">📭</div>
                            <h5>এখনও কোনো চাকরি পোস্ট করা হয়নি</h5>
                            <p>নতুন চাকরি যোগ হলে এখানে দেখাবে।</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-section-v2 py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="stats-banner-v2">
                <div class="row g-4 text-center">
                    <div class="col-md-3 col-6">
                        <div class="stat-item"><span class="stat-icon">🗺️</span><h3><?php echo $total_districts; ?>+</h3><p>জেলা কভারেজ</p></div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item"><span class="stat-icon">💼</span><h3><?php echo $total_jobs; ?>+</h3><p>পোস্ট করা চাকরি</p></div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item"><span class="stat-icon">👥</span><h3><?php echo $total_job_seekers; ?>+</h3><p>চাকরি প্রার্থী</p></div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item"><span class="stat-icon">📝</span><h3><?php echo $total_applications; ?>+</h3><p>আবেদনসমূহ</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUPPORT BANNER -->
    <section class="py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="support-banner-v2">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-2 text-center"><img src="/assets/image/govt_data.png" alt="Reports" style="width:140px;border-radius:20px;"></div>
                    <div class="col-lg-7">
                        <h2>বাস্তব ডাটার মাধ্যমে প্রশিক্ষণ ও উন্নয়নকে শক্তিশালী করুন</h2>
                        <p class="mb-0">সরকার ও এনজিও সহজেই বুঝতে পারবে কোন এলাকায় প্রশিক্ষণ প্রয়োজন এবং কোথায় কর্মসংস্থান কার্যক্রম বেশি দরকার।</p>
                    </div>
                    <div class="col-lg-3 text-lg-end"><a href="/admin/reports.php" class="btn btn-warning btn-lg">রিপোর্ট দেখুন →</a></div>
                </div>
            </div>
        </div>
    </section>

    <!-- UPDATES with images -->
    <section class="updates-v2-section py-5" style="background: linear-gradient(135deg, #fef3c7, #fdf2f8);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="mb-5 text-center">
                <h2 class="section-title">আপডেট ও সুযোগসমূহ</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="/assets/image/update_training.png" alt="Digital Skills">
                        <div class="update-card-body">
                            <span class="update-tag">প্রশিক্ষণ</span>
                            <h5>ডিজিটাল স্কিল প্রশিক্ষণে নতুন সহায়তা</h5>
                            <p>তরুণ ও চাকরি প্রার্থীদের জন্য এলাকা-ভিত্তিক ডিজিটাল স্কিল ডেভেলপমেন্ট সুযোগ।</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="/assets/image/update_hiring.png" alt="New Jobs">
                        <div class="update-card-body">
                            <span class="update-tag">কর্মসংস্থান</span>
                            <h5>বিভিন্ন জেলা থেকে নতুন চাকরি পোস্ট হচ্ছে</h5>
                            <p>নতুন কর্মসংস্থানের সুযোগ ধাপে ধাপে বিভিন্ন জেলা ও উপজেলায় যুক্ত হচ্ছে।</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="/assets/image/update_govt.png" alt="Govt Data">
                        <div class="update-card-body">
                            <span class="update-tag">সরকারি</span>
                            <h5>সরকার ও এনজিওর জন্য ডাটা সাপোর্ট</h5>
                            <p>গঠনমূলক বেকারত্বের ডাটা পরিকল্পনা ও সহায়তা কার্যক্রমকে শক্তিশালী করতে পারে।</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TOP HIRING AREAS -->
    <section class="hiring-areas-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title">শীর্ষ নিয়োগদাতা এলাকা</h2>
            </div>
            <div class="row g-4">
                <?php if ($top_areas_result && mysqli_num_rows($top_areas_result) > 0): ?>
                    <?php 
                        $area_images = ['city_dhaka.png', 'city_ctg.png', 'city_sylhet.png', 'city_general.png']; 
                        $ai=0; 
                    ?>
                    <?php while ($area = mysqli_fetch_assoc($top_areas_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="area-card-v3" style="background-image: url('/assets/image/<?php echo $area_images[$ai % 4]; ?>');">
                                <div class="area-overlay">
                                    <div class="area-icon-v3"><i class="fa-solid fa-location-dot"></i></div>
                                    <h5><?php echo htmlspecialchars($area['location']); ?></h5>
                                    <div class="area-count"><?php echo (int)$area['total_jobs']; ?> টি চাকরি</div>
                                    <a href="/jobseeker/jobs.php" class="btn btn-sm btn-light mt-3 fw-bold rounded-pill px-3 shadow-sm text-primary">চাকরি দেখুন &rarr;</a>
                                </div>
                            </div>
                        </div>
                    <?php $ai++; endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center"><p>এখনও কোনো hiring area data পাওয়া যায়নি।</p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- RESOURCES -->
    <section class="resources-v2-section py-5" style="background: linear-gradient(135deg, #eff6ff, #f0fdf4);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title">জীবিকা রিসোর্সসমূহ</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-2 col-md-4 col-6"><a href="/cv_guide.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-regular fa-file-lines"></i></span><h6>সিভি লেখার গাইড</h6></div></a></div>
                <div class="col-lg-2 col-md-4 col-6"><a href="/interview_tips.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-solid fa-microphone-lines"></i></span><h6>ইন্টারভিউ টিপস</h6></div></a></div>
                <div class="col-lg-2 col-md-4 col-6"><a href="/trainings.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-solid fa-user-graduate"></i></span><h6>স্কিল ডেভেলপমেন্ট</h6></div></a></div>
                <div class="col-lg-2 col-md-4 col-6"><a href="/career_counseling.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-solid fa-compass"></i></span><h6>ক্যারিয়ার কাউন্সেলিং</h6></div></a></div>
                <div class="col-lg-2 col-md-4 col-6"><a href="/entrepreneur_support.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-solid fa-rocket"></i></span><h6>উদ্যোক্তা সহায়তা</h6></div></a></div>
                <div class="col-lg-2 col-md-4 col-6"><a href="/jobseeker/partner_finder.php" class="text-decoration-none text-dark d-block"><div class="resource-card-v2"><span><i class="fa-solid fa-handshake-angle"></i></span><h6>পার্টনার ফাইন্ডার</h6></div></a></div>
            </div>
        </div>
    </section>

</div>

<?php include('includes/footer.php'); ?>