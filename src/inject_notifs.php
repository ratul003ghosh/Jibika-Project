<?php
include('assets/config/db.php');

$user_res = $conn->query("SELECT user_id FROM users WHERE email = 'user@gmail.com'");
if (!$user_res || $user_res->num_rows == 0) die("User not found.");
$user_id = $user_res->fetch_assoc()['user_id'];

// Clear old ones just in case
$conn->query("DELETE FROM notifications WHERE user_id = $user_id");

// Inject 5 notifications with correct bilingual columns
$notifs = [
    [
        'job_id' => 0,
        't_en' => 'Nearby Jobs Alert',
        't_bn' => 'নিকটবর্তী চাকরির সতর্কতা',
        'm_en' => '📍 3 new IT & Computer jobs have been posted in Dhaka (Your preferred district).',
        'm_bn' => '📍 ঢাকা (আপনার পছন্দের জেলা) এলাকায় ৩টি নতুন আইটি ও কম্পিউটার চাকরি পোস্ট করা হয়েছে।'
    ],
    [
        'job_id' => 0,
        't_en' => 'Interview Notice',
        't_bn' => 'সাক্ষাৎকার নোটিশ',
        'm_en' => '📅 MetaTech has proposed an interview schedule for you.',
        'm_bn' => '📅 মেটাটেক আপনার জন্য একটি সাক্ষাৎকারের সময়সূচী প্রস্তাব করেছে।'
    ],
    [
        'job_id' => 0,
        't_en' => 'Application Rejected',
        't_bn' => 'আবেদন প্রত্যাখ্যাত',
        'm_en' => '❌ Your application for Frontend Developer at Skyline Soft was unfortunately rejected.',
        'm_bn' => '❌ স্কাইলাইন সফট-এ ফ্রন্টএন্ড ডেভেলপার পদের জন্য আপনার আবেদনটি দুর্ভাগ্যবশত বাতিল করা হয়েছে।'
    ],
    [
        'job_id' => 0,
        't_en' => 'Preferred Category Alert',
        't_bn' => 'পছন্দের ক্যাটাগরি সতর্কতা',
        'm_en' => '🚀 A new Remote Frontend Developer job was just posted in your preferred category!',
        'm_bn' => '🚀 আপনার পছন্দের ক্যাটাগরিতে একটি নতুন রিমোট ফ্রন্টএন্ড ডেভেলপার চাকরি পোস্ট করা হয়েছে!'
    ],
    [
        'job_id' => 0,
        't_en' => 'Job Offer / Selected',
        't_bn' => 'চাকরির প্রস্তাব / নির্বাচিত',
        'm_en' => '🎉 Congratulations! You have been Selected for the Frontend Developer position at WebWizards.',
        'm_bn' => '🎉 অভিনন্দন! আপনি ওয়েবউইজার্ডস-এ ফ্রন্টএন্ড ডেভেলপার পদের জন্য নির্বাচিত হয়েছেন।'
    ]
];

foreach ($notifs as $n) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
    $stmt->bind_param("iissss", $user_id, $n['job_id'], $n['t_en'], $n['t_bn'], $n['m_en'], $n['m_bn']);
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error . "\n";
    }
}

echo "Bilingual Notifications successfully injected!\n";
?>
