<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

if(!isset($_GET['email']) || empty($_GET['email'])){
    header("Location: unemployed_details.php");
    exit();
}

$email = $conn->real_escape_string($_GET['email']);
$message = "";

// Fetch user + current status
$user_sql = "
    SELECT 
        u.user_id,
        u.full_name,
        u.email,
        es.current_status,
        es.remarks
    FROM users u
    LEFT JOIN employment_status es ON u.user_id = es.user_id
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

if(isset($_POST['update_status'])){
    $new_status = $_POST['current_status'];
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $user_id = $user['user_id'];

    $allowed_statuses = ['unemployed', 'employed', 'training', 'self_employed'];

    if(in_array($new_status, $allowed_statuses)){
        $update_sql = "
            UPDATE employment_status
            SET current_status = '$new_status',
                remarks = '$remarks'
            WHERE user_id = '$user_id'
        ";

        if($conn->query($update_sql)){
            $message = "Employment status updated successfully!";

            // refresh current data
            $user_result = $conn->query($user_sql);
            $user = $user_result->fetch_assoc();
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
            <h2 class="mb-1">Update Employment Status</h2>
            <p class="text-muted mb-0">Modify the employment condition of a job seeker.</p>
        </div>
        <a href="unemployed_details.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow p-4">
        <?php if($message != ""): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <h5 class="mb-2">Job Seeker Information</h5>
            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="mb-0"><strong>Current Status:</strong> <?php echo htmlspecialchars($user['current_status']); ?></p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select New Status</label>
                <select name="current_status" class="form-control" required>
                    <option value="unemployed" <?php echo ($user['current_status'] == 'unemployed') ? 'selected' : ''; ?>>Unemployed</option>
                    <option value="employed" <?php echo ($user['current_status'] == 'employed') ? 'selected' : ''; ?>>Employed</option>
                    <option value="training" <?php echo ($user['current_status'] == 'training') ? 'selected' : ''; ?>>Training</option>
                    <option value="self_employed" <?php echo ($user['current_status'] == 'self_employed') ? 'selected' : ''; ?>>Self Employed</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="4" placeholder="Write notes or update reason..."><?php echo htmlspecialchars($user['remarks'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="update_status" class="btn btn-success w-100">
                Update Status
            </button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>