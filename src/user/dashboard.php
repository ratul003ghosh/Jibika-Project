<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$dashText = [
    'bn' => [
        'title' => 'চাকরিপ্রার্থী ড্যাশবোর্ড',
        'welcome' => 'স্বাগতম, ',
        'email' => 'ইমেইল: ',
        'role' => 'ভূমিকা: ',
        'status_sec' => 'বর্তমান কর্মসংস্থান অবস্থা',
        'remarks' => 'মন্তব্য:',
        'last_updated' => 'সর্বশেষ আপডেট:',
        'btn_profile' => 'আমার প্রোফাইল',
        'btn_skills' => 'আমার দক্ষতা',
        'btn_jobs' => 'চাকরি খুঁজুন',
        'btn_apps' => 'আমার আবেদনসমূহ',
        'btn_logout' => 'লগআউট',
        'status_employed' => 'কর্মরত',
        'status_training' => 'প্রশিক্ষণাধীন',
        'status_self' => 'স্বনির্ভর',
        'status_unemployed' => 'বেকার'
    ],
    'en' => [
        'title' => 'Job Seeker Dashboard',
        'welcome' => 'Welcome, ',
        'email' => 'Email: ',
        'role' => 'Role: ',
        'status_sec' => 'Current Employment Status',
        'remarks' => 'Remarks:',
        'last_updated' => 'Last Updated:',
        'btn_profile' => 'My Profile',
        'btn_skills' => 'My Skills',
        'btn_jobs' => 'Browse Jobs',
        'btn_apps' => 'My Applications',
        'btn_logout' => 'Logout',
        'status_employed' => 'Employed',
        'status_training' => 'Training',
        'status_self' => 'Self Employed',
        'status_unemployed' => 'Unemployed'
    ]
];
$d = $dashText[$lang];

$user_id = $_SESSION['user_id'];

// Fetch current employment status
$status_result = $conn->query("SELECT current_status, remarks, updated_at FROM employment_status WHERE user_id='$user_id' LIMIT 1");
$status_data = $status_result ? $status_result->fetch_assoc() : null;

$current_status = $status_data['current_status'] ?? 'unemployed';
$status_remarks = $status_data['remarks'] ?? '';
$status_updated = $status_data['updated_at'] ?? '';
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2><?php echo htmlspecialchars($d['title']); ?></h2>
        <p><?php echo htmlspecialchars($d['welcome']); ?><?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <p><?php echo htmlspecialchars($d['email']); ?><?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <p><?php echo htmlspecialchars($d['role']); ?><?php echo htmlspecialchars($_SESSION['role']); ?></p>

        <div class="mt-4 p-3 bg-light rounded">
            <h5 class="mb-3"><?php echo htmlspecialchars($d['status_sec']); ?></h5>
            <p class="mb-2">
                <?php
                    if($current_status == 'employed'){
                        echo "<span class='badge bg-success'>" . htmlspecialchars($d['status_employed']) . "</span>";
                    } elseif($current_status == 'training'){
                        echo "<span class='badge bg-warning text-dark'>" . htmlspecialchars($d['status_training']) . "</span>";
                    } elseif($current_status == 'self_employed'){
                        echo "<span class='badge bg-info text-dark'>" . htmlspecialchars($d['status_self']) . "</span>";
                    } else {
                        echo "<span class='badge bg-danger'>" . htmlspecialchars($d['status_unemployed']) . "</span>";
                    }
                ?>
            </p>

            <?php if($status_remarks != ""): ?>
                <p class="mb-1"><strong><?php echo htmlspecialchars($d['remarks']); ?></strong> <?php echo htmlspecialchars($status_remarks); ?></p>
            <?php endif; ?>

            <?php if($status_updated != ""): ?>
                <p class="mb-0"><strong><?php echo htmlspecialchars($d['last_updated']); ?></strong> <?php echo htmlspecialchars($status_updated); ?></p>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <a href="profile.php" class="btn btn-success me-2"><?php echo htmlspecialchars($d['btn_profile']); ?></a>
            <a href="skills.php" class="btn btn-primary me-2"><?php echo htmlspecialchars($d['btn_skills']); ?></a>
            <a href="jobs.php" class="btn btn-warning me-2"><?php echo htmlspecialchars($d['btn_jobs']); ?></a>
            <a href="my_applications.php" class="btn btn-info me-2"><?php echo htmlspecialchars($d['btn_apps']); ?></a>
            <a href="../logout.php" class="btn btn-danger"><?php echo htmlspecialchars($d['btn_logout']); ?></a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>