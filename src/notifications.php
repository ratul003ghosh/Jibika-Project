<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: auth/login.php");
    exit();
}

include('assets/config/db.php');

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

// Mark all as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0");

$notifText = [
    'bn' => [
        'title' => 'বিজ্ঞপ্তি',
        'subtitle' => 'আপনার সাম্প্রতিক সমস্ত বিজ্ঞপ্তি এবং সতর্কতা',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'no_notifications' => 'আপনার কোনো বিজ্ঞপ্তি নেই।',
        'btn_view_job' => 'চাকরি দেখুন'
    ],
    'en' => [
        'title' => 'Notifications',
        'subtitle' => 'All your recent notifications and alerts',
        'back_btn' => 'Back to Dashboard',
        'no_notifications' => 'You have no notifications.',
        'btn_view_job' => 'View Job'
    ]
];
$ct = $notifText[$lang];

$sql = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 50";
$result = $conn->query($sql);
?>

<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="fa-solid fa-bell text-success"></i> <?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0"><?php echo $ct['subtitle']; ?></p>
        </div>
        <a href="jobseeker/dashboard.php" class="btn btn-secondary"><?php echo $ct['back_btn']; ?></a>
    </div>

    <div class="card shadow p-4 mb-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($n = $result->fetch_assoc()): ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-primary">
                                <?php echo htmlspecialchars($lang == 'bn' ? $n['title_bn'] : $n['title_en']); ?>
                            </h5>
                            <p class="mb-1">
                                <?php echo htmlspecialchars($lang == 'bn' ? $n['message_bn'] : $n['message_en']); ?>
                            </p>
                            <small class="text-muted"><?php echo translateDate(date('d M Y, h:i A', strtotime($n['created_at'])), $lang); ?></small>
                        </div>
                        <?php if ($n['job_id'] > 0): ?>
                            <a href="job_details.php?id=<?php echo $n['job_id']; ?>" class="btn btn-outline-success btn-sm">
                                <?php echo $ct['btn_view_job']; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="fa fa-info-circle"></i> <?php echo $ct['no_notifications']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
