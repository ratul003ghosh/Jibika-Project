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
        background-color: #f1f5f9;
        border-bottom: 1px solid #dde3ec;
        font-size: 0.8rem;
        padding: 6px 0;
        color: #475569;
        letter-spacing: 0.01em;
    }
    .gov-top-bar a {
        color: #475569;
        text-decoration: none;
        margin-right: 14px;
        font-weight: 500;
        transition: color 0.15s;
    }
    .gov-top-bar a:hover { color: #006a4e; }


   .main-navbar-gov {
    background: linear-gradient(90deg, #003d2b 0%, #005c42 30%, #006a4e 65%, #007a5a 100%) !important;
    border-bottom: 3px solid #f42a41;
    box-shadow: 0 3px 16px rgba(0,0,0,0.22);
    padding: 0 !important;
}
   .main-navbar-gov .container-fluid {
    flex-direction: column !important;
    align-items: stretch !important;
}
    /* Brand (logo + title inside navbar) */
    .nav-brand-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 8px 0;
}
    .nav-brand-gov {
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    flex-shrink: 0;
}
 .nav-brand-gov .nb-logo {
    position: relative;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
}
    .nav-brand-gov .nb-logo-ring {
        position: absolute; inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.28);
        animation: nbPulse 2.6s ease-in-out infinite;
    }
    @keyframes nbPulse {
        0%,100% { transform: scale(1);   opacity: 0.6; }
        50%      { transform: scale(1.1); opacity: 0.12; }
    }
