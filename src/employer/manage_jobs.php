<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$mjText = [
    'bn' => [
        'title' => 'চাকরি পরিচালনা',
        'subtitle' => 'আপনার পোস্ট করা চাকরিগুলো দেখুন, বন্ধ করুন, পুনরায় খুলুন বা মুছে ফেলুন।',
        'post_btn' => 'নতুন চাকরি পোস্ট করুন',
        'back_btn' => 'ফিরে যান',
        'msg_deleted' => 'চাকরিটি সফলভাবে মুছে ফেলা হয়েছে!',
        'msg_status_updated' => 'চাকরির স্থিতি সফলভাবে আপডেট করা হয়েছে!',
        'th_serial' => '#',
        'th_info' => 'চাকরির তথ্য',
        'th_category' => 'ক্যাটাগরি',
        'th_type' => 'কাজের ধরন',
        'th_area' => 'এলাকা',
        'th_salary' => 'বেতন',
        'th_deadline' => 'শেষ সময়সীমা',
        'th_status' => 'স্থিতি',
        'th_action' => 'পদক্ষেপ',
        'confirm_close' => 'এই চাকরিটি বন্ধ করবেন?',
        'confirm_reopen' => 'এই চাকরিটি পুনরায় খুলবেন?',
        'confirm_delete' => 'আপনি কি নিশ্চিত যে এই চাকরিটি মুছে ফেলতে চান?',
        'btn_applicants' => 'আবেদনকারীগণ',
        'btn_close' => 'বন্ধ করুন',
        'btn_reopen' => 'পুনরায় খুলুন',
        'btn_delete' => 'মুছে ফেলুন',
        'status_active' => 'সক্রিয়',
        'status_closed' => 'বন্ধ',
        'no_jobs_title' => 'এখনও কোনো চাকরি পোস্ট করা হয়নি',
        'no_jobs_desc' => 'আপনি এখনও কোনো চাকরি পোস্ট করেননি।',
        'no_jobs_btn' => 'আপনার প্রথম চাকরিটি পোস্ট করুন',
        'vacancy' => 'খালি পদ:',
        'negotiable' => 'আলোচনা সাপেক্ষে',
    ],
    'en' => [
        'title' => 'Manage Jobs',
        'subtitle' => 'View, close, reopen, or delete your posted jobs.',
        'post_btn' => 'Post New Job',
        'back_btn' => 'Back',
        'msg_deleted' => 'Job deleted successfully!',
        'msg_status_updated' => 'Job status updated successfully!',
        'th_serial' => '#',
        'th_info' => 'Job Info',
        'th_category' => 'Category',
        'th_type' => 'Type',
        'th_area' => 'Area',
        'th_salary' => 'Salary',
        'th_deadline' => 'Deadline',
        'th_status' => 'Status',
        'th_action' => 'Action',
        'confirm_close' => 'Close this job?',
        'confirm_reopen' => 'Reopen this job?',
        'confirm_delete' => 'Are you sure you want to delete this job?',
        'btn_applicants' => 'Applicants',
        'btn_close' => 'Close',
        'btn_reopen' => 'Reopen',
        'btn_delete' => 'Delete',
        'status_active' => 'Active',
        'status_closed' => 'Closed',
        'no_jobs_title' => 'No Jobs Posted Yet',
        'no_jobs_desc' => 'You have not posted any job yet.',
        'no_jobs_btn' => 'Post Your First Job',
        'vacancy' => 'Vacancy:',
        'negotiable' => 'Negotiable',
    ]
];
$ct = $mjText[$lang];

$message = "";

