<?php
session_start();
include('assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$tipsText = [
    'bn' => [
        'title' => 'ইন্টারভিউ টিপস',
        'sub' => 'ইন্টারভিউয়ের কৌশল আয়ত্ত করুন এবং আপনার স্বপ্নের চাকরিটি নিশ্চিত করুন।',
        'sec1_title' => 'ইন্টারভিউয়ের আগে',
        'sec2_title' => 'ইন্টারভিউ চলাকালীন',
        'tip1_title' => 'কোম্পানি সম্পর্কে গবেষণা করুন',
        'tip1_desc' => 'কোম্পানির পণ্য, পরিষেবা, কাজের পরিবেশ এবং সাম্প্রতিক খবরাখবর পড়তে সময় দিন। তাদের লক্ষ্যগুলো বোঝা আপনাকে প্রশ্নের আরও কার্যকর উত্তর দিতে সাহায্য করবে।',
        'tip2_title' => 'পেশাদার পোশাক পরিধান করুন',
        'tip2_desc' => 'কোম্পানির ড্রেস কোডের সাথে মানানসই পোশাক বেছে নিন। কোনো সন্দেহ থাকলে, সাধারণ পোশাকের চেয়ে একটু বেশি আনুষ্ঠানিক পোশাক পরাই ভালো। আপনার পোশাক পরিষ্কার এবং ইস্ত্রি করা তা নিশ্চিত করুন।',
        'tip3_title' => 'সময়ানুবর্তিতা অত্যন্ত গুরুত্বপূর্ণ',
        'tip3_desc' => 'নির্ধারিত ইন্টারভিউয়ের সময়ের অন্তত ১৫ মিনিট আগে পৌঁছানোর চেষ্টা করুন। এটি যদি অনলাইন ইন্টারভিউ হয়, তবে আপনার অডিও এবং ভিডিও সেটআপ পরীক্ষা করতে ৫ মিনিট আগে লগ ইন করুন।',
        'tip4_title' => 'শারীরিক অঙ্গভঙ্গি ঠিক রাখুন',
        'tip4_desc' => 'একটি দৃঢ় হ্যান্ডশেক করুন (সরাসরি ইন্টারভিউ হলে), চোখে চোখ রেখে কথা বলুন এবং সোজা হয়ে বসুন। ইতিবাচক শারীরিক অঙ্গভঙ্গি আত্মবিশ্বাস এবং আগ্রহ প্রকাশ করে।',
        'tip5_title' => 'STAR পদ্ধতি ব্যবহার করুন',
        'tip5_desc' => 'STAR টেকনিক ব্যবহার করে আচরণগত প্রশ্নের উত্তর দিন: Situation (পরিস্থিতি), Task (কাজ), Action (পদক্ষেপ), এবং Result (ফলাফল)। এটি আপনার উত্তরগুলোকে সুগঠিত, সংক্ষিপ্ত এবং প্রভাবশালী রাখে।',
        'tip6_title' => 'প্রশ্ন জিজ্ঞাসা করুন',
        'tip6_desc' => 'ইন্টারভিউয়ের শেষে যখন জিজ্ঞাসা করা হবে যে আপনার কোনো প্রশ্ন আছে কি না, তখন ভূমিকা বা কোম্পানির সংস্কৃতি সম্পর্কে ১-২টি চিন্তাশীল প্রশ্ন সবসময় প্রস্তুত রাখুন।',
        'cta_title' => 'ব্যক্তিগতকৃত সাহায্য প্রয়োজন?',
        'cta_desc' => 'একটি মক ইন্টারভিউ দিতে এবং আপনার আত্মবিশ্বাস বাড়াতে আমাদের ক্যারিয়ার বিশেষজ্ঞদের সাথে একটি সেশন বুক করুন।',
        'cta_btn' => 'মক ইন্টারভিউ বুক করুন'
    ],
    'en' => [
        'title' => 'Interview Tips',
        'sub' => 'Master the art of interviewing and secure your dream job.',
        'sec1_title' => 'Before the Interview',
        'sec2_title' => 'During the Interview',
        'tip1_title' => 'Research the Company',
        'tip1_desc' => "Spend time reading about the company's products, services, culture, and recent news. Understanding their goals will help you answer questions more effectively.",
        'tip2_title' => 'Dress Professionally',
        'tip2_desc' => "Choose attire that aligns with the company's dress code. When in doubt, it is always better to overdress slightly than to underdress. Ensure your clothes are neat and ironed.",
        'tip3_title' => 'Punctuality is Key',
        'tip3_desc' => 'Aim to arrive at least 15 minutes before the scheduled interview time. If it is an online interview, log in 5 minutes early to test your audio and video setup.',
        'tip4_title' => 'Maintain Body Language',
        'tip4_desc' => 'Offer a firm handshake (if in person), maintain good eye contact, and sit up straight. Positive body language shows confidence and engagement.',
        'tip5_title' => 'Use the STAR Method',
        'tip5_desc' => 'Answer behavioral questions using the STAR technique: Situation, Task, Action, and Result. This keeps your answers structured, concise, and impactful.',
        'tip6_title' => 'Ask Questions',
        'tip6_desc' => 'At the end of the interview, when asked if you have questions, always have 1-2 thoughtful questions ready about the role or company culture.',
        'cta_title' => 'Need Personalized Help?',
        'cta_desc' => 'Book a session with one of our career experts to do a mock interview and improve your confidence.',
        'cta_btn' => 'Book Mock Interview'
    ]
];
$t = $tipsText[$lang];

include('includes/header.php');
include('includes/navbar.php');
?>

<style>
    body { background-color: #f8f9fa; }
    .resource-header {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }
    .tip-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-top: 4px solid #006a4e;
        transition: transform 0.2s;
    }
    .tip-card:hover {
        transform: translateY(-5px);
    }
    .tip-icon {
        font-size: 2.5rem;
        color: #006a4e;
        margin-bottom: 15px;
    }
</style>

<div class="resource-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold"><i class="fa-solid fa-microphone-lines me-3"></i><?php echo htmlspecialchars($t['title']); ?></h1>
        <p class="fs-5 opacity-75 mb-0"><?php echo htmlspecialchars($t['sub']); ?></p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    
    <div class="mb-5">
        <h3 class="fw-bold border-bottom pb-2 text-dark"><?php echo htmlspecialchars($t['sec1_title']); ?></h3>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip1_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip1_desc']); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-solid fa-shirt"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip2_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip2_desc']); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-regular fa-clock"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip3_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip3_desc']); ?></p>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h3 class="fw-bold border-bottom pb-2 text-dark"><?php echo htmlspecialchars($t['sec2_title']); ?></h3>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-eye"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip4_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip4_desc']); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-bullseye"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip5_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip5_desc']); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-circle-question"></i></div>
                <h5 class="fw-bold"><?php echo htmlspecialchars($t['tip6_title']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($t['tip6_desc']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-4 shadow-sm p-5 border text-center">
        <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($t['cta_title']); ?></h4>
        <p class="text-muted mb-4"><?php echo htmlspecialchars($t['cta_desc']); ?></p>
        <a href="career_counseling.php" class="btn btn-success btn-lg px-5 rounded-pill fw-bold" style="background-color: #006a4e; border: none;"><?php echo htmlspecialchars($t['cta_btn']); ?></a>
    </div>

</div>

<?php include('includes/footer.php'); ?>
