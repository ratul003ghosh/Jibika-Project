<?php
mysqli_report(MYSQLI_REPORT_OFF);
$host = "127.0.0.1";
$port = 3307;
$user = "root";
$pass = "";
$dbname = "jibika_db";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function getJobImage($title, $category) {
    $t = strtolower($title . ' ' . $category);
    if (strpos($t, 'plumb') !== false) return '../assets/image/plumber.jpg'; // plumber
    if (strpos($t, 'web') !== false || strpos($t, 'software') !== false || strpos($t, 'it') !== false || strpos($t, 'develop') !== false) return '../assets/image/developer.png';
    if (strpos($t, 'garment') !== false || strpos($t, 'tailor') !== false) return 'https://images.unsplash.com/photo-1558024920-b41e1887dc32?w=600&q=80';
    if (strpos($t, 'driv') !== false) return 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80';
    if (strpos($t, 'teach') !== false || strpos($t, 'educat') !== false) return 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=600&q=80';
    if (strpos($t, 'health') !== false || strpos($t, 'doctor') !== false || strpos($t, 'nurs') !== false || strpos($t, 'medical') !== false) return 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&q=80';
    if (strpos($t, 'market') !== false || strpos($t, 'sale') !== false) return 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80';
    if (strpos($t, 'engineer') !== false) return 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=600&q=80';
    if (strpos($t, 'cook') !== false || strpos($t, 'chef') !== false || strpos($t, 'restaurant') !== false) return 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=600&q=80';
    // generic business/office
    return 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&q=80';
}

function translateJobTitle($title, $lang) {
    if ($lang !== 'bn') return $title;
    $translations = [
        'web developer' => 'ওয়েব ডেভেলপার',
        'plumber' => 'প্লাম্বার',
        'driver' => 'ড্রাইভার',
        'teacher' => 'শিক্ষক',
        'engineer' => 'প্রকৌশলী',
        'software engineer' => 'সফটওয়্যার প্রকৌশলী',
        'office assistant' => 'অফিস সহকারী',
        'accountant' => 'হিসাবরক্ষক',
        'sales representative' => 'বিক্রয় প্রতিনিধি',
        'manager' => 'ব্যবস্থাপক',
        'cashier' => 'ক্যাশিয়ার',
        'electrician' => 'ইলেকট্রিশিয়ান',
        'security guard' => 'নিরাপত্তা প্রহরী',
        'delivery rider' => 'ডেলিভারি রাইডার',
        'delivery man' => 'ডেলিভারি ম্যান',
        'mason' => 'রাজমিস্ত্রি',
        'carpenter' => 'কাঠমিস্ত্রি',
        'welder' => 'ওয়েল্ডার',
        'mechanic' => 'মেকানিক',
        'painter' => 'পেইন্টার',
    ];
    $lower = strtolower(trim($title));
    if (isset($translations[$lower])) {
        return $translations[$lower];
    }
    foreach ($translations as $key => $val) {
        if (strpos($lower, $key) !== false) {
            return $val;
        }
    }
    return $title;
}

function translateEmployerName($name, $lang) {
    if ($lang !== 'bn') return $name;
    $translations = [
        'employee1' => 'নিয়োগকারী ১',
        'employee 1' => 'নিয়োগকারী ১',
        'sharif ahmed' => 'শরীফ আহমেদ',
        'ratul' => 'রাতুল',
        'test user' => 'টেস্ট ইউজার',
        'admin' => 'অ্যাডমিন',
    ];
    $lower = strtolower(trim($name));
    return $translations[$lower] ?? $name;
}

function translateSalary($salary, $lang) {
    if (empty($salary) || strtolower($salary) === 'negotiable') {
        return $lang === 'bn' ? 'আলোচনা সাপেক্ষে' : 'Negotiable';
    }
    if ($lang !== 'bn') return $salary;
    
    $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    
    $cleanSalary = preg_replace('/[^0-9]/', '', $salary);
    if (is_numeric($cleanSalary)) {
        $formatted = number_format((float)$cleanSalary);
        return str_replace($eng_nums, $bng_nums, $formatted);
    }
    return str_replace($eng_nums, $bng_nums, $salary);
}