// Delete job
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);

    $delete_sql = "DELETE FROM jobs WHERE job_id='$job_id' AND employer_id='$employer_id'";

    if ($conn->query($delete_sql)) {
        $message = $ct['msg_deleted'];
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Toggle job status
if (isset($_GET['status']) && isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $status = ($_GET['status'] == 'closed') ? 'closed' : 'active';

    $update_sql = "UPDATE jobs 
                   SET status='$status' 
                   WHERE job_id='$job_id' AND employer_id='$employer_id'";

    if ($conn->query($update_sql)) {
        $message = $ct['msg_status_updated'];
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch employer jobs
$sql = "SELECT 
            jobs.*,
            d.district_name,
            u.upazila_name,
            w.ward_name
        FROM jobs
        LEFT JOIN districts d ON jobs.district_id = d.district_id
        LEFT JOIN upazilas u ON jobs.upazila_id = u.upazila_id
        LEFT JOIN wards w ON jobs.ward_id = w.ward_id
        WHERE jobs.employer_id='$employer_id'
        ORDER BY jobs.job_id DESC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1"><?php echo $ct['title']; ?></h2>
                <p class="text-muted mb-0"><?php echo $ct['subtitle']; ?></p>
            </div>

            <div>
                <a href="post_job.php" class="btn btn-success me-2"><?php echo $ct['post_btn']; ?></a>
                <a href="dashboard.php" class="btn btn-secondary"><?php echo $ct['back_btn']; ?></a>
            </div>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo $ct['th_serial']; ?></th>
                            <th><?php echo $ct['th_info']; ?></th>
                            <th><?php echo $ct['th_category']; ?></th>
                            <th><?php echo $ct['th_type']; ?></th>
                            <th><?php echo $ct['th_area']; ?></th>
                            <th><?php echo $ct['th_salary']; ?></th>
                            <th><?php echo $ct['th_deadline']; ?></th>
                            <th><?php echo $ct['th_status']; ?></th>
                            <th><?php echo $ct['th_action']; ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $count = 1; ?>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo translateNumber($count++, $lang); ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars(translateJobTitle($job['title'] ?? '', $lang)); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($job['description'] ?? '', 0, 80)); ?>...
                                    </small>
                                    <br>
                                    <small>
                                        <strong><?php echo $ct['vacancy']; ?></strong>
                                        <?php echo htmlspecialchars(translateNumber($job['vacancy'] ?? 'N/A', $lang)); ?>
                                    </small>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(translateJobCategory($job['job_category'] ?? 'N/A', $lang)); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(translateJobType($job['job_type'] ?? 'N/A', $lang)); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(translateDistrict($job['district_name'] ?? '', $lang) ?: 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars(translateUpazila($job['upazila_name'] ?? '', $lang) ?: 'N/A'); ?>
                                    /
                                    <?php echo htmlspecialchars(translateWard($job['ward_name'] ?? '', $lang) ?: 'N/A'); ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($job['location'] ?? ''); ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?php 
                                            $st = $job['salary_type'] ?? 'Negotiable';
                                            if ($lang === 'bn') {
                                                if (strtolower($st) === 'fixed') echo 'নির্দিষ্ট';
                                                elseif (strtolower($st) === 'range') echo 'সীমা';
                                                else echo 'আলোচনা সাপেক্ষে';
                                            } else {
                                                echo htmlspecialchars($st);
                                            }
                                        ?>
                                    </span>
                                    <br>
                                    <?php echo (empty($job['salary']) || strtolower($job['salary']) === 'negotiable') ? $ct['negotiable'] : '৳ ' . translateSalary($job['salary'], $lang); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(translateDate($job['application_deadline'] ?? 'N/A', $lang)); ?>
                                </td>

                                <td>
                                    <?php if (($job['status'] ?? 'active') == 'active'): ?>
                                        <span class="badge bg-success"><?php echo $ct['status_active']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo $ct['status_closed']; ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="applicants.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-primary btn-sm mb-1">
                                        <?php echo $ct['btn_applicants']; ?>
                                    </a>

                                    <?php if (($job['status'] ?? 'active') == 'active'): ?>
                                        <a href="manage_jobs.php?status=closed&job_id=<?php echo $job['job_id']; ?>"
                                           class="btn btn-warning btn-sm mb-1"
                                           onclick="return confirm('<?php echo addslashes($ct['confirm_close']); ?>')">
                                            <?php echo $ct['btn_close']; ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="manage_jobs.php?status=active&job_id=<?php echo $job['job_id']; ?>"
                                           class="btn btn-success btn-sm mb-1"
                                           onclick="return confirm('<?php echo addslashes($ct['confirm_reopen']); ?>')">
                                            <?php echo $ct['btn_reopen']; ?>
                                        </a>
                                    <?php endif; ?>

                                    <a href="manage_jobs.php?delete=<?php echo $job['job_id']; ?>"
                                       class="btn btn-danger btn-sm mb-1"
                                       onclick="return confirm('<?php echo addslashes($ct['confirm_delete']); ?>')">
                                        <?php echo $ct['btn_delete']; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        <?php else: ?>

            <div class="alert alert-warning text-center mb-0">
                <h5 class="mb-2"><?php echo $ct['no_jobs_title']; ?></h5>
                <p class="mb-3"><?php echo $ct['no_jobs_desc']; ?></p>
                <a href="post_job.php" class="btn btn-success"><?php echo $ct['no_jobs_btn']; ?></a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>