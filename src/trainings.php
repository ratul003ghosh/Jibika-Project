<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');

$lang = $_SESSION['lang'] ?? 'bn';

// Helper function to translate English numbers and months/units to Bengali
if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            $num = str_replace($eng_nums, $bng_nums, (string)$num);
            
            $en_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'AM', 'PM', 'Weeks', 'Week'];
            $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'এএম', 'পিএম', 'সপ্তাহ', 'সপ্তাহ'];
            $num = str_replace($en_months, $bn_months, $num);
        }
        return $num;
    }
}

$trText = [
    'bn' => [
        'hero_title' => 'জাতীয় দক্ষতা উন্নয়ন কার্যক্রম',
        'hero_sub' => 'উন্নত এবং শিল্প-উপযোগী দক্ষতার সাথে জনশক্তিকে শক্তিশালী করতে সরকারি উদ্যোগ।',
        'search_placeholder' => 'প্রশিক্ষণ প্রোগ্রাম খুঁজুন...',
        'search_btn' => 'খুঁজুন',
        'filter_all' => 'সব প্রোগ্রাম',
        'filter_it' => 'আইটি ও টেক',
        'filter_industry' => 'ইন্ডাস্ট্রি ও উৎপাদন',
        'filter_eng' => 'ইঞ্জিনিয়ারিং',
        'filter_biz' => 'ব্যবসা',
        'filter_health' => 'স্বাস্থ্যসেবা',
        'apply_btn' => 'আবেদন করুন',
        'app_modal_title' => 'প্রশিক্ষণের আবেদন',
        'app_modal_heading' => 'আপনি আবেদন করছেন:',
        'app_modal_nid_notice' => 'দয়া করে মনে রাখবেন যে সমস্ত সরকারি প্রশিক্ষণ প্রোগ্রামের জন্য এনআইডি যাচাইকরণ প্রয়োজন। এগিয়ে যাওয়ার মাধ্যমে, আপনি আপনার জীবিকা প্রোফাইলের বিবরণ প্রশিক্ষণ কর্তৃপক্ষের সাথে শেয়ার করতে সম্মত হচ্ছেন।',
        'app_modal_confirm' => 'আমি নিশ্চিত করছি যে আমি এই প্রোগ্রামের জন্য ন্যূনতম শিক্ষাগত যোগ্যতা পূরণ করি।',
        'app_modal_cancel' => 'বাতিল',
        'app_modal_submit' => 'আবেদন জমা দিন'
    ],
    'en' => [
        'hero_title' => 'National Skill Development Programs',
        'hero_sub' => 'Official government initiatives to empower the workforce with advanced, industry-ready skills.',
        'search_placeholder' => 'Search training programs...',
        'search_btn' => 'Find',
        'filter_all' => 'All Programs',
        'filter_it' => 'IT & Tech',
        'filter_industry' => 'Industry & Mfg',
        'filter_eng' => 'Engineering',
        'filter_biz' => 'Business',
        'filter_health' => 'Healthcare',
        'apply_btn' => 'Apply for Program',
        'app_modal_title' => 'Training Application',
        'app_modal_heading' => 'You are applying for:',
        'app_modal_nid_notice' => 'Please note that all government training programs require NID verification. By proceeding, you agree to share your Jibika profile details with the training authority.',
        'app_modal_confirm' => 'I confirm that I meet the minimum educational requirements for this program.',
        'app_modal_cancel' => 'Cancel',
        'app_modal_submit' => 'Submit Application'
    ]
];
$t = $trText[$lang];
?>

<style>
    .training-hero {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        padding: 80px 0;
        color: white;
        text-align: center;
        border-bottom: 5px solid #f42a41;
    }
    .training-card {
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    .training-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }
    .icon-box {
        transition: all 0.3s ease;
    }
    .training-card:hover .icon-box {
        background-color: #006a4e !important;
        color: white !important;
    }
</style>

