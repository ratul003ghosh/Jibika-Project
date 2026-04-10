<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<div class="container-fluid p-0">

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <p class="hero-quote">প্রতিটি দক্ষতার পেছনে লুকিয়ে আছে একটি সম্ভাবনার গল্প</p>

                    <h1 class="hero-title">
                        একটি দক্ষতা, একটি সুযোগ —<br>
                        বদলে দিন আপনার গল্প
                    </h1>

                    <p class="hero-subtitle">
                        Jibika is an area-based unemployment monitoring and smart job matching system.
                    </p>

                    <div class="hero-buttons">
                        <a href="/jibika/register.php" class="btn btn-warning btn-lg me-2 mb-2">Get Started</a>
                        <a href="/jibika/login.php" class="btn btn-outline-light btn-lg mb-2">Login</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-info-box">
                        <div class="info-card">
                            <h5>এলাকা ভিত্তিক মনিটরিং</h5>
                            <p>জেলা, উপজেলা ও ওয়ার্ডভিত্তিক বেকারত্ব পর্যবেক্ষণ</p>
                        </div>

                        <div class="info-card">
                            <h5>স্কিল ম্যাপিং</h5>
                            <p>কার কী দক্ষতা আছে এবং কোন এলাকায় আছে তা সহজে জানা</p>
                        </div>

                        <div class="info-card">
                            <h5>স্মার্ট জব ম্যাচিং</h5>
                            <p>সঠিক দক্ষতাকে সঠিক চাকরির সাথে দ্রুত যুক্ত করা</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- START JOURNEY -->
    <section class="journey-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Start Your Journey with Jibika</h2>
                <p class="section-subtitle">
                    Whether you are a job seeker, employer, policymaker, or entrepreneur, Jibika helps you move forward.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card">
                        <h5>Job Seekers</h5>
                        <p>Find jobs based on your skills, district, and local opportunities.</p>
                        <a href="/jibika/register.php" class="btn btn-sm btn-light">Explore</a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="journey-card">
                        <h5>Employers</h5>
                        <p>Hire suitable workers easily by skill, area, and job profile.</p>
                        <a href="/jibika/register.php" class="btn btn-sm btn-light">Hire Now</a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="journey-card">
                        <h5>Government</h5>
                        <p>Monitor unemployment trends area-wise for better planning and policy.</p>
                        <a href="/jibika/admin_login.php" class="btn btn-sm btn-light">View Access</a>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="journey-card">
                        <h5>Entrepreneurs</h5>
                        <p>Find potential partners and build small businesses with shared skills.</p>
                        <a href="/jibika/register.php" class="btn btn-sm btn-light">Start Building</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEARCH SECTION -->
    <section class="search-section py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">আপনি কী খুঁজছেন?</h2>
                <p class="section-subtitle">চাকরির শিরোনাম, জেলা, উপজেলা বা দক্ষতা অনুযায়ী খুঁজুন।</p>
            </div>

            <div class="search-box">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <input type="text" class="form-control custom-input" placeholder="চাকরির নাম বা দক্ষতা দিয়ে খুঁজুন">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control custom-input" placeholder="জেলা নির্বাচন করুন">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control custom-input" placeholder="উপজেলা">
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="/jibika/user/jobs.php" class="btn btn-warning">চাকরি খুঁজুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTORS -->
    <section class="sector-section py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">বাংলাদেশের গুরুত্বপূর্ণ চাকরির খাতসমূহ</h2>
            </div>

            <div class="row g-3">
                <div class="col-md-3 col-6"><div class="sector-card">আইটি ও ফ্রিল্যান্সিং</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">গার্মেন্টস ও টেক্সটাইল</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">ড্রাইভিং ও ট্রান্সপোর্ট</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">স্বাস্থ্যসেবা</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">কৃষি</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">বিক্রয় ও মার্কেটিং</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">শিক্ষা ও প্রশিক্ষণ</div></div>
                <div class="col-md-3 col-6"><div class="sector-card">ক্ষুদ্র ব্যবসা</div></div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="stats-box">
                        <h3>64+</h3>
                        <p>District Coverage</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <h3>1000+</h3>
                        <p>Jobs Posted</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <h3>5000+</h3>
                        <p>Job Seekers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <h3>Smart</h3>
                        <p>Area-Based Monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUPPORT BANNER -->
    <section class="support-section py-5">
        <div class="container">
            <div class="support-banner">
                <div class="row align-items-center gy-3">
                    <div class="col-lg-8">
                        <h2>বাস্তব ডাটার মাধ্যমে প্রশিক্ষণ ও উন্নয়নকে শক্তিশালী করুন</h2>
                        <p class="mb-0">
                            সরকার ও এনজিও সহজেই বুঝতে পারবে কোন এলাকায় প্রশিক্ষণ প্রয়োজন, কোন দক্ষতার ঘাটতি রয়েছে,
                            এবং কোথায় কর্মসংস্থান কার্যক্রম বেশি দরকার।
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="/jibika/admin/reports.php" class="btn btn-warning">রিপোর্ট দেখুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- UPDATES -->
    <section class="updates-section py-5">
        <div class="container">
            <div class="mb-4 text-center">
                <h2 class="section-title">আপডেট ও সুযোগসমূহ</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="update-card">
                        <div class="update-image-placeholder"></div>
                        <h5>ডিজিটাল স্কিল প্রশিক্ষণে নতুন সহায়তা</h5>
                        <p>তরুণ ও চাকরি প্রার্থীদের জন্য এলাকা-ভিত্তিক ডিজিটাল স্কিল ডেভেলপমেন্ট সুযোগ।</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">দেখুন</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="update-card">
                        <div class="update-image-placeholder"></div>
                        <h5>বিভিন্ন জেলা থেকে নতুন চাকরি পোস্ট হচ্ছে</h5>
                        <p>নতুন কর্মসংস্থানের সুযোগ ধাপে ধাপে বিভিন্ন জেলা ও উপজেলায় যুক্ত হচ্ছে।</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">দেখুন</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="update-card">
                        <div class="update-image-placeholder"></div>
                        <h5>সরকার ও এনজিওর জন্য ডাটা সাপোর্ট</h5>
                        <p>গঠনমূলক বেকারত্বের ডাটা পরিকল্পনা, প্রশিক্ষণ ও সহায়তা কার্যক্রমকে আরও শক্তিশালী করতে পারে।</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">দেখুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ORGANIZATIONS -->
    <section class="organizations-section py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">শীর্ষ নিয়োগদাতা প্রতিষ্ঠান</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="org-card">
                        <h6>স্থানীয় এসএমই নেটওয়ার্ক</h6>
                        <a href="/jibika/user/jobs.php" class="btn btn-outline-primary btn-sm mt-3">চাকরি দেখুন</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="org-card">
                        <h6>স্কিল ডেভেলপমেন্ট হাব</h6>
                        <a href="/jibika/user/jobs.php" class="btn btn-outline-primary btn-sm mt-3">চাকরি দেখুন</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="org-card">
                        <h6>যুব কর্মসংস্থান উদ্যোগ</h6>
                        <a href="/jibika/user/jobs.php" class="btn btn-outline-primary btn-sm mt-3">চাকরি দেখুন</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="org-card">
                        <h6>কমিউনিটি গ্রোথ পার্টনার্স</h6>
                        <a href="/jibika/user/jobs.php" class="btn btn-outline-primary btn-sm mt-3">চাকরি দেখুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RESOURCES -->
    <section class="resources-section py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">জীবিকা রিসোর্সসমূহ</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-2 col-6"><div class="resource-card">সিভি লেখার গাইড</div></div>
                <div class="col-md-2 col-6"><div class="resource-card">ইন্টারভিউ টিপস</div></div>
                <div class="col-md-2 col-6"><div class="resource-card">স্কিল ডেভেলপমেন্ট</div></div>
                <div class="col-md-2 col-6"><div class="resource-card">ক্যারিয়ার কাউন্সেলিং</div></div>
                <div class="col-md-2 col-6"><div class="resource-card">উদ্যোক্তা সহায়তা</div></div>
                <div class="col-md-2 col-6"><div class="resource-card">পার্টনার ফাইন্ডার</div></div>
            </div>
        </div>
    </section>

</div>

<?php include('includes/footer.php'); ?>