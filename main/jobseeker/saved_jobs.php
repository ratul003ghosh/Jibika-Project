<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];

$message = "";
$message_type = "";

// REMOVE SAVED JOB
if (isset($_GET['remove'])) {

    $job_id = intval($_GET['remove']);

    $conn->query("
        DELETE FROM saved_jobs
        WHERE user_id='$user_id'
        AND job_id='$job_id'
    ");

    $message = "Saved job removed successfully.";
    $message_type = "info";
}

// APPLY JOB
if (isset($_GET['apply'])) {

    $job_id = intval($_GET['apply']);

    $check_job = $conn->query("
        SELECT status, application_deadline
        FROM jobs
        WHERE job_id='$job_id'
        LIMIT 1
    ");

    if ($check_job && $check_job->num_rows > 0) {

        $job_data = $check_job->fetch_assoc();

        $deadline_over = (
            !empty($job_data['application_deadline']) &&
            $job_data['application_deadline'] < date('Y-m-d')
        );

        if (($job_data['status'] ?? 'active') == 'closed') {

            $message = "This job is closed.";
            $message_type = "warning";

        } elseif ($deadline_over) {

            $message = "Application deadline is over.";
            $message_type = "warning";

        } else {

            $check_applied = $conn->query("
                SELECT *
                FROM applications
                WHERE job_id='$job_id'
                AND user_id='$user_id'
            ");

            if ($check_applied && $check_applied->num_rows > 0) {

                $message = "You already applied for this job.";
                $message_type = "warning";

            } else {

                $apply_sql = "
                    INSERT INTO applications (job_id, user_id)
                    VALUES ('$job_id', '$user_id')
                ";

                if ($conn->query($apply_sql)) {

                    $message = "Job applied successfully!";
                    $message_type = "success";

                } else {

                    $message = "Error applying job.";
                    $message_type = "danger";
                }
            }
        }
    }
}

$sql = "
    SELECT 
        jobs.*,

        d.district_name,
        up.upazila_name,
        w.ward_name,

        users.full_name AS company_name,

        saved_jobs.saved_at AS saved_at

    FROM saved_jobs

    JOIN jobs
        ON saved_jobs.job_id = jobs.job_id

    LEFT JOIN districts d
        ON jobs.district_id = d.district_id

    LEFT JOIN upazilas up
        ON jobs.upazila_id = up.upazila_id

    LEFT JOIN wards w
        ON jobs.ward_id = w.ward_id

    LEFT JOIN users
        ON jobs.employer_id = users.user_id

    WHERE saved_jobs.user_id='$user_id'

    ORDER BY saved_jobs.id DESC
";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

            <div>

                <h2 class="mb-1">
                    My Saved Jobs
                </h2>

                <p class="text-muted mb-0">
                    View and manage your bookmarked jobs.
                </p>

            </div>

            <a href="dashboard.php"
               class="btn btn-secondary">

               Back to Dashboard

            </a>

        </div>

        <?php if ($message != ""): ?>

            <div class="alert alert-<?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>

            <div class="row">

                <?php while ($job = $result->fetch_assoc()): ?>

                    <?php
                    $job_id = $job['job_id'];

                    $check_applied = $conn->query("
                        SELECT *
                        FROM applications
                        WHERE user_id='$user_id'
                        AND job_id='$job_id'
                    ");

                    $already_applied = (
                        $check_applied &&
                        $check_applied->num_rows > 0
                    );

                    $deadline = $job['application_deadline'] ?? '';

                    $deadline_over = (
                        !empty($deadline) &&
                        $deadline < date('Y-m-d')
                    );
                    ?>

                    <div class="col-md-6 mb-4">

                        <div class="card h-100 shadow-sm border-0">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-start mb-2">

                                    <h4 class="mb-0">
                                        <?php echo htmlspecialchars($job['title']); ?>
                                    </h4>

                                    <?php if ($deadline_over): ?>

                                        <span class="badge bg-danger">
                                            Deadline Over
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <p class="text-muted mb-2">

                                    <?php echo htmlspecialchars($job['company_name'] ?? 'Unknown Employer'); ?>

                                </p>

                                <div class="mb-3">

                                    <span class="badge bg-primary me-1">

                                        <?php echo htmlspecialchars($job['job_category'] ?? 'General'); ?>

                                    </span>

                                    <span class="badge bg-info text-dark me-1">

                                        <?php echo htmlspecialchars($job['job_type'] ?? 'N/A'); ?>

                                    </span>

                                </div>

                                <p class="mb-2">

                                    <strong>Area:</strong>

                                    <?php echo htmlspecialchars($job['district_name'] ?? 'N/A'); ?>

                                    /

                                    <?php echo htmlspecialchars($job['upazila_name'] ?? 'N/A'); ?>

                                </p>

                                <p class="mb-2">

                                    <strong>Salary:</strong>

                                    <?php echo htmlspecialchars($job['salary_type'] ?? 'Negotiable'); ?>

                                    -

                                    <?php echo !empty($job['salary']) ? htmlspecialchars($job['salary']) : 'Negotiable'; ?>

                                </p>

                                <p class="mb-3">

                                    <strong>Saved At:</strong>

                                    <?php echo htmlspecialchars($job['saved_at']); ?>

                                </p>

                                <div class="d-flex flex-wrap gap-2">

                                    <?php if ($already_applied): ?>

                                        <button class="btn btn-outline-secondary" disabled>

                                            Already Applied

                                        </button>

                                    <?php elseif ($deadline_over): ?>

                                        <button class="btn btn-outline-danger" disabled>

                                            Deadline Over

                                        </button>

                                    <?php else: ?>

                                        <a href="saved_jobs.php?apply=<?php echo $job['job_id']; ?>"
                                           class="btn btn-success"
                                           onclick="return confirm('Apply for this job?')">

                                            Apply Now

                                        </a>

                                    <?php endif; ?>

                                    <a href="saved_jobs.php?remove=<?php echo $job['job_id']; ?>"
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Remove saved job?')">

                                        Remove

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="alert alert-warning text-center mb-0">

                <h5 class="mb-2">
                    No Saved Jobs Yet
                </h5>

                <p class="mb-3">
                    Save jobs to view them later.
                </p>

                <a href="jobs.php"
                   class="btn btn-primary">

                    Browse Jobs

                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php include('../includes/footer.php'); ?>