.nav-brand-gov .nb-logo-inner {
    position: relative;
    z-index: 1;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.45);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    backdrop-filter: blur(4px);
    transition: background 0.3s, transform 0.3s;
}
    .nav-brand-gov:hover .nb-logo-inner {
        background: rgba(255,255,255,0.24);
        transform: scale(1.06);
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
        color: #ff6b7a;
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
    .main-navbar-gov .navbar-collapse {
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        padding: 6px 0 8px 0;
    }
    .main-navbar-gov .navbar-nav {
        display: flex;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    @media (min-width: 992px) {
        .main-navbar-gov .navbar-nav {
            flex-direction: row !important;
            justify-content: flex-start;
            gap: 6px;
        }
    }
    @media (max-width: 991.98px) {
        .main-navbar-gov .navbar-collapse {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding: 8px 0;
        }
        .main-navbar-gov .navbar-nav {
            flex-direction: column !important;
            align-items: stretch;
            gap: 4px;
            padding-top: 8px;
        }
    }
    .main-navbar-gov .nav-item {
        display: flex;
        align-items: center;
    }
    .main-navbar-gov .nav-link {
        color: rgba(255,255,255,0.85) !important;
        font-weight: 500;
        padding: 8px 14px !important;
        font-size: 0.9rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }
    .main-navbar-gov .nav-link:hover {
        background-color: rgba(255,255,255,0.13);
        color: #ffffff !important;
    }
    .main-navbar-gov .nav-item.active .nav-link {
        background-color: #f42a41;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(244,42,65,0.4);
    }

    /* ── Top Row Info Block ── */
    .gov-info-badge {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 5px 12px;
        border-radius: 30px;
        color: #ffffff;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .gov-info-badge i {
        color: #ffd700;
        font-size: 0.85rem;
    }
    .gov-info-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.15);
    }
    .gov-info-stat {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }
    .gov-info-stat .stat-value {
        font-size: 0.88rem;
        font-weight: 800;
        color: #ffd700;
        text-align: center;
    }
    .gov-info-stat .stat-desc {
        font-size: 0.62rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        text-align: center;
        margin-top: 1px;
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
        color: #006a4e;
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
    /* Header card inside dropdown */
    .profile-dropdown .profile-card-header {
        background: linear-gradient(135deg, #005c42 0%, #007a5a 100%);
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
    /* Dropdown items */
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
    .icon-dashboard { background: #ecfdf5; color: #006a4e; }
    .icon-logout    { background: #fef2f2; color: #dc2626; }

    /* ── Auth Buttons ── */
    .btn-nav-register {
        background-color: #198754;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 6px;
        padding: 7px 20px !important;
        font-size: 0.9rem;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-nav-register:hover {
        background-color: #157347;
        box-shadow: 0 4px 12px rgba(25,135,84,0.35);
    }
    .btn-nav-register.active-reg {
        background-color: #f42a41;
        box-shadow: 0 2px 8px rgba(244,42,65,0.4);
    }

    /* Scroll transitions for premium sticky behavior */
    .main-navbar-gov {
        transition: all 0.3s ease-in-out;
    }
    .nav-brand-row {
        transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out, opacity 0.2s ease-in-out;
        max-height: 80px;
        opacity: 1;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .navbar-scrolled .nav-brand-row {
            max-height: 0;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            opacity: 0;
            pointer-events: none;
        }
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


<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark main-navbar-gov sticky-top">
    <div class="container-fluid px-3 px-lg-4">

        <!-- Top Row: Brand & Mobile Toggler -->
        <div class="nav-brand-row">
            <!-- Brand: Logo + Title -->
            <a class="navbar-brand nav-brand-gov" href="<?php echo $path_prefix; ?>index.php">
                <div class="nb-logo">
                    <div class="nb-logo-ring"></div>
                    <div class="nb-logo-inner">
                        <i class="fa-solid fa-hands-holding-circle"></i>
                    </div>
                </div>
                <div class="nb-text d-none d-md-block">
                    <span class="nb-title">জীবিকা <span class="nb-accent">|</span> Jibika</span>
                    <span class="d-block nb-sub"><?php echo $t['gov_subtitle']; ?></span>
                </div>
            </a>

            <!-- Center/Right info block (desktop only) -->
            <div class="d-none d-lg-flex align-items-center gap-3 ms-auto me-4">
                <!-- Official Badge -->
                <div class="gov-info-badge">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    <span><?php echo $lang == 'bn' ? 'সরকারি কর্মসংস্থান পোর্টাল' : 'Official Government Job Portal'; ?></span>
                </div>
                <!-- Divider -->
                <div class="gov-info-divider"></div>
                <!-- Stat chips -->
                <div class="gov-info-stats d-flex align-items-center gap-3">
                    <div class="gov-info-stat">
                        <span class="stat-value"><?php echo $lang == 'bn' ? '৬৪+' : '64+'; ?></span>
                        <span class="stat-desc"><?php echo $lang == 'bn' ? 'জেলা' : 'Districts'; ?></span>
                    </div>
                    <div class="gov-info-stat">
                        <span class="stat-value"><?php echo $lang == 'bn' ? '৫কে+' : '5K+'; ?></span>
                        <span class="stat-desc"><?php echo $lang == 'bn' ? 'চাকরি' : 'Jobs'; ?></span>
                    </div>
                    <div class="gov-info-stat">
                        <span class="stat-value"><?php echo $lang == 'bn' ? '২৫কে+' : '25K+'; ?></span>
                        <span class="stat-desc"><?php echo $lang == 'bn' ? 'ব্যবহারকারী' : 'Users'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Mobile toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <!-- Bottom Row: Collapse containing centered Nav Links -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $path_prefix; ?>index.php"><i class="fa-solid fa-house me-1"></i> <?php echo $t['home']; ?></a>
                </li>
                <li class="nav-item <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo $path_prefix; ?>about.php"><?php echo $t['about']; ?></a>
                </li>
                <li class="nav-item <?php echo in_array($currentPage, ['jobs.php', 'manage_jobs.php']) ? 'active' : ''; ?>">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a class="nav-link" href="<?php echo $path_prefix; ?>admin/jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'employer'): ?>
                        <a class="nav-link" href="<?php echo $path_prefix; ?>employer/manage_jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php else: ?>
                        <a class="nav-link" href="<?php echo $path_prefix; ?>jobseeker/jobs.php"><?php echo $t['job_portal']; ?></a>
                    <?php endif; ?>
                </li>
                <li class="nav-item <?php echo $currentPage == 'eservices.php' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo $path_prefix; ?>eservices.php"><?php echo $t['eservices']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'trainings.php' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo $path_prefix; ?>trainings.php"><?php echo $t['trainings']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'notice.php' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo $path_prefix; ?>notice.php"><?php echo $t['notice']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'statistics.php' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo $path_prefix; ?>statistics.php"><?php echo $t['statistics']; ?></a></li>
                <li class="nav-item <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>"><a class="nav-link" href="<?php echo $path_prefix; ?>contact.php"><?php echo $t['contact']; ?></a></li>
            </ul>

            <!-- Profile / Auth links (always aligned right inside collapse on desktop) -->
            <div class="d-flex align-items-center ms-0 ms-lg-auto mt-2 mt-lg-0 py-1 py-lg-0">
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
                        <!-- Pill-shaped profile trigger -->
                        <a class="profile-trigger" href="#" id="profileDropdown"
                           role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <div class="d-none d-sm-block lh-1">
                                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <span class="d-block profile-role"><?php echo $role_label; ?></span>
                            </div>
                            <div class="profile-avatar"><?php echo $initial; ?></div>
                            <i class="fa-solid fa-chevron-down profile-chevron d-none d-sm-block"></i>
                        </a>

                        <!-- Professional dropdown -->
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="profileDropdown">
                            <!-- Green identity card header -->
                            <div class="profile-card-header">
                                <div class="drop-avatar"><?php echo $initial; ?></div>
                                <div>
                                    <div class="drop-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                    <div class="drop-email"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
                                    <span class="drop-role-badge"><?php echo $role_label; ?></span>
                                </div>
                            </div>

                            <!-- Menu items -->
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
                    <a class="nav-link text-white me-2 px-2" href="<?php echo $path_prefix; ?>auth/login.php"><?php echo $t['login']; ?></a>
                    <a class="btn-nav-register<?php echo $currentPage == 'register.php' ? ' active-reg' : ''; ?>" href="<?php echo $path_prefix; ?>auth/register.php">
                        <?php echo $t['register']; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>

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