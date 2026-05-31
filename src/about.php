<?php
session_start();
include('assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';
$about_text = [
    'bn' => [
        'badge' => 'সরকারি উদ্যোগ',
        'title' => 'জীবিকা — স্মার্ট কর্মসংস্থান প্ল্যাটফর্ম',
        'desc' => 'গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের ভাবনায় তৈরি একটি আধুনিক, ডেটা-চালিত এবং এলাকাভিত্তিক কর্মসংস্থান ও বেকারত্ব দূরীকরণ প্ল্যাটফর্ম।',
        'join_now' => 'এখনই যোগ দিন →',
        'mission_title' => 'আমাদের লক্ষ্য ও উদ্দেশ্য',
        'mission_sub' => 'বাংলাদেশের প্রতিটি প্রান্তে কর্মসংস্থানের সুযোগ পৌঁছে দেওয়া',
        'mission_card_title' => 'আমাদের লক্ষ্য',
        'mission_card_desc' => 'বাংলাদেশের প্রতিটি জেলার উপজেলা ও ওয়ার্ড পর্যায়ে বেকারত্বের হার মনিটরিং করা এবং তরুণদের দক্ষতা অনুযায়ী কাজের সুযোগ তৈরি করা।',
        'govt_card_title' => 'সরকারি সহায়তা',
        'govt_card_desc' => 'সরকারি নীতিনির্ধারক ও এনজিওদের জন্য বাস্তব ডেটা সরবরাহ করা, যাতে তারা কার্যকরী প্রশিক্ষণ ও উন্নয়ন কার্যক্রম গ্রহণ করতে পারে।',
        'partner_card_title' => 'পার্টনারশিপ',
        'partner_card_desc' => 'উদ্যোক্তা ও ক্ষুদ্র ব্যবসায়ীদের জন্য দক্ষতাভিত্তিক পার্টনার খুঁজে পেতে সাহায্য করা এবং কর্মসংস্থান বৃদ্ধি করা।',
        'how_it_works' => 'কিভাবে কাজ করে?',
        'step1_num' => '০১',
        'step1_title' => 'রেজিস্ট্রেশন',
        'step1_desc' => 'চাকরি প্রার্থী বা নিয়োগকারী হিসেবে বিনামূল্যে অ্যাকাউন্ট তৈরি করুন।',
        'step2_num' => '০২',
        'step2_title' => 'প্রোফাইল সেটআপ',
        'step2_desc' => 'আপনার দক্ষতা, অভিজ্ঞতা এবং এলাকার তথ্য যোগ করুন।',
        'step3_num' => '০৩',
        'step3_title' => 'স্মার্ট ম্যাচিং',
        'step3_desc' => 'আপনার দক্ষতা অনুযায়ী সেরা চাকরি বা কর্মী খুঁজে পান।',
        'step4_num' => '০৪',
        'step4_title' => 'আবেদন ও নিয়োগ',
        'step4_desc' => 'সরাসরি আবেদন করুন এবং দ্রুত নিয়োগ প্রক্রিয়া সম্পন্ন করুন।',
        'features_title' => 'প্রধান বৈশিষ্ট্যসমূহ',
        'feature1_title' => 'স্মার্ট জব ম্যাচিং',
        'feature1_desc' => 'আপনার দক্ষতা ও অবস্থান অনুযায়ী সঠিক চাকরি খুঁজে পান। আমাদের অ্যালগরিদম আপনার প্রোফাইলের সাথে সর্বোচ্চ মিলসম্পন্ন চাকরি দেখায়।',
        'feature2_title' => 'এলাকাভিত্তিক মনিটরিং',
        'feature2_desc' => 'জেলা, উপজেলা ও ওয়ার্ড পর্যায়ে বেকারত্বের রিয়েল-টাইম ডেটা। সরকার ও পরিকল্পনাকারীরা সহজেই বুঝতে পারবেন কোথায় কী ধরনের সহায়তা প্রয়োজন।',
        'cta_title' => 'আজই শুরু করুন আপনার জীবিকার যাত্রা',
        'cta_sub' => 'লক্ষ লক্ষ চাকরি প্রার্থী ও নিয়োগকারী ইতিমধ্যে জীবিকায় যুক্ত হয়েছেন।',
        'cta_btn' => 'বিনামূল্যে রেজিস্ট্রেশন করুন'
    ],
    'en' => [
        'badge' => 'Government Initiative',
        'title' => 'Jibika — Smart Employment Platform',
        'desc' => 'A modern, data-driven, area-based employment and unemployment reduction platform, envisioned by the Government of Bangladesh.',
        'join_now' => 'Join Now →',
        'mission_title' => 'Our Mission & Vision',
        'mission_sub' => 'Reaching employment opportunities to every corner of Bangladesh',
        'mission_card_title' => 'Our Goal',
        'mission_card_desc' => 'Monitoring unemployment rates at district, upazila, and ward levels across Bangladesh and creating job opportunities based on youth skills.',
        'govt_card_title' => 'Government Support',
        'govt_card_desc' => 'Providing real data for government policymakers and NGOs so they can adopt effective training and development programs.',
        'partner_card_title' => 'Partnership',
        'partner_card_desc' => 'Helping entrepreneurs and small business owners find skill-based partners and increasing employment.',
        'how_it_works' => 'How it works?',
        'step1_num' => '01',
        'step1_title' => 'Registration',
        'step1_desc' => 'Create a free account as a job seeker or employer.',
        'step2_num' => '02',
        'step2_title' => 'Profile Setup',
        'step2_desc' => 'Add your skills, experience, and location information.',
        'step3_num' => '03',
        'step3_title' => 'Smart Matching',
        'step3_desc' => 'Find the best job or employee according to your skills.',
        'step4_num' => '04',
        'step4_title' => 'Apply & Hire',
        'step4_desc' => 'Apply directly and complete the hiring process quickly.',
        'features_title' => 'Key Features',
        'feature1_title' => 'Smart Job Matching',
        'feature1_desc' => 'Find the right job based on your skills and location. Our algorithm shows jobs with the highest match to your profile.',
        'feature2_title' => 'Area-based Monitoring',
        'feature2_desc' => 'Real-time unemployment data at district, upazila, and ward levels. Planners can easily understand where support is needed.',
        'cta_title' => 'Start Your Jibika Journey Today',
        'cta_sub' => 'Millions of job seekers and employers have already joined Jibika.',
        'cta_btn' => 'Register for Free'
    ]
];
$at = $about_text[$lang];
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<!-- About Hero -->
<section class="about-hero">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="about-badge"><?php echo $at['badge']; ?></span>
                <h1 class="about-hero-title"><?php echo $at['title']; ?></h1>
                <p class="about-hero-text"><?php echo $at['desc']; ?></p>
                <a href="/auth/register.php" class="btn btn-warning btn-lg mt-3"><?php echo $at['join_now']; ?></a>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <img src="/assets/image/govt_data.png" alt="Jibika Platform" class="img-fluid rounded-4 shadow-lg" style="width:100%; max-width:650px; height:auto; border: 5px solid white;">
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="text-center mb-5">
            <h2 class="section-title"><?php echo $at['mission_title']; ?></h2>
            <p class="section-subtitle"><?php echo $at['mission_sub']; ?></p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <a href="/jobseeker/jobs.php" class="text-decoration-none">
                    <div class="about-feature-card">
                        <div class="about-feature-icon">🎯</div>
                        <h4><?php echo $at['mission_card_title']; ?></h4>
                        <p><?php echo $at['mission_card_desc']; ?></p>
                    </div>
                </a>
            </div>
            <div class="col-lg-4">
                <a href="/admin/reports.php" class="text-decoration-none">
                    <div class="about-feature-card">
                        <div class="about-feature-icon">🏛️</div>
                        <h4><?php echo $at['govt_card_title']; ?></h4>
                        <p><?php echo $at['govt_card_desc']; ?></p>
                    </div>
                </a>
            </div>
            <div class="col-lg-4">
                <a href="/jobseeker/partner_finder.php" class="text-decoration-none">
                    <div class="about-feature-card">
                        <div class="about-feature-icon">🤝</div>
                        <h4><?php echo $at['partner_card_title']; ?></h4>
                        <p><?php echo $at['partner_card_desc']; ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="about-how-section py-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="text-center mb-5">
            <h2 class="section-title"><?php echo $at['how_it_works']; ?></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <a href="/auth/register.php" class="text-decoration-none">
                    <div class="about-step-card">
                        <div class="about-step-num"><?php echo $at['step1_num']; ?></div>
                        <h5><?php echo $at['step1_title']; ?></h5>
                        <p><?php echo $at['step1_desc']; ?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="/auth/login.php" class="text-decoration-none">
                    <div class="about-step-card">
                        <div class="about-step-num"><?php echo $at['step2_num']; ?></div>
                        <h5><?php echo $at['step2_title']; ?></h5>
                        <p><?php echo $at['step2_desc']; ?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="/jobseeker/jobs.php" class="text-decoration-none">
                    <div class="about-step-card">
                        <div class="about-step-num"><?php echo $at['step3_num']; ?></div>
                        <h5><?php echo $at['step3_title']; ?></h5>
                        <p><?php echo $at['step3_desc']; ?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="/jobseeker/jobs.php" class="text-decoration-none">
                    <div class="about-step-card">
                        <div class="about-step-num"><?php echo $at['step4_num']; ?></div>
                        <h5><?php echo $at['step4_title']; ?></h5>
                        <p><?php echo $at['step4_desc']; ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Key Features -->
<section class="py-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="text-center mb-5">
            <h2 class="section-title"><?php echo $at['features_title']; ?></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <a href="/jobseeker/jobs.php" class="text-decoration-none">
                    <div class="about-highlight-card">
                        <img src="/assets/image/digital_skills.png" alt="Smart Job Matching" class="about-highlight-img">
                        <div class="about-highlight-body">
                            <h4><?php echo $at['feature1_title']; ?></h4>
                            <p><?php echo $at['feature1_desc']; ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="/admin/reports.php" class="text-decoration-none">
                    <div class="about-highlight-card">
                        <img src="/assets/image/new_jobs.png" alt="Area Monitoring" class="about-highlight-img">
                        <div class="about-highlight-body">
                            <h4><?php echo $at['feature2_title']; ?></h4>
                            <p><?php echo $at['feature2_desc']; ?></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="about-cta-section py-5">
    <div class="container-fluid px-4 px-xl-5 text-center">
        <h2 class="text-white mb-3" style="font-weight:800; font-size:2.5rem;"><?php echo $at['cta_title']; ?></h2>
        <p class="text-white mb-4" style="font-size:1.2rem; opacity:0.9;"><?php echo $at['cta_sub']; ?></p>
        <a href="/auth/register.php" class="btn btn-warning btn-lg px-5"><?php echo $at['cta_btn']; ?></a>
    </div>
</section>

<?php include('includes/footer.php'); ?>
