<?php
session_start();
require_once("../assets/config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Job Seeker';

$profile_check = $conn->query("SELECT profile_id FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");

if (!$profile_check || $profile_check->num_rows == 0) {
    header("Location: profile.php");
    exit();
}

$total_jobs = 0;
$matched_jobs = 0;
$applied_jobs = 0;
$saved_jobs = 0;

$employment_status = 'unemployed';
$employment_remarks = '';

$status_query = $conn->query("
    SELECT current_status, remarks
    FROM employment_status
    WHERE user_id='$user_id'
    LIMIT 1
");

if ($status_query && $status_query->num_rows > 0) {
    $status_data = $status_query->fetch_assoc();
    $employment_status = $status_data['current_status'] ?? 'unemployed';
    $employment_remarks = $status_data['remarks'] ?? '';
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE status='active'");
if ($q) $total_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE user_id='$user_id'");
if ($q) $applied_jobs = $q->fetch_assoc()['total'] ?? 0;

$q = $conn->query("SELECT COUNT(*) AS total FROM saved_jobs WHERE user_id='$user_id'");
if ($q) $saved_jobs = $q->fetch_assoc()['total'] ?? 0;

$profile_result = $conn->query("
    SELECT district_id 
    FROM job_seeker_profiles 
    WHERE user_id='$user_id' 
    LIMIT 1
");

$profile = ($profile_result && $profile_result->num_rows > 0) ? $profile_result->fetch_assoc() : null;
$user_district_id = $profile['district_id'] ?? 0;

$user_skills_result = $conn->query("
    SELECT skill_name 
    FROM skills 
    WHERE user_id='$user_id'
");

$skill_conditions = [];

if ($user_skills_result && $user_skills_result->num_rows > 0) {
    while ($skill_row = $user_skills_result->fetch_assoc()) {
        $safe_skill = $conn->real_escape_string($skill_row['skill_name']);

        $skill_conditions[] = "
            (
                jobs.title LIKE '%$safe_skill%' 
                OR jobs.description LIKE '%$safe_skill%'
                OR jobs.job_category LIKE '%$safe_skill%'
            )
        ";
    }
}

$matched_condition = "jobs.status='active'";

if (!empty($skill_conditions)) {
    $matched_condition .= " AND (" . implode(" OR ", $skill_conditions) . ")";
}

if (!empty($user_district_id)) {
    $matched_condition .= " OR (jobs.status='active' AND jobs.district_id='$user_district_id')";
}

$q = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE $matched_condition");
if ($q) $matched_jobs = $q->fetch_assoc()['total'] ?? 0;

$recommended_jobs = $conn->query("
    SELECT jobs.*, users.full_name AS company_name
    FROM jobs
    LEFT JOIN users ON jobs.employer_id = users.user_id
    WHERE ($matched_condition)
    AND jobs.job_id NOT IN (
        SELECT job_id
        FROM applications
        WHERE user_id='$user_id'
    )
    AND (
        jobs.application_deadline IS NULL
        OR jobs.application_deadline >= CURDATE()
    )
    ORDER BY jobs.job_id DESC
    LIMIT 3
");

$recent_applications = $conn->query("
    SELECT 
        applications.status,
        applications.applied_at,
        jobs.title,
        users.full_name AS company_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    LEFT JOIN users ON jobs.employer_id = users.user_id
    WHERE applications.user_id='$user_id'
    ORDER BY applications.application_id DESC
    LIMIT 5
");

$notifications = [
    "Complete your profile and skills to improve job matching.",
    "Browse latest area-based jobs from your dashboard.",
    "Use Partner Finder if you want to start a small business."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard</title>

    <link rel="stylesheet" href="../assets/css/job_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">
            <h2>JIBIKA</h2>
            <p>Job Seeker Panel</p>
        </div>

        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="jobs.php"><i class="fa-solid fa-briefcase"></i> Browse Jobs</a></li>
            <li><a href="saved_jobs.php"><i class="fa-solid fa-bookmark"></i> Saved Jobs</a></li>
            <li><a href="my_applications.php"><i class="fa-solid fa-file-circle-check"></i> My Applications</a></li>
            <li><a href="skills.php"><i class="fa-solid fa-screwdriver-wrench"></i> My Skills</a></li>
            <li><a href="partner_finder.php"><i class="fa-solid fa-handshake"></i> Partner Finder</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="#"><i class="fa-solid fa-bell"></i> Notifications</a></li>
            <li><a href="/auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1>Welcome Back, <?php echo htmlspecialchars($full_name); ?></h1>
                <p>Find suitable jobs based on your skills, location, and experience.</p>
            </div>

            <div class="topbar-actions">
                <a href="jobs.php" class="btn primary-btn">Browse Jobs</a>
                <a href="profile.php" class="btn secondary-btn">Update Profile</a>
            </div>
        </div>

        <section class="summary-cards">
            <div class="card">
                <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
                <div>
                    <h3><?php echo $total_jobs; ?></h3>
                    <p>Total Available Jobs</p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-star"></i></div>
                <div>
                    <h3><?php echo $matched_jobs; ?></h3>
                    <p>Matched Jobs</p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-paper-plane"></i></div>
                <div>
                    <h3><?php echo $applied_jobs; ?></h3>
                    <p>Applied Jobs</p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-bookmark"></i></div>
                <div>
                    <h3><?php echo $saved_jobs; ?></h3>
                    <p>Saved Jobs</p>
                </div>
            </div>

            <div class="card">
                <div class="icon"><i class="fa-solid fa-user-check"></i></div>
                <div>
                    <?php
                    if ($employment_status == 'employed') {
                        echo "<h3 style='color:green;'>Employed</h3>";
                    } elseif ($employment_status == 'training') {
                        echo "<h3 style='color:orange;'>Training</h3>";
                    } elseif ($employment_status == 'self_employed') {
                        echo "<h3 style='color:deepskyblue;'>Self Employed</h3>";
                    } else {
                        echo "<h3 style='color:red;'>Unemployed</h3>";
                    }
                    ?>
                    <p>Current Employment Status</p>
                </div>
            </div>
        </section>

        <section class="search-filter-section">
            <form class="search-filter-form" method="GET" action="jobs.php">
                <input type="text" name="search" placeholder="Search by job title, company, skill...">

                <select name="district_id">
                    <option value="">Select District</option>
                    <?php
                    $districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
                    if ($districts && $districts->num_rows > 0) {
                        while ($district = $districts->fetch_assoc()) {
                            echo "<option value='" . $district['district_id'] . "'>" . htmlspecialchars($district['district_name']) . "</option>";
                        }
                    }
                    ?>
                </select>
                
                <select name="job_type">
                    <option value="">Any Job Type</option>
                    <option value="Part-time (Student)">Part-time (Student)</option>
                    <option value="Day Labor">Day Labor</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Contract">Contract</option>
                </select>

                <button type="submit" class="btn primary-btn">Search</button>
            </form>
        </section>

        <div class="dashboard-grid">

            <section class="panel large-panel">
                <div class="panel-header">
                    <h2>Recommended Jobs</h2>
                    <a href="jobs.php">View All</a>
                </div>

                <div class="job-cards">
                    <?php if ($recommended_jobs && $recommended_jobs->num_rows > 0): ?>
                        <?php while ($job = $recommended_jobs->fetch_assoc()): ?>

                            <?php
                            $match_reason = "Recommended";
                            $job_district_id = $job['district_id'] ?? 0;

                            if (!empty($user_district_id) && $job_district_id == $user_district_id) {
                                $match_reason = "Area Match";
                            }

                            $user_skills_result2 = $conn->query("
                                SELECT skill_name
                                FROM skills
                                WHERE user_id='$user_id'
                            ");

                            if ($user_skills_result2 && $user_skills_result2->num_rows > 0) {
                                while ($skill2 = $user_skills_result2->fetch_assoc()) {
                                    $skill_name2 = strtolower(trim($skill2['skill_name']));

                                    if (
                                        $skill_name2 != "" &&
                                        (
                                            strpos(strtolower($job['title'] ?? ''), $skill_name2) !== false
                                            || strpos(strtolower($job['description'] ?? ''), $skill_name2) !== false
                                            || strpos(strtolower($job['job_category'] ?? ''), $skill_name2) !== false
                                        )
                                    ) {
                                        $match_reason = "Skill Match";
                                        break;
                                    }
                                }
                            }
                            ?>

                            <div class="job-card">
                                <div class="job-card-top">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="match-badge"><?php echo $match_reason; ?></span>
                                </div>

                                <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name'] ?? 'N/A'); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location'] ?? 'N/A'); ?></p>
                                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary'] ?? 'Negotiable'); ?></p>

                                <div class="job-card-actions">
                                    <a href="jobs.php" class="btn secondary-btn">View Details</a>
                                    <a href="jobs.php?apply=<?php echo $job['job_id']; ?>" 
                                       class="btn primary-btn" 
                                       onclick="return confirm('Apply for this job?')">
                                        Apply Now
                                    </a>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="job-card">
                            <h3>No recommended jobs found</h3>
                            <p>Update your profile and skills to get better recommendations.</p>
                            <a href="profile.php" class="btn primary-btn">Update Profile</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="panel small-panel">
                <div class="panel-header">
                    <h2>Notifications</h2>
                    <a href="#">See All</a>
                </div>

                <ul class="notification-list">
                    <?php foreach ($notifications as $note): ?>
                        <li><i class="fa-solid fa-bell"></i> <?php echo htmlspecialchars($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="panel full-panel">
                <div class="panel-header">
                    <h2>Recent Applications</h2>
                    <a href="my_applications.php">View All</a>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>
                                <?php while ($app = $recent_applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['title']); ?></td>
                                        <td><?php echo htmlspecialchars($app['company_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                                        <td>
                                            <span class="status <?php echo strtolower($app['status']); ?>">
                                                <?php echo htmlspecialchars($app['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No recent applications found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </main>
</div>

</body>
</html>