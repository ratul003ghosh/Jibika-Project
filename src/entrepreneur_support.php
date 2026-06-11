<?php
session_start();
include('assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$entText = [
    'bn' => [
        'title' => 'উদ্যোক্তা সহায়তা',
        'sub' => 'সম্পদ, অর্থায়ন এবং মেন্টরশিপের মাধ্যমে পরবর্তী প্রজন্মের ব্যবসায়িক নেতাদের ক্ষমতায়ন।',
        'card1_title' => 'ক্ষুদ্র ও মাঝারি ঋণ সহায়তা (SME Loan)',
        'card1_desc' => 'আপনার স্টার্টআপ বা ক্ষুদ্র ব্যবসার জন্য সরকারি সহায়তাপুষ্ট এসএমই লোন, কম সুদে অর্থায়নের সুবিধা এবং আবেদন প্রক্রিয়া সম্পর্কে জানুন।',
        'card1_btn' => 'আরও জানুন',
        'card2_title' => 'আইনি ও নিবন্ধন সহায়তা',
        'card2_desc' => 'বাংলাদেশে কীভাবে ট্রেড লাইসেন্স, টিআইএন, ভ্যাট রেজিস্ট্রেশন এবং কোম্পানি নিবন্ধন করা যায় তার ধাপে ধাপে নির্দেশিকা পান।',
        'card2_btn' => 'ই-সেবা ব্যবহার করুন',
        'card3_title' => 'পার্টনার এবং কো-ফাউন্ডার খুঁজুন',
        'card3_desc' => 'কারিগরি দক্ষতা বা বিনিয়োগের মূলধন সম্পন্ন কোনো ব্যক্তি খুঁজছেন? সম্ভাব্য অংশীদারদের সাথে সংযোগ করতে আমাদের পার্টনার ফাইন্ডার ব্যবহার করুন।',
        'card3_btn' => 'পার্টনার খুঁজুন',
        'inc_title' => 'জীবিকা স্টার্টআপ ইনকিউবেটরে যোগ দিন',
        'inc_desc' => 'আমরা নিবিড় মেন্টরশিপ, অফিস স্পেস বরাদ্দ এবং সরাসরি ভেঞ্চার ক্যাপিটালিস্টদের সাথে সংযোগের জন্য প্রতি বছর ৫০টি প্রতিশ্রুতিশীল স্টার্টআপ নির্বাচন করি।',
        'inc_btn' => 'ইনকিউবেশনের জন্য আবেদন করুন',
        'inc_alert' => 'পরবর্তী কোহর্টের জন্য আবেদন ২০২৭ সালের জানুয়ারিতে শুরু হবে।'
    ],
    'en' => [
        'title' => 'Entrepreneur Support',
        'sub' => 'Empowering the next generation of business leaders with resources, funding, and mentorship.',
        'card1_title' => 'SME Loan Assistance',
        'card1_desc' => 'Learn about government-backed SME loans, low-interest funding options, and application processes for your startup or small business.',
        'card1_btn' => 'Learn More',
        'card2_title' => 'Legal & Registration',
        'card2_desc' => 'Get step-by-step guidance on how to acquire Trade Licenses, TIN, VAT registration, and company incorporation in Bangladesh.',
        'card2_btn' => 'Access E-Services',
        'card3_title' => 'Partner & Co-founder',
        'card3_desc' => 'Looking for someone with technical skills or investment capital? Use our Partner Finder to connect with potential co-founders.',
        'card3_btn' => 'Find a Partner',
        'inc_title' => 'Join the Jibika Startup Incubator',
        'inc_desc' => 'We select 50 promising startups every year for intensive mentorship, office space allocation, and direct connections to venture capitalists.',
        'inc_btn' => 'Apply for Incubation',
        'inc_alert' => 'Applications for the next cohort will open in January 2027.'
    ]
];
$e = $entText[$lang];

include('includes/header.php');
include('includes/navbar.php');
?>

<link rel="stylesheet" href="assets/css/entrepreneur_support.css">

<div class="resource-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold"><i class="fa-solid fa-rocket me-3"></i><?php echo htmlspecialchars($e['title']); ?></h1>
        <p class="fs-5 opacity-75 mb-0"><?php echo htmlspecialchars($e['sub']); ?></p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($e['card1_title']); ?></h4>
                <p class="text-muted mb-4"><?php echo htmlspecialchars($e['card1_desc']); ?></p>
                <a href="#" class="btn btn-outline-success rounded-pill fw-bold"><?php echo htmlspecialchars($e['card1_btn']); ?></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-file-contract"></i></div>
                <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($e['card2_title']); ?></h4>
                <p class="text-muted mb-4"><?php echo htmlspecialchars($e['card2_desc']); ?></p>
                <a href="eservices.php" class="btn btn-outline-success rounded-pill fw-bold"><?php echo htmlspecialchars($e['card2_btn']); ?></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($e['card3_title']); ?></h4>
                <p class="text-muted mb-4"><?php echo htmlspecialchars($e['card3_desc']); ?></p>
                <a href="jobseeker/partner_finder.php" class="btn btn-outline-success rounded-pill fw-bold"><?php echo htmlspecialchars($e['card3_btn']); ?></a>
            </div>
        </div>
    </div>

    <div class="bg-dark text-white rounded-4 p-5 shadow-lg position-relative overflow-hidden">
        <div class="row align-items-center position-relative" style="z-index: 1;">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($e['inc_title']); ?></h2>
                <p class="fs-5 opacity-75 mb-4"><?php echo htmlspecialchars($e['inc_desc']); ?></p>
                <button class="btn btn-warning btn-lg px-5 fw-bold rounded-pill text-dark" onclick="alert('<?php echo addslashes($e['inc_alert']); ?>')"><?php echo htmlspecialchars($e['inc_btn']); ?></button>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <i class="fa-solid fa-lightbulb text-warning" style="font-size: 8rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>

</div>

<?php include('includes/footer.php'); ?>