function translateDistrict($district, $lang) {
    if (empty($district) || $lang !== 'bn') return $district;
    $translations = [
        'dhaka' => 'ঢাকা',
        'chattogram' => 'চট্টগ্রাম',
        'chittagong' => 'চট্টগ্রাম',
        'khulna' => 'খুলনা',
        'rajshahi' => 'রাজশাহী',
        'barishal' => 'বরিশাল',
        'barisal' => 'বরিশাল',
        'sylhet' => 'সিলেট',
        'rangpur' => 'রংপুর',
        'mymensingh' => 'ময়মনসিংহ',
        'bogura' => 'বগুড়া',
        'bogra' => 'বগুড়া',
    ];
    $lower = strtolower(trim($district));
    return $translations[$lower] ?? $district;
}

function translateNumber($num, $lang) {
    if ($lang !== 'bn') return $num;
    $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return str_replace($eng_nums, $bng_nums, (string)$num);
}

function translateDate($dateStr, $lang) {
    if ($lang !== 'bn' || empty($dateStr)) return $dateStr;
    $months = [
        'January' => 'জানুয়ারি', 'February' => 'ফেব্রুয়ারি', 'March' => 'মার্চ',
        'April' => 'এপ্রিল', 'May' => 'মে', 'June' => 'জুন',
        'July' => 'জুলাই', 'August' => 'আগস্ট', 'September' => 'সেপ্টেম্বর',
        'October' => 'অক্টোবর', 'November' => 'নভেম্বর', 'December' => 'ডিসেম্বর',
        'Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ',
        'Apr' => 'এপ্রিল', 'Jun' => 'জুন', 'Jul' => 'জুলাই',
        'Aug' => 'আগস্ট', 'Sep' => 'সেপ্টেম্বর', 'Oct' => 'অক্টোবর',
        'Nov' => 'নভেম্বর', 'Dec' => 'ডিসেম্বর',
        'AM' => 'পূর্বাহ্ন', 'PM' => 'অপরাহ্ন',
        'Sunday' => 'রবিবার', 'Monday' => 'সোমবার', 'Tuesday' => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার', 'Thursday' => 'বৃহস্পতিবার', 'Friday' => 'শুক্রবার',
        'Saturday' => 'শনিবার'
    ];
    foreach ($months as $en => $bn) {
        $dateStr = str_ireplace($en, $bn, $dateStr);
    }
    return translateNumber($dateStr, $lang);
}

function translateJobCategory($category, $lang) {
    if (empty($category) || $lang !== 'bn') return $category;
    $translations = [
        'it & computer' => 'আইটি ও কম্পিউটার',
        'garments' => 'গার্মেন্টস',
        'driving' => 'ড্রাইভিং',
        'sales & marketing' => 'বিক্রয় ও বিপণন',
        'office support' => 'অফিস সাপোর্ট',
        'healthcare' => 'স্বাস্থ্যসেবা',
        'education' => 'শিক্ষা',
        'small business' => 'ক্ষুদ্র ব্যবসা',
        'other' => 'অন্যান্য'
    ];
    $lower = strtolower(trim($category));
    return $translations[$lower] ?? $category;
}

function translateJobType($type, $lang) {
    if (empty($type) || $lang !== 'bn') return $type;
    $translations = [
        'full-time' => 'পূর্ণকালীন',
        'part-time' => 'খণ্ডকালীন',
        'part-time (student)' => 'খণ্ডকালীন (শিক্ষার্থী)',
        'day labor' => 'দৈনিক শ্রমিক',
        'internship' => 'ইন্টার্নশিপ',
        'contract' => 'চুক্তিভিত্তিক',
        'remote' => 'রিমোট'
    ];
    $lower = strtolower(trim($type));
    return $translations[$lower] ?? $type;
}

function translateUpazila($upazila, $lang) {
    if (empty($upazila) || $lang !== 'bn') return $upazila;
    $translations = [
        'dhamrai' => 'ধামরাই',
        'savar' => 'সাভার',
        'keraniganj' => 'কেরানীগঞ্জ',
        'patiya' => 'পটিয়া',
        'raozan' => 'রাউজান',
        'batiaghata' => 'বটিয়াঘাটা',
        'paba' => 'পবা',
        'babuganj' => 'বাবুগঞ্জ',
        'beanibazar' => 'বিয়ানীবাজার',
        'pirgacha' => 'পীরগাছা',
        'trishal' => 'ত্রিশাল',
        'shajahanpur' => 'শাহজাহানপুর',
    ];
    $lower = strtolower(trim($upazila));
    return $translations[$lower] ?? $upazila;
}

function translateWard($ward, $lang) {
    if (empty($ward) || $lang !== 'bn') return $ward;
    $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $translated = str_ireplace('ward', 'ওয়ার্ড', $ward);
    return str_replace($eng_nums, $bng_nums, $translated);
}
?>