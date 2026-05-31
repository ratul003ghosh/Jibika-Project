<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$employer_id = $_SESSION['user_id'];
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

                $message = "Application status updated successfully!";
            } else {
                $message = "Error: " . $conn->error;
            }
        } else {
            $message = "Application not found or permission denied.";
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
                <h2 class="mb-1">Applicants List</h2>
                <p class="text-muted mb-0">Review applicants. Accepted applicants are automatically marked as employed.</p>
            </div>

            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
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
                            <th>#</th>
                            <th>Job Information</th>
                            <th>Applicant Information</th>
                            <th>Skills & Education</th>
                            <th>Application Status</th>
                            <th>Applied At</th>
                            <th>Action</th>
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
                                <td><?php echo $count++; ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars($row['job_title']); ?></strong>
                                    <br>

                                    <span class="badge bg-primary me-1">
                                        <?php echo htmlspecialchars($row['job_category'] ?? 'General'); ?>
                                    </span>

                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($row['job_type'] ?? 'N/A'); ?>
                                    </span>

                                    <br><br>

                                    <small>
                                        <strong>Salary:</strong>
                                        <?php echo htmlspecialchars($row['salary_type'] ?? 'Negotiable'); ?>
                                        -
                                        <?php echo !empty($row['salary']) ? htmlspecialchars($row['salary']) : 'Negotiable'; ?>
                                    </small>

                                    <br>

                                    <small>
                                        <strong>Deadline:</strong>
                                        <?php echo htmlspecialchars($row['application_deadline'] ?? 'N/A'); ?>
                                    </small>
                                </td>

                                <td>
                                    <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
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
                                        <strong>Education:</strong><br>
                                        <?php echo !empty($row['education']) ? htmlspecialchars($row['education']) : 'Not specified'; ?>
                                    </small>

                                    <hr class="my-2">

                                    <small>
                                        <strong>Skills:</strong><br>
                                        <?php
                                        if (!empty($skills)) {
                                            foreach ($skills as $skill) {
                                                echo "<span class='badge bg-secondary me-1 mb-1'>" . htmlspecialchars($skill) . "</span>";
                                            }
                                        } else {
                                            echo "Not specified";
                                        }
                                        ?>
                                    </small>
                                </td>

                                <td>
                                    <?php
                                    $status = $row['status'];

                                    if ($status == 'Accepted') {
                                        echo "<span class='badge bg-success'>Accepted</span>";
                                        echo "<br><small class='text-success'>Employment updated</small>";
                                    } elseif ($status == 'Rejected') {
                                        echo "<span class='badge bg-danger'>Rejected</span>";
                                    } else {
                                        echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                    }
                                    ?>
                                </td>

                                <td><?php echo htmlspecialchars($row['applied_at']); ?></td>

                                <td>
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <a href="applicants.php?action=accept&application_id=<?php echo $row['application_id']; ?>"
                                           class="btn btn-success btn-sm me-1 mb-1"
                                           onclick="return confirm('Accept this applicant? This will mark the user as employed.')">
                                            Accept
                                        </a>

                                        <a href="applicants.php?action=reject&application_id=<?php echo $row['application_id']; ?>"
                                           class="btn btn-danger btn-sm mb-1"
                                           onclick="return confirm('Reject this applicant?')">
                                            Reject
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Decision Done</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="alert alert-warning mb-0">
                No applicants found yet.
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>