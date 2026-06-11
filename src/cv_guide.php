<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<?php
$cvText = [
    'bn' => [
        'badge' => 'জীবিকা রিসোর্স',
        'title' => 'সিভি লেখার চূড়ান্ত নির্দেশিকা',
        'subtitle' => 'আপনার সিভি আপনার প্রথম ধারণা। পেশাদারদের দ্বারা ব্যবহৃত সঠিক কাঠামো এবং কৌশলগুলো শিখুন আজকের প্রতিযোগিতামূলক চাকরির বাজারে নিয়োগ পেতে।',
        'btn_download' => 'ওয়ার্ড টেমপ্লেট ডাউনলোড করুন',
        'step_title' => 'ধাপে ধাপে সিভির গঠনশৈলী',
        'step1_title' => '১. যোগাযোগের তথ্য',
        'step1_desc' => 'এটি নিয়োগকর্তাদের প্রথমে দেখা উচিত। আপনার পুরো নাম (বড় ফন্টে), একটি পেশাদার ইমেল, ফোন নম্বর এবং লিঙ্কডইন ইউআরএল অন্তর্ভুক্ত করুন। <strong class="text-dark">কখনও</strong> অপেশাদার ইমেল ব্যবহার করবেন না যেমন <em>coolboy99@email.com</em>।',
        'step2_title' => '২. পেশাগত সারসংক্ষেপ',
        'step2_desc' => 'একটি শক্তিশালী ৩ লাইনের অনুচ্ছেদ যা আপনার পরিচয় এবং আপনি কী মূল্য আনেন তা সংক্ষেপে তুলে ধরে। "আমি একজন কঠোর পরিশ্রমী ব্যক্তি" এর মতো সাধারণ বাক্য ব্যবহার করবেন না। পরিবর্তে তথ্য ব্যবহার করুন: "৩ বছরের অভিজ্ঞতাসম্পন্ন ডিজিটাল মার্কেটার যিনি আরওআই ৪০% বৃদ্ধি করেছেন।"',
        'step3_title' => '৩. কাজের অভিজ্ঞতা',
        'step3_desc' => 'আপনার আগের চাকরিগুলো <strong>বিপরীত কালানুক্রমিক ক্রমে</strong> তালিকাভুক্ত করুন। বুলেট পয়েন্ট ব্যবহার করুন। প্রতিটি বুলেট পয়েন্ট একটি অ্যাকশন ভার্ব (যেমন: তৈরি করা, পরিচালনা করা, ডিজাইন করা) দিয়ে শুরু করুন। প্রতিদিনের দায়িত্বের চেয়ে অর্জন এবং সংখ্যার ওপর জোর দিন।',
        'step4_title' => '৪. শিক্ষা',
        'step4_desc' => 'আপনার সর্বোচ্চ ডিগ্রিগুলো প্রথমে উল্লেখ করুন। বিশ্ববিদ্যালয়/কলেজ, গ্রাজুয়েশন সাল এবং মেজর বিষয় অন্তর্ভুক্ত করুন। আপনি যদি বিশ্ববিদ্যালয় থেকে গ্রাজুয়েশন শেষ করে থাকেন, তবে এসএসসি/এইচএসসির বিবরণ দেওয়ার প্রয়োজন নেই যতক্ষণ না বিশেষভাবে চাওয়া হয়।',
        'step5_title' => '৫. মূল দক্ষতাসমূহ',
        'step5_desc' => 'আপনি যে চাকরির জন্য আবেদন করছেন তার জন্য ৫-৮টি অত্যন্ত প্রাসঙ্গিক দক্ষতা তালিকাভুক্ত করুন। হার্ড স্কিল (যেমন: পাইথন, ডাটা এন্ট্রি, অটোক্যাড) এবং সফট স্কিল (যেমন: নেতৃত্ব, যোগাযোগ) এর সংমিশ্রণ করুন। সেগুলোকে সংক্ষিপ্ত এবং সহজে পড়ার উপযোগী রাখুন।',
        'avoid_title' => 'সিভি লেখার সময় যে মারাত্মক ভুলগুলো এড়িয়ে চলবেন',
        'mistake1_title' => 'অপ্রয়োজনীয় ব্যক্তিগত তথ্য অন্তর্ভুক্ত করা',
        'mistake1_desc' => 'সরকারি ফরম্যাটে বিশেষভাবে অনুরোধ না করা হলে আপনার ধর্ম, বৈবাহিক অবস্থা, রক্তের গ্রুপ বা পিতা-মাতার নাম অন্তর্ভুক্ত করবেন না। সাধারণ কর্পোরেট সিভিতে এগুলোর প্রয়োজন নেই।',
        'mistake2_title' => 'অস্বাভাবিক ফন্ট বা রং ব্যবহার করা',
        'mistake2_desc' => 'অ্যারিয়াল, ক্যালিব্রি বা রোবোটোর মতো সাধারণ এবং সহজে পড়ার যোগ্য ফন্ট ব্যবহার করুন। উজ্জ্বল লাল বা গোলাপী টেক্সট ব্যবহার করলে সিভিটি অপেশাদার দেখায়।',
        'anatomy_title' => 'একটি নিখুঁত সিভির চাক্ষুষ গঠনশৈলী',
        'toast_msg' => 'স্ট্যান্ডার্ড জীবিকা টেমপ্লেট ডাউনলোড হচ্ছে...',
        'mock_summary' => 'সারসংক্ষেপ',
        'mock_experience' => 'অভিজ্ঞতা',
        'mock_education' => 'শিক্ষা'
    ],
    'en' => [
        'badge' => 'Jibika Resources',
        'title' => 'The Ultimate CV Writing Guide',
        'subtitle' => 'Your CV is your first impression. Learn the exact structure and techniques used by professionals to get hired in today\'s competitive job market.',
        'btn_download' => 'Download Word Template',
        'step_title' => 'Step-by-Step CV Architecture',
        'step1_title' => '1. Contact Header',
        'step1_desc' => 'This must be the very first thing employers see. Include your full name (large font), a professional email, phone number, and LinkedIn URL. <strong class="text-dark">Never</strong> use unprofessional emails like <em>coolboy99@email.com</em>.',
        'step2_title' => '2. Professional Summary',
        'step2_desc' => 'A powerful 3-line paragraph summarizing who you are and what value you bring. Do not use generic statements like "I am a hardworking person." Instead use facts: "Digital Marketer with 3 years experience increasing ROI by 40%."',
        'step3_title' => '3. Work Experience',
        'step3_desc' => 'List your previous jobs in <strong>reverse-chronological order</strong>. Use bullet points. Start each bullet point with an action verb (e.g., Developed, Managed, Designed). Focus on achievements and numbers, not just daily duties.',
        'step4_title' => '4. Education',
        'step4_desc' => 'Mention your highest degrees first. Include the university/college, graduation year, and major. If you have graduated from university, you do not need to include your SSC/HSC details unless specifically asked.',
        'step5_title' => '5. Core Skills',
        'step5_desc' => 'List 5-8 highly relevant skills for the job you are applying for. Mix hard skills (e.g., Python, Data Entry, AutoCAD) and soft skills (e.g., Leadership, Communication). Keep them punchy and easily scannable.',
        'avoid_title' => 'Critical CV Mistakes to Avoid',
        'mistake1_title' => 'Including Unnecessary Personal Info',
        'mistake1_desc' => 'Do not include your religion, marital status, blood group, or parents\' names unless explicitly requested in a government format. Standard corporate CVs do not need this.',
        'mistake2_title' => 'Using Crazy Fonts or Colors',
        'mistake2_desc' => 'Stick to standard, readable fonts like Arial, Calibri, or Roboto. Using bright red or pink text makes the CV look unprofessional.',
        'anatomy_title' => 'Visual Anatomy of a Perfect CV',
        'toast_msg' => 'Downloading Standard Jibika Template...',
        'mock_summary' => 'Summary',
        'mock_experience' => 'Experience',
        'mock_education' => 'Education'
    ]
];
$ct = $cvText[$lang];
?>

