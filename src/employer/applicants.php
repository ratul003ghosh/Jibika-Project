<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$apText = [
    'bn' => [
        'title' => 'আবেদনকারী তালিকা',
        'subtitle' => 'আবেদনকারীদের পর্যালোচনা করুন। গৃহীত আবেদনকারীদের স্বয়ংক্রিয়ভাবে নিযুক্ত হিসেবে চিহ্নিত করা হবে।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'msg_success' => 'আবেদনকারীর অবস্থা সফলভাবে আপডেট করা হয়েছে!',
        'msg_error' => 'ত্রুটি: ',
        'msg_not_found' => 'আবেদনপত্র পাওয়া যায়নি বা অ্যাক্সেস করার অনুমতি নেই।',
        'th_serial' => '#',
        'th_job_info' => 'চাকরির তথ্য',
        'th_applicant_info' => 'আবেদনকারীর তথ্য',
        'th_skills_edu' => 'দক্ষতা ও শিক্ষা',
        'th_status' => 'আবেদনের অবস্থা',
        'th_applied_at' => 'আবেদনের সময়',
        'th_action' => 'পদক্ষেপ',
        'label_salary' => 'বেতন:',
        'label_deadline' => 'শেষ সময়সীমা:',
        'label_education' => 'শিক্ষা:',
        'label_skills' => 'দক্ষতা:',
        'status_accepted' => 'গৃহীত',
        'status_rejected' => 'প্রত্যাখ্যাত',
        'status_pending' => 'অপেক্ষমাণ',
        'status_emp_updated' => 'কর্মসংস্থান আপডেট করা হয়েছে',
        'btn_accept' => 'গ্রহণ করুন',
        'btn_reject' => 'প্রত্যাখ্যান করুন',
        'btn_done' => 'সিদ্ধান্ত সম্পন্ন',
        'confirm_accept' => 'আপনি কি এই আবেদনকারীকে গ্রহণ করতে চান? এটি ব্যবহারকারীকে কর্মসংস্থানিত হিসেবে চিহ্নিত করবে।',
        'confirm_reject' => 'আপনি কি এই আবেদনকারীকে প্রত্যাখ্যান করতে চান?',
        'no_applicants' => 'এখনও কোনো আবেদনকারী পাওয়া যায়নি।',
        'not_specified' => 'নির্দিষ্ট করা নেই',
        'negotiable' => 'আলোচনা সাপেক্ষে',
        'fixed' => 'নির্দিষ্ট',
        'range' => 'সীমা',
        'general' => 'সাধারণ',
        'na' => 'প্রযোজ্য নয়',
        // Categories
        'IT & Computer' => 'আইটি ও কম্পিউটার', 'Garments' => 'গার্মেন্টস', 'Driving' => 'ড্রাইভিং',
        'Sales & Marketing' => 'বিক্রয় ও বিপণন', 'Office Support' => 'অফিস সাপোর্ট', 'Healthcare' => 'স্বাস্থ্যসেবা',
        'Education' => 'শিক্ষা', 'Small Business' => 'ক্ষুদ্র ব্যবসা', 'Other' => 'অন্যান্য',
        // Types
        'Full-time' => 'পূর্ণকালীন', 'Part-time' => 'খণ্ডকালীন', 'Part-time (Student)' => 'খণ্ডকালীন (শিক্ষার্থী)',
        'Day Labor' => 'দৈনিক শ্রমিক', 'Internship' => 'ইন্টার্নশিপ', 'Contract' => 'চুক্তিভিত্তিক', 'Remote' => 'রিমোট',
    ],
    'en' => [
        'title' => 'Applicants List',
        'subtitle' => 'Review applicants. Accepted applicants are automatically marked as employed.',
        'back_btn' => 'Back to Dashboard',
        'msg_success' => 'Application status updated successfully!',
        'msg_error' => 'Error: ',
        'msg_not_found' => 'Application not found or permission denied.',
        'th_serial' => '#',
        'th_job_info' => 'Job Information',
        'th_applicant_info' => 'Applicant Information',
        'th_skills_edu' => 'Skills & Education',
        'th_status' => 'Application Status',
        'th_applied_at' => 'Applied At',
        'th_action' => 'Action',
        'label_salary' => 'Salary:',
        'label_deadline' => 'Deadline:',
        'label_education' => 'Education:',
        'label_skills' => 'Skills:',
        'status_accepted' => 'Accepted',
        'status_rejected' => 'Rejected',
        'status_pending' => 'Pending',
        'status_emp_updated' => 'Employment updated',
        'btn_accept' => 'Accept',
        'btn_reject' => 'Reject',
        'btn_done' => 'Decision Done',
        'confirm_accept' => 'Accept this applicant? This will mark the user as employed.',
        'confirm_reject' => 'Reject this applicant?',
        'no_applicants' => 'No applicants found yet.',
        'not_specified' => 'Not specified',
        'negotiable' => 'Negotiable',
        'fixed' => 'Fixed',
        'range' => 'Range',
        'general' => 'General',
        'na' => 'N/A',
        // Categories
        'IT & Computer' => 'IT & Computer', 'Garments' => 'Garments', 'Driving' => 'Driving',
        'Sales & Marketing' => 'Sales & Marketing', 'Office Support' => 'Office Support', 'Healthcare' => 'Healthcare',
        'Education' => 'Education', 'Small Business' => 'Small Business', 'Other' => 'Other',
        // Types
        'Full-time' => 'Full-time', 'Part-time' => 'Part-time', 'Part-time (Student)' => 'Part-time (Student)',
        'Day Labor' => 'Day Labor', 'Internship' => 'Internship', 'Contract' => 'Contract', 'Remote' => 'Remote',
    ]
];
$ct = $apText[$lang];

$message = "";

// Accept / Reject action
if (isset($_GET['action']) && isset($_GET['application_id'])) {

    $action = $_GET['action'];
    $application_id = intval($_GET['application_id']);

    if ($action == 'accept') {
        $status = 'Accepted';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    } else {
        $status = '';
    }

    if ($status != "") {

        $app_sql = "
            SELECT 
                applications.user_id,
                applications.status AS old_application_status,
                jobs.title AS job_title
            FROM applications
            JOIN jobs ON applications.job_id = jobs.job_id
            WHERE applications.application_id='$application_id'
            AND jobs.employer_id='$employer_id'
            LIMIT 1
        ";

        $app_result = $conn->query($app_sql);

        if ($app_result && $app_result->num_rows > 0) {

            $app_data = $app_result->fetch_assoc();
            $applicant_user_id = $app_data['user_id'];
            $job_title = $conn->real_escape_string($app_data['job_title']);

            $update_sql = "
                UPDATE applications 
                JOIN jobs ON applications.job_id = jobs.job_id
                SET applications.status = '$status'
                WHERE applications.application_id = '$application_id'
                AND jobs.employer_id = '$employer_id'
            ";

            if ($conn->query($update_sql)) {

                if ($status == 'Accepted') {

                    $status_check = $conn->query("
                        SELECT current_status 
                        FROM employment_status 
                        WHERE user_id='$applicant_user_id'
                        LIMIT 1
                    ");

                    if ($status_check && $status_check->num_rows > 0) {
                        $old_status = $status_check->fetch_assoc()['current_status'];

                        $conn->query("
                            UPDATE employment_status
                            SET current_status='employed',
                                remarks='Automatically marked employed after employer accepted application for $job_title',
                                updated_at=NOW()
                            WHERE user_id='$applicant_user_id'
                        ");
                    } else {
                        $old_status = 'unemployed';

                        $conn->query("
                            INSERT INTO employment_status
                            (user_id, current_status, remarks, updated_at)
                            VALUES
                            ('$applicant_user_id', 'employed', 'Automatically marked employed after employer accepted application for $job_title', NOW())
                        ");
                    }

                    $conn->query("
                        INSERT INTO employment_status_logs
                        (user_id, old_status, new_status, remarks, updated_by)
                        VALUES
                        ('$applicant_user_id', '$old_status', 'employed', 'Employer accepted application for $job_title', '$employer_id')
                    ");

                    $conn->query("
                        INSERT INTO activity_logs
                        (user_id, action, description, ip_address)
                        VALUES
                        ('$employer_id', 'Application Accepted', 'Employer accepted applicant and system updated employment status to employed', '{$_SERVER['REMOTE_ADDR']}')
                    ");
                }

                $message = $ct['msg_success'];
            } else {
                $message = $ct['msg_error'] . $conn->error;
            }
        } else {
            $message = $ct['msg_not_found'];
        }
    }
}

$sql = "SELECT 
            applications.application_id,
            applications.status,
            applications.applied_at,

            jobs.title AS job_title,
            jobs.job_category,
            jobs.job_type,
            jobs.salary,
            jobs.salary_type,
            jobs.application_deadline,

            users.user_id AS applicant_id,
            users.full_name,
            users.email,

            jsp.education,
            jsp.about

        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
        JOIN users ON applications.user_id = users.user_id
        LEFT JOIN job_seeker_profiles jsp ON applications.user_id = jsp.user_id

        WHERE jobs.employer_id = '$employer_id'
        ORDER BY applications.application_id DESC";

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

            <a href="dashboard.php" class="btn btn-secondary"><?php echo $ct['back_btn']; ?></a>
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
                            <th><?php echo $ct['th_job_info']; ?></th>
                            <th><?php echo $ct['th_applicant_info']; ?></th>
                            <th><?php echo $ct['th_skills_edu']; ?></th>
                            <th><?php echo $ct['th_status']; ?></th>
                            <th><?php echo $ct['th_applied_at']; ?></th>
                            <th><?php echo $ct['th_action']; ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $count = 1; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <?php
                            $skills_result = $conn->query("
                                SELECT skill_name 
                                FROM skills 
                                WHERE user_id='{$row['applicant_id']}'
                            ");

                            $skills = [];

                            if ($skills_result && $skills_result->num_rows > 0) {
                                while ($skill = $skills_result->fetch_assoc()) {
                                    $skills[] = $skill['skill_name'];
                                }
                            }
                            ?>

                            <tr>
                                <td><?php echo translateNumber($count++, $lang); ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars(translateJobTitle($row['job_title'] ?? '', $lang)); ?></strong>
                                    <br>

                                    <span class="badge bg-primary me-1">
                                        <?php echo htmlspecialchars($ct[$row['job_category']] ?? $row['job_category'] ?? $ct['general']); ?>
                                    </span>

                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($ct[$row['job_type']] ?? $row['job_type'] ?? $ct['na']); ?>
                                    </span>

                                    <br><br>

                                    <small>
                                        <strong><?php echo $ct['label_salary']; ?></strong>
                                        <?php echo htmlspecialchars($ct[$row['salary_type']] ?? $row['salary_type'] ?? $ct['negotiable']); ?>
                                        -
                                        <?php echo (empty($row['salary']) || strtolower($row['salary']) === 'negotiable') ? $ct['negotiable'] : '৳ ' . translateSalary($row['salary'], $lang); ?>
                                    </small>

                                    <br>

                                    <small>
                                        <strong><?php echo $ct['label_deadline']; ?></strong>
                                        <?php echo htmlspecialchars(translateDate($row['application_deadline'] ?? '', $lang) ?: $ct['na']); ?>
                                    </small>
                                </td>

                                <td>
                                    <strong><?php echo htmlspecialchars(translateEmployerName($row['full_name'] ?? '', $lang)); ?></strong>
                                    <br>
                                    <small><?php echo htmlspecialchars($row['email']); ?></small>

                                    <?php if (!empty($row['about'])): ?>
                                        <hr class="my-2">
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($row['about'], 0, 120)); ?>...
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <small>
                                        <strong><?php echo $ct['label_education']; ?></strong><br>
                                        <?php echo !empty($row['education']) ? htmlspecialchars($row['education']) : $ct['not_specified']; ?>
                                    </small>

                                    <hr class="my-2">

                                    <small>
                                        <strong><?php echo $ct['label_skills']; ?></strong><br>
                                        <?php
                                        if (!empty($skills)) {
                                            foreach ($skills as $skill) {
                                                echo "<span class='badge bg-secondary me-1 mb-1'>" . htmlspecialchars($skill) . "</span>";
                                            }
                                        } else {
                                            echo $ct['not_specified'];
                                        }
                                        ?>
                                    </small>
                                </td>

                                <td>
                                    <?php
                                    $status = $row['status'];

                                    if ($status == 'Accepted') {
                                        echo "<span class='badge bg-success'>" . $ct['status_accepted'] . "</span>";
                                        echo "<br><small class='text-success'>" . $ct['status_emp_updated'] . "</small>";
                                    } elseif ($status == 'Rejected') {
                                        echo "<span class='badge bg-danger'>" . $ct['status_rejected'] . "</span>";
                                    } else {
                                        echo "<span class='badge bg-warning text-dark'>" . $ct['status_pending'] . "</span>";
                                    }
                                    ?>
                                </td>

                                <td><?php echo htmlspecialchars(translateDate($row['applied_at'], $lang)); ?></td>

                                <td>
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <a href="applicants.php?action=accept&application_id=<?php echo $row['application_id']; ?>"
                                           class="btn btn-success btn-sm me-1 mb-1"
                                           onclick="return confirm('<?php echo addslashes($ct['confirm_accept']); ?>')">
                                            <?php echo $ct['btn_accept']; ?>
                                        </a>

                                        <a href="applicants.php?action=reject&application_id=<?php echo $row['application_id']; ?>"
                                           class="btn btn-danger btn-sm mb-1"
                                           onclick="return confirm('<?php echo addslashes($ct['confirm_reject']); ?>')">
                                            <?php echo $ct['btn_reject']; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo $ct['btn_done']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="alert alert-warning mb-0">
                <?php echo $ct['no_applicants']; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>