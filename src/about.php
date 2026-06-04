<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';



$about_en = [
    // Hero
    'hero_badge' => 'About Jibika',
    'hero_title' => 'Good Life Begins With A',
    'hero_title_span' => 'Good Company',
    'hero_desc' => 'Jibika is the National Employment & Skills Platform, an area-based workforce intelligence and smart matching ecosystem connecting talent, employers, and government agencies across Bangladesh.',
    'btn_explore' => 'Explore Opportunities',
    'btn_discover' => 'Discover How',

    // Why
    'why_title' => 'Why Jibika Matters',
    'why_sub' => 'Addressing the core challenges of unemployment, skill mismatches, and geographic inequality in the Bangladesh workforce.',
    'w1_t' => 'Skill Gap', 'w1_d' => 'Bridging the divide between academic qualifications and actual industry skill requirements through smart matching.',
    'w2_t' => 'Geographic Inequality', 'w2_d' => 'Decentralizing opportunities so rural and suburban talents can find employment without forced urban migration.',
    'w3_t' => 'Lack of Visibility', 'w3_d' => 'Bringing informal, day-labor, and SME jobs into a structured, verified, and easily accessible digital ecosystem.',
    'w4_t' => 'Data-Driven Planning', 'w4_d' => 'Equipping government and NGOs with real-time district, upazila, and ward-level unemployment analytics.',

    // Mission Vision
    'mission_title' => 'Our Mission',
    'mission_desc' => 'To build a transparent, inclusive, and highly efficient digital bridge that connects the diverse workforce of Bangladesh with the right employers, eliminating friction in the hiring process and fostering localized economic growth.',
    'vision_title' => 'Our Vision',
    'vision_desc' => 'To establish Jibika as the central nervous system of the national workforce—where zero talent goes unnoticed, every business finds the skills they need, and policymakers have precise data to eradicate unemployment.',

    // Stats
    's1' => 'Districts Covered', 's2' => 'Upazilas Monitored', 's3' => 'Registered Seekers', 's4' => 'Active Employers', 's5' => 'Successful Matches',

    // Core Values
    'cv_title' => 'Core Values', 'cv_sub' => 'The fundamental principles guiding the development and operation of the Jibika platform.',
    'cv1_t' => 'Transparency', 'cv1_d' => 'Clear, verified job postings and honest candidate profiles ensuring trust.',
    'cv2_t' => 'Inclusivity', 'cv2_d' => 'From corporate professionals to day laborers and students, a platform built for everyone.',
    'cv3_t' => 'Innovation', 'cv3_d' => 'Leveraging AI, real-time analytics, and modern architecture to solve age-old employment problems.',
    'cv4_t' => 'Sustainability', 'cv4_d' => 'Promoting local hiring to reduce carbon footprint and support sustainable community economics.',
    'cv5_t' => 'Privacy & Security', 'cv5_d' => 'Enterprise-grade security protecting sensitive user data and enterprise recruitment intel.',

    // How
    'hw_title' => 'How Jibika Works', 'hw_sub' => 'A seamless four-step journey designed to connect talent with opportunity efficiently. Click on any step to begin.',
    'hw1_t' => 'Registration', 'hw1_d' => 'Sign up securely as a Job Seeker, Employer, or Government/NGO entity. Verify your identity.',
    'hw2_t' => 'Profile Setup', 'hw2_d' => 'Build a comprehensive digital portfolio. Add your precise location (District, Upazila, Ward), education, and core skills.',
    'hw3_t' => 'Smart Matching', 'hw3_d' => 'Our intelligence engine automatically recommends the best-fit opportunities or candidates based on skill proximity.',
    'hw4_t' => 'Apply & Hire', 'hw4_d' => 'Connect directly. Job seekers apply with one click, and employers manage pipelines through a powerful dashboard.',

    // Benefits
    'ben_title' => 'Platform Benefits', 'ben_sub' => 'Tailored tools and analytics for every stakeholder in the employment ecosystem.',
    'b_seek' => 'For Job Seekers',
    'b_seek_1' => 'Smart skill-based job recommendations',
    'b_seek_2' => 'Location-aware opportunities',
    'b_seek_3' => 'Interactive CV builder',
    'b_seek_4' => 'AI Career guidance',
    'b_seek_5' => 'Custom training recommendations',
    
    'b_emp' => 'For Employers',
    'b_emp_1' => 'Access to verified local talent',
    'b_emp_2' => 'AI-powered candidate matching',
    'b_emp_3' => 'Advanced recruitment analytics',
    'b_emp_4' => 'Skill-based deep filtering',
    'b_emp_5' => 'Streamlined applicant tracking',
    
    'b_gov' => 'For Govt & NGOs',
    'b_gov_1' => 'Real-time unemployment analytics',
    'b_gov_2' => 'Area-wise skill gap reports',
    'b_gov_3' => 'Workforce planning insights',
    'b_gov_4' => 'Training impact measurement',
    'b_gov_5' => 'Policy data visualization',

    // RoadmapTech
    'tech_title' => 'Technology Behind Jibika', 'tech_sub' => 'Built on a robust, scalable, and secure modern tech stack to handle national-level traffic.',

    // SDG
    'sdg_title' => 'Alignment With SDGs', 'sdg_sub' => 'Jibika is directly contributing to the United Nations Sustainable Development Goals for a better Bangladesh.',
    'sdg1_t' => 'No Poverty', 'sdg1_d' => 'Eradicating poverty by ensuring equal access to earning opportunities.',
    'sdg4_t' => 'Quality Education', 'sdg4_d' => 'Promoting lifelong learning through targeted skill training insights.',
    'sdg8_t' => 'Decent Work', 'sdg8_d' => 'Fostering sustained, inclusive economic growth and productive employment.',
    'sdg9_t' => 'Industry & Innovation', 'sdg9_d' => 'Building resilient infrastructure and fostering innovation in the tech-recruitment sector.',
    'sdg10_t' => 'Reduced Inequalities', 'sdg10_d' => 'Reducing geographic and demographic inequalities by decentralizing job access.',

    // Who can use
    'who_title' => 'Who Can Use Jibika?', 'who_sub' => 'An ecosystem designed for every layer of the national economy.',
    'w_stu' => 'Students', 'w_sw' => 'Skilled Workers', 'w_fl' => 'Freelancers', 'w_com' => 'Companies', 'w_fac' => 'Factories', 'w_gov' => 'Govt Agencies', 'w_ngo' => 'NGOs',

    // Success
    'suc_title' => 'Impact & Success Stories', 'suc_sub' => 'Real numbers showing how Jibika is transforming the employment landscape.',
    'sc1_t' => 'Youth Employed', 'sc1_d' => 'Local youths successfully employed in their own districts through advanced skill matching, reducing migration to the capital.',
    'sc2_t' => 'Women Entrepreneurs Supported', 'sc2_d' => 'Female business owners connected with skilled partners and reliable employees in rural areas.',
    'sc3_t' => 'Efficient Local Hiring', 'sc3_d' => 'Small and medium enterprises cut recruitment costs by 60% by hiring verified local talent directly through the platform.',
    'sc4_t' => 'Urban-Rural Connectivity', 'sc4_d' => 'Connecting remote agricultural and skilled workers with high-paying urban opportunities and contracting jobs.',

    // Roadmap
    'rm_title' => 'Future Roadmap', 'rm_sub' => 'Our vision for the next 5 years of workforce development. Click cards for details.',
    'rm_view' => 'View Plan',
    'rm26' => 'Launch Employment Portal', 'rm27' => 'AI Career Guidance Assistant', 'rm28' => 'National Skill Registry', 'rm29' => 'Mobile Super App', 'rm30' => 'National Workforce Intelligence',

    // FAQ
    'faq_title' => 'Frequently Asked Questions', 'faq_sub' => 'Everything you need to know about the Jibika platform.',
    
    // Team
    'team_title' => 'Team & Governance', 'team_sub' => 'Powered by dedicated professionals combining technology, policy, and data science.',
    
    // CTA
    'cta_title' => 'Empowering Bangladesh Through Employment',
    'cta_sub' => 'Join Jibika today and become part of a nationwide movement connecting talent, opportunity, and economic growth.',
    'cta_btn1' => 'Register as Job Seeker', 'cta_btn2' => 'Register as Employer'
];

