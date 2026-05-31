<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

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

            $message = "Employment status updated successfully!";

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

            $message = "Error: " . $conn->error;
        }

    } else {

        $message = "Invalid status selected.";
    }
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <div>
            <h2 class="mb-1">Employment Status Tracking</h2>
            <p class="text-muted mb-0">
                Monitor and update employment journey of job seekers.
            </p>
        </div>

        <a href="unemployed_details.php" class="btn btn-secondary">
            Back
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

                <h4 class="mb-4">Job Seeker Information</h4>

                <p>
                    <strong>Name:</strong>
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>

                <p>
                    <strong>Current Status:</strong>

                    <?php
                    $status = $user['current_status'];

                    if($status == 'employed'){
                        echo "<span class='badge bg-success'>Employed</span>";
                    }
                    elseif($status == 'training'){
                        echo "<span class='badge bg-warning text-dark'>Training</span>";
                    }
                    elseif($status == 'self_employed'){
                        echo "<span class='badge bg-info text-dark'>Self Employed</span>";
                    }
                    else{
                        echo "<span class='badge bg-danger'>Unemployed</span>";
                    }
                    ?>
                </p>

                <p>
                    <strong>Last Updated:</strong>
                    <?php echo htmlspecialchars($user['updated_at'] ?? 'N/A'); ?>
                </p>

                <hr>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Select New Status
                        </label>

                        <select name="current_status"
                                class="form-select"
                                required>

                            <option value="unemployed"
                                <?php echo ($status == 'unemployed') ? 'selected' : ''; ?>>
                                Unemployed
                            </option>

                            <option value="training"
                                <?php echo ($status == 'training') ? 'selected' : ''; ?>>
                                Training
                            </option>

                            <option value="employed"
                                <?php echo ($status == 'employed') ? 'selected' : ''; ?>>
                                Employed
                            </option>

                            <option value="self_employed"
                                <?php echo ($status == 'self_employed') ? 'selected' : ''; ?>>
                                Self Employed
                            </option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Admin Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="5"
                                  placeholder="Write status update notes..."><?php echo htmlspecialchars($user['remarks'] ?? ''); ?></textarea>

                    </div>

                    <button type="submit"
                            name="update_status"
                            class="btn btn-success w-100">

                        Update Employment Status

                    </button>

                </form>

            </div>

        </div>

        <div class="col-lg-7">

            <div class="card shadow border-0 p-4 h-100">

                <h4 class="mb-4">
                    Employment Journey Timeline
                </h4>

                <?php if($status_history && $status_history->num_rows > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>#</th>
                                    <th>Old Status</th>
                                    <th>New Status</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php $count = 1; ?>

                                <?php while($row = $status_history->fetch_assoc()): ?>

                                    <tr>

                                        <td><?php echo $count++; ?></td>

                                        <td>
                                            <?php echo htmlspecialchars($row['old_status'] ?? 'N/A'); ?>
                                        </td>

                                        <td>

                                            <?php
                                            $new = $row['new_status'];

                                            if($new == 'employed'){
                                                echo "<span class='badge bg-success'>Employed</span>";
                                            }
                                            elseif($new == 'training'){
                                                echo "<span class='badge bg-warning text-dark'>Training</span>";
                                            }
                                            elseif($new == 'self_employed'){
                                                echo "<span class='badge bg-info text-dark'>Self Employed</span>";
                                            }
                                            else{
                                                echo "<span class='badge bg-danger'>Unemployed</span>";
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
                        No employment history found yet.
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>