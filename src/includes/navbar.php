<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($path_prefix)) {
    $current_script = $_SERVER['SCRIPT_NAME'];
    $src_pos = strrpos($current_script, '/src/');
    if ($src_pos !== false) {
        $sub_path = substr($current_script, $src_pos + 5);
        $slash_count = substr_count($sub_path, '/');
        $path_prefix = str_repeat('../', $slash_count);
    } else {
        $slash_count = substr_count(ltrim($current_script, '/'), '/');
        $path_prefix = str_repeat('../', $slash_count);
    }
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}

$lang = $_SESSION['lang'] ?? 'bn';

$unread_notifs = 2; // Hardcoded for demo presentation

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

<link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/navbar.css">

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

<!-- Primary Navigation Bar -->
<nav class="navbar navbar-dark main-navbar-gov sticky-top">
    <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between flex-row">

        <!-- Left: Logo + Title -->
        <a class="navbar-brand nav-brand-gov" href="<?php echo $path_prefix; ?>index.php">
            <img src="<?php echo $path_prefix; ?>assets/images/jibika_logo.png" alt="Jibika Logo" class="nb-logo-img">
            <div class="nb-text d-none d-md-block">
                <span class="nb-title">জীবিকা <span class="nb-accent">|</span> Jibika</span>
                <span class="d-block nb-sub"><?php echo $t['gov_subtitle']; ?></span>
            </div>
        </a>

        <!-- Right: Auth Options + 3-Bar Menu -->
        <div class="d-flex align-items-center gap-3">
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'job_seeker'): ?>
                <a href="<?php echo $path_prefix; ?>notifications.php" class="position-relative text-white me-3" style="font-size: 1.25rem; text-decoration: none;">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($unread_notifs > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?php echo translateNumber($unread_notifs, $lang); ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- Auth Buttons / Profile Trigger -->
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>
                    <?php
                        $role_display = str_replace('_', ' ', $_SESSION['role']);
                        $role_label = $lang == 'bn' && $_SESSION['role'] == 'job_seeker' ? 'চাকরি প্রার্থী' :
                                     ($lang == 'bn' && $_SESSION['role'] == 'employer' ? 'নিয়োগকর্তা' :
                                     ($lang == 'bn' && $_SESSION['role'] == 'admin' ? 'অ্যাডমিন' : ucwords($role_display)));
                        $profile_link = '#';
                        if ($_SESSION['role'] == 'job_seeker') $profile_link = $path_prefix . 'jobseeker/dashboard.php';
                        elseif ($_SESSION['role'] == 'employer') $profile_link = $path_prefix . 'employer/dashboard.php';
                        elseif ($_SESSION['role'] == 'admin') $profile_link = $path_prefix . 'admin/dashboard.php';
                        $initial = strtoupper(mb_substr($_SESSION['full_name'], 0, 1, 'UTF-8'));
                    ?>
                    <div class="dropdown">
                        <a class="profile-trigger" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-none d-sm-block lh-1 text-end me-1">
                                <span class="profile-name d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <span class="profile-role" style="font-size: 0.65rem; color: #34d399; font-weight: 600;"><?php echo $role_label; ?></span>
                            </div>
                            <div class="profile-avatar"><?php echo $initial; ?></div>
                            <i class="fa-solid fa-chevron-down profile-chevron d-none d-sm-block ms-1"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="profileDropdown">
                            <div class="profile-card-header">
                                <div class="drop-avatar"><?php echo $initial; ?></div>
                                <div>
                                    <div class="drop-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                    <div class="drop-email"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
                                    <span class="drop-role-badge"><?php echo $role_label; ?></span>
                                </div>
                            </div>
                            <div class="drop-menu-body">
                                <a class="drop-item" href="<?php echo $profile_link; ?>">
                                    <span class="drop-item-icon icon-dashboard"><i class="fa-solid fa-gauge-high"></i></span>
                                    <?php echo $t['dashboard']; ?>
                                </a>
                                <hr class="drop-divider">
                                <a class="drop-item drop-danger" href="<?php echo $path_prefix; ?>auth/logout.php">
                                    <span class="drop-item-icon icon-logout"><i class="fa-solid fa-right-from-bracket"></i></span>
                                    <?php echo $t['logout']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="btn-nav-login" href="<?php echo $path_prefix; ?>auth/login.php"><?php echo $t['login']; ?></a>
                    <a class="btn-nav-register<?php echo $currentPage == 'register.php' ? ' active-reg' : ''; ?>" href="<?php echo $path_prefix; ?>auth/register.php">
                        <?php echo $t['register']; ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- 3-Bar Hamburger Menu Trigger (Triggers Offcanvas Menu) -->
            <button class="btn-menu-trigger d-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation" style="font-size: 24px; line-height: 1; padding-bottom: 4px;">
                &#9776;
            </button>

        </div>
    </div>
</nav>

<!-- Offcanvas Sidebar Menu -->
<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="background: #06231a !important; width: 320px; border-left: 1px solid rgba(255,255,255,0.08);">
  <div class="offcanvas-header border-bottom border-light border-opacity-10 py-3 px-4 d-flex justify-content-between align-items-center">
    <h5 class="offcanvas-title" id="offcanvasNavbarLabel" style="color: #34d399; font-weight: 700; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-compass"></i> <?php echo $lang == 'bn' ? 'মেনু নেভিগেশন' : 'Navigation Menu'; ?>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-4">
     
     <ul class="navbar-nav flex-column gap-2">
         <?php if (isset($_SESSION['role'])): ?>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo strpos($currentPage, 'dashboard.php') !== false ? 'active' : ''; ?>" href="<?php 
                 echo $path_prefix . ($_SESSION['role'] == 'admin' ? 'admin/dashboard.php' : ($_SESSION['role'] == 'employer' ? 'employer/dashboard.php' : 'jobseeker/dashboard.php')); 
             ?>">
                 <i class="fa-solid fa-gauge-high icon-w"></i> <?php echo $t['dashboard']; ?>
             </a>
         </li>
         <?php endif; ?>
         
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>index.php">
                 <i class="fa-solid fa-house icon-w"></i> <?php echo $t['home']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>about.php">
                 <i class="fa-solid fa-address-card icon-w"></i> <?php echo $t['about']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo in_array($currentPage, ['jobs.php', 'manage_jobs.php']) ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?><?php 
                 if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') echo 'admin/jobs.php';
                 elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'employer') echo 'employer/manage_jobs.php';
                 else echo 'jobseeker/jobs.php';
             ?>">
                 <i class="fa-solid fa-briefcase icon-w"></i> <?php echo $t['job_portal']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'eservices.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>eservices.php">
                 <i class="fa-solid fa-server icon-w"></i> <?php echo $t['eservices']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'trainings.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>trainings.php">
                 <i class="fa-solid fa-graduation-cap icon-w"></i> <?php echo $t['trainings']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'notice.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>notice.php">
                 <i class="fa-solid fa-bullhorn icon-w"></i> <?php echo $t['notice']; ?>
             </a>
         </li>
         <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'employer'])): ?>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'statistics.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>statistics.php">
                 <i class="fa-solid fa-chart-simple icon-w"></i> <?php echo $t['statistics']; ?>
             </a>
         </li>
         <?php endif; ?>
         
         <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'employer'): ?>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'calendar.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>employer/calendar.php">
                 <i class="fa-solid fa-calendar icon-w"></i> <?php echo $lang == 'bn' ? 'সাক্ষাৎকার ক্যালেন্ডার' : 'Interview Calendar'; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'partner_finder.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>employer/partner_finder.php">
                 <i class="fa-solid fa-users-viewfinder icon-w"></i> <?php echo $lang == 'bn' ? 'পার্টনার ফাইন্ডার' : 'Partner Finder'; ?>
             </a>
         </li>
         <?php endif; ?>

         <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'job_seeker'): ?>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'application_tracking.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>jobseeker/application_tracking.php">
                 <i class="fa-solid fa-route icon-w"></i> <?php echo $lang == 'bn' ? 'আবেদন ট্র্যাকিং' : 'App Tracking'; ?>
             </a>
         </li>
         <?php endif; ?>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>contact.php">
                 <i class="fa-solid fa-envelope icon-w"></i> <?php echo $t['contact']; ?>
             </a>
         </li>
     </ul>
  </div>
</div>

<script src="<?php echo $path_prefix; ?>assets/js/navbar.js"></script>