<link rel="stylesheet" href="assets/css/cv_guide.css">

<!-- Hero Section -->
<div class="resource-hero mb-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row align-items-center">
            <div class="col-lg-8" style="z-index: 2;">
                <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-3 fw-bold"><?php echo $ct['badge']; ?></span>
                <h1 class="fw-bold display-5 mb-3"><?php echo $ct['title']; ?></h1>
                <p class="fs-5 opacity-75 mb-0"><?php echo $ct['subtitle']; ?></p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" style="z-index: 2;">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-light btn-lg px-4 fw-bold text-success shadow-sm" onclick="alert('<?php echo addslashes($ct['toast_msg']); ?>')"><i class="fa-solid fa-download me-2"></i><?php echo $ct['btn_download']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    <div class="row g-5">
        
        <!-- Left Column: The Timeline Steps -->
        <div class="col-lg-7">
            <h3 class="fw-bold text-dark border-bottom pb-3 mb-4"><?php echo $ct['step_title']; ?></h3>
            
            <div class="timeline">
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-address-card"></i>
                            </div>
                            <h4 class="fw-bold mb-0"><?php echo $ct['step1_title']; ?></h4>
                        </div>
                        <p class="text-muted mb-0"><?php echo $ct['step1_desc']; ?></p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h4 class="fw-bold mb-0"><?php echo $ct['step2_title']; ?></h4>
                        </div>
                        <p class="text-muted mb-0"><?php echo $ct['step2_desc']; ?></p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <h4 class="fw-bold mb-0"><?php echo $ct['step3_title']; ?></h4>
                        </div>
                        <p class="text-muted mb-0"><?php echo $ct['step3_desc']; ?></p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 text-info rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <h4 class="fw-bold mb-0"><?php echo $ct['step4_title']; ?></h4>
                        </div>
                        <p class="text-muted mb-0"><?php echo $ct['step4_desc']; ?></p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 text-danger rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <h4 class="fw-bold mb-0"><?php echo $ct['step5_title']; ?></h4>
                        </div>
                        <p class="text-muted mb-0"><?php echo $ct['step5_desc']; ?></p>
                    </div>
                </div>

            </div>

            <div class="mt-5 pt-4 border-top">
                <h3 class="fw-bold text-dark mb-4"><?php echo $ct['avoid_title']; ?></h3>
                
                <div class="mistake-card d-flex">
                    <div class="mistake-icon me-3"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1"><?php echo $ct['mistake1_title']; ?></h6>
                        <p class="text-muted small mb-0"><?php echo $ct['mistake1_desc']; ?></p>
                    </div>
                </div>

                <div class="mistake-card d-flex">
                    <div class="mistake-icon me-3"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1"><?php echo $ct['mistake2_title']; ?></h6>
                        <p class="text-muted small mb-0"><?php echo $ct['mistake2_desc']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Visual CV Mockup -->
        <div class="col-lg-5">
            <div class="cv-mockup-container d-none d-md-block">
                <h5 class="fw-bold text-muted mb-3 text-center text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;"><?php echo $ct['anatomy_title']; ?></h5>
                <div class="cv-mockup">
                    
                    <!-- Header Mock -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <div class="mock-title"></div>
                        <div class="mock-subtitle"></div>
                        <div class="d-flex justify-content-center gap-2">
                            <div class="mock-line" style="width: 20px;"></div>
                            <div class="mock-line" style="width: 40px;"></div>
                            <div class="mock-line" style="width: 30px;"></div>
                        </div>
                    </div>

                    <!-- Summary Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-primary"><?php echo $ct['mock_summary']; ?></div>
                        <div class="mock-line long"></div>
                        <div class="mock-line long"></div>
                        <div class="mock-line medium"></div>
                    </div>

                    <!-- Experience Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-warning text-dark"><?php echo $ct['mock_experience']; ?></div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 20%; background: #cbd5e1;"></div>
                        </div>
                        <ul class="ps-3 mb-4">
                            <li><div class="mock-line long mt-2"></div></li>
                            <li><div class="mock-line medium"></div></li>
                        </ul>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 20%; background: #cbd5e1;"></div>
                        </div>
                        <ul class="ps-3 mb-0">
                            <li><div class="mock-line long mt-2"></div></li>
                        </ul>
                    </div>

                    <!-- Education Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-info text-dark"><?php echo $ct['mock_education']; ?></div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 15%; background: #cbd5e1;"></div>
                        </div>
                        <div class="mock-line medium mb-0"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include('includes/footer.php'); ?>
