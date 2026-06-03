<?php
session_start();
include('assets/config/db.php');
?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<?php
$role_route = $_SESSION['role'] ?? 'guest';
$jobs_link = "jobseeker/jobs.php";
$skills_link = "jobseeker/skills.php";
$dashboard_link = "auth/login.php";

if ($role_route == 'admin') {
    $jobs_link = "admin/jobs.php";
    $skills_link = "admin/reports.php";
    $dashboard_link = "admin/dashboard.php";
} elseif ($role_route == 'employer') {
    $jobs_link = "employer/manage_jobs.php";
    $skills_link = "employer/dashboard.php";
    $dashboard_link = "employer/dashboard.php";
} elseif ($role_route == 'job_seeker') {
    $dashboard_link = "jobseeker/dashboard.php";
}

$total_districts = 0;
$total_jobs = 0;
$total_job_seekers = 0;
$total_applications = 0;

$district_count_query = mysqli_query($conn, "SELECT COUNT(DISTINCT location) AS total FROM jobs WHERE location IS NOT NULL AND location != ''");
if ($district_count_query) {
    $district_count_data = mysqli_fetch_assoc($district_count_query);
    $total_districts = $district_count_data['total'] ?? 0;
}

$jobs_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jobs");
if ($jobs_count_query) {
    $jobs_count_data = mysqli_fetch_assoc($jobs_count_query);
    $total_jobs = $jobs_count_data['total'] ?? 0;
}

$job_seekers_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'job_seeker'");
if ($job_seekers_count_query) {
    $job_seekers_count_data = mysqli_fetch_assoc($job_seekers_count_query);
    $total_job_seekers = $job_seekers_count_data['total'] ?? 0;
}

$applications_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications");
if ($applications_count_query) {
    $applications_count_data = mysqli_fetch_assoc($applications_count_query);
    $total_applications = $applications_count_data['total'] ?? 0;
}

$locations_result = mysqli_query($conn, "SELECT DISTINCT location FROM jobs WHERE location IS NOT NULL AND location != '' ORDER BY location ASC");

$latest_jobs_result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY id DESC LIMIT 6");

