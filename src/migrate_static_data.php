<?php
include('assets/config/db.php');

// Create tables
$conn->query("CREATE TABLE IF NOT EXISTS eservices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100),
    title_en VARCHAR(255),
    title_bn VARCHAR(255),
    desc_en TEXT,
    desc_bn TEXT
)");

$conn->query("CREATE TABLE IF NOT EXISTS trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100),
    start_date VARCHAR(100),
    cat_en VARCHAR(100),
    cat_bn VARCHAR(100),
    group_name VARCHAR(100),
    title_en VARCHAR(255),
    title_bn VARCHAR(255),
    loc_en VARCHAR(255),
    loc_bn VARCHAR(255),
    duration_en VARCHAR(100),
    duration_bn VARCHAR(100),
    desc_en TEXT,
    desc_bn TEXT
)");

// Clear previous data if any
$conn->query("TRUNCATE TABLE eservices");
$conn->query("TRUNCATE TABLE trainings");

// E-Services Data
$services_data = [
    [
        "icon" => "fa-file-shield", 
        "en" => ["Online Police Clearance", "Apply for an official police clearance certificate required for employment."],
        "bn" => ["অনলাইন পুলিশ ক্লিয়ারেন্স", "কর্মসংস্থানের জন্য প্রয়োজনীয় অফিসিয়াল পুলিশ ক্লিয়ারেন্স সার্টিফিকেটের জন্য আবেদন করুন।"]
    ],
    [
        "icon" => "fa-file-invoice", 
        "en" => ["e-Trade License", "Apply for a new trade license or renew your existing business license online."],
        "bn" => ["ই-ট্রেড লাইসেন্স", "নতুন ট্রেড লাইসেন্সের জন্য আবেদন করুন অথবা অনলাইনে আপনার বর্তমান লাইসেন্স নবায়ন করুন।"]
    ],
    [
        "icon" => "fa-file-signature", 
        "en" => ["Online TIN Certificate", "Register for a new Taxpayer Identification Number or download your certificate."],
        "bn" => ["অনলাইন টিআইএন সার্টিফিকেট", "নতুন করদাতা সনাক্তকরণ নম্বরের জন্য নিবন্ধন করুন বা আপনার সার্টিফিকেট ডাউনলোড করুন।"]
    ],
    [
        "icon" => "fa-passport", 
        "en" => ["e-Passport Portal", "Apply for a new machine-readable e-Passport or check application status."],
        "bn" => ["ই-পাসপোর্ট পোর্টাল", "নতুন ই-পাসপোর্টের জন্য আবেদন করুন অথবা আবেদনের অবস্থা চেক করুন।"]
    ],
    [
        "icon" => "fa-plane-departure", 
        "en" => ["Expatriate Clearance (BMET)", "Register for overseas employment and obtain BMET clearance securely."],
        "bn" => ["প্রবাসী ক্লিয়ারেন্স (BMET)", "বিদেশি কর্মসংস্থানের জন্য নিবন্ধন করুন এবং নিরাপদে বিএমইটি ক্লিয়ারেন্স গ্রহণ করুন।"]
    ],
    [
        "icon" => "fa-building", 
        "en" => ["Employer Registration", "Register your company to legally hire employees through government portals."],
        "bn" => ["নিয়োগকর্তা নিবন্ধন", "সরকারি পোর্টালের মাধ্যমে আইনসম্মতভাবে কর্মী নিয়োগের জন্য আপনার কোম্পানি নিবন্ধন করুন।"]
    ],
    [
        "icon" => "fa-graduation-cap", 
        "en" => ["Skill Certification Check", "Verify authenticity of vocational and technical training certificates."],
        "bn" => ["স্কিল সার্টিফিকেশন চেক", "ভোকেশনাল এবং টেকনিক্যাল ট্রেনিং সার্টিফিকেটের সত্যতা যাচাই করুন।"]
    ],
    [
        "icon" => "fa-file-invoice-dollar", 
        "en" => ["Online Tax Return Filing", "Submit your annual income tax return digitally to the NBR."],
        "bn" => ["অনলাইন ট্যাক্স রিটার্ন ফাইলিং", "এনবিআর-এ আপনার বার্ষিক আয়কর রিটার্ন ডিজিটালি জমা দিন।"]
    ]
];

foreach ($services_data as $s) {
    $stmt = $conn->prepare("INSERT INTO eservices (icon, title_en, title_bn, desc_en, desc_bn) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $s['icon'], $s['en'][0], $s['bn'][0], $s['en'][1], $s['bn'][1]);
    $stmt->execute();
}

// Trainings Data
$trainings = [
    ["fa-cloud", "Jul 15", "IT & Cloud", "আইটি ও ক্লাউড", "IT", "SEIP: Advanced Cloud Computing & AWS", "SEIP: অ্যাডভান্সড ক্লাউড কম্পিউটিং এবং এডাব্লুএস", "BCC IT Hub, Agargaon", "বিসিসি আইটি হাব, আগারগাঁও", "12 Weeks", "১২ সপ্তাহ", "Government-sponsored Skill for Employment Investment Program focusing on cloud architecture and Azure/AWS deployment.", "ক্লাউড আর্কিটেকচার এবং অ্যাজিউর/এডাব্লুএস ডিপ্লয়মেন্টের উপর জোর দিয়ে সরকারি অর্থায়নে পরিচালিত স্কিলস ফর এমপ্লয়মেন্ট ইনভেস্টমেন্ট প্রোগ্রাম।"],
    ["fa-shoe-prints", "Aug 02", "Manufacturing", "ম্যানুফ্যাকচারিং", "Industry", "Leather Goods & Footwear Manufacturing", "চামড়াজাত পণ্য ও জুতো উৎপাদন", "LSC Training Center, Gazipur", "এলএসসি ট্রেনিং সেন্টার, গাজীপুর", "8 Weeks", "৮ সপ্তাহ", "Specialized technical training in leather processing, footwear design, and quality assurance for export-oriented factories.", "রপ্তানিমুখী কারখানার জন্য চামড়া প্রক্রিয়াকরণ, জুতো ডিজাইন এবং মান নিশ্চিতকরণের বিশেষ প্রযুক্তিগত প্রশিক্ষণ।"],
    ["fa-plane", "Aug 10", "Aviation", "এভিয়েশন", "Engineering", "Biman Ground Handling & Logistics", "বিমান গ্রাউন্ড হ্যান্ডলিং ও লজিস্টিকস", "Hazrat Shahjalal Airport Admin", "হযরত শাহজালাল বিমানবন্দর প্রশাসন", "6 Weeks", "৬ সপ্তাহ", "Professional certification in airport ground operations, logistics management, and cargo handling.", "বিমানবন্ধরের গ্রাউন্ড অপারেশন, লজিস্টিকস ম্যানেজমেন্ট এবং কার্গো হ্যান্ডলিংয়ে পেশাদার সার্টিফিকেশন।"],
    ["fa-shield-halved", "Sep 01", "Cybersecurity", "সাইবার সিকিউরিটি", "IT", "National Cyber Security Certification", "জাতীয় সাইবার নিরাপত্তা সার্টিফিকেশন", "ICT Division Head Office", "তথ্য ও যোগাযোগ প্রযুক্তি বিভাগ প্রধান কার্যালয়", "16 Weeks", "১৬ সপ্তাহ", "Intensive ethical hacking and network security defense course designed for government and banking IT professionals.", "সরকারি ও ব্যাংকিং আইটি পেশাদারদের জন্য ডিজাইন করা নিবিড় ইথিক্যাল হ্যাকিং এবং নেটওয়ার্ক নিরাপত্তা কোর্স।"],
    ["fa-ship", "Sep 15", "Marine Eng.", "মেরিন ইঞ্জি.", "Engineering", "Advanced Shipbuilding & Marine Welding", "অ্যাডভান্সড শিপবিল্ডিং ও মেরিন ওয়েল্ডিং", "Chittagong Marine Academy", "চট্টগ্রাম মেরিন একাডেমি", "10 Weeks", "১০ সপ্তাহ", "High-tier TIG and MIG welding certifications specifically required for the deep-sea shipbuilding industry.", "গভীর সমুদ্রের জাহাজ নির্মাণ শিল্পের জন্য বিশেষভাবে প্রয়োজনীয় উচ্চ-স্তরের টিআইজি (TIG) এবং এমআইজি (MIG) ওয়েল্ডিং সার্টিফিকেশন।"],
    ["fa-chart-line", "Oct 05", "Business", "ব্যবসা", "Business", "BIDA Entrepreneurship Development", "বিডা উদ্যোক্তা উন্নয়ন প্রশিক্ষণ", "BIDA HQ, Dhaka", "বিডা প্রধান কার্যালয়, ঢাকা", "4 Weeks", "৪ সপ্তাহ", "Complete training on company formation, tax compliance, and securing government funding for new startups.", "নতুন স্টার্টআপগুলোর জন্য কোম্পানি গঠন, কর কমপ্লায়েন্স এবং সরকারি তহবিল প্রাপ্তির সম্পূর্ণ প্রশিক্ষণ।"],
    ["fa-robot", "Oct 20", "AI & Robotics", "এআই ও রোবোটিক্স", "IT", "Hi-Tech Park AI & Machine Learning", "হাই-টেক পার্ক এআই ও মেশিন লার্নিং", "Bangabandhu Hi-Tech City", "বঙ্গবন্ধু হাই-টেক সিটি", "14 Weeks", "১৪ সপ্তাহ", "Cutting-edge bootcamp on Python data science, neural networks, and computer vision algorithms.", "পাইথন ডেটা সায়েন্স, নিউরাল নেটওয়ার্ক এবং কম্পিউটার ভিশন অ্যালগরিদমের উপর অত্যাধুনিক বুটক্যাম্প।"],
    ["fa-truck-monster", "Nov 02", "Infrastructure", "অবকাঠামো", "Engineering", "Heavy Equipment Operations (RHD)", "ভারী যন্ত্রপাতি চালনা (সওজ)", "Roads & Highways Dept, Savar", "সড়ক ও জনপথ বিভাগ, সাভার", "8 Weeks", "৮ সপ্তাহ", "Official certification for operating excavators, cranes, and heavy road construction machinery safely.", "এস্কেভেটর, ক্রেন এবং ভারী সড়ক নির্মাণ যন্ত্রপাতি নিরাপদে চালনার জন্য সরকারি সার্টিফিকেশন।"],
    ["fa-cart-shopping", "Nov 15", "E-Commerce", "ই-কমার্স", "IT", "National E-Commerce Management", "জাতীয় ই-কমার্স ব্যবস্থাপনা", "Online / Virtual Campus", "অনলাইন / ভার্চুয়াল ক্যাম্পাস", "6 Weeks", "৬ সপ্তাহ", "Comprehensive training on digital storefront management, payment gateways, and supply chain logistics.", "ডিজিটাল স্টোরফ্রন্ট ম্যানেজমেন্ট, পেমেন্ট গেটওয়ে এবং সাপ্লাই চেইন লজিস্টিকস বিষয়ক ব্যাপক প্রশিক্ষণ।"],
    ["fa-shirt", "Dec 01", "Textile", "টেক্সটাইল", "Industry", "Apparel Merchandising & Supply Chain", "অ্যাপারেল মার্চেন্ডাইজিং ও সাপ্লাই চেইন", "BGMEA University of Fashion", "বিজিএমইএ ইউনিভার্সিটি অফ ফ্যাশন", "12 Weeks", "১২ সপ্তাহ", "Advanced merchandising principles, export compliance, and international buyer management for the RMG sector.", "তৈরি পোশাক খাতের জন্য উন্নত মার্চেন্ডাইজিং নীতি, রপ্তানি কমপ্লায়েন্স এবং আন্তর্জাতিক ক্রেতা ব্যবস্থাপনা।"],
    ["fa-stethoscope", "Dec 10", "Healthcare", "স্বাস্থ্যসেবা", "Healthcare", "Medical Equipment Troubleshooting", "চিকিৎসা সরঞ্জাম মেরামত ও রক্ষণাবেক্ষণ", "NITOR Campus, Dhaka", "নিটোর ক্যাম্পাস, ঢাকা", "10 Weeks", "১০ সপ্তাহ", "Technical skills for maintaining and repairing hospital machinery, MRI scanners, and life-support systems.", "হাসপাতালের যন্ত্রপাতি, এমআরআই স্ক্যানার এবং লাইফ সাপোর্ট সিস্টেম মেরামত ও রক্ষণাবেক্ষণের প্রযুক্তিগত দক্ষতা।"],
    ["fa-solar-panel", "Dec 20", "Energy", "জ্বালানি", "Engineering", "Renewable Energy Grid Installation", "নবায়নযোগ্য শক্তি গ্রিড স্থাপন", "SREDA Training Center", "স্রেডা ট্রেনিং সেন্টার", "8 Weeks", "৮ সপ্তাহ", "Design, setup, and maintenance of large-scale solar power grids and renewable energy storage solutions.", "বৃহৎ আকারের সৌরবিদ্যুৎ গ্রিড এবং নবায়নযোগ্য শক্তি স্টোরেজ ব্যবস্থার ডিজাইন, সেটআপ ও রক্ষণাবেক্ষণ।"]
];

foreach ($trainings as $t) {
    $stmt = $conn->prepare("INSERT INTO trainings (icon, start_date, cat_en, cat_bn, group_name, title_en, title_bn, loc_en, loc_bn, duration_en, duration_bn, desc_en, desc_bn) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $t[6], $t[7], $t[8], $t[9], $t[10], $t[11], $t[12]);
    $stmt->execute();
}

echo "E-Services and Trainings data correctly migrated to database tables!";
?>