$about_bn = [
    // Hero
    'hero_badge' => 'জীবিকা সম্পর্কে',
    'hero_title' => 'ভালো কোম্পানির সাথে',
    'hero_title_span' => 'ভালো জীবনের সূচনা',
    'hero_desc' => 'জীবিকা হলো জাতীয় কর্মসংস্থান ও দক্ষতা প্ল্যাটফর্ম, একটি এলাকা-ভিত্তিক কর্মশক্তি বুদ্ধিমত্তা এবং স্মার্ট ম্যাচিং ইকোসিস্টেম যা সারা বাংলাদেশে প্রতিভা, নিয়োগকর্তা এবং সরকারি সংস্থাগুলোকে সংযুক্ত করে।',
    'btn_explore' => 'সুযোগ অন্বেষণ করুন',
    'btn_discover' => 'কিভাবে কাজ করে জানুন',

    // Why
    'why_title' => 'জীবিকা কেন গুরুত্বপূর্ণ',
    'why_sub' => 'বাংলাদেশের কর্মশক্তিতে বেকারত্ব, দক্ষতার অমিল এবং ভৌগলিক বৈষম্যের মূল চ্যালেঞ্জগুলি মোকাবেলা করা।',
    'w1_t' => 'দক্ষতার ব্যবধান', 'w1_d' => 'স্মার্ট ম্যাচিংয়ের মাধ্যমে একাডেমিক যোগ্যতা এবং প্রকৃত শিল্প দক্ষতার প্রয়োজনীয়তার মধ্যে ব্যবধান কমানো।',
    'w2_t' => 'ভৌগলিক বৈষম্য', 'w2_d' => 'সুযোগের বিকেন্দ্রীকরণ যাতে গ্রামীণ ও শহরতলির প্রতিভারা শহরে বাধ্য হয়ে অভিবাসন না করেই কর্মসংস্থান খুঁজে পেতে পারে।',
    'w3_t' => 'দৃশ্যমানতার অভাব', 'w3_d' => 'অনানুষ্ঠানিক, দিনমজুর এবং এসএমই কাজগুলোকে একটি কাঠামোগত, যাচাইকৃত এবং সহজে অ্যাক্সেসযোগ্য ডিজিটাল ইকোসিস্টেমে আনা।',
    'w4_t' => 'ডেটা-চালিত পরিকল্পনা', 'w4_d' => 'রিয়েল-টাইম জেলা, উপজেলা এবং ওয়ার্ড-স্তরের বেকারত্ব বিশ্লেষণ দিয়ে সরকার এবং এনজিওগুলোকে সজ্জিত করা।',

    // Mission Vision
    'mission_title' => 'আমাদের লক্ষ্য',
    'mission_desc' => 'একটি স্বচ্ছ, অন্তর্ভুক্তিমূলক এবং অত্যন্ত কার্যকর ডিজিটাল সেতু তৈরি করা যা বাংলাদেশের বৈচিত্র্যময় কর্মশক্তিকে সঠিক নিয়োগকর্তাদের সাথে সংযুক্ত করে, নিয়োগ প্রক্রিয়ায় ঘর্ষণ দূর করে এবং স্থানীয় অর্থনৈতিক প্রবৃদ্ধি বৃদ্ধি করে।',
    'vision_title' => 'আমাদের রূপকল্প',
    'vision_desc' => 'জীবিকাকে জাতীয় কর্মশক্তির কেন্দ্রীয় স্নায়ুতন্ত্র হিসেবে প্রতিষ্ঠা করা—যেখানে কোনো প্রতিভাই নজরে পড়বে না, প্রতিটি ব্যবসা তাদের প্রয়োজনীয় দক্ষতা খুঁজে পাবে এবং নীতি নির্ধারকদের কাছে বেকারত্ব দূর করার জন্য সুনির্দিষ্ট তথ্য থাকবে।',

    // Stats
    's1' => 'অন্তর্ভুক্ত জেলা', 's2' => 'উপজেলা পর্যবেক্ষণ', 's3' => 'নিবন্ধিত প্রার্থী', 's4' => 'সক্রিয় নিয়োগকর্তা', 's5' => 'সফল সংযোগ',

    // Core Values
    'cv_title' => 'মূল মানসমূহ', 'cv_sub' => 'জীবিকা প্ল্যাটফর্মের উন্নয়ন এবং পরিচালনাকে পরিচালিত করার মৌলিক নীতিসমূহ।',
    'cv1_t' => 'স্বচ্ছতা', 'cv1_d' => 'পরিষ্কার, যাচাইকৃত চাকরির পোস্টিং এবং সৎ প্রার্থীর প্রোফাইল প্রতিটি পদক্ষেপে বিশ্বাস নিশ্চিত করে।',
    'cv2_t' => 'অন্তর্ভুক্তি', 'cv2_d' => 'কর্পোরেট পেশাদার থেকে শুরু করে দিনমজুর এবং শিক্ষার্থী—সবার জন্য নির্মিত একটি প্ল্যাটফর্ম।',
    'cv3_t' => 'উদ্ভাবন', 'cv3_d' => 'দীর্ঘদিনের কর্মসংস্থান সমস্যা সমাধানের জন্য এআই, রিয়েল-টাইম অ্যানালিটিক্স এবং আধুনিক আর্কিটেকচার ব্যবহার করা।',
    'cv4_t' => 'টেকসই উন্নয়ন', 'cv4_d' => 'কার্বন পদচিহ্ন কমাতে এবং টেকসই সম্প্রদায়ের অর্থনীতিকে সমর্থন করতে স্থানীয় নিয়োগের প্রচার।',
    'cv5_t' => 'গোপনীয়তা ও নিরাপত্তা', 'cv5_d' => 'সংবেদনশীল ব্যবহারকারীর ডেটা এবং এন্টারপ্রাইজ নিয়োগের তথ্য সুরক্ষায় এন্টারপ্রাইজ-গ্রেড নিরাপত্তা।',

    // How
    'hw_title' => 'জীবিকা কীভাবে কাজ করে', 'hw_sub' => 'প্রতিভাকে সুযোগের সাথে দক্ষতার সাথে যুক্ত করার জন্য ডিজাইন করা একটি নিরবচ্ছিন্ন চার-ধাপের যাত্রা।',
    'hw1_t' => 'নিবন্ধন', 'hw1_d' => 'চাকরিপ্রার্থী, নিয়োগকর্তা বা সরকারি/এনজিও সত্তা হিসেবে নিরাপদে সাইন আপ করুন। আপনার পরিচয় যাচাই করুন।',
    'hw2_t' => 'প্রোফাইল সেটআপ', 'hw2_d' => 'একটি বিস্তৃত ডিজিটাল পোর্টফোলিও তৈরি করুন। আপনার সুনির্দিষ্ট অবস্থান (জেলা, উপজেলা, ওয়ার্ড), শিক্ষা এবং মূল দক্ষতা যুক্ত করুন।',
    'hw3_t' => 'স্মার্ট ম্যাচিং', 'hw3_d' => 'আমাদের বুদ্ধিমত্তা ইঞ্জিন দক্ষতার নৈকট্যের উপর ভিত্তি করে স্বয়ংক্রিয়ভাবে সেরা সুযোগ বা প্রার্থীদের সুপারিশ করে।',
    'hw4_t' => 'আবেদন ও নিয়োগ', 'hw4_d' => 'সরাসরি সংযোগ করুন। চাকরিপ্রার্থীরা এক ক্লিকে আবেদন করে এবং নিয়োগকর্তারা ড্যাশবোর্ডের মাধ্যমে পরিচালনা করেন।',

    // Benefits
    'ben_title' => 'প্ল্যাটফর্মের সুবিধা', 'ben_sub' => 'কর্মসংস্থান ইকোসিস্টেমের প্রতিটি স্টেকহোল্ডারের জন্য উপযুক্ত সরঞ্জাম এবং বিশ্লেষণ।',
    'b_seek' => 'চাকরিপ্রার্থীদের জন্য',
    'b_seek_1' => 'দক্ষতা-ভিত্তিক চাকরির স্মার্ট সুপারিশ',
    'b_seek_2' => 'অবস্থান-ভিত্তিক সুযোগ',
    'b_seek_3' => 'ইন্টারেক্টিভ সিভি বিল্ডার',
    'b_seek_4' => 'এআই ক্যারিয়ার গাইডেন্স',
    'b_seek_5' => 'কাস্টমাইজড প্রশিক্ষণ সুপারিশ',
    
    'b_emp' => 'নিয়োগকর্তাদের জন্য',
    'b_emp_1' => 'যাচাইকৃত স্থানীয় প্রতিভার অ্যাক্সেস',
    'b_emp_2' => 'এআই-চালিত প্রার্থী ম্যাচিং',
    'b_emp_3' => 'উন্নত নিয়োগ বিশ্লেষণ',
    'b_emp_4' => 'দক্ষতা-ভিত্তিক ফিল্টারিং',
    'b_emp_5' => 'সহজ আবেদনকারী ট্র্যাকিং',
    
    'b_gov' => 'সরকার ও এনজিওর জন্য',
    'b_gov_1' => 'রিয়েল-টাইম বেকারত্ব বিশ্লেষণ',
    'b_gov_2' => 'এলাকাভিত্তিক দক্ষতা ব্যবধান প্রতিবেদন',
    'b_gov_3' => 'কর্মশক্তি পরিকল্পনা অন্তর্দৃষ্টি',
    'b_gov_4' => 'প্রশিক্ষণের প্রভাব পরিমাপ',
    'b_gov_5' => 'নীতি ডেটা ভিজ্যুয়ালাইজেশন',

    // RoadmapTech
    'tech_title' => 'জীবিকার পেছনের প্রযুক্তি', 'tech_sub' => 'জাতীয়-স্তরের ট্রাফিক পরিচালনার জন্য একটি শক্তিশালী, স্কেলযোগ্য এবং সুরক্ষিত আধুনিক টেক স্ট্যাকের উপর নির্মিত।',

    // SDG
    'sdg_title' => 'এসডিজির সাথে সামঞ্জস্য', 'sdg_sub' => 'জীবিকা সরাসরি উন্নত বাংলাদেশের জন্য জাতিসংঘের টেকসই উন্নয়ন লক্ষ্যমাত্রায় অবদান রাখছে।',
    'sdg1_t' => 'দারিদ্র্য বিলোপ', 'sdg1_d' => 'উপার্জনের সুযোগে সমান অ্যাক্সেস নিশ্চিত করার মাধ্যমে দারিদ্র্য দূর করা।',
    'sdg4_t' => 'গুণগত শিক্ষা', 'sdg4_d' => 'লক্ষ্যভিত্তিক দক্ষতা প্রশিক্ষণ অন্তর্দৃষ্টির মাধ্যমে জীবনব্যাপী শিক্ষাকে উন্নীত করা।',
    'sdg8_t' => 'শোভন কাজ', 'sdg8_d' => 'টেকসই, অন্তর্ভুক্তিমূলক অর্থনৈতিক প্রবৃদ্ধি এবং উৎপাদনশীল কর্মসংস্থান বৃদ্ধি।',
    'sdg9_t' => 'শিল্প ও উদ্ভাবন', 'sdg9_d' => 'টেকসই অবকাঠামো তৈরি করা এবং প্রযুক্তি-নিয়োগ খাতে উদ্ভাবন বাড়ানো।',
    'sdg10_t' => 'অসমতা হ্রাস', 'sdg10_d' => 'কাজের অ্যাক্সেস বিকেন্দ্রীকরণের মাধ্যমে ভৌগলিক এবং জনসংখ্যাগত বৈষম্য কমানো।',

    // Who can use
    'who_title' => 'জীবিকা কারা ব্যবহার করতে পারে?', 'who_sub' => 'জাতীয় অর্থনীতির প্রতিটি স্তরের জন্য ডিজাইন করা একটি ইকোসিস্টেম।',
    'w_stu' => 'শিক্ষার্থী', 'w_sw' => 'দক্ষ শ্রমিক', 'w_fl' => 'ফ্রিল্যান্সার', 'w_com' => 'কোম্পানি', 'w_fac' => 'কারখানা', 'w_gov' => 'সরকারি সংস্থা', 'w_ngo' => 'এনজিও',

    // Success
    'suc_title' => 'প্রভাব এবং সাফল্যের গল্প', 'suc_sub' => 'বাস্তব সংখ্যা যা দেখাচ্ছে কিভাবে জীবিকা কর্মসংস্থানের দৃশ্যপট পরিবর্তন করছে।',
    'sc1_t' => 'যুব কর্মসংস্থান', 'sc1_d' => 'উন্নত দক্ষতা ম্যাচিংয়ের মাধ্যমে স্থানীয় যুবকরা তাদের নিজস্ব জেলাতেই সফলভাবে নিযুক্ত হচ্ছে, রাজধানীতে অভিবাসন কমাচ্ছে।',
    'sc2_t' => 'নারী উদ্যোক্তাদের সহায়তা', 'sc2_d' => 'নারী ব্যবসা মালিকদের গ্রামাঞ্চলে দক্ষ অংশীদার এবং নির্ভরযোগ্য কর্মীদের সাথে যুক্ত করা।',
    'sc3_t' => 'দক্ষ স্থানীয় নিয়োগ', 'sc3_d' => 'ক্ষুদ্র ও মাঝারি শিল্প প্রতিষ্ঠানগুলো প্ল্যাটফর্মের মাধ্যমে সরাসরি যাচাইকৃত স্থানীয় প্রতিভা নিয়োগ করে নিয়োগ খরচ ৬০% কমিয়েছে।',
    'sc4_t' => 'শহর-গ্রাম সংযোগ', 'sc4_d' => 'প্রত্যন্ত কৃষি এবং দক্ষ কর্মীদের উচ্চ বেতনের শহুরে সুযোগ এবং চুক্তির কাজের সাথে যুক্ত করা।',

    // Roadmap
    'rm_title' => 'ভবিষ্যত রোডম্যাপ', 'rm_sub' => 'পরবর্তী ৫ বছরের কর্মশক্তি উন্নয়নের রূপকল্প। বিস্তারিত জানতে কার্ডে ক্লিক করুন।',
    'rm_view' => 'পরিকল্পনা দেখুন',
    'rm26' => 'কর্মসংস্থান পোর্টাল চালু', 'rm27' => 'এআই ক্যারিয়ার গাইডেন্স সহকারী', 'rm28' => 'জাতীয় দক্ষতা রেজিস্ট্রি', 'rm29' => 'মোবাইল সুপার অ্যাপ', 'rm30' => 'জাতীয় কর্মশক্তি বুদ্ধিমত্তা',

    // FAQ
    'faq_title' => 'সচরাচর জিজ্ঞাসিত প্রশ্নাবলী', 'faq_sub' => 'জীবিকা প্ল্যাটফর্ম সম্পর্কে আপনার যা কিছু জানা দরকার।',
    
    // Team
    'team_title' => 'টিম এবং পরিচালনা', 'team_sub' => 'প্রযুক্তি, নীতি এবং ডেটা সায়েন্সের সমন্বয়ে নিবেদিত পেশাদারদের দ্বারা পরিচালিত।',
    
    // CTA
    'cta_title' => 'কর্মসংস্থানের মাধ্যমে বাংলাদেশের ক্ষমতায়ন',
    'cta_sub' => 'আজই জীবিকায় যোগ দিন এবং প্রতিভা, সুযোগ এবং অর্থনৈতিক প্রবৃদ্ধিকে সংযুক্ত করার একটি দেশব্যাপী আন্দোলনের অংশ হোন।',
    'cta_btn1' => 'চাকরিপ্রার্থী হিসেবে নিবন্ধন', 'cta_btn2' => 'নিয়োগকর্তা হিসেবে নিবন্ধন'
];