<!-- Hero Section -->
<div class="training-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?php echo $t['hero_title']; ?></h1>
        <p class="lead mb-4" style="opacity: 0.9;"><?php echo $t['hero_sub']; ?></p>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <input type="text" id="searchInput" class="form-control border-0 px-4" placeholder="<?php echo $t['search_placeholder']; ?>">
                    <button class="btn btn-light px-4 text-success fw-bold" type="button" id="searchBtn"><i class="fa-solid fa-search me-2"></i><?php echo $t['search_btn']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mb-5">
    
    <!-- Filter Tabs -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
        <button class="btn btn-success rounded-pill px-4 fw-bold filter-btn" data-filter="all"><?php echo $t['filter_all']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="IT"><?php echo $t['filter_it']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Industry"><?php echo $t['filter_industry']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Engineering"><?php echo $t['filter_eng']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Business"><?php echo $t['filter_biz']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Healthcare"><?php echo $t['filter_health']; ?></button>
    </div>

    <div class="row g-4" id="trainingsGrid">
        <?php
        $trainings = [
            [
                "icon" => "fa-cloud", 
                "date" => "Jul 15", 
                "cat_bn" => "আইটি ও ক্লাউড", "cat_en" => "IT & Cloud", 
                "group" => "IT", 
                "title_bn" => "SEIP: অ্যাডভান্সড ক্লাউড কম্পিউটিং এবং এডাব্লুএস", "title_en" => "SEIP: Advanced Cloud Computing & AWS", 
                "loc_bn" => "বিসিসি আইটি হাব, আগারগাঁও", "loc_en" => "BCC IT Hub, Agargaon", 
                "duration_bn" => "১২ সপ্তাহ", "duration_en" => "12 Weeks", 
                "desc_bn" => "ক্লাউড আর্কিটেকচার এবং অ্যাজিউর/এডাব্লুএস ডিপ্লয়মেন্টের উপর জোর দিয়ে সরকারি অর্থায়নে পরিচালিত স্কিলস ফর এমপ্লয়মেন্ট ইনভেস্টমেন্ট প্রোগ্রাম।", "desc_en" => "Government-sponsored Skill for Employment Investment Program focusing on cloud architecture and Azure/AWS deployment."
            ],
            [
                "icon" => "fa-shoe-prints", 
                "date" => "Aug 02", 
                "cat_bn" => "ম্যানুফ্যাকচারিং", "cat_en" => "Manufacturing", 
                "group" => "Industry", 
                "title_bn" => "চামড়াজাত পণ্য ও জুতো উৎপাদন", "title_en" => "Leather Goods & Footwear Manufacturing", 
                "loc_bn" => "এলএসসি ট্রেনিং সেন্টার, গাজীপুর", "loc_en" => "LSC Training Center, Gazipur", 
                "duration_bn" => "৮ সপ্তাহ", "duration_en" => "8 Weeks", 
                "desc_bn" => "রপ্তানিমুখী কারখানার জন্য চামড়া প্রক্রিয়াকরণ, জুতো ডিজাইন এবং মান নিশ্চিতকরণের বিশেষ প্রযুক্তিগত প্রশিক্ষণ।", "desc_en" => "Specialized technical training in leather processing, footwear design, and quality assurance for export-oriented factories."
            ],
            [
                "icon" => "fa-plane", 
                "date" => "Aug 10", 
                "cat_bn" => "এভিয়েশন", "cat_en" => "Aviation", 
                "group" => "Engineering", 
                "title_bn" => "বিমান গ্রাউন্ড হ্যান্ডলিং ও লজিস্টিকস", "title_en" => "Biman Ground Handling & Logistics", 
                "loc_bn" => "হযরত শাহজালাল বিমানবন্দর প্রশাসন", "loc_en" => "Hazrat Shahjalal Airport Admin", 
                "duration_bn" => "৬ সপ্তাহ", "duration_en" => "6 Weeks", 
                "desc_bn" => "বিমানবন্ধরের গ্রাউন্ড অপারেশন, লজিস্টিকস ম্যানেজমেন্ট এবং কার্গো হ্যান্ডলিংয়ে পেশাদার সার্টিফিকেশন।", "desc_en" => "Professional certification in airport ground operations, logistics management, and cargo handling."
            ],
            [
                "icon" => "fa-shield-halved", 
                "date" => "Sep 01", 
                "cat_bn" => "সাইবার সিকিউরিটি", "cat_en" => "Cybersecurity", 
                "group" => "IT", 
                "title_bn" => "জাতীয় সাইবার নিরাপত্তা সার্টিফিকেশন", "title_en" => "National Cyber Security Certification", 
                "loc_bn" => "তথ্য ও যোগাযোগ প্রযুক্তি বিভাগ প্রধান কার্যালয়", "loc_en" => "ICT Division Head Office", 
                "duration_bn" => "১৬ সপ্তাহ", "duration_en" => "16 Weeks", 
                "desc_bn" => "সরকারি ও ব্যাংকিং আইটি পেশাদারদের জন্য ডিজাইন করা নিবিড় ইথিক্যাল হ্যাকিং এবং নেটওয়ার্ক নিরাপত্তা কোর্স।", "desc_en" => "Intensive ethical hacking and network security defense course designed for government and banking IT professionals."
            ],
            [
                "icon" => "fa-ship", 
                "date" => "Sep 15", 
                "cat_bn" => "মেরিন ইঞ্জি.", "cat_en" => "Marine Eng.", 
                "group" => "Engineering", 
                "title_bn" => "অ্যাডভান্সড শিপবিল্ডিং ও মেরিন ওয়েল্ডিং", "title_en" => "Advanced Shipbuilding & Marine Welding", 
                "loc_bn" => "চট্টগ্রাম মেরিন একাডেমি", "loc_en" => "Chittagong Marine Academy", 
                "duration_bn" => "১০ সপ্তাহ", "duration_en" => "10 Weeks", 
                "desc_bn" => "গভীর সমুদ্রের জাহাজ নির্মাণ শিল্পের জন্য বিশেষভাবে প্রয়োজনীয় উচ্চ-স্তরের টিআইজি (TIG) এবং এমআইজি (MIG) ওয়েল্ডিং সার্টিফিকেশন।", "desc_en" => "High-tier TIG and MIG welding certifications specifically required for the deep-sea shipbuilding industry."
            ],
            [
                "icon" => "fa-chart-line", 
                "date" => "Oct 05", 
                "cat_bn" => "ব্যবসা", "cat_en" => "Business", 
                "group" => "Business", 
                "title_bn" => "বিডা উদ্যোক্তা উন্নয়ন প্রশিক্ষণ", "title_en" => "BIDA Entrepreneurship Development", 
                "loc_bn" => "বিডা প্রধান কার্যালয়, ঢাকা", "loc_en" => "BIDA HQ, Dhaka", 
                "duration_bn" => "৪ সপ্তাহ", "duration_en" => "4 Weeks", 
                "desc_bn" => "নতুন স্টার্টআপগুলোর জন্য কোম্পানি গঠন, কর কমপ্লায়েন্স এবং সরকারি তহবিল প্রাপ্তির সম্পূর্ণ প্রশিক্ষণ।", "desc_en" => "Complete training on company formation, tax compliance, and securing government funding for new startups."
            ],
            [
                "icon" => "fa-robot", 
                "date" => "Oct 20", 
                "cat_bn" => "এআই ও রোবোটিক্স", "cat_en" => "AI & Robotics", 
                "group" => "IT", 
                "title_bn" => "হাই-টেক পার্ক এআই ও মেশিন লার্নিং", "title_en" => "Hi-Tech Park AI & Machine Learning", 
                "loc_bn" => "বঙ্গবন্ধু হাই-টেক সিটি", "loc_en" => "Bangabandhu Hi-Tech City", 
                "duration_bn" => "১৪ সপ্তাহ", "duration_en" => "14 Weeks", 
                "desc_bn" => "পাইথন ডেটা সায়েন্স, নিউরাল নেটওয়ার্ক এবং কম্পিউটার ভিশন অ্যালগরিদমের উপর অত্যাধুনিক বুটক্যাম্প।", "desc_en" => "Cutting-edge bootcamp on Python data science, neural networks, and computer vision algorithms."
            ],
            [
                "icon" => "fa-truck-monster", 
                "date" => "Nov 02", 
                "cat_bn" => "অবকাঠামো", "cat_en" => "Infrastructure", 
                "group" => "Engineering", 
                "title_bn" => "ভারী যন্ত্রপাতি চালনা (সওজ)", "title_en" => "Heavy Equipment Operations (RHD)", 
                "loc_bn" => "সড়ক ও জনপথ বিভাগ, সাভার", "loc_en" => "Roads & Highways Dept, Savar", 
                "duration_bn" => "৮ সপ্তাহ", "duration_en" => "8 Weeks", 
                "desc_bn" => "এস্কেভেটর, ক্রেন এবং ভারী সড়ক নির্মাণ যন্ত্রপাতি নিরাপদে চালনার জন্য সরকারি সার্টিফিকেশন।", "desc_en" => "Official certification for operating excavators, cranes, and heavy road construction machinery safely."
            ],
            [
                "icon" => "fa-cart-shopping", 
                "date" => "Nov 15", 
                "cat_bn" => "ই-কমার্স", "cat_en" => "E-Commerce", 
                "group" => "IT", 
                "title_bn" => "জাতীয় ই-কমার্স ব্যবস্থাপনা", "title_en" => "National E-Commerce Management", 
                "loc_bn" => "অনলাইন / ভার্চুয়াল ক্যাম্পাস", "loc_en" => "Online / Virtual Campus", 
                "duration_bn" => "৬ সপ্তাহ", "duration_en" => "6 Weeks", 
                "desc_bn" => "ডিজিটাল স্টোরফ্রন্ট ম্যানেজমেন্ট, পেমেন্ট গেটওয়ে এবং সাপ্লাই চেইন লজিস্টিকস বিষয়ক ব্যাপক প্রশিক্ষণ।", "desc_en" => "Comprehensive training on digital storefront management, payment gateways, and supply chain logistics."
            ],
            [
                "icon" => "fa-shirt", 
                "date" => "Dec 01", 
                "cat_bn" => "টেক্সটাইল", "cat_en" => "Textile", 
                "group" => "Industry", 
                "title_bn" => "অ্যাপারেল মার্চেন্ডাইজিং ও সাপ্লাই চেইন", "title_en" => "Apparel Merchandising & Supply Chain", 
                "loc_bn" => "বিজিএমইএ ইউনিভার্সিটি অফ ফ্যাশন", "loc_en" => "BGMEA University of Fashion", 
                "duration_bn" => "১২ সপ্তাহ", "duration_en" => "12 Weeks", 
                "desc_bn" => "তৈরি পোশাক খাতের জন্য উন্নত মার্চেন্ডাইজিং নীতি, রপ্তানি কমপ্লায়েন্স এবং আন্তর্জাতিক ক্রেতা ব্যবস্থাপনা।", "desc_en" => "Advanced merchandising principles, export compliance, and international buyer management for the RMG sector."
            ],
            [
                "icon" => "fa-stethoscope", 
                "date" => "Dec 10", 
                "cat_bn" => "স্বাস্থ্যসেবা", "cat_en" => "Healthcare", 
                "group" => "Healthcare", 
                "title_bn" => "চিকিৎসা সরঞ্জাম মেরামত ও রক্ষণাবেক্ষণ", "title_en" => "Medical Equipment Troubleshooting", 
                "loc_bn" => "নিটোর ক্যাম্পাস, ঢাকা", "loc_en" => "NITOR Campus, Dhaka", 
                "duration_bn" => "১০ সপ্তাহ", "duration_en" => "10 Weeks", 
                "desc_bn" => "হাসপাতালের যন্ত্রপাতি, এমআরআই স্ক্যানার এবং লাইফ সাপোর্ট সিস্টেম মেরামত ও রক্ষণাবেক্ষণের প্রযুক্তিগত দক্ষতা।", "desc_en" => "Technical skills for maintaining and repairing hospital machinery, MRI scanners, and life-support systems."
            ],
            [
                "icon" => "fa-solar-panel", 
                "date" => "Dec 20", 
                "cat_bn" => "জ্বালানি", "cat_en" => "Energy", 
                "group" => "Engineering", 
                "title_bn" => "নবায়নযোগ্য শক্তি গ্রিড স্থাপন", "title_en" => "Renewable Energy Grid Installation", 
                "loc_bn" => "স্রেডা ট্রেনিং সেন্টার", "loc_en" => "SREDA Training Center", 
                "duration_bn" => "৮ সপ্তাহ", "duration_en" => "8 Weeks", 
                "desc_bn" => "বৃহৎ আকারের সৌরবিদ্যুৎ গ্রিড এবং নবায়নযোগ্য শক্তি স্টোরেজ ব্যবস্থার ডিজাইন, সেটআপ ও রক্ষণাবেক্ষণ।", "desc_en" => "Design, setup, and maintenance of large-scale solar power grids and renewable energy storage solutions."
            ]
        ];

        foreach($trainings as $t_item):
            $cat = ($lang == 'bn') ? $t_item['cat_bn'] : $t_item['cat_en'];
            $title = ($lang == 'bn') ? $t_item['title_bn'] : $t_item['title_en'];
            $desc = ($lang == 'bn') ? $t_item['desc_bn'] : $t_item['desc_en'];
            $loc = ($lang == 'bn') ? $t_item['loc_bn'] : $t_item['loc_en'];
            $duration = ($lang == 'bn') ? translateNumber($t_item['duration_bn'], 'bn') : $t_item['duration_en'];
            $date = ($lang == 'bn') ? translateNumber($t_item['date'], 'bn') : $t_item['date'];
        ?>
        <div class="col-xl-4 col-md-6 training-item" data-group="<?php echo $t_item['group']; ?>">
            <div class="card training-card h-100 border-0 shadow-sm position-relative">
                <div class="card-body p-4 p-xl-5">
                    
                    <!-- Top Ribbon -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <i class="fa-solid <?php echo $t_item['icon']; ?> fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-bold shadow-sm"><?php echo htmlspecialchars($cat); ?></span>
                    </div>
                    
                    <!-- Content -->
                    <h5 class="fw-bold text-dark mb-3 training-title-text" style="line-height: 1.4;"><?php echo htmlspecialchars($title); ?></h5>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;"><?php echo htmlspecialchars($desc); ?></p>
                    
                    <!-- Meta Info Grid -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-clock text-warning me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo htmlspecialchars($duration); ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-calendar-day text-primary me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo htmlspecialchars($date); ?></span>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="d-flex align-items-center text-muted small bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-location-dot me-2 text-danger"></i> <span class="text-truncate fw-medium"><?php echo htmlspecialchars($loc); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="card-footer bg-white border-top-0 px-4 px-xl-5 pb-4 pt-0 mt-auto">
                    <button onclick="showTrainingModal('<?php echo htmlspecialchars($title, ENT_QUOTES); ?>')" class="btn btn-outline-success w-100 rounded-pill fw-bold" style="border-width: 2px;"><?php echo $t['apply_btn']; ?></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Category Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.training-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state of buttons
            filterBtns.forEach(b => {
                b.classList.remove('btn-success', 'fw-bold');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success', 'fw-bold');

            const filterValue = btn.getAttribute('data-filter');

            // Show/Hide cards
            items.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else {
                    if (item.getAttribute('data-group') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    });

    // Real-time Search Filtering
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        items.forEach(item => {
            const title = item.querySelector('.training-title-text').textContent.toLowerCase();
            const desc = item.querySelector('p').textContent.toLowerCase();
            const matches = title.includes(query) || desc.includes(query);

            // Respect both current category filter and search query
            const activeFilter = document.querySelector('.filter-btn.btn-success').getAttribute('data-filter');
            const matchesCategory = (activeFilter === 'all') || (item.getAttribute('data-group') === activeFilter);

            if (matches && matchesCategory) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
    }
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
});

function showTrainingModal(courseName) {
    document.getElementById('modalCourseName').textContent = courseName;
    var myModal = new bootstrap.Modal(document.getElementById('trainingModal'));
    myModal.show();
}
</script>

<!-- Training Application Modal -->
<div class="modal fade" id="trainingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background-color: #006a4e !important;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-pen me-2"></i><?php echo $t['app_modal_title']; ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <h6 class="fw-bold text-dark mb-3"><?php echo $t['app_modal_heading']; ?> <br><span id="modalCourseName" class="text-success fs-5"></span></h6>
        <p class="text-muted small mb-4"><?php echo $t['app_modal_nid_notice']; ?></p>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
          <label class="form-check-label text-dark small fw-medium" for="flexCheckDefault">
            <?php echo $t['app_modal_confirm']; ?>
          </label>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-between">
        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal"><?php echo $t['app_modal_cancel']; ?></button>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" style="background-color: #006a4e;" onclick="window.location.reload();"><?php echo $t['app_modal_submit']; ?></button>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