$top_areas_result = mysqli_query($conn, "
    SELECT location, COUNT(*) AS total_jobs
    FROM jobs
    WHERE location IS NOT NULL AND location != ''
    GROUP BY location
    ORDER BY total_jobs DESC, location ASC
    LIMIT 4
");

// ── Homepage bilingual translations ────────────────────────────────────────
$home_text = [
    'bn' => [
        // Hero
        'hero_quote'        => 'প্রতিটি দক্ষতার পেছনে লুকিয়ে আছে একটি সম্ভাবনার গল্প',
        'hero_title'        => "আজই আপনার স্বপ্নের চাকরি খুঁজুন!",
        'hero_subtitle'     => 'জীবিকা আপনাকে বাংলাদেশের সেরা নিয়োগকর্তাদের সাথে যুক্ত করে — এলাকাভিত্তিক, স্মার্ট এবং দ্রুততম সময়ে।',
        'my_dashboard'      => 'আমার ড্যাশবোর্ড',
        'employer_dashboard'=> 'নিয়োগকর্তার ড্যাশবোর্ড',
        'go_panel'          => 'প্যানেলে যান',
        'browse_jobs'       => 'চাকরি দেখুন',
        'get_started'       => 'শুরু করুন',
        'login'             => 'লগইন',
        'area_monitor'      => 'এলাকা ভিত্তিক মনিটরিং',
        'area_monitor_sub'  => 'জেলা, উপজেলা ও ওয়ার্ডভিত্তিক বেকারত্ব পর্যবেক্ষণ',
        'skill_mapping'     => 'স্কিল ম্যাপিং',
        'skill_mapping_sub' => 'কার কী দক্ষতা আছে এবং কোন এলাকায় আছে তা সহজে জানা',
        'smart_match'       => 'স্মার্ট জব ম্যাচিং',
        'smart_match_sub'   => 'সঠিক দক্ষতাকে সঠিক চাকরির সাথে দ্রুত যুক্ত করা',
        // Journey section
        'journey_title'     => 'জীবিকায় আপনার যাত্রা শুরু করুন',
        'journey_subtitle'  => 'আপনি চাকরি প্রার্থী, নিয়োগকর্তা, নীতিনির্ধারক বা উদ্যোক্তা যাই হোন না কেন, জীবিকা আপনাকে এগিয়ে যেতে সাহায্য করে।',
        'journey_jobseeker' => 'চাকরি প্রার্থী',
        'journey_js_sub'    => 'আপনার দক্ষতা, জেলা ও স্থানীয় সুযোগ অনুযায়ী চাকরি খুঁজুন।',
        'explore'           => 'অন্বেষণ করুন',
        'journey_employer'  => 'নিয়োগকর্তা',
        'journey_emp_sub'   => 'দক্ষতা, এলাকা ও কাজের প্রোফাইল অনুযায়ী সহজে কর্মী নিয়োগ করুন।',
        'hire_now'          => 'নিয়োগ দিন',
        'journey_govt'      => 'সরকার',
        'journey_govt_sub'  => 'ভালো পরিকল্পনার জন্য এলাকাভিত্তিক বেকারত্বের ধারা পর্যবেক্ষণ করুন।',
        'view_access'       => 'অ্যাক্সেস দেখুন',
        'journey_entre'     => 'উদ্যোক্তা',
        'journey_entre_sub' => 'সম্ভাব্য অংশীদার খুঁজুন এবং ভাগ করা দক্ষতায় ক্ষুদ্র ব্যবসা গড়ুন।',
        'start_building'    => 'শুরু করুন',
        // Search
        'search_title'      => 'আপনি কী খুঁজছেন?',
        'search_subtitle'   => 'চাকরির শিরোনাম, জেলা, উপজেলা বা দক্ষতা অনুযায়ী খুঁজুন।',
        'search_placeholder'=> 'চাকরির নাম বা দক্ষতা দিয়ে খুঁজুন',
        'select_district'   => 'জেলা নির্বাচন করুন',
        'upazila'           => 'উপজেলা',
        'search_btn'        => 'চাকরি খুঁজুন',
        // Sectors
        'sectors_title'     => 'বাংলাদেশের গুরুত্বপূর্ণ চাকরির খাতসমূহ',
        'sectors_subtitle'  => 'আপনার পছন্দের খাতে চাকরি খুঁজুন',
        // Specialized categories
        'special_title'     => 'বিশেষায়িত ক্যাটাগরি',
        'special_subtitle'  => 'শিক্ষার্থী এবং দৈনিক শ্রমিকদের জন্য দ্রুত চাকরির সুযোগ',
        'student_jobs'      => 'শিক্ষার্থী চাকরি',
        'student_jobs_sub'  => 'পার্ট-টাইম ও নমনীয় সময়সূচি',
        'day_labor'         => 'দৈনিক শ্রম',
        'day_labor_sub'     => 'দৈনিক ও সাপ্তাহিক মজুরি',
        'internships'       => 'ইন্টার্নশিপ',
        'internships_sub'   => 'আপনার ক্যারিয়ার শুরু করুন',
        // Latest jobs
        'latest_title'      => 'সাম্প্রতিক চাকরির সুযোগ',
        'latest_subtitle'   => 'নতুন পোস্ট হওয়া চাকরিগুলো এক নজরে দেখুন',
        'new_badge'         => 'নতুন',
        'apply_btn'         => 'আবেদন করুন →',
        'no_jobs'           => 'এখনও কোনো চাকরি পোস্ট করা হয়নি',
        'no_jobs_sub'       => 'নতুন চাকরি যোগ হলে এখানে দেখাবে।',
        'negotiable'        => 'আলোচনা সাপেক্ষে',
        // Stats
        'stat_districts'    => 'জেলা কভারেজ',
        'stat_jobs'         => 'পোস্ট করা চাকরি',
        'stat_seekers'      => 'চাকরি প্রার্থী',
        'stat_applications' => 'আবেদনসমূহ',
        // Support banner
        'support_title'     => 'বাস্তব ডাটার মাধ্যমে প্রশিক্ষণ ও উন্নয়নকে শক্তিশালী করুন',
        'support_text'      => 'সরকার ও এনজিও সহজেই বুঝতে পারবে কোন এলাকায় প্রশিক্ষণ প্রয়োজন এবং কোথায় কর্মসংস্থান কার্যক্রম বেশি দরকার।',
        'view_reports'      => 'রিপোর্ট দেখুন →',
        // Updates
        'updates_title'     => 'আপডেট ও সুযোগসমূহ',
        'tag_training'      => 'প্রশিক্ষণ',
        'tag_employment'    => 'কর্মসংস্থান',
        'tag_govt'          => 'সরকারি',
        'update1_title'     => 'ডিজিটাল স্কিল প্রশিক্ষণে নতুন সহায়তা',
        'update1_text'      => 'তরুণ ও চাকরি প্রার্থীদের জন্য এলাকা-ভিত্তিক ডিজিটাল স্কিল ডেভেলপমেন্ট সুযোগ।',
        'update2_title'     => 'বিভিন্ন জেলা থেকে নতুন চাকরি পোস্ট হচ্ছে',
        'update2_text'      => 'নতুন কর্মসংস্থানের সুযোগ ধাপে ধাপে বিভিন্ন জেলা ও উপজেলায় যুক্ত হচ্ছে।',
        'update3_title'     => 'সরকার ও এনজিওর জন্য ডাটা সাপোর্ট',
        'update3_text'      => 'গঠনমূলক বেকারত্বের ডাটা পরিকল্পনা ও সহায়তা কার্যক্রমকে শক্তিশালী করতে পারে।',
        // Top areas
        'top_areas_title'   => 'শীর্ষ নিয়োগদাতা এলাকা',
        'jobs_count_suffix' => 'টি চাকরি',
        'view_jobs'         => 'চাকরি দেখুন →',
        'no_area_data'      => 'এখনও কোনো hiring area data পাওয়া যায়নি।',
        // Resources
        'resources_title'   => 'জীবিকা রিসোর্সসমূহ',
        'cv_guide'          => 'সিভি লেখার গাইড',
        'cv_guide_sub'      => 'পেশাদার ও চমৎকার সিভি লেখার পদ্ধতি জানুন।',
        'interview_tips'    => 'ইন্টারভিউ টিপস',
        'interview_tips_sub'=> 'আমাদের বিশেষজ্ঞ গাইডেন্সে পরবর্তী ইন্টারভিউ জয় করুন।',
        'skill_dev'         => 'স্কিল ডেভেলপমেন্ট',
        'skill_dev_sub'     => 'আপনার কাছাকাছি সেরা স্কিল ও প্রশিক্ষণ কার্যক্রম খুঁজুন।',
        'career_counsel'    => 'ক্যারিয়ার কাউন্সেলিং',
        'career_counsel_sub'=> 'আপনার ক্যারিয়ার পথ পরিচালনায় ব্যক্তিগত পরামর্শ নিন।',
        'entrepreneur'      => 'উদ্যোক্তা সহায়তা',
        'entrepreneur_sub'  => 'আপনার ক্ষুদ্র ব্যবসা বিকাশে সম্পদ ও সহায়তা।',
        'partner_finder'    => 'পার্টনার ফাইন্ডার',
        'partner_finder_sub'=> 'সম্ভাব্য ব্যবসায়িক অংশীদার ও সহযোগীদের সাথে সংযুক্ত হন।',
        // Sectors
        'sec_it'          => 'আইটি ও ফ্রিল্যান্সিং',  'sec_it_sub'     => 'ওয়েব, অ্যাপ, ডাটা',
        'sec_garments'    => 'গার্মেন্টস ও টেক্সটাইল', 'sec_garments_sub'=> 'উৎপাদন, পোশাক',
        'sec_transport'   => 'ড্রাইভিং ও ট্রান্সপোর্ট','sec_transport_sub'=> 'পরিবহন, লজিস্টিক',
        'sec_health'      => 'স্বাস্থ্যসেবা',           'sec_health_sub'  => 'চিকিৎসা, নার্সিং',
        'sec_agri'        => 'কৃষি',                    'sec_agri_sub'    => 'চাষাবাদ, মৎস্য',
        'sec_sales'       => 'বিক্রয় ও মার্কেটিং',    'sec_sales_sub'   => 'বিপণন, সেলস',
        'sec_edu'         => 'শিক্ষা ও প্রশিক্ষণ',     'sec_edu_sub'     => 'টিউশন, কোচিং',
        'sec_biz'         => 'ক্ষুদ্র ব্যবসা',          'sec_biz_sub'     => 'উদ্যোক্তা, SME',
        // Testimonials
        'testi_badge'       => 'গ্রাহকরা কী বলেন',
        'testi_title'       => 'আমাদের গ্রাহকদের মতামত',
        'testi_subtitle'    => 'চাকরি প্রার্থী এবং নিয়োগকর্তাদের বাস্তব গল্প যারা জীবিকায় আস্থা রেখেছেন',
        't1_text'           => '"জীবিকা আমাকে মাত্র ২ সপ্তাহের মধ্যে ঢাকায় একটি দারুণ আইটি চাকরি খুঁজে পেতে সাহায্য করেছে! এলাকাভিত্তিক সার্চের মাধ্যমে আমার বাড়ির কাছাকাছি সুযোগ খুঁজে পাওয়া অনেক সহজ হয়েছে।"',
        't1_role'           => 'সফটওয়্যার ডেভেলপার, ঢাকা',
        't2_text'           => '"নিয়োগকর্তা হিসেবে, আমি আমাদের কারখানার জন্য কয়েক দিনের মধ্যে চট্টগ্রাম থেকে দক্ষ কর্মী পেয়েছি। স্কিল ম্যাচিং ফিচারটি সত্যিই অসাধারণ!"',
        't2_role'           => 'এইচআর ম্যানেজার, চট্টগ্রাম',
        't3_text'           => '"জীবিকা খুঁজে পাওয়ার আগে আমি কয়েক মাস বেকার ছিলাম। এখন আমি সিলেটে আমার পরিবারের কাছাকাছি একজন নার্স হিসেবে কাজ করছি। এটি জীবন পরিবর্তনকারী একটি প্ল্যাটফর্ম!"',
        't3_role'           => 'নার্স, সিলেট',
        't4_text'           => '"সদ্য স্নাতক হিসেবে আমি প্রথম চাকরি খুঁজতে গিয়ে সংগ্রাম করছিলাম। জীবিকা আমাকে একটি স্থানীয় স্টার্টআপের সাথে যুক্ত করেছে যারা নতুন প্রতিভা খুঁজছিল। এখন আমি একজন জুনিয়র ডেটা অ্যানালিস্ট!"',
        't4_role'           => 'জুনিয়র অ্যানালিস্ট, রাজশাহী',
        't5_text'           => '"আমাদের খুলনার নির্মাণ প্রকল্পের জন্য বিশ্বস্ত দিনমজুরের প্রয়োজন ছিল। জীবিকার ডেডিকেটেড দিনমজুর সেকশন ভেরিফাইড কর্মী খুঁজে পাওয়া অবিশ্বাস্যভাবে দ্রুত করেছে।"',
        't5_role'           => 'সাইট কন্ট্রাক্টর, খুলনা',
        't6_text'           => '"স্কিলস ম্যাপিং ফিচারটি দারুণ! আমি শুধু আমার সার্টিফিকেশন আপলোড করেছি এবং প্ল্যাটফর্মটি স্বয়ংক্রিয়ভাবে সেরা মিলে যাওয়া অ্যাডমিনিস্ট্রেটিভ ভূমিকাগুলোর পরামর্শ দিয়েছে।"',
        't6_role'           => 'অ্যাডমিন এক্সিকিউটিভ, ঢাকা',
    ],
    'en' => [
        // Hero
        'hero_quote'        => 'Behind every skill lies a story of potential',
        'hero_title'        => "Find Your Dream Job<br>Today!",
        'hero_subtitle'     => 'Jibika connects skilled job seekers with top employers across Bangladesh — area-based, smart, and fast.',
        'my_dashboard'      => 'My Dashboard',
        'employer_dashboard'=> 'Employer Dashboard',
        'go_panel'          => 'Go to Panel',
        'browse_jobs'       => 'Browse Jobs',
        'get_started'       => 'Get Started',
        'login'             => 'Login',
        'area_monitor'      => 'Area-based Monitoring',
        'area_monitor_sub'  => 'Track unemployment at district, upazila and ward level',
        'skill_mapping'     => 'Skill Mapping',
        'skill_mapping_sub' => 'Easily find who has what skills and where they are located',
        'smart_match'       => 'Smart Job Matching',
        'smart_match_sub'   => 'Quickly connect the right skills to the right job',
        // Journey section
        'journey_title'     => 'Start Your Journey with Jibika',
        'journey_subtitle'  => 'Whether you are a job seeker, employer, policymaker, or entrepreneur, Jibika helps you move forward.',
        'journey_jobseeker' => 'Job Seekers',
        'journey_js_sub'    => 'Find jobs based on your skills, district, and local opportunities.',
        'explore'           => 'Explore',
        'journey_employer'  => 'Employers',
        'journey_emp_sub'   => 'Hire suitable workers easily by skill, area, and job profile.',
        'hire_now'          => 'Hire Now',
        'journey_govt'      => 'Government',
        'journey_govt_sub'  => 'Monitor unemployment trends area-wise for better planning and policy.',
        'view_access'       => 'View Access',
        'journey_entre'     => 'Entrepreneurs',
        'journey_entre_sub' => 'Find potential partners and build small businesses with shared skills.',
        'start_building'    => 'Start Building',
        // Search
        'search_title'      => 'What are you looking for?',
        'search_subtitle'   => 'Search by job title, district, upazila or skill.',
        'search_placeholder'=> 'Search by job title or skill',
        'select_district'   => 'Select District',
        'upazila'           => 'Upazila',
        'search_btn'        => 'Search Jobs',
        // Sectors
        'sectors_title'     => 'Important Job Sectors of Bangladesh',
        'sectors_subtitle'  => 'Search for jobs in your preferred sector',
        // Specialized categories
        'special_title'     => 'Specialized Categories',
        'special_subtitle'  => 'Quick job opportunities for students and daily laborers',
        'student_jobs'      => 'Student Jobs',
        'student_jobs_sub'  => 'Part-time & Flexible Hours',
        'day_labor'         => 'Day Labor',
        'day_labor_sub'     => 'Daily & Weekly wages',
        'internships'       => 'Internships',
        'internships_sub'   => 'Kickstart your career',
        // Latest jobs
        'latest_title'      => 'Latest Job Opportunities',
        'latest_subtitle'   => 'Browse newly posted jobs at a glance',
        'new_badge'         => 'New',
        'apply_btn'         => 'Apply Now →',
        'no_jobs'           => 'No jobs have been posted yet',
        'no_jobs_sub'       => 'New jobs will appear here when added.',
        'negotiable'        => 'Negotiable',
        // Stats
        'stat_districts'    => 'District Coverage',
        'stat_jobs'         => 'Jobs Posted',
        'stat_seekers'      => 'Job Seekers',
        'stat_applications' => 'Applications',
        // Support banner
        'support_title'     => 'Strengthen Training & Development with Real Data',
        'support_text'      => 'Government and NGOs can easily understand which areas need training and where employment programs are most needed.',
        'view_reports'      => 'View Reports →',
        // Updates
        'updates_title'     => 'Updates & Opportunities',
        'tag_training'      => 'Training',
        'tag_employment'    => 'Employment',
        'tag_govt'          => 'Government',
        'update1_title'     => 'New Support for Digital Skills Training',
        'update1_text'      => 'Area-based digital skill development opportunities for youth and job seekers.',
        'update2_title'     => 'New Jobs Being Posted from Various Districts',
        'update2_text'      => 'New employment opportunities are gradually being added across districts and upazilas.',
        'update3_title'     => 'Data Support for Government & NGOs',
        'update3_text'      => 'Structured unemployment data can strengthen planning and support programs.',
        // Top areas
        'top_areas_title'   => 'Top Hiring Areas',
        'jobs_count_suffix' => 'jobs',
        'view_jobs'         => 'View Jobs →',
        'no_area_data'      => 'No hiring area data available yet.',
        // Resources
        'resources_title'   => 'Jibika Resources',
        'cv_guide'          => 'CV Writing Guide',
        'cv_guide_sub'      => 'Learn how to write a professional and outstanding CV.',
        'interview_tips'    => 'Interview Tips',
        'interview_tips_sub'=> 'Master your next interview with our expert guidance.',
        'skill_dev'         => 'Skill Development',
        'skill_dev_sub'     => 'Discover top skills and training programs near you.',
        'career_counsel'    => 'Career Counseling',
        'career_counsel_sub'=> 'Get 1-on-1 advice to navigate your career path.',
        'entrepreneur'      => 'Entrepreneur Support',
        'entrepreneur_sub'  => 'Resources and support for growing your small business.',
        'partner_finder'    => 'Partner Finder',
        'partner_finder_sub'=> 'Connect with potential business partners and collaborators.',
        // Sectors
        'sec_it'          => 'IT & Freelancing',         'sec_it_sub'      => 'Web, App, Data',
        'sec_garments'    => 'Garments & Textile',       'sec_garments_sub'=> 'Production, Clothing',
        'sec_transport'   => 'Driving & Transport',      'sec_transport_sub'=> 'Transport, Logistics',
        'sec_health'      => 'Healthcare',               'sec_health_sub'  => 'Medical, Nursing',
        'sec_agri'        => 'Agriculture',              'sec_agri_sub'    => 'Farming, Fisheries',
        'sec_sales'       => 'Sales & Marketing',        'sec_sales_sub'   => 'Marketing, Sales',
        'sec_edu'         => 'Education & Training',     'sec_edu_sub'     => 'Tutoring, Coaching',
        'sec_biz'         => 'Small Business',           'sec_biz_sub'     => 'Entrepreneur, SME',
        // Testimonials
        'testi_badge'       => 'What People Say',
        'testi_title'       => 'Testimonials From Our Customers',
        'testi_subtitle'    => 'Real stories from job seekers and employers who trust Jibika',
        't1_text'           => '"Jibika helped me find a great IT job in Dhaka within just 2 weeks! The area-based search made it so easy to find opportunities near my home."',
        't1_role'           => 'Software Developer, Dhaka',
        't2_text'           => '"As an employer, I found skilled workers from Chittagong for our factory within days. The skill matching feature is outstanding!"',
        't2_role'           => 'HR Manager, Chittagong',
        't3_text'           => '"I was unemployed for months before finding Jibika. Now I\'m working as a nurse in Sylhet, close to my family. Life-changing platform!"',
        't3_role'           => 'Nurse, Sylhet',
        't4_text'           => '"As a recent graduate, I was struggling to find my first job. Jibika connected me with a local startup looking for fresh talent. Now I\'m a junior data analyst!"',
        't4_role'           => 'Junior Analyst, Rajshahi',
        't5_text'           => '"We needed reliable day laborers for our construction project in Khulna. Jibika\'s dedicated day labor section made it incredibly fast to find verified workers."',
        't5_role'           => 'Site Contractor, Khulna',
        't6_text'           => '"The skills mapping feature is brilliant! I simply uploaded my certifications, and the platform automatically suggested the best matching administrative roles."',
        't6_role'           => 'Admin Executive, Dhaka',
    ]
];
$ht = $home_text[$lang];
?>


<div class="container-fluid p-0">

    <!-- HERO SECTION -->
    <section class="hero-section" style="display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding: 90px 0 100px; position:relative;">
        
        <!-- BACKGROUND SLIDER LAYERS -->
        <div class="hero-slider-bg active" style="background-image: url('assets/image/bd.jpg');"></div>
        <div class="hero-slider-bg" style="background-image: url('assets/image/bd_2.png');"></div>
        <div class="hero-slider-bg" style="background-image: url('assets/image/bd_4.png');"></div>
        
        <!-- LATEST UPDATE TICKER -->
        <div class="w-100 shadow-sm" style="background: rgba(0, 0, 0, 0.4); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 10px 0; position:absolute; top:0; left:0; z-index:10; backdrop-filter: blur(10px);">
            <div class="container-fluid px-4 px-xl-5">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-pill me-3 px-3 py-2 text-white" style="background-color: #10B981; font-size: 0.85rem; font-weight:700;"><i class="fa-solid fa-bullhorn me-1"></i> <?php echo $lang == 'bn' ? 'বিজ্ঞপ্তি' : 'UPDATE'; ?></span>
                    <marquee behavior="scroll" direction="left" class="text-white mb-0" style="font-weight: 500; font-size: 1rem; letter-spacing: 0.5px;">
                        <?php echo $lang == 'bn' ? 'জীবিকা পোর্টালে নতুন ফিচার যুক্ত হয়েছে! এখন আপনি এলাকা ও দক্ষতা অনুযায়ী স্মার্ট সার্চের মাধ্যমে খুব সহজেই চাকরি খুঁজতে পারবেন। নতুন ইন্টার্নশিপ এবং পার্ট-টাইম জব যুক্ত হয়েছে।' : 'New features added to Jibika Portal! Now you can easily search for jobs using smart search based on area and skills. New internship and part-time jobs have been added.'; ?>
                    </marquee>
                </div>
            </div>
        </div>

        <style>
            .job-filter-btn { transition: all 0.3s ease; }
            .job-filter-btn:hover { background-color: #10B981 !important; border-color: #10B981 !important; color: #fff !important; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.4); }
        </style>
        <div class="container-fluid px-4 px-xl-5 hero-content" style="max-width: 1100px; margin: 0 auto; padding-top: 0;">
            <div class="mb-4">
                <span class="badge rounded-pill shadow-sm" style="font-size:0.85rem; font-weight:700; color: #fff; background-color: rgba(16, 185, 129, 0.25); border: 1px solid rgba(16, 185, 129, 0.5); padding: 8px 18px; letter-spacing: 0.5px; backdrop-filter: blur(4px);">
                    <i class="fa-solid fa-bolt me-1" style="color:#fff;"></i> BANGLADESH'S #1 JOB PORTAL
                </span>
            </div>
            <h1 class="hero-title" style="font-size:4.5rem; font-weight:800; line-height:1.15; text-shadow: 0 4px 12px rgba(0,0,0,0.3); margin-bottom: 20px;"><?php echo $ht['hero_title']; ?></h1>
            <p class="hero-subtitle mx-auto" style="font-size:1.15rem; opacity:0.95; max-width:650px; text-shadow: 0 2px 6px rgba(0,0,0,0.5); line-height:1.6; margin-bottom: 40px; color:#e2e8f0;"><?php echo $ht['hero_subtitle']; ?></p>
            
            <div class="search-box bg-white rounded-pill shadow-lg mt-4 mx-auto" style="max-width: 1050px; padding: 8px;">
                <form action="jobseeker/jobs.php" method="GET">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-4 d-flex align-items-center bg-transparent ps-4">
                            <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                            <input type="text" name="keyword" list="jobTitleList" class="form-control border-0 shadow-none bg-transparent" placeholder="<?php echo $ht['search_placeholder']; ?>" style="font-size:1.15rem; padding: 18px 15px;">
                            <datalist id="jobTitleList">
                                <option value="Software Engineer">
                                <option value="Data Entry">
                                <option value="Graphic Designer">
                                <option value="Teacher">
                                <option value="Driver">
                                <option value="Nurse">
                                <option value="Sales Executive">
                                <option value="Accountant">
                            </datalist>
                        </div>
                        <div class="col-md-3 border-start">
                            <select name="district" id="districtSelect" class="form-select border-0 shadow-none bg-transparent text-muted" style="font-size:1.15rem; padding: 18px 15px; cursor:pointer;">
                                <option value=""><?php echo $ht['select_district']; ?></option>
                                <?php if ($locations_result && mysqli_num_rows($locations_result) > 0): ?>
                                    <?php while ($location_row = mysqli_fetch_assoc($locations_result)): ?>
                                        <option value="<?php echo htmlspecialchars($location_row['location']); ?>"><?php echo htmlspecialchars($location_row['location']); ?></option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 border-start">
                            <input type="text" name="upazila" list="upazilaList" class="form-control border-0 shadow-none bg-transparent" placeholder="<?php echo $ht['upazila']; ?>" style="font-size:1.15rem; padding: 18px 15px;">
                            <datalist id="upazilaList"></datalist>
                        </div>
                        <div class="col-md-2 pe-1">
                            <button type="submit" class="btn w-100 rounded-pill fw-bold text-white d-flex align-items-center justify-content-center" style="font-size:1.2rem; background-color:#10B981; border:none; padding:16px 0;">
                                <i class="fa-solid fa-magnifying-glass me-2"></i> Search Jobs
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4 pt-2" style="max-width: 900px; margin: 0 auto;">
                <a href="jobseeker/jobs.php" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 shadow-sm job-filter-btn" style="background-color: #10B981; border:none; font-weight:600; font-size:1rem;"><i class="fa-solid fa-border-all"></i> All Jobs</a>
                <a href="jobseeker/jobs.php?job_type=Full-time" class="btn btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 job-filter-btn" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); font-weight:600; font-size:1rem; border-color: rgba(255,255,255,0.25);"><i class="fa-solid fa-briefcase"></i> Full-time</a>
                <a href="jobseeker/jobs.php?job_type=Part-time+(Student)" class="btn btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 job-filter-btn" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); font-weight:600; font-size:1rem; border-color: rgba(255,255,255,0.25);"><i class="fa-solid fa-graduation-cap"></i> Part-time</a>
                <a href="jobseeker/jobs.php?job_type=Internship" class="btn btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 job-filter-btn" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); font-weight:600; font-size:1rem; border-color: rgba(255,255,255,0.25);"><i class="fa-solid fa-star"></i> Internship</a>
                <a href="jobseeker/jobs.php?job_type=Day+Labor" class="btn btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 job-filter-btn" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); font-weight:600; font-size:1rem; border-color: rgba(255,255,255,0.25);"><i class="fa-solid fa-hammer"></i> Day Labor</a>
                <a href="jobseeker/jobs.php?job_type=Remote" class="btn btn-outline-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 job-filter-btn" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); font-weight:600; font-size:1rem; border-color: rgba(255,255,255,0.25);"><i class="fa-solid fa-house-laptop"></i> Remote</a>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const upazilaData = {
                    'Dhaka': ['Savar', 'Dhamrai', 'Keraniganj', 'Nawabganj', 'Dohar'],
                    'Chattogram': ['Hathazari', 'Sitakunda', 'Mirsharai', 'Patiya', 'Fatikchhari'],
                    'Sylhet': ['Sylhet Sadar', 'Beanibazar', 'Golapganj', 'Companiganj', 'Gowainghat'],
                    'Rajshahi': ['Paba', 'Godagari', 'Tanor', 'Bagmara', 'Mohanpur'],
                    'Khulna': ['Batiaghata', 'Dacope', 'Dumuria', 'Koyra', 'Paikgachha'],
                    'Barishal': ['Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gournadi'],
                    'Rangpur': ['Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha'],
                    'Mymensingh': ['Bhaluka', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur'],
                    'Comilla': ['Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram'],
                    'Gazipur': ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
                    'Narayanganj': ['Araihazar', 'Bandar', 'Narayanganj Sadar', 'Rupganj', 'Sonargaon']
                };

                const districtSelect = document.getElementById('districtSelect');
                if(districtSelect) {
                    districtSelect.addEventListener('change', function() {
                        const upazilaList = document.getElementById('upazilaList');
                        upazilaList.innerHTML = '';
                        const selectedDistrict = this.value;
                        if (upazilaData[selectedDistrict]) {
                            upazilaData[selectedDistrict].forEach(upazila => {
                                const option = document.createElement('option');
                                option.value = upazila;
                                upazilaList.appendChild(option);
                            });
                        }
                    });
                }

                // --- Hero Background Slider (3D Prism Transition) ---
                const slides = document.querySelectorAll('.hero-slider-bg');
                let currentSlide = 0;
                if(slides.length > 0) {
                    setInterval(() => {
                        // Remove classes from all slides
                        slides.forEach(slide => {
                            slide.classList.remove('active', 'prev');
                        });
                        
                        // Mark the outgoing slide as 'prev'
                        slides[currentSlide].classList.add('prev');
                        
                        // Select the next slide
                        currentSlide = (currentSlide + 1) % slides.length;
                        
                        // Mark the incoming slide as 'active'
                        slides[currentSlide].classList.add('active');
                    }, 5500); // 5.5 seconds delay (2s slower for optimal reading pace)
                }

                // --- Testimonial 3D Stack Slider ---
                const stackCards = document.querySelectorAll('.testimonial-card-3d');
                const stackDots = document.querySelectorAll('.stack-dot');
                const stackPrevBtn = document.querySelector('.stack-nav-btn.prev');
                const stackNextBtn = document.querySelector('.stack-nav-btn.next');
                let activeStackIndex = 0;

                function updateStackSlider() {
                    stackCards.forEach((card, idx) => {
                        card.classList.remove('active', 'prev-card', 'next-card');
                        
                        if (idx === activeStackIndex) {
                            card.classList.add('active');
                        } else if (idx === (activeStackIndex - 1 + stackCards.length) % stackCards.length) {
                            card.classList.add('prev-card');
                        } else if (idx === (activeStackIndex + 1) % stackCards.length) {
                            card.classList.add('next-card');
                        }
                    });

                    stackDots.forEach((dot, idx) => {
                        dot.classList.toggle('active', idx === activeStackIndex);
                    });
                }

                let stackInterval;
                
                function startStackTimer() {
                    clearInterval(stackInterval);
                    stackInterval = setInterval(() => {
                        activeStackIndex = (activeStackIndex + 1) % stackCards.length;
                        updateStackSlider();
                    }, 3500); // Snappy 3.5s delay
                }

                if (stackCards.length > 0) {
                    // Initialize positions
                    updateStackSlider();
                    startStackTimer();

                    stackNextBtn.addEventListener('click', () => {
                        activeStackIndex = (activeStackIndex + 1) % stackCards.length;
                        updateStackSlider();
                        startStackTimer(); // reset interval on user click
                    });

                    stackPrevBtn.addEventListener('click', () => {
                        activeStackIndex = (activeStackIndex - 1 + stackCards.length) % stackCards.length;
                        updateStackSlider();
                        startStackTimer(); // reset interval on user click
                    });

                    stackDots.forEach((dot, idx) => {
                        dot.addEventListener('click', () => {
                            activeStackIndex = idx;
                            updateStackSlider();
                            startStackTimer(); // reset interval on user click
                        });
                    });
                }
            });
        </script>
    </section>

    <!-- START JOURNEY -->
    <section class="journey-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title"><?php echo $ht['journey_title']; ?></h2>
                <p class="section-subtitle"><?php echo $ht['journey_subtitle']; ?></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('assets/image/journey_jobseeker.png');">
                        <div class="journey-overlay-content">
                            <h5><?php echo $ht['journey_jobseeker']; ?></h5>
                            <p><?php echo $ht['journey_js_sub']; ?></p>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'job_seeker'): ?>
                                <a href="jobseeker/dashboard.php" class="btn btn-sm journey-btn"><?php echo $ht['explore']; ?></a>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-sm journey-btn"><?php echo $ht['explore']; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('assets/image/journey_employer.png');">
                        <div class="journey-overlay-content">
                            <h5><?php echo $ht['journey_employer']; ?></h5>
                            <p><?php echo $ht['journey_emp_sub']; ?></p>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'employer'): ?>
                                <a href="employer/dashboard.php" class="btn btn-sm journey-btn"><?php echo $ht['hire_now']; ?></a>
                            <?php else: ?>
                                <a href="auth/register.php" class="btn btn-sm journey-btn"><?php echo $ht['hire_now']; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('assets/image/journey_government.png');">
                        <div class="journey-overlay-content">
                            <h5><?php echo $ht['journey_govt']; ?></h5>
                            <p><?php echo $ht['journey_govt_sub']; ?></p>
                            <a href="admin_login.php" class="btn btn-sm journey-btn"><?php echo $ht['view_access']; ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="journey-card glass-card" style="background-image: url('assets/image/journey_entrepreneur.png');">
                        <div class="journey-overlay-content">
                            <h5><?php echo $ht['journey_entre']; ?></h5>
                            <p><?php echo $ht['journey_entre_sub']; ?></p>
                            <a href="jobseeker/partner_finder.php" class="btn btn-sm journey-btn"><?php echo $ht['start_building']; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- SECTORS with icons -->
    <section class="sector-section py-5" style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title"><?php echo $ht['sectors_title']; ?></h2>
                <p class="section-subtitle"><?php echo $ht['sectors_subtitle']; ?></p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_it']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_it.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_it']; ?></h5><span><?php echo $ht['sec_it_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_garments']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_garments.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_garments']; ?></h5><span><?php echo $ht['sec_garments_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_transport']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_transport.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_transport']; ?></h5><span><?php echo $ht['sec_transport_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_health']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_healthcare.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_health']; ?></h5><span><?php echo $ht['sec_health_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_agri']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_agriculture.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_agri']; ?></h5><span><?php echo $ht['sec_agri_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_sales']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_sales.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_sales']; ?></h5><span><?php echo $ht['sec_sales_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_edu']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_education.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_edu']; ?></h5><span><?php echo $ht['sec_edu_sub']; ?></span></div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?php echo $jobs_link; ?>?search=<?php echo urlencode($ht['sec_biz']); ?>" class="sector-img-card d-block text-decoration-none overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height:200px;">
                        <div class="sector-img-bg" style="background-image:url('/assets/image/sector_business.png');"></div>
                        <div class="sector-img-overlay"><h5><?php echo $ht['sec_biz']; ?></h5><span><?php echo $ht['sec_biz_sub']; ?></span></div>
                    </a>
                </div>
                <style>
                    .sector-img-card .sector-img-bg {
                        position: absolute; inset: 0;
                        background-size: cover; background-position: center;
                        transition: transform 0.5s ease;
                    }
                    .sector-img-card:hover .sector-img-bg { transform: scale(1.08); }
                    .sector-img-card .sector-img-overlay {
                        position: absolute; inset: 0;
                        background: linear-gradient(to top, rgba(10,20,50,0.88) 0%, rgba(10,20,50,0.3) 55%, transparent 100%);
                        display: flex; flex-direction: column; justify-content: flex-end;
                        padding: 1.4rem 1.2rem;
                    }
                    .sector-img-overlay h5 {
                        color: #fff; font-weight: 700; margin-bottom: 4px; font-size: 1rem;
                        text-shadow: 0 1px 4px rgba(0,0,0,0.4);
                    }
                    .sector-img-overlay span {
                        color: rgba(255,255,255,0.65); font-size: 0.8rem;
                    }
                    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
                    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(37,99,235,0.18) !important; }
                </style>
            </div>
        </div>
    </section>

    <!-- SPECIAL CATEGORIES (STUDENT & DAY LABOR) -->
    <section class="py-5" style="background-color: #fff;">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title"><?php echo $ht['special_title']; ?></h2>
                <p class="section-subtitle"><?php echo $ht['special_subtitle']; ?></p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Part-time+(Student)" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 220px;">
                        <div style="background-image: url('/assets/image/student_jobs.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 2rem;">
                            <h4 class="fw-bold text-white mb-1"><?php echo $ht['student_jobs']; ?></h4>
                            <p class="text-white-50 mb-0"><?php echo $ht['student_jobs_sub']; ?></p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Day+Labor" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 220px;">
                        <div style="background-image: url('/assets/image/day_labor.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 2rem;">
                            <h4 class="fw-bold text-white mb-1"><?php echo $ht['day_labor']; ?></h4>
                            <p class="text-white-50 mb-0"><?php echo $ht['day_labor_sub']; ?></p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo $jobs_link; ?>?job_type=Internship" class="text-decoration-none d-block overflow-hidden rounded-4 shadow-sm position-relative hover-lift" style="height: 220px;">
                        <div style="background-image: url('/assets/image/internships.png'); background-size: cover; background-position: center; position: absolute; inset: 0; transition: transform 0.5s;" class="specialized-bg"></div>
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.2) 60%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 2rem;">
                            <h4 class="fw-bold text-white mb-1"><?php echo $ht['internships']; ?></h4>
                            <p class="text-white-50 mb-0"><?php echo $ht['internships_sub']; ?></p>
                        </div>
                    </a>
                </div>
                <style>
                    .hover-lift:hover .specialized-bg { transform: scale(1.05); }
                    .hover-lift { transition: transform 0.2s; }
                    .hover-lift:hover { transform: translateY(-5px); }
                </style>
            </div>
        </div>
    </section>

    <!-- LATEST JOBS -->
    <section class="latest-jobs-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="mb-5 text-center">
                <h2 class="section-title"><?php echo $ht['latest_title']; ?></h2>
                <p class="section-subtitle"><?php echo $ht['latest_subtitle']; ?></p>
            </div>
            <div class="row g-4">
                <?php if ($latest_jobs_result && mysqli_num_rows($latest_jobs_result) > 0): ?>
                    <?php $job_icons = ['💼','🏢','📋','🎯','⚡','🔧']; $ji=0; ?>
                    <?php while ($job = mysqli_fetch_assoc($latest_jobs_result)): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="job-card-v2">
                                <div class="job-card-header">
                                    <span class="job-icon-circle"><?php echo $job_icons[$ji % 6]; ?></span>
                                    <span class="job-badge"><?php echo $ht['new_badge']; ?></span>
                                </div>
                                <h5 class="job-card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                <div class="job-card-meta">
                                    <span>📍 <?php echo htmlspecialchars($job['location']); ?></span>
                                    <span>💰 <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : $ht['negotiable']; ?></span>
                                </div>
                                <a href="jobseeker/jobs.php" class="btn btn-apply"><?php echo $ht['apply_btn']; ?></a>
                            </div>
                        </div>
                    <?php $ji++; endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state-v2">
                            <div class="empty-icon">📭</div>
                            <h5><?php echo $ht['no_jobs']; ?></h5>
                            <p><?php echo $ht['no_jobs_sub']; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>



    <!-- UPDATES -->
    <section class="updates-v2-section py-5" style="background: linear-gradient(135deg, #fef3c7, #fdf2f8);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="mb-5 text-center">
                <h2 class="section-title"><?php echo $ht['updates_title']; ?></h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="assets/image/update_training.png" alt="Training">
                        <div class="update-card-body">
                            <span class="update-tag"><?php echo $ht['tag_training']; ?></span>
                            <h5><?php echo $ht['update1_title']; ?></h5>
                            <p><?php echo $ht['update1_text']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="assets/image/update_hiring.png" alt="New Jobs">
                        <div class="update-card-body">
                            <span class="update-tag"><?php echo $ht['tag_employment']; ?></span>
                            <h5><?php echo $ht['update2_title']; ?></h5>
                            <p><?php echo $ht['update2_text']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="update-card-v2">
                        <img src="assets/image/update_govt.png" alt="Govt Data">
                        <div class="update-card-body">
                            <span class="update-tag"><?php echo $ht['tag_govt']; ?></span>
                            <h5><?php echo $ht['update3_title']; ?></h5>
                            <p><?php echo $ht['update3_text']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TOP HIRING AREAS -->
    <section class="hiring-areas-section py-5">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title"><?php echo $ht['top_areas_title']; ?></h2>
            </div>
            <div class="row g-4">
                <?php if ($top_areas_result && mysqli_num_rows($top_areas_result) > 0): ?>
                    <?php $area_images = ['city_dhaka.png','city_ctg.png','city_sylhet.png','city_general.png']; $ai=0; ?>
                    <?php while ($area = mysqli_fetch_assoc($top_areas_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="area-card-v3" style="background-image: url('assets/image/<?php echo $area_images[$ai % 4]; ?>');">
                                <div class="area-overlay">
                                    <div class="area-icon-v3"><i class="fa-solid fa-location-dot"></i></div>
                                    <h5><?php echo htmlspecialchars($area['location']); ?></h5>
                                    <div class="area-count"><?php echo (int)$area['total_jobs']; ?> <?php echo $ht['jobs_count_suffix']; ?></div>
                                    <a href="jobseeker/jobs.php" class="btn btn-sm btn-light mt-3 fw-bold rounded-pill px-3 shadow-sm text-primary"><?php echo $ht['view_jobs']; ?></a>
                                </div>
                            </div>
                        </div>
                    <?php $ai++; endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center"><p><?php echo $ht['no_area_data']; ?></p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- RESOURCES -->
    <section class="resources-v2-section py-5" style="background: linear-gradient(135deg, #eff6ff, #f0fdf4);">
        <div class="container-fluid px-4 px-xl-5">
            <div class="text-center mb-5">
                <h2 class="section-title"><?php echo $ht['resources_title']; ?></h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="cv_guide.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/cv_guide.png" style="width:100%; height:220px; object-fit:cover;" alt="CV Guide">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['cv_guide']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['cv_guide_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="interview_tips.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/interview_tips.png" style="width:100%; height:220px; object-fit:cover;" alt="Interview Tips">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['interview_tips']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['interview_tips_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="trainings.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/skill_dev.png" style="width:100%; height:220px; object-fit:cover;" alt="Skill Development">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['skill_dev']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['skill_dev_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="career_counseling.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/career_counseling.png" style="width:100%; height:220px; object-fit:cover;" alt="Career Counseling">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['career_counsel']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['career_counsel_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="entrepreneur_support.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/entrepreneur.png" style="width:100%; height:220px; object-fit:cover;" alt="Entrepreneur Support">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['entrepreneur']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['entrepreneur_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="jobseeker/partner_finder.php" class="text-decoration-none text-dark d-block hover-lift">
                        <div class="resource-card-v2 p-0 overflow-hidden text-start bg-white border-0">
                            <img src="assets/image/partner_finder.png" style="width:100%; height:220px; object-fit:cover;" alt="Partner Finder">
                            <div class="p-4"><h5 class="fw-bold mb-2"><?php echo $ht['partner_finder']; ?></h5><p class="text-muted small mb-0"><?php echo $ht['partner_finder_sub']; ?></p></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS (3D PERSPECTIVE CARD STACK SLIDER) -->
    <section class="testimonial-section py-5" style="background-color: #030712; color: #fff; overflow: hidden; position: relative;">
        <div class="container-fluid px-4 px-xl-5 text-center position-relative">
            <h5 style="color: #10B981; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem;"><?php echo $ht['testi_badge']; ?></h5>
            <h2 class="section-title text-white mb-2" style="font-weight: 800; font-size: 2.6rem;"><?php echo $ht['testi_title']; ?></h2>
            <p class="section-subtitle mb-5" style="color: #9CA3AF; max-width: 600px; margin: 0 auto;"><?php echo $ht['testi_subtitle']; ?></p>
            
            <div class="testimonial-3d-stack-wrapper">
                <div class="testimonial-stack-container">
                    
                    <!-- Card 1 -->
                    <div class="testimonial-card-3d active" data-index="0">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-laptop-code"></i></span>
                            <span class="brand-name">BRAIN STATION 23</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t1_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #10B981;">R</div>
                            <div class="author-info">
                                <h6 class="author-name">Rahim Hossain</h6>
                                <span class="author-role"><?php echo $ht['t1_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 5.0</div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="testimonial-card-3d next-1" data-index="1">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-industry"></i></span>
                            <span class="brand-name">KDS GROUP</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t2_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #3B82F6;">F</div>
                            <div class="author-info">
                                <h6 class="author-name">Fatema Begum</h6>
                                <span class="author-role"><?php echo $ht['t2_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 4.9</div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="testimonial-card-3d next-2" data-index="2">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-hand-holding-heart"></i></span>
                            <span class="brand-name">IBN SINA HOSPITAL</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t3_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #8B5CF6;">A</div>
                            <div class="author-info">
                                <h6 class="author-name">Ayesha Khatun</h6>
                                <span class="author-role"><?php echo $ht['t3_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 5.0</div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="testimonial-card-3d" data-index="3">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-chart-line"></i></span>
                            <span class="brand-name">VARENDRA IT SOLUTIONS</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t4_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #F59E0B;">T</div>
                            <div class="author-info">
                                <h6 class="author-name">Tariqul Islam</h6>
                                <span class="author-role"><?php echo $ht['t4_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 4.8</div>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="testimonial-card-3d" data-index="4">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-helmet-safety"></i></span>
                            <span class="brand-name">KHULNA SHIPYARD</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t5_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #EF4444;">K</div>
                            <div class="author-info">
                                <h6 class="author-name">Kamal Uddin</h6>
                                <span class="author-role"><?php echo $ht['t5_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 5.0</div>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="testimonial-card-3d" data-index="5">
                        <div class="card-brand-header">
                            <span class="brand-logo"><i class="fa-solid fa-user-tie"></i></span>
                            <span class="brand-name">PATHAO</span>
                        </div>
                        <p class="card-quote"><?php echo $ht['t6_text']; ?></p>
                        <div class="card-author-footer">
                            <div class="author-avatar" style="background-color: #06B6D4;">N</div>
                            <div class="author-info">
                                <h6 class="author-name">Nusrat Jahan</h6>
                                <span class="author-role"><?php echo $ht['t6_role']; ?></span>
                            </div>
                            <div class="author-rating"><i class="fa-solid fa-star text-warning"></i> 4.9</div>
                        </div>
                    </div>

                </div>

                <!-- Navigation Controls -->
                <div class="stack-navigation mt-5">
                    <button type="button" class="stack-nav-btn prev"><i class="fa-solid fa-arrow-left"></i></button>
                    <div class="stack-indicators">
                        <span class="stack-dot active" data-slide="0"></span>
                        <span class="stack-dot" data-slide="1"></span>
                        <span class="stack-dot" data-slide="2"></span>
                        <span class="stack-dot" data-slide="3"></span>
                        <span class="stack-dot" data-slide="4"></span>
                        <span class="stack-dot" data-slide="5"></span>
                    </div>
                    <button type="button" class="stack-nav-btn next"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <style>
            .testimonial-3d-stack-wrapper {
                margin: 40px auto 20px;
                max-width: 680px;
                position: relative;
            }
            .testimonial-stack-container {
                position: relative;
                height: 380px;
                width: 100%;
                perspective: 1200px;
                transform-style: preserve-3d;
            }
            .testimonial-card-3d {
                position: absolute;
                width: 100%;
                height: 100%;
                left: 0;
                top: 0;
                background: #0b0f19; /* Sleek elegant near-black like the attachment */
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 28px;
                padding: 2.8rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
                transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.5s cubic-bezier(0.25, 1, 0.5, 1);
                opacity: 0;
                transform: translateX(100%) scale(0.9) rotateY(15deg);
                pointer-events: none;
                text-align: left;
                z-index: 1;
            }
            
            /* Active top card */
            .testimonial-card-3d.active {
                opacity: 1;
                transform: translateX(0) scale(1) rotateY(0deg);
                z-index: 5;
                pointer-events: auto;
            }
            
            /* Outgoing animated transition (sliding to left and fading out) */
            .testimonial-card-3d.prev-card {
                opacity: 0;
                transform: translateX(-100%) scale(0.9) rotateY(-15deg);
                z-index: 2;
            }

            /* Incoming transition (placed on right) */
            .testimonial-card-3d.next-card {
                opacity: 0;
                transform: translateX(100%) scale(0.9) rotateY(15deg);
                z-index: 2;
            }

            .card-brand-header {
                display: flex;
                align-items: center;
                gap: 0.8rem;
                margin-bottom: 1.5rem;
            }
            .brand-logo {
                font-size: 1.6rem;
                color: #10B981;
            }
            .brand-name {
                font-size: 1.1rem;
                font-weight: 700;
                letter-spacing: 1.5px;
                color: #fff;
            }
            .card-quote {
                font-size: 1.25rem;
                line-height: 1.7;
                color: #e5e7eb;
                font-style: italic;
                font-weight: 400;
                margin-bottom: 2rem;
                flex-grow: 1;
            }
            .card-author-footer {
                display: flex;
                align-items: center;
                border-top: 1px solid rgba(255, 255, 255, 0.06);
                padding-top: 1.5rem;
                gap: 1rem;
            }
            .author-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                color: #fff;
                font-size: 1.3rem;
                font-weight: 800;
                display: flex;
                justify-content: center;
                align-items: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            }
            .author-info {
                flex-grow: 1;
            }
            .author-name {
                font-size: 1.1rem;
                font-weight: 700;
                color: #fff;
                margin: 0 0 3px 0;
            }
            .author-role {
                font-size: 0.85rem;
                color: #9CA3AF;
            }
            .author-rating {
                background: rgba(255,255,255,0.06);
                padding: 6px 14px;
                border-radius: 50px;
                font-weight: 700;
                color: #10B981;
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.95rem;
                border: 1px solid rgba(255,255,255,0.03);
            }

            .stack-navigation {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 2rem;
            }
            .stack-nav-btn {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.08);
                color: #fff;
                width: 52px;
                height: 52px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 1.1rem;
                transition: all 0.3s;
            }
            .stack-nav-btn:hover {
                background: #10B981;
                border-color: #10B981;
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
                transform: scale(1.08);
            }
            .stack-indicators {
                display: flex;
                gap: 8px;
            }
            .stack-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                cursor: pointer;
                transition: all 0.3s;
            }
            .stack-dot.active {
                background: #10B981;
                transform: scale(1.4);
                box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            }
        </style>
    </section>

</div>

<?php include('includes/footer.php'); ?>