$about_t = $lang === 'en' ? $about_en : $about_bn;
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    :root {
        --navy: #152334;
        --navy-light: #1e3148;
        --emerald: #129B6F;
        --emerald-dark: #0e7d59;
        --emerald-light: #e6f7f1;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --bg-light: #E5E7EB;
        --bg-white: #ffffff;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-dark);
        overflow-x: hidden;
    }

    /* Typography */
    h1, h2, h3, h4, h5, h6 { font-weight: 700; letter-spacing: -0.02em; }
    .section-title { font-size: 2.5rem; color: var(--navy); margin-bottom: 1rem; }
    .section-subtitle { font-size: 1.15rem; color: var(--text-muted); max-width: 700px; margin: 0 auto 3rem; line-height: 1.6; }
    
    .dark-section .section-title { color: #ffffff; }
    .dark-section .section-subtitle { color: #cbd5e1; }

    /* Glassmorphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.25rem;
        padding: 2.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        border-color: rgba(18, 155, 111, 0.3);
    }

    /* Buttons */
    .btn-emerald {
        background-color: var(--emerald);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-emerald:hover { background-color: var(--emerald-dark); color: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(18,155,111,0.2); }
    .btn-outline-light-glass {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: all 0.3s;
    }
    .btn-outline-light-glass:hover { background: rgba(255,255,255,0.2); color: white; border-color: white; }

    /* Animations */
    .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
    .reveal.active { opacity: 1; transform: translateY(0); }

    /* 1. Hero Section */
    .about-hero {
        background: linear-gradient(135deg, var(--navy) 0%, #0a111a 100%);
        position: relative;
        padding: 120px 0 100px;
        overflow: hidden;
        color: white;
    }
    .hero-accent-1 { position: absolute; top: -10%; left: -10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(18,155,111,0.15) 0%, transparent 70%); border-radius: 50%; }
    .hero-accent-2 { position: absolute; bottom: -20%; right: -5%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(21,35,52,0.8) 0%, transparent 70%); border-radius: 50%; }
    
    .hero-badge {
        display: inline-block;
        padding: 0.4rem 1.2rem;
        background: rgba(18, 155, 111, 0.15);
        border: 1px solid rgba(18, 155, 111, 0.3);
        color: var(--emerald);
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }
    .hero-title { font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem; }
    .hero-title span { color: var(--emerald); }

    /* Custom Colored Cards for Why Section */
    .why-card {
        background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
        border-radius: 1.25rem;
        padding: 2.5rem 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .why-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 5px;
        background: var(--emerald);
    }
    .why-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(18,155,111,0.12);
    }
    .why-card.border-navy::before { background: var(--navy); }
    .why-card.border-warning::before { background: #f59e0b; }
    .why-card.border-info::before { background: #0ea5e9; }

    /* Generic White Cards */
    .white-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .white-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }
    .icon-box {
        width: 60px; height: 60px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .icon-emerald { background: var(--emerald-light); color: var(--emerald); }
    .icon-navy { background: #e2e8f0; color: var(--navy); }
    .icon-warning { background: #fef3c7; color: #d97706; }
    .icon-info { background: #e0f2fe; color: #0284c7; }

    /* 4. Stats */
    .stats-section { background: var(--emerald); color: white; padding: 4rem 0; }
    .stat-box { text-align: center; }
    .stat-num { font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; }
    .stat-label { font-size: 1rem; font-weight: 500; opacity: 0.9; }

    /* 6. Timeline (How it works) */
    .timeline-container { position: relative; max-width: 1000px; margin: 0 auto; }
    .timeline-container::before {
        content: ''; position: absolute; top: 0; bottom: 0; left: 50%;
        width: 2px; background: rgba(255,255,255,0.1); transform: translateX(-50%);
    }
    .timeline-item { display: flex; justify-content: flex-end; padding-right: 50%; position: relative; margin-bottom: 3rem; }
    .timeline-item:nth-child(even) { justify-content: flex-start; padding-right: 0; padding-left: 50%; }
    
    .timeline-content {
        width: 85%; padding: 2rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 1rem; position: relative; backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    .timeline-item:nth-child(odd) .timeline-content { margin-right: 3rem; }
    .timeline-item:nth-child(even) .timeline-content { margin-left: 3rem; }
    
    .timeline-hover-card:hover {
        background: rgba(18, 155, 111, 0.15);
        border-color: var(--emerald);
        transform: translateX(-10px);
    }
    .timeline-item:nth-child(even) .timeline-hover-card:hover {
        transform: translateX(10px);
    }

    .timeline-dot {
        position: absolute; top: 50%; right: -38px; transform: translateY(-50%);
        width: 20px; height: 20px; border-radius: 50%; background: var(--emerald);
        border: 4px solid var(--navy); z-index: 2;
    }
    .timeline-item:nth-child(even) .timeline-dot { left: -38px; }

    /* 8. Tech Stack */
    .tech-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; background: var(--bg-light);
        border: 1px solid #e2e8f0; border-radius: 50px;
        font-weight: 600; font-size: 0.9rem; color: var(--navy);
        margin: 4px;
    }

    /* Roadmap */
    .roadmap-card {
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .roadmap-card:hover {
        background: rgba(18,155,111,0.15);
        border-color: rgba(18,155,111,0.5);
        transform: scale(1.05);
    }
    .roadmap-card .click-hint {
        font-size: 0.8rem;
        color: var(--emerald);
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s;
    }
    .roadmap-card:hover .click-hint {
        opacity: 1;
        transform: translateY(0);
    }

    /* 13. FAQ */
    .accordion-item { border: none; margin-bottom: 1rem; border-radius: 1rem !important; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .accordion-button { padding: 1.5rem; font-weight: 600; font-size: 1.1rem; color: var(--navy); background: white; }
    .accordion-button:not(.collapsed) { background: var(--emerald-light); color: var(--emerald-dark); box-shadow: none; }
    .accordion-button:focus { box-shadow: none; }
    .accordion-body { padding: 0 1.5rem 1.5rem; background: white; color: var(--text-muted); line-height: 1.6; }

    /* 15. CTA */
    .cta-section {
        background: linear-gradient(135deg, var(--emerald) 0%, var(--navy) 100%);
        padding: 100px 0; color: white; text-align: center; position: relative; overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
        .timeline-container::before { left: 20px; }
        .timeline-item { justify-content: flex-start; padding-right: 0; padding-left: 40px; }
        .timeline-item:nth-child(even) { padding-left: 40px; }
        .timeline-item:nth-child(odd) .timeline-content, .timeline-item:nth-child(even) .timeline-content { margin: 0; width: 100%; }
        .timeline-dot { left: -30px !important; }
        .timeline-hover-card:hover, .timeline-item:nth-child(even) .timeline-hover-card:hover { transform: translateX(5px); }
    }
</style>

<!-- 1. Hero Section -->
<section class="about-hero">
    <div class="hero-accent-1"></div>
    <div class="hero-accent-2"></div>
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-7 reveal">
                <div class="hero-badge"><?= $about_t['hero_badge'] ?></div>
                <h1 class="hero-title"><?= $about_t['hero_title'] ?> <span><?= $about_t['hero_title_span'] ?></span></h1>
                <p class="lead mb-4 opacity-75" style="max-width: 600px;"><?= $about_t['hero_desc'] ?></p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="auth/register.php" class="btn btn-emerald"><?= $about_t['btn_explore'] ?></a>
                    <a href="#why-jibika" class="btn btn-outline-light-glass"><?= $about_t['btn_discover'] ?></a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-5 mt-lg-0 reveal" style="transition-delay: 0.2s;">
                <img src="assets/images/jibika_logo.png" alt="Jibika Logo" class="img-fluid rounded-circle shadow-lg" style="width: 250px; background: white; padding: 20px;">
            </div>
        </div>
    </div>
</section>

<!-- 2. Why Jibika Matters -->
<section id="why-jibika" class="py-5" style="background: white;">
    <div class="container py-5">
        <div class="text-center reveal">
            <h2 class="section-title"><?= $about_t['why_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['why_sub'] ?></p>
        </div>
        <div class="row g-4 mt-2">
            <div class="col-md-6 col-lg-3 reveal" style="transition-delay: 0.1s;">
                <div class="why-card">
                    <div class="icon-box icon-emerald"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h4><?= $about_t['w1_t'] ?></h4>
                    <p class="text-muted mt-3"><?= $about_t['w1_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal" style="transition-delay: 0.2s;">
                <div class="why-card border-navy">
                    <div class="icon-box icon-navy"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h4><?= $about_t['w2_t'] ?></h4>
                    <p class="text-muted mt-3"><?= $about_t['w2_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal" style="transition-delay: 0.3s;">
                <div class="why-card border-warning">
                    <div class="icon-box icon-warning"><i class="fa-solid fa-eye-slash"></i></div>
                    <h4><?= $about_t['w3_t'] ?></h4>
                    <p class="text-muted mt-3"><?= $about_t['w3_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal" style="transition-delay: 0.4s;">
                <div class="why-card border-info">
                    <div class="icon-box icon-info"><i class="fa-solid fa-chart-pie"></i></div>
                    <h4><?= $about_t['w4_t'] ?></h4>
                    <p class="text-muted mt-3"><?= $about_t['w4_d'] ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Mission & Vision -->
<section class="py-5 dark-section" style="background: var(--navy);">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6 reveal">
                <div class="glass-card h-100">
                    <div class="icon-box icon-emerald mb-4"><i class="fa-solid fa-rocket"></i></div>
                    <h2 class="text-white mb-3"><?= $about_t['mission_title'] ?></h2>
                    <p class="text-white opacity-75 fs-5 lh-lg"><?= $about_t['mission_desc'] ?></p>
                </div>
            </div>
            <div class="col-lg-6 reveal" style="transition-delay: 0.2s;">
                <div class="glass-card h-100">
                    <div class="icon-box icon-emerald mb-4"><i class="fa-solid fa-eye"></i></div>
                    <h2 class="text-white mb-3"><?= $about_t['vision_title'] ?></h2>
                    <p class="text-white opacity-75 fs-5 lh-lg"><?= $about_t['vision_desc'] ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Platform Impact Statistics -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-lg reveal" style="transition-delay: 0.1s;">
                <div class="stat-num" data-target="64"><?= translateNumber('0', $lang) ?></div>
                <div class="stat-label"><?= $about_t['s1'] ?></div>
            </div>
            <div class="col-6 col-lg reveal" style="transition-delay: 0.2s;">
                <div class="stat-num" data-target="495"><?= translateNumber('0', $lang) ?></div>
                <div class="stat-label"><?= $about_t['s2'] ?></div>
            </div>
            <div class="col-4 col-lg reveal" style="transition-delay: 0.3s;">
                <div class="stat-num" data-target="10000"><?= translateNumber('0', $lang) ?></div>
                <div class="stat-label"><?= $about_t['s3'] ?></div>
            </div>
            <div class="col-4 col-lg reveal" style="transition-delay: 0.4s;">
                <div class="stat-num" data-target="2500"><?= translateNumber('0', $lang) ?></div>
                <div class="stat-label"><?= $about_t['s4'] ?></div>
            </div>
            <div class="col-4 col-lg reveal" style="transition-delay: 0.5s;">
                <div class="stat-num" data-target="5000"><?= translateNumber('0', $lang) ?></div>
                <div class="stat-label"><?= $about_t['s5'] ?></div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Core Values -->
<section class="py-5" style="background: var(--bg-light);">
    <div class="container py-5">
        <div class="text-center reveal">
            <h2 class="section-title"><?= $about_t['cv_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['cv_sub'] ?></p>
        </div>
        <div class="row g-4 justify-content-center mt-2">
            <div class="col-md-4 reveal">
                <div class="white-card text-center">
                    <i class="fa-solid fa-shield-halved fa-3x mb-3" style="color: var(--emerald);"></i>
                    <h4 class="mb-3"><?= $about_t['cv1_t'] ?></h4>
                    <p class="text-muted"><?= $about_t['cv1_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 0.1s;">
                <div class="white-card text-center">
                    <i class="fa-solid fa-users fa-3x mb-3" style="color: var(--emerald);"></i>
                    <h4 class="mb-3"><?= $about_t['cv2_t'] ?></h4>
                    <p class="text-muted"><?= $about_t['cv2_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 0.2s;">
                <div class="white-card text-center">
                    <i class="fa-solid fa-lightbulb fa-3x mb-3" style="color: var(--emerald);"></i>
                    <h4 class="mb-3"><?= $about_t['cv3_t'] ?></h4>
                    <p class="text-muted"><?= $about_t['cv3_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 0.3s;">
                <div class="white-card text-center">
                    <i class="fa-solid fa-leaf fa-3x mb-3" style="color: var(--emerald);"></i>
                    <h4 class="mb-3"><?= $about_t['cv4_t'] ?></h4>
                    <p class="text-muted"><?= $about_t['cv4_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay: 0.4s;">
                <div class="white-card text-center">
                    <i class="fa-solid fa-lock fa-3x mb-3" style="color: var(--emerald);"></i>
                    <h4 class="mb-3"><?= $about_t['cv5_t'] ?></h4>
                    <p class="text-muted"><?= $about_t['cv5_d'] ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. How Jibika Works -->
<section class="py-5 dark-section" style="background: var(--navy);">
    <div class="container py-5">
        <div class="text-center reveal mb-5">
            <h2 class="section-title"><?= $about_t['hw_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['hw_sub'] ?></p>
        </div>
        
        <div class="timeline-container">
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <a href="auth/register.php" class="text-decoration-none" style="width: 85%;">
                    <div class="timeline-content text-white timeline-hover-card w-100">
                        <h3 class="mb-3"><span style="color:var(--emerald);"><?= translateNumber('01.', $lang) ?></span> <?= $about_t['hw1_t'] ?></h3>
                        <p class="opacity-75 text-white mb-0"><?= $about_t['hw1_d'] ?></p>
                    </div>
                </a>
            </div>
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <a href="auth/login.php" class="text-decoration-none" style="width: 85%;">
                    <div class="timeline-content text-white timeline-hover-card w-100">
                        <h3 class="mb-3"><span style="color:var(--emerald);"><?= translateNumber('02.', $lang) ?></span> <?= $about_t['hw2_t'] ?></h3>
                        <p class="opacity-75 text-white mb-0"><?= $about_t['hw2_d'] ?></p>
                    </div>
                </a>
            </div>
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <a href="jobseeker/jobs.php" class="text-decoration-none" style="width: 85%;">
                    <div class="timeline-content text-white timeline-hover-card w-100">
                        <h3 class="mb-3"><span style="color:var(--emerald);"><?= translateNumber('03.', $lang) ?></span> <?= $about_t['hw3_t'] ?></h3>
                        <p class="opacity-75 text-white mb-0"><?= $about_t['hw3_d'] ?></p>
                    </div>
                </a>
            </div>
            <div class="timeline-item reveal">
                <div class="timeline-dot"></div>
                <a href="jobseeker/jobs.php" class="text-decoration-none" style="width: 85%;">
                    <div class="timeline-content text-white timeline-hover-card w-100">
                        <h3 class="mb-3"><span style="color:var(--emerald);"><?= translateNumber('04.', $lang) ?></span> <?= $about_t['hw4_t'] ?></h3>
                        <p class="opacity-75 text-white mb-0"><?= $about_t['hw4_d'] ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 7. Platform Benefits -->
<section class="py-5" style="background: white;">
    <div class="container py-5">
        <div class="text-center reveal mb-5">
            <h2 class="section-title"><?= $about_t['ben_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['ben_sub'] ?></p>
        </div>
        
        <div class="row g-5">
            <div class="col-lg-4 reveal">
                <div class="white-card" style="background: var(--bg-light); border-top: 4px solid var(--emerald);">
                    <h3 class="mb-4"><?= $about_t['b_seek'] ?></h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_seek_1'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_seek_2'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_seek_3'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_seek_4'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_seek_5'] ?></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 reveal" style="transition-delay: 0.2s;">
                <div class="white-card" style="background: var(--bg-light); border-top: 4px solid var(--navy);">
                    <h3 class="mb-4"><?= $about_t['b_emp'] ?></h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_emp_1'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_emp_2'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_emp_3'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_emp_4'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_emp_5'] ?></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 reveal" style="transition-delay: 0.4s;">
                <div class="white-card" style="background: var(--bg-light); border-top: 4px solid #f59e0b;">
                    <h3 class="mb-4"><?= $about_t['b_gov'] ?></h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_gov_1'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_gov_2'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_gov_3'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_gov_4'] ?></li>
                        <li class="mb-3"><i class="fa-solid fa-check text-success me-2"></i> <?= $about_t['b_gov_5'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. Technology Behind Jibika -->
<section class="py-5" style="background: var(--bg-light);">
    <div class="container py-5 text-center reveal">
        <h2 class="section-title"><?= $about_t['tech_title'] ?></h2>
        <p class="section-subtitle"><?= $about_t['tech_sub'] ?></p>
        
        <div class="row g-4 justify-content-center mt-4">
            <div class="col-md-6 col-lg-3">
                <div class="white-card">
                    <h5 class="mb-3">Frontend</h5>
                    <div class="tech-pill"><i class="fa-brands fa-react" style="color:#61dafb;"></i> React.js</div>
                    <div class="tech-pill"><i class="fa-solid fa-n"></i> Next.js</div>
                    <div class="tech-pill"><i class="fa-solid fa-wind" style="color:#38bdf8;"></i> Tailwind CSS</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="white-card">
                    <h5 class="mb-3">Backend</h5>
                    <div class="tech-pill"><i class="fa-brands fa-node" style="color:#68a063;"></i> Node.js</div>
                    <div class="tech-pill"><i class="fa-brands fa-php" style="color:#8892be;"></i> PHP</div>
                    <div class="tech-pill"><i class="fa-solid fa-server"></i> Express.js</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="white-card">
                    <h5 class="mb-3">Database</h5>
                    <div class="tech-pill"><i class="fa-solid fa-database" style="color:#336791;"></i> PostgreSQL</div>
                    <div class="tech-pill"><i class="fa-solid fa-leaf" style="color:#47A248;"></i> MongoDB</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="white-card">
                    <h5 class="mb-3">Intelligence</h5>
                    <div class="tech-pill"><i class="fa-solid fa-brain" style="color:#f472b6;"></i> AI Matching</div>
                    <div class="tech-pill"><i class="fa-solid fa-chart-line" style="color:#fbbf24;"></i> Analytics</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 9. SDG Alignment -->
<section class="py-5 dark-section" style="background: var(--navy);">
    <div class="container py-5">
        <div class="text-center reveal mb-5">
            <h2 class="section-title"><?= $about_t['sdg_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['sdg_sub'] ?></p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 reveal">
                <div class="glass-card d-flex align-items-center gap-3 p-4">
                    <div style="background:#e5243b; color:white; width:60px; height:60px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.5rem; flex-shrink:0;">1</div>
                    <div>
                        <h5 class="text-white mb-1"><?= $about_t['sdg1_t'] ?></h5>
                        <p class="text-white opacity-75 mb-0" style="font-size:0.85rem;"><?= $about_t['sdg1_d'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.1s;">
                <div class="glass-card d-flex align-items-center gap-3 p-4">
                    <div style="background:#c5192d; color:white; width:60px; height:60px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.5rem; flex-shrink:0;">4</div>
                    <div>
                        <h5 class="text-white mb-1"><?= $about_t['sdg4_t'] ?></h5>
                        <p class="text-white opacity-75 mb-0" style="font-size:0.85rem;"><?= $about_t['sdg4_d'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.2s;">
                <div class="glass-card d-flex align-items-center gap-3 p-4">
                    <div style="background:#a21942; color:white; width:60px; height:60px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.5rem; flex-shrink:0;">8</div>
                    <div>
                        <h5 class="text-white mb-1"><?= $about_t['sdg8_t'] ?></h5>
                        <p class="text-white opacity-75 mb-0" style="font-size:0.85rem;"><?= $about_t['sdg8_d'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 reveal" style="transition-delay: 0.3s;">
                <div class="glass-card d-flex align-items-center gap-3 p-4">
                    <div style="background:#fd6925; color:white; width:60px; height:60px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.5rem; flex-shrink:0;">9</div>
                    <div>
                        <h5 class="text-white mb-1"><?= $about_t['sdg9_t'] ?></h5>
                        <p class="text-white opacity-75 mb-0" style="font-size:0.85rem;"><?= $about_t['sdg9_d'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 reveal" style="transition-delay: 0.4s;">
                <div class="glass-card d-flex align-items-center gap-3 p-4">
                    <div style="background:#dd1367; color:white; width:60px; height:60px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.5rem; flex-shrink:0;">10</div>
                    <div>
                        <h5 class="text-white mb-1"><?= $about_t['sdg10_t'] ?></h5>
                        <p class="text-white opacity-75 mb-0" style="font-size:0.85rem;"><?= $about_t['sdg10_d'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. Who Can Use Jibika? -->
<section class="py-5" style="background: white;">
    <div class="container py-5 text-center reveal">
        <h2 class="section-title"><?= $about_t['who_title'] ?></h2>
        <p class="section-subtitle"><?= $about_t['who_sub'] ?></p>
        
        <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-user-graduate"></i> <?= $about_t['w_stu'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-helmet-safety"></i> <?= $about_t['w_sw'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-laptop-code"></i> <?= $about_t['w_fl'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-building"></i> <?= $about_t['w_com'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-industry"></i> <?= $about_t['w_fac'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-landmark"></i> <?= $about_t['w_gov'] ?></span>
            <span class="tech-pill fs-6 px-4 py-2"><i class="fa-solid fa-hands-holding-child"></i> <?= $about_t['w_ngo'] ?></span>
        </div>
    </div>
</section>

<!-- 11. Success Stories -->
<section class="py-5" style="background: var(--bg-light);">
    <div class="container py-5">
        <div class="text-center reveal mb-5">
            <h2 class="section-title"><?= $about_t['suc_title'] ?></h2>
            <p class="section-subtitle"><?= $about_t['suc_sub'] ?></p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 reveal">
                <div class="white-card border-start border-4 border-success">
                    <h1 class="text-success mb-2"><?= translateNumber('500+', $lang) ?></h1>
                    <h5 class="mb-2"><?= $about_t['sc1_t'] ?></h5>
                    <p class="text-muted mb-0"><?= $about_t['sc1_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 reveal" style="transition-delay: 0.2s;">
                <div class="white-card border-start border-4 border-warning">
                    <h1 class="text-warning mb-2"><?= translateNumber('300+', $lang) ?></h1>
                    <h5 class="mb-2"><?= $about_t['sc2_t'] ?></h5>
                    <p class="text-muted mb-0"><?= $about_t['sc2_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 reveal" style="transition-delay: 0.3s;">
                <div class="white-card border-start border-4 border-info">
                    <h1 class="text-info mb-2"><?= translateNumber('SME+', $lang) ?></h1>
                    <h5 class="mb-2"><?= $about_t['sc3_t'] ?></h5>
                    <p class="text-muted mb-0"><?= $about_t['sc3_d'] ?></p>
                </div>
            </div>
            <div class="col-md-6 reveal" style="transition-delay: 0.4s;">
                <div class="white-card border-start border-4 border-primary">
                    <h1 class="text-primary mb-2"><?= translateNumber('Govt', $lang) ?></h1>
                    <h5 class="mb-2"><?= $about_t['sc4_t'] ?></h5>
                    <p class="text-muted mb-0"><?= $about_t['sc4_d'] ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 12. Future Roadmap -->
<section class="py-5 dark-section" style="background: var(--navy);">
    <div class="container py-5 text-center reveal">
        <h2 class="section-title"><?= $about_t['rm_title'] ?></h2>
        <p class="section-subtitle"><?= $about_t['rm_sub'] ?></p>
        
        <div class="row g-4 mt-4">
            <div class="col-lg col-md-6">
                <div class="glass-card p-4 h-100 text-center roadmap-card" data-bs-toggle="modal" data-bs-target="#modal2026">
                    <h3 class="text-emerald mb-2" style="color:var(--emerald);"><?= translateNumber('2026', $lang) ?></h3>
                    <h6 class="text-white"><?= $about_t['rm26'] ?></h6>
                    <div class="click-hint mt-3"><i class="fa-solid fa-hand-pointer"></i> <?= $about_t['rm_view'] ?></div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="glass-card p-4 h-100 text-center roadmap-card" data-bs-toggle="modal" data-bs-target="#modal2027">
                    <h3 class="text-emerald mb-2" style="color:var(--emerald);"><?= translateNumber('2027', $lang) ?></h3>
                    <h6 class="text-white"><?= $about_t['rm27'] ?></h6>
                    <div class="click-hint mt-3"><i class="fa-solid fa-hand-pointer"></i> <?= $about_t['rm_view'] ?></div>
                </div>
            </div>
            <div class="col-lg col-md-4">
                <div class="glass-card p-4 h-100 text-center roadmap-card" data-bs-toggle="modal" data-bs-target="#modal2028">
                    <h3 class="text-emerald mb-2" style="color:var(--emerald);"><?= translateNumber('2028', $lang) ?></h3>
                    <h6 class="text-white"><?= $about_t['rm28'] ?></h6>
                    <div class="click-hint mt-3"><i class="fa-solid fa-hand-pointer"></i> <?= $about_t['rm_view'] ?></div>
                </div>
            </div>
            <div class="col-lg col-md-4">
                <div class="glass-card p-4 h-100 text-center roadmap-card" data-bs-toggle="modal" data-bs-target="#modal2029">
                    <h3 class="text-emerald mb-2" style="color:var(--emerald);"><?= translateNumber('2029', $lang) ?></h3>
                    <h6 class="text-white"><?= $about_t['rm29'] ?></h6>
                    <div class="click-hint mt-3"><i class="fa-solid fa-hand-pointer"></i> <?= $about_t['rm_view'] ?></div>
                </div>
            </div>
            <div class="col-lg col-md-4">
                <div class="glass-card p-4 h-100 text-center roadmap-card" data-bs-toggle="modal" data-bs-target="#modal2030">
                    <h3 class="text-emerald mb-2" style="color:var(--emerald);"><?= translateNumber('2030', $lang) ?></h3>
                    <h6 class="text-white"><?= $about_t['rm30'] ?></h6>
                    <div class="click-hint mt-3"><i class="fa-solid fa-hand-pointer"></i> <?= $about_t['rm_view'] ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 15. Final Call To Action -->
<section class="cta-section">
    <div class="container position-relative z-1 reveal">
        <h1 class="display-4 fw-bold mb-4"><?= $about_t['cta_title'] ?></h1>
        <p class="fs-4 mb-5 opacity-75 mx-auto" style="max-width: 800px;"><?= $about_t['cta_sub'] ?></p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="auth/register.php?role=job_seeker" class="btn btn-light btn-lg px-5 text-dark fw-bold" style="border-radius: 50px;"><?= $about_t['cta_btn1'] ?></a>
            <a href="auth/register.php?role=employer" class="btn btn-outline-light-glass px-5 fs-5"><?= $about_t['cta_btn2'] ?></a>
        </div>
    </div>
</section>

<!-- Include JavaScript for Animations & Counters -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll Reveal Animation
    const reveals = document.querySelectorAll('.reveal');
    const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
    
    const revealOnScroll = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('active');
            observer.unobserve(entry.target);
        });
    }, revealOptions);

    reveals.forEach(reveal => {
        revealOnScroll.observe(reveal);
    });

    // Animated Counters with Bengali Support
    const counters = document.querySelectorAll('.stat-num');
    const isBn = "<?= $lang ?>" === "bn";
    const eng = ['0','1','2','3','4','5','6','7','8','9'];
    const bng = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

    const toBnStr = (numStr) => {
        if(!isBn) return numStr;
        let res = "";
        for(let i=0; i<numStr.length; i++) {
            let ch = numStr[i];
            let idx = eng.indexOf(ch);
            res += (idx !== -1) ? bng[idx] : ch;
        }
        return res;
    };

    const counterObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            
            const target = +entry.target.getAttribute('data-target');
            let count = 0;
            const duration = 2000;
            const increment = target / (duration / 16);

            const updateCounter = () => {
                count += increment;
                if(count < target) {
                    let formatted = Math.ceil(count).toLocaleString();
                    entry.target.innerText = toBnStr(formatted) + "+";
                    requestAnimationFrame(updateCounter);
                } else {
                    let formatted = target.toLocaleString();
                    entry.target.innerText = toBnStr(formatted) + "+";
                }
            };
            updateCounter();
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
});
</script>

<?php include('includes/footer.php'); ?>
