<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$employer_id = $_SESSION['user_id'];
$message = "";

// Accept / Reject action
if(isset($_GET['action']) && isset($_GET['application_id'])){
    $action = $_GET['action'];
    $application_id = intval($_GET['application_id']);

    if($action == 'accept'){
        $status = 'Accepted';
    } elseif($action == 'reject'){
        $status = 'Rejected';
    } else {
        $status = '';
    }

    if($status != ""){
        $update_sql = "UPDATE applications 
                       JOIN jobs ON applications.job_id = jobs.job_id
                       SET applications.status = '$status'
                       WHERE applications.application_id = '$application_id'
                       AND jobs.employer_id = '$employer_id'";

        if($conn->query($update_sql)){
            $message = "Application status updated successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$sql = "SELECT 
            applications.application_id,
            applications.status,
            applications.applied_at,
            jobs.title AS job_title,
            users.full_name,
            users.email
        FROM applications
        JOIN jobs ON applications.job_id = jobs.job_id
        JOIN users ON applications.user_id = users.user_id
        WHERE jobs.employer_id = '$employer_id'
        ORDER BY applications.application_id DESC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">Applicants List</h2>

        <?php if($message != ""): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Job Title</th>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Applied At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        if($status == 'Accepted'){
                                            echo "<span class='badge bg-success'>Accepted</span>";
                                        } elseif($status == 'Rejected'){
                                            echo "<span class='badge bg-danger'>Rejected</span>";
                                        } else {
                                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        }
                                    ?>
                                </td>
                                <td><?php echo $row['applied_at']; ?></td>
                                <td>
                                    <a href="applicants.php?action=accept&application_id=<?php echo $row['application_id']; ?>" 
                                       class="btn btn-success btn-sm me-1"
                                       onclick="return confirm('Accept this applicant?')">
                                       Accept
                                    </a>

                                    <a href="applicants.php?action=reject&application_id=<?php echo $row['application_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Reject this applicant?')">
                                       Reject
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mb-0">No applicants found yet.</div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>