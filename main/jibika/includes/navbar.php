<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}

$lang = $_SESSION['lang'] ?? 'bn';

$navText = [
    'bn' => [
        'gov_badge' => 'বাংলাদেশ',
        'gov_text' => 'গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের ভাবনায় কর্মসংস্থান ও জীবিকা সহায়তা প্ল্যাটফর্ম',
        'date' => 'সোমবার, ২৩ চৈত্র ১৪৩২',
        'home' => 'হোম',
        'about' => 'আমাদের সম্পর্কে',
        'jobs' => 'চাকরি',
        'login' => 'লগইন',
        'register' => 'রেজিস্টার',
        'dashboard' => 'ড্যাশবোর্ড',
        'logout' => 'লগআউট'
    ],
    'en' => [
        'gov_badge' => 'Bangladesh',
        'gov_text' => 'Employment and livelihood support platform inspired by the Government of Bangladesh',
        'date' => 'Monday, 23 Choitro 1432',
        'home' => 'Home',
        'about' => 'About',
        'jobs' => 'Jobs',
        'login' => 'Login',
        'register' => 'Register',
        'dashboard' => 'Dashboard',
        'logout' => 'Logout'
    ]
];

$t = $navText[$lang];
?>

<div class="top-gov-bar">
    <div class="container gov-bar-inner">
        <div class="gov-left">
            <div class="gov-badge"><?php echo $t['gov_badge']; ?></div>
            <div class="gov-text"><?php echo $t['gov_text']; ?></div>
        </div>

        <div class="gov-right">
            <span class="gov-date"><?php echo $t['date']; ?></span>
            <div class="lang-switch">
                <a href="?lang=bn" class="<?php echo $lang == 'bn' ? 'lang-active' : ''; ?>">বাংলা</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'lang-active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top main-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold brand-text" href="/jibika/jibika/index.php">
            <span class="brand-en">Jibika</span>
            <span class="brand-bn">জীবিকা</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="/jibika/jibika/index.php"><?php echo $t['home']; ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/jibika/jibika/about.php"><?php echo $t['about']; ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/jibika/jibika/jobseeker/jobs.php"><?php echo $t['jobs']; ?></a>
                </li>

                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>

                    <?php if ($_SESSION['role'] == 'job_seeker'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/jibika/jibika/jobseeker/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'employer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/jibika/jibika/employer/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/jibika/jibika/admin/dashboard.php"><?php echo $t['dashboard']; ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-danger px-4" href="/jibika/jibika/auth/logout.php">
                            <?php echo $t['logout']; ?>
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/jibika/jibika/auth/login.php"><?php echo $t['login']; ?></a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-success px-4" href="/jibika/jibika/auth/register.php">
                            <?php echo $t['register']; ?>
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>