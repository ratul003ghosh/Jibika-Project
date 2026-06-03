<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$usText = [
    'bn' => [
        'title' => 'কর্মসংস্থানের অবস্থা ট্র্যাকিং',
        'subtitle' => 'চাকরিপ্রার্থীদের কর্মসংস্থানের যাত্রা পর্যবেক্ষণ ও আপডেট করুন।',
        'back_btn' => 'ফিরে যান',
        'msg_success' => 'কর্মসংস্থানের অবস্থা সফলভাবে আপডেট করা হয়েছে!',
        'msg_error' => 'ত্রুটি: ',
        'msg_invalid' => 'অবৈধ অবস্থা নির্বাচন করা হয়েছে।',
        'seeker_info' => 'চাকরিপ্রার্থীর তথ্য',
        'label_name' => 'নাম:',
        'label_email' => 'ইমেইল:',
        'label_status' => 'বর্তমান অবস্থা:',
        'label_updated' => 'সর্বশেষ আপডেট:',
        'select_new_status' => 'নতুন অবস্থা নির্বাচন করুন',
        'admin_remarks' => 'অ্যাডমিন মন্তব্য',
        'placeholder_remarks' => 'অবস্থা আপডেটের নোট লিখুন...',
        'update_btn' => 'কর্মসংস্থানের অবস্থা আপডেট করুন',
        'timeline' => 'কর্মসংস্থানের যাত্রার টাইমলাইন',
        'th_serial' => '#',
        'th_old_status' => 'পূর্ববর্তী অবস্থা',
        'th_new_status' => 'নতুন অবস্থা',
        'th_remarks' => 'মন্তব্য',
        'th_date' => 'তারিখ',
        'no_history' => 'এখনও কোনো কর্মসংস্থানের ইতিহাস পাওয়া যায়নি।',
        'unemployed' => 'বেকার',
        'employed' => 'নিযুক্ত',
        'training' => 'প্রশিক্ষণরত',
        'self_employed' => 'স্বনির্ভর',
        'na' => 'প্রযোজ্য নয়',
    ],
    'en' => [
        'title' => 'Employment Status Tracking',
        'subtitle' => 'Monitor and update employment journey of job seekers.',
        'back_btn' => 'Back',
        'msg_success' => 'Employment status updated successfully!',
        'msg_error' => 'Error: ',
        'msg_invalid' => 'Invalid status selected.',
        'seeker_info' => 'Job Seeker Information',
        'label_name' => 'Name:',
        'label_email' => 'Email:',
        'label_status' => 'Current Status:',
        'label_updated' => 'Last Updated:',
        'select_new_status' => 'Select New Status',
        'admin_remarks' => 'Admin Remarks',
        'placeholder_remarks' => 'Write status update notes...',
        'update_btn' => 'Update Employment Status',
        'timeline' => 'Employment Journey Timeline',
        'th_serial' => '#',
        'th_old_status' => 'Old Status',
        'th_new_status' => 'New Status',
        'th_remarks' => 'Remarks',
        'th_date' => 'Date',
        'no_history' => 'No employment history found yet.',
        'unemployed' => 'Unemployed',
        'employed' => 'Employed',
        'training' => 'Training',
        'self_employed' => 'Self Employed',
        'na' => 'N/A',
    ]
];
$ct = $usText[$lang];

if(!isset($_GET['email']) || empty($_GET['email'])){
    header("Location: unemployed_details.php");
    exit();
}

$email = $conn->real_escape_string($_GET['email']);
$message = "";

$user_sql = "
    SELECT 
        u.user_id,
        u.full_name,
        u.email,
        es.current_status,
        es.remarks,
        es.updated_at
    FROM users u
    LEFT JOIN employment_status es 
        ON u.user_id = es.user_id
    WHERE u.email = '$email'
    AND u.role = 'job_seeker'
    LIMIT 1
";

$user_result = $conn->query($user_sql);

if(!$user_result || $user_result->num_rows == 0){
    header("Location: unemployed_details.php");
    exit();
}

$user = $user_result->fetch_assoc();

$user_id = $user['user_id'];

$status_history = $conn->query("
    SELECT *
    FROM employment_status_logs
    WHERE user_id='$user_id'
    ORDER BY log_id DESC
");

if(isset($_POST['update_status'])){

    $new_status = trim($_POST['current_status']);
    $remarks = trim($_POST['remarks']);

    $allowed_statuses = [
        'unemployed',
        'employed',
        'training',
        'self_employed'
    ];

    if(in_array($new_status, $allowed_statuses)){

        $old_status = $user['current_status'];

        // Update current status
        $update_sql = "
            UPDATE employment_status
            SET 
                current_status='$new_status',
                remarks='$remarks',
                updated_at=NOW()
            WHERE user_id='$user_id'
        ";

        if($conn->query($update_sql)){

            // Save status history
            $log_sql = "
                INSERT INTO employment_status_logs
                (
                    user_id,
                    old_status,
                    new_status,
                    remarks,
                    updated_by
                )
                VALUES
                (
                    '$user_id',
                    '$old_status',
                    '$new_status',
                    '$remarks',
                    '{$_SESSION['user_id']}'
                )
            ";

            $conn->query($log_sql);

            // Activity log
            $activity_sql = "
                INSERT INTO activity_logs
                (
                    user_id,
                    action,
                    description,
                    ip_address
                )
                VALUES
                (
                    '{$_SESSION['user_id']}',
                    'Employment Status Updated',
                    'Updated {$user['full_name']} status from $old_status to $new_status',
                    '{$_SERVER['REMOTE_ADDR']}'
                )
            ";

            $conn->query($activity_sql);

            $message = $ct['msg_success'];

            // Refresh user data
            $user_result = $conn->query($user_sql);
            $user = $user_result->fetch_assoc();

            // Refresh history
            $status_history = $conn->query("
                SELECT *
                FROM employment_status_logs
                WHERE user_id='$user_id'
                ORDER BY log_id DESC
            ");

        } else {

            $message = $ct['msg_error'] . $conn->error;
        }

    } else {

        $message = $ct['msg_invalid'];
    }
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <div>
            <h2 class="mb-1"><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0">
                <?php echo $ct['subtitle']; ?>
            </p>
        </div>

        <a href="unemployed_details.php" class="btn btn-secondary">
            <?php echo $ct['back_btn']; ?>
        </a>

    </div>

    <?php if($message != ""): ?>

        <div class="alert alert-info shadow-sm">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-5">

            <div class="card shadow border-0 p-4 h-100">

                <h4 class="mb-4"><?php echo $ct['seeker_info']; ?></h4>

                <p>
                    <strong><?php echo $ct['label_name']; ?></strong>
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </p>

                <p>
                    <strong><?php echo $ct['label_email']; ?></strong>
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>

                <p>
                    <strong><?php echo $ct['label_status']; ?></strong>

                    <?php
                    $status = $user['current_status'];

                    if($status == 'employed'){
                        echo "<span class='badge bg-success'>" . $ct['employed'] . "</span>";
                    }
                    elseif($status == 'training'){
                        echo "<span class='badge bg-warning text-dark'>" . $ct['training'] . "</span>";
                    }
                    elseif($status == 'self_employed'){
                        echo "<span class='badge bg-info text-dark'>" . $ct['self_employed'] . "</span>";
                    }
                    else{
                        echo "<span class='badge bg-danger'>" . $ct['unemployed'] . "</span>";
                    }
                    ?>
                </p>

                <p>
                    <strong><?php echo $ct['label_updated']; ?></strong>
                    <?php echo htmlspecialchars($user['updated_at'] ?? $ct['na']); ?>
                </p>

                <hr>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            <?php echo $ct['select_new_status']; ?>
                        </label>

                        <select name="current_status"
                                class="form-select"
                                required>

                            <option value="unemployed"
                                <?php echo ($status == 'unemployed') ? 'selected' : ''; ?>>
                                <?php echo $ct['unemployed']; ?>
                            </option>

                            <option value="training"
                                <?php echo ($status == 'training') ? 'selected' : ''; ?>>
                                <?php echo $ct['training']; ?>
                            </option>

                            <option value="employed"
                                <?php echo ($status == 'employed') ? 'selected' : ''; ?>>
                                <?php echo $ct['employed']; ?>
                            </option>

                            <option value="self_employed"
                                <?php echo ($status == 'self_employed') ? 'selected' : ''; ?>>
                                <?php echo $ct['self_employed']; ?>
                            </option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            <?php echo $ct['admin_remarks']; ?>
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="5"
                                  placeholder="<?php echo $ct['placeholder_remarks']; ?>"><?php echo htmlspecialchars($user['remarks'] ?? ''); ?></textarea>

                    </div>

                    <button type="submit"
                            name="update_status"
                            class="btn btn-success w-100">

                        <?php echo $ct['update_btn']; ?>

                    </button>

                </form>

            </div>

        </div>

        <div class="col-lg-7">

            <div class="card shadow border-0 p-4 h-100">

                <h4 class="mb-4">
                    <?php echo $ct['timeline']; ?>
                </h4>

                <?php if($status_history && $status_history->num_rows > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th><?php echo $ct['th_serial']; ?></th>
                                    <th><?php echo $ct['th_old_status']; ?></th>
                                    <th><?php echo $ct['th_new_status']; ?></th>
                                    <th><?php echo $ct['th_remarks']; ?></th>
                                    <th><?php echo $ct['th_date']; ?></th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php $count = 1; ?>

                                <?php while($row = $status_history->fetch_assoc()): ?>

                                    <tr>

                                        <td><?php echo $count++; ?></td>

                                        <td>
                                            <?php echo htmlspecialchars($ct[$row['old_status']] ?? $row['old_status'] ?? $ct['na']); ?>
                                        </td>

                                        <td>

                                            <?php
                                            $new = $row['new_status'];

                                            if($new == 'employed'){
                                                echo "<span class='badge bg-success'>" . $ct['employed'] . "</span>";
                                            }
                                            elseif($new == 'training'){
                                                echo "<span class='badge bg-warning text-dark'>" . $ct['training'] . "</span>";
                                            }
                                            elseif($new == 'self_employed'){
                                                echo "<span class='badge bg-info text-dark'>" . $ct['self_employed'] . "</span>";
                                            }
                                            else{
                                                echo "<span class='badge bg-danger'>" . $ct['unemployed'] . "</span>";
                                            }
                                            ?>

                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['remarks'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['created_at']); ?>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="alert alert-warning mb-0">
                        <?php echo $ct['no_history']; ?>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>