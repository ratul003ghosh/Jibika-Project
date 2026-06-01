<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}

$lang = $_SESSION['lang'] ?? 'bn';

$eng_date = date('l, d F Y');
$eng_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$bng_days = ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
$eng_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$bng_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
$eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
$bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
$bng_date = str_replace($eng_days, $bng_days, $eng_date);
$bng_date = str_replace($eng_months, $bng_months, $bng_date);
$bng_date = str_replace($eng_nums, $bng_nums, $bng_date);

$navText = [
    'bn' => [
        'gov_badge' => 'বাংলাদেশ',
        'gov_text' => 'গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের ভাবনায় কর্মসংস্থান ও জীবিকা সহায়তা প্ল্যাটফর্ম',
        'gov_subtitle' => 'জাতীয় কর্মসংস্থান ও দক্ষতা প্ল্যাটফর্ম',
        'date' => $bng_date,
        'home' => 'হোম',
        'about' => 'আমাদের সম্পর্কে',
        'job_portal' => 'চাকরি পোর্টাল',
        'eservices' => 'ই-সেবা',
        'trainings' => 'প্রশিক্ষণ',
        'notice' => 'নোটিশ বোর্ড',
        'statistics' => 'পরিসংখ্যান',
        'contact' => 'যোগাযোগ',
        'login' => 'লগইন',
        'register' => 'রেজিস্টার',
        'dashboard' => 'ড্যাশবোর্ড',
        'logout' => 'লগআউট'
    ],
    'en' => [
        'gov_badge' => 'Bangladesh',
        'gov_text' => 'Employment and livelihood support platform inspired by the Government of Bangladesh',
        'gov_subtitle' => 'National Employment & Skills Platform',
        'date' => $eng_date,
        'home' => 'Home',
        'about' => 'About Us',
        'job_portal' => 'Job Portal',
        'eservices' => 'E-Services',
        'trainings' => 'Trainings',
        'notice' => 'Notice Board',
        'statistics' => 'Statistics',
        'contact' => 'Contact',
        'login' => 'Login',
        'register' => 'Register',
        'dashboard' => 'Dashboard',
        'logout' => 'Logout'
    ]
];

$t = $navText[$lang];
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    .gov-top-bar {
        background-color: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.85rem;
        padding: 5px 0;
        color: #475569;
    }
    .gov-top-bar a {
        color: #475569;
        text-decoration: none;
        margin-right: 15px;
        font-weight: 500;
    }
    .gov-top-bar a:hover {
        text-decoration: underline;
        color: #006a4e;
    }
    
    .gov-header {
        background-color: #ffffff;
        padding: 15px 0;
    }
    .gov-title {
        color: #006a4e;
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
        line-height: 1.1;
    }
    .gov-subtitle {
        color: #64748b;
        font-size: 1rem;
        margin: 0;
        font-weight: 500;
    }

    .main-navbar-gov {
        background-color: #006a4e; /* Bangladesh Green */
        border-bottom: 4px solid #f42a41; /* Bangladesh Red */
    }
    .main-navbar-gov .nav-item {
        margin: 0 3px;
        display: flex;
        align-items: center;
    }
    .main-navbar-gov .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        padding: 8px 16px !important;
        font-size: 0.95rem;
        border-radius: 6px;
        transition: all 0.2s ease-in-out;
    }
    .main-navbar-gov .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
    }
    .main-navbar-gov .nav-item.active .nav-link {
        background-color: #f42a41; /* Distinct Red Highlight */
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(244, 42, 65, 0.4);
    }
    
    .btn-auth-gov {
        background-color: #ffffff;
        color: #006a4e !important;
        font-weight: 600;
        border-radius: 4px;
        padding: 6px 20px !important;
        margin-left: 10px;
        transition: all 0.2s;
    }
    .btn-auth-gov:hover {
        background-color: #f8fafc;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<!-- Top Mini Bar -->
<div class="gov-top-bar d-none d-md-block">
    <div class="container-fluid px-4 px-lg-5 d-flex justify-content-between align-items-center">
        <div>
            <i class="fa-solid fa-building-columns me-2 text-success"></i>
            <span><?php echo $t['gov_text']; ?></span>
        </div>
        <div>
            <span class="me-3"><i class="fa-regular fa-calendar me-2"></i><?php echo $t['date']; ?></span>
            <a href="?lang=bn" class="<?php echo $lang == 'bn' ? 'fw-bold text-success' : ''; ?>">বাংলা</a>
            |
            <a href="?lang=en" class="<?php echo $lang == 'en' ? 'fw-bold text-success' : ''; ?> ms-2">English</a>
        </div>
    </div>
</div>

<!-- Main Header Area (Logo & Title) -->
<div class="gov-header">
    <div class="container-fluid px-4 px-lg-5">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #006a4e; color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0, 106, 78, 0.2);">
                        <i class="fa-solid fa-hands-holding-circle fs-2"></i>
                    </div>
                </div>
                <div>
                    <h1 class="gov-title">জীবিকা - Jibika Portal</h1>
                    <p class="gov-subtitle"><?php echo $t['gov_subtitle']; ?></p>
                </div>
            </div>

            <!-- Professional Info Block to fill empty space -->
            <div class="d-none d-lg-flex align-items-center">
                <div class="text-end me-3 border-end pe-3">
                    <span class="d-block fw-bold text-dark" style="font-size: 0.95rem; line-height: 1.2;"><?php echo $lang == 'bn' ? 'অফিসিয়াল অ্যাপ' : 'Official App'; ?></span>
                    <span class="text-muted" style="font-size: 0.8rem;"><?php echo $lang == 'bn' ? 'ডাউনলোড করে যুক্ত থাকুন' : 'Download to stay connected'; ?></span>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" class="btn bg-white d-flex align-items-center px-3 py-1" style="border-radius: 8px; border: 1px solid #cbd5e1; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <i class="fa-brands fa-google-play fs-3 me-2" style="color: #10B981;"></i>
                        <div class="text-start lh-1">
                            <small class="d-block text-uppercase" style="font-size: 0.6rem; color: #64748b; letter-spacing: 0.5px;">GET IT ON</small>
                            <span class="fw-bold text-dark" style="font-size: 1.05rem; letter-spacing: -0.5px;">Google Play</span>
                        </div>
                    </a>
                    <a href="#" class="btn bg-white d-flex align-items-center px-3 py-1" style="border-radius: 8px; border: 1px solid #cbd5e1; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <i class="fa-brands fa-apple fs-2 me-2" style="color: #334155; margin-top: -3px;"></i>
                        <div class="text-start lh-1">
                            <small class="d-block" style="font-size: 0.6rem; color: #64748b; letter-spacing: 0.5px;">Download on the</small>
                            <span class="fw-bold text-dark" style="font-size: 1.05rem; letter-spacing: -0.5px;">App Store</span>
                        </div>
                    </a>
                </div>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['full_name'])): ?>
                <?php 
                    $profile_link = '#';
                    if ($_SESSION['role'] == 'job_seeker') $profile_link = '/jobseeker/dashboard.php';
                    elseif ($_SESSION['role'] == 'employer') $profile_link = '/employer/dashboard.php';
                    elseif ($_SESSION['role'] == 'admin') $profile_link = '/admin/dashboard.php';
                ?>
                <a href="<?php echo $profile_link; ?>" class="ms-4 ps-4 border-start d-flex align-items-center text-decoration-none" style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                    <div class="text-end me-3 lh-1">
                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 1rem;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php 
                                $role_display = str_replace('_', ' ', $_SESSION['role']);
                                echo $lang == 'bn' && $_SESSION['role'] == 'job_seeker' ? 'চাকরি প্রার্থী' : 
                                     ($lang == 'bn' && $_SESSION['role'] == 'employer' ? 'নিয়োগকর্তা' : 
                                     ($lang == 'bn' && $_SESSION['role'] == 'admin' ? 'অ্যাডমিন' : $role_display));
                            ?>
                        </span>
                    </div>
                    <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center shadow-sm" style="width: 48px; height: 48px; font-size: 1.4rem; font-weight: 800; border: 2px solid #e2e8f0;">
                        <?php echo strtoupper(mb_substr($_SESSION['full_name'], 0, 1, 'UTF-8')); ?>
                    </div>
                </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark main-navbar-gov sticky-top">
    <div class="container-fluid px-4 px-lg-5">
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="/index.php"><i class="fa-solid fa-house me-1"></i> <?php echo $t['home']; ?></a>
                </li>
                <li class="nav-item <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="/about.php"><?php echo $t['about']; ?></a>
                </li>
                <li class="nav-item <?php echo in_array($currentPage, ['jobs.php', 'manage_jobs.php']) ? 'active' : ''; ?>">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a class="nav-link" href="/admin/jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'employer'): ?>
                        <a class="nav-link" href="/employer/manage_jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php else: ?>
                        <a class="nav-link" href="/jobseeker/jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php endif; ?>
                </li>
                <li class="nav-item <?php echo $currentPage == 'eservices.php' ? 'active' : ''; ?>"><a class="nav-link" href="/eservices.php"><?php echo $t['eservices']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'trainings.php' ? 'active' : ''; ?>"><a class="nav-link" href="/trainings.php"><?php echo $t['trainings']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'notice.php' ? 'active' : ''; ?>"><a class="nav-link" href="/notice.php"><?php echo $t['notice']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'statistics.php' ? 'active' : ''; ?>"><a class="nav-link" href="/statistics.php"><?php echo $t['statistics']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>"><a class="nav-link" href="/contact.php"><?php echo $t['contact']; ?></a></li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>

                    <?php if ($_SESSION['role'] == 'job_seeker'): ?>
                        <li class="nav-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                            <a class="nav-link" href="/jobseeker/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'employer'): ?>
                        <li class="nav-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                            <a class="nav-link" href="/employer/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                            <a class="nav-link" href="/admin/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-danger px-4" href="/auth/logout.php">
                            <?php echo $t['logout']; ?>
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item <?php echo $currentPage == 'login.php' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/auth/login.php"><?php echo $t['login']; ?></a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn px-4 text-white" href="/auth/register.php" style="background-color: <?php echo $currentPage == 'register.php' ? '#f42a41' : '#198754'; ?>; font-weight: <?php echo $currentPage == 'register.php' ? '600' : 'normal'; ?>; box-shadow: <?php echo $currentPage == 'register.php' ? '0 2px 5px rgba(244, 42, 65, 0.4)' : 'none'; ?>;">
                            <?php echo $t['register']; ?>
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>