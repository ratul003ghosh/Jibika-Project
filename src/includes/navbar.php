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
    /* ── Top Mini Bar ── */
    .gov-top-bar {
        background-color: #05261d;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 0.8rem;
        padding: 8px 0;
        color: #94a3b8;
        letter-spacing: 0.01em;
    }
    .gov-top-bar a {
        color: #cbd5e1;
        text-decoration: none;
        margin-right: 14px;
        font-weight: 500;
        transition: color 0.15s;
    }
    .gov-top-bar a:hover { color: #34d399; }
    .gov-top-bar .text-success {
        color: #34d399 !important;
    }
    .gov-top-bar .fw-bold.text-success {
        color: #34d399 !important;
        border-bottom: 2px solid #34d399;
        padding-bottom: 2px;
    }

    .main-navbar-gov {
        background: #09372a !important;
        border-bottom: 3px solid #e11d48;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        padding: 12px 0 !important;
        transition: all 0.3s ease;
    }
    .navbar-scrolled .main-navbar-gov {
        padding: 8px 0 !important;
        background: rgba(9, 55, 42, 0.96) !important;
        backdrop-filter: blur(8px);
    }
    .main-navbar-gov .container-fluid {
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    /* Brand (logo + title inside navbar) */
    .nav-brand-gov {
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        flex-shrink: 0;
    }
    .nb-logo-img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: contain;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        background: #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .nav-brand-gov:hover .nb-logo-img {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(244, 42, 65, 0.25);
    }
    .nav-brand-gov .nb-text .nb-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.01em;
        line-height: 1.1;
        white-space: nowrap;
    }
    .nav-brand-gov .nb-text .nb-title .nb-accent {
        color: #e11d48;
    }
    .nav-brand-gov .nb-text .nb-sub {
        font-size: 0.64rem;
        color: rgba(255,255,255,0.7);
        font-weight: 500;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin-top: 2px;
        white-space: nowrap;
    }

    /* ── Profile Trigger ── */
    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 10px 4px 12px !important;
        border-radius: 40px;
        border: 1.5px solid rgba(255,255,255,0.25);
        background: rgba(255,255,255,0.07) !important;
        transition: background 0.2s, border-color 0.2s;
        text-decoration: none;
        cursor: pointer;
    }
    .profile-trigger:hover,
    .profile-trigger.show {
        background: rgba(255,255,255,0.16) !important;
        border-color: rgba(255,255,255,0.55);
    }
    .profile-trigger .profile-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
        letter-spacing: 0.01em;
    }
    .profile-trigger .profile-role {
        font-size: 0.64rem;
        color: rgba(255,255,255,0.72);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1;
    }
    .profile-trigger .profile-chevron {
        color: rgba(255,255,255,0.55);
        font-size: 0.65rem;
        margin-left: 2px;
        transition: transform 0.2s;
    }
    .profile-trigger.show .profile-chevron {
        transform: rotate(180deg);
    }
    .profile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ffffff;
        color: #09372a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        border: 2px solid rgba(255,255,255,0.8);
    }

    /* ── Profile Dropdown ── */
    .profile-dropdown {
        min-width: 272px;
        margin-top: 10px !important;
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.16), 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        padding: 0;
        animation: dropFadeIn 0.18s ease;
    }
    @keyframes dropFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .profile-dropdown .profile-card-header {
        background: linear-gradient(135deg, #09372a 0%, #10b981 100%);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .profile-card-header .drop-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 800;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.4);
    }
    .profile-card-header .drop-name {
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
    }
    .profile-card-header .drop-email {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.75);
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 170px;
    }
    .profile-card-header .drop-role-badge {
        display: inline-block;
        margin-top: 5px;
        padding: 2px 8px;
        border-radius: 20px;
        background: rgba(255,255,255,0.18);
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .profile-dropdown .drop-menu-body {
        padding: 6px 0;
    }
    .profile-dropdown .drop-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        font-size: 0.92rem;
        font-weight: 500;
        color: #1e293b;
        text-decoration: none;
        transition: background 0.15s;
        border: none;
    }
    .profile-dropdown .drop-item .drop-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .profile-dropdown .drop-item:hover { background-color: #f1f5f9; }
    .profile-dropdown .drop-item.drop-danger { color: #dc2626; }
    .profile-dropdown .drop-item.drop-danger:hover { background-color: #fef2f2; }
    .profile-dropdown .drop-divider {
        border: none;
        border-top: 1px solid #e8edf3;
        margin: 2px 0;
    }
    .icon-dashboard { background: #ecfdf5; color: #09372a; }
    .icon-logout    { background: #fef2f2; color: #dc2626; }

    /* ── Auth Buttons ── */
    .btn-nav-login {
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.2s ease;
        padding: 0 10px;
        margin-right: 8px;
    }
    .btn-nav-login:hover {
        color: #f42a41 !important;
    }

    .btn-nav-register {
        background-color: #198754;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 6px;
        padding: 7px 20px !important;
        font-size: 0.9rem;
        transition: background 0.2s, box-shadow 0.2s;
        text-decoration: none;
    }
    .btn-nav-register:hover {
        background-color: #f42a41;
        box-shadow: 0 4px 12px rgba(244,42,65,0.35);
    }
    .btn-nav-register.active-reg {
        background-color: #f42a41;
        box-shadow: 0 2px 8px rgba(244,42,65,0.4);
    }

    /* ── 3-Bar Menu Trigger ── */
    .btn-menu-trigger {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-menu-trigger:hover {
        background: rgba(244, 42, 65, 0.15);
        border-color: rgba(244, 42, 65, 0.3);
        color: #f42a41;
        transform: scale(1.05);
    }

    /* ── Offcanvas Sidebar Styling ── */
    .nav-link-offcanvas {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.75) !important;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .nav-link-offcanvas .icon-w {
        width: 20px;
        text-align: center;
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.4);
        transition: color 0.2s ease;
    }
    .nav-link-offcanvas:hover {
        background: rgba(244, 42, 65, 0.08);
        color: #f42a41 !important;
    }
    .nav-link-offcanvas:hover .icon-w {
        color: #f42a41;
    }
    .nav-link-offcanvas.active {
        background: rgba(244, 42, 65, 0.12) !important;
        color: #f42a41 !important;
        font-weight: 600;
    }
    .nav-link-offcanvas.active .icon-w {
        color: #f42a41 !important;
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
            <button class="btn-menu-trigger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
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
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'statistics.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>statistics.php">
                 <i class="fa-solid fa-chart-simple icon-w"></i> <?php echo $t['statistics']; ?>
             </a>
         </li>
         <li class="nav-item-offcanvas">
             <a class="nav-link-offcanvas <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>" href="<?php echo $path_prefix; ?>contact.php">
                 <i class="fa-solid fa-envelope icon-w"></i> <?php echo $t['contact']; ?>
             </a>
         </li>
     </ul>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var navbar = document.querySelector('.main-navbar-gov');
    
    function checkScroll() {
        if (window.scrollY > 40) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
    
    window.addEventListener('scroll', checkScroll);
    checkScroll(); // Check on load
});
</script>