<?php
session_start();
include('assets/config/db.php');
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<!-- About Hero -->
<section class="about-hero">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="about-badge">সরকারি উদ্যোগ</span>
                <h1 class="about-hero-title">জীবিকা — স্মার্ট কর্মসংস্থান প্ল্যাটফর্ম</h1>
                <p class="about-hero-text">গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের ভাবনায় তৈরি একটি আধুনিক, ডেটা-চালিত এবং এলাকাভিত্তিক কর্মসংস্থান ও বেকারত্ব দূরীকরণ প্ল্যাটফর্ম।</p>
                <a href="/auth/register.php" class="btn btn-warning btn-lg mt-3">এখনই যোগ দিন →</a>
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
            <h2 class="section-title">আমাদের লক্ষ্য ও উদ্দেশ্য</h2>
            <p class="section-subtitle">বাংলাদেশের প্রতিটি প্রান্তে কর্মসংস্থানের সুযোগ পৌঁছে দেওয়া</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="about-feature-card">
                    <div class="about-feature-icon">🎯</div>
                    <h4>আমাদের লক্ষ্য</h4>
                    <p>বাংলাদেশের প্রতিটি জেলার উপজেলা ও ওয়ার্ড পর্যায়ে বেকারত্বের হার মনিটরিং করা এবং তরুণদের দক্ষতা অনুযায়ী কাজের সুযোগ তৈরি করা।</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about-feature-card">
                    <div class="about-feature-icon">🏛️</div>
                    <h4>সরকারি সহায়তা</h4>
                    <p>সরকারি নীতিনির্ধারক ও এনজিওদের জন্য বাস্তব ডেটা সরবরাহ করা, যাতে তারা কার্যকরী প্রশিক্ষণ ও উন্নয়ন কার্যক্রম গ্রহণ করতে পারে।</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about-feature-card">
                    <div class="about-feature-icon">🤝</div>
                    <h4>পার্টনারশিপ</h4>
                    <p>উদ্যোক্তা ও ক্ষুদ্র ব্যবসায়ীদের জন্য দক্ষতাভিত্তিক পার্টনার খুঁজে পেতে সাহায্য করা এবং কর্মসংস্থান বৃদ্ধি করা।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="about-how-section py-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="text-center mb-5">
            <h2 class="section-title">কিভাবে কাজ করে?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="about-step-card">
                    <div class="about-step-num">০১</div>
                    <h5>রেজিস্ট্রেশন</h5>
                    <p>চাকরি প্রার্থী বা নিয়োগকারী হিসেবে বিনামূল্যে অ্যাকাউন্ট তৈরি করুন।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="about-step-card">
                    <div class="about-step-num">০২</div>
                    <h5>প্রোফাইল সেটআপ</h5>
                    <p>আপনার দক্ষতা, অভিজ্ঞতা এবং এলাকার তথ্য যোগ করুন।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="about-step-card">
                    <div class="about-step-num">০৩</div>
                    <h5>স্মার্ট ম্যাচিং</h5>
                    <p>আপনার দক্ষতা অনুযায়ী সেরা চাকরি বা কর্মী খুঁজে পান।</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="about-step-card">
                    <div class="about-step-num">০৪</div>
                    <h5>আবেদন ও নিয়োগ</h5>
                    <p>সরাসরি আবেদন করুন এবং দ্রুত নিয়োগ প্রক্রিয়া সম্পন্ন করুন।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Features -->
<section class="py-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="text-center mb-5">
            <h2 class="section-title">প্রধান বৈশিষ্ট্যসমূহ</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="about-highlight-card">
                    <img src="/assets/image/digital_skills.png" alt="Skills" class="about-highlight-img">
                    <div class="about-highlight-body">
                        <h4>স্মার্ট জব ম্যাচিং</h4>
                        <p>আপনার দক্ষতা ও অবস্থান অনুযায়ী সঠিক চাকরি খুঁজে পান। আমাদের অ্যালগরিদম আপনার প্রোফাইলের সাথে সর্বোচ্চ মিলসম্পন্ন চাকরি দেখায়।</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="about-highlight-card">
                    <img src="/assets/image/new_jobs.png" alt="Monitoring" class="about-highlight-img">
                    <div class="about-highlight-body">
                        <h4>এলাকাভিত্তিক মনিটরিং</h4>
                        <p>জেলা, উপজেলা ও ওয়ার্ড পর্যায়ে বেকারত্বের রিয়েল-টাইম ডেটা। সরকার ও পরিকল্পনাকারীরা সহজেই বুঝতে পারবেন কোথায় কী ধরনের সহায়তা প্রয়োজন।</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="about-cta-section py-5">
    <div class="container-fluid px-4 px-xl-5 text-center">
        <h2 class="text-white mb-3" style="font-weight:800; font-size:2.5rem;">আজই শুরু করুন আপনার জীবিকার যাত্রা</h2>
        <p class="text-white mb-4" style="font-size:1.2rem; opacity:0.9;">লক্ষ লক্ষ চাকরি প্রার্থী ও নিয়োগকারী ইতিমধ্যে জীবিকায় যুক্ত হয়েছেন।</p>
        <a href="/auth/register.php" class="btn btn-warning btn-lg px-5">বিনামূল্যে রেজিস্ট্রেশন করুন</a>
    </div>
</section>

<?php include('includes/footer.php'); ?>
