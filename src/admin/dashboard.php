<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

function safeValue($array, $key, $default = '') {
    return (is_array($array) && isset($array[$key])) ? $array[$key] : $default;
}

function safeQuery($conn, $sql) {
    if (!$conn) {
        return false;
    }
    return $conn->query($sql);
}

function tableExists($conn, $table) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
    if ($table === '') {
        return false;
    }
    $result = safeQuery($conn, "SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    return ($result && $result->num_rows > 0);
}

function columnExists($conn, $table, $column) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
    if ($table === '' || $column === '' || !tableExists($conn, $table)) {
        return false;
    }
    $result = safeQuery(
        $conn,
        "SHOW COLUMNS FROM `$table` LIKE '" . $conn->real_escape_string($column) . "'"
    );
    return ($result && $result->num_rows > 0);
}

function safeCount($conn, $table, $condition = '') {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
    if ($table === '' || !tableExists($conn, $table)) {
        return 0;
    }
    $sql = "SELECT COUNT(*) AS total FROM `$table`";
    if (trim((string)$condition) !== '') {
        $sql .= " WHERE " . $condition;
    }
    $result = safeQuery($conn, $sql);
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return (int) safeValue($row, 'total', 0);
}

function safeRows($result) {
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function fmtNum($value) {
    return number_format((int)$value);
}

function pct($value, $total) {
    $total = max(1, (int)$total);
    return round(((int)$value / $total) * 100, 1);
}

function validDateParam($value) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $value);
    return ($dt && $dt->format('Y-m-d') === $value) ? $value : '';
}

function periodConfig($period) {
    $periods = [
        'this_week' => ['This Week', "DATE_SUB(CURDATE(), INTERVAL 7 DAY)"],
        'this_month' => ['This Month', "DATE_FORMAT(CURDATE(), '%Y-%m-01')"],
        'last_3_months' => ['Last 3 Months', "DATE_SUB(CURDATE(), INTERVAL 3 MONTH)"],
        'this_year' => ['This Year', "DATE_FORMAT(CURDATE(), '%Y-01-01')"],
    ];
    return $periods[$period] ?? $periods['this_month'];
}

function addDateCondition(&$conditions, $column, $startDate, $endDate) {
    if ($startDate !== '') {
        $conditions[] = "DATE($column) >= '$startDate'";
    }
    if ($endDate !== '') {
        $conditions[] = "DATE($column) <= '$endDate'";
    }
}

function whereClause($conditions) {
    return $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
}

function selectedQueryUrl($params = []) {
    $query = array_merge($_GET, $params);
    foreach ($query as $key => $value) {
        if ($value === '' || $value === null) {
            unset($query[$key]);
        }
    }
    return 'dashboard.php' . ($query ? '?' . http_build_query($query) : '');
}

$dbConnected = isset($conn) && $conn && !$conn->connect_error;
$adminName = safeValue($_SESSION, 'full_name', 'Admin User');
$adminRole = 'Super Admin';

$hasUsers = tableExists($conn, 'users');
$hasJobs = tableExists($conn, 'jobs');
$hasApplications = tableExists($conn, 'applications');
$hasSkills = tableExists($conn, 'skills');
$hasProfiles = tableExists($conn, 'job_seeker_profiles');
$hasEmployment = tableExists($conn, 'employment_status');
$hasDistricts = tableExists($conn, 'districts');
$hasEmployerProfiles = tableExists($conn, 'employer_profiles');

$districtOptions = [];
if ($hasDistricts && columnExists($conn, 'districts', 'district_name')) {
    $districtOptions = safeRows(safeQuery($conn, "SELECT district_id, district_name FROM districts ORDER BY district_name ASC"));
}

$selectedDistrict = trim((string)($_GET['district'] ?? ''));
$startDate = validDateParam($_GET['start_date'] ?? '');
$endDate = validDateParam($_GET['end_date'] ?? '');
if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
    [$startDate, $endDate] = [$endDate, $startDate];
}
$activePeriod = preg_replace('/[^a-z0-9_]/', '', (string)($_GET['period'] ?? 'this_month'));
[$activePeriodLabel, $activePeriodStartSql] = periodConfig($activePeriod);

$selectedDistrictId = null;
if ($selectedDistrict !== '' && $hasDistricts) {
    foreach ($districtOptions as $districtRow) {
        if (strcasecmp((string)safeValue($districtRow, 'district_name', ''), $selectedDistrict) === 0) {
            $selectedDistrictId = (int)safeValue($districtRow, 'district_id', 0);
            break;
        }
    }
}

$userDateConditions = [];
if ($hasUsers && columnExists($conn, 'users', 'created_at')) {
    addDateCondition($userDateConditions, 'u.created_at', $startDate, $endDate);
}
$jobDateConditions = [];
if ($hasJobs && columnExists($conn, 'jobs', 'created_at')) {
    addDateCondition($jobDateConditions, 'j.created_at', $startDate, $endDate);
}
$applicationDateConditions = [];
if ($hasApplications && columnExists($conn, 'applications', 'applied_at')) {
    addDateCondition($applicationDateConditions, 'a.applied_at', $startDate, $endDate);
}

$totalUsers = 0;
$totalJobSeekers = 0;
$totalEmployers = 0;
if ($hasUsers) {
    $roleColumn = columnExists($conn, 'users', 'role');
    $districtUserJoin = '';
    $districtUserConditions = $userDateConditions;
    if ($selectedDistrict !== '' && $selectedDistrictId && $hasProfiles && columnExists($conn, 'job_seeker_profiles', 'district_id')) {
        $districtUserJoin .= " LEFT JOIN job_seeker_profiles jspf ON u.user_id = jspf.user_id";
        $districtUserConditions[] = "(jspf.district_id = $selectedDistrictId";
        if ($hasEmployerProfiles && columnExists($conn, 'employer_profiles', 'district_id')) {
            $districtUserJoin .= " LEFT JOIN employer_profiles epf ON u.user_id = epf.user_id";
            $districtUserConditions[count($districtUserConditions) - 1] .= " OR epf.district_id = $selectedDistrictId";
        }
        $districtUserConditions[count($districtUserConditions) - 1] .= ")";
    }
    $userWhere = whereClause($districtUserConditions);
    $row = safeRows(safeQuery($conn, "SELECT COUNT(DISTINCT u.user_id) AS total FROM users u $districtUserJoin $userWhere"));
    $totalUsers = (int)safeValue($row[0] ?? [], 'total', 0);
    if ($roleColumn) {
        $seekerConditions = array_merge($districtUserConditions, ["u.role='job_seeker'"]);
        $employerConditions = array_merge($districtUserConditions, ["u.role='employer'"]);
        $row = safeRows(safeQuery($conn, "SELECT COUNT(DISTINCT u.user_id) AS total FROM users u $districtUserJoin " . whereClause($seekerConditions)));
        $totalJobSeekers = (int)safeValue($row[0] ?? [], 'total', 0);
        $row = safeRows(safeQuery($conn, "SELECT COUNT(DISTINCT u.user_id) AS total FROM users u $districtUserJoin " . whereClause($employerConditions)));
        $totalEmployers = (int)safeValue($row[0] ?? [], 'total', 0);
    }
}

$totalJobs = 0;
if ($hasJobs) {
    $jobConditions = $jobDateConditions;
    if ($selectedDistrict !== '') {
        if ($selectedDistrictId && columnExists($conn, 'jobs', 'district_id')) {
            $jobConditions[] = "j.district_id = $selectedDistrictId";
        } elseif (columnExists($conn, 'jobs', 'location')) {
            $jobConditions[] = "j.location = '" . $conn->real_escape_string($selectedDistrict) . "'";
        }
    }
    $row = safeRows(safeQuery($conn, "SELECT COUNT(*) AS total FROM jobs j" . whereClause($jobConditions)));
    $totalJobs = (int)safeValue($row[0] ?? [], 'total', 0);
}

$totalApplications = 0;
if ($hasApplications) {
    $applicationJoin = '';
    $appConditions = $applicationDateConditions;
    if ($selectedDistrict !== '' && $hasJobs && columnExists($conn, 'applications', 'job_id')) {
        $applicationJoin = " LEFT JOIN jobs j ON a.job_id = j.job_id";
        if ($selectedDistrictId && columnExists($conn, 'jobs', 'district_id')) {
            $appConditions[] = "j.district_id = $selectedDistrictId";
        } elseif (columnExists($conn, 'jobs', 'location')) {
            $appConditions[] = "j.location = '" . $conn->real_escape_string($selectedDistrict) . "'";
        }
    }
    $row = safeRows(safeQuery($conn, "SELECT COUNT(*) AS total FROM applications a $applicationJoin" . whereClause($appConditions)));
    $totalApplications = (int)safeValue($row[0] ?? [], 'total', 0);
}

$verifiedUsers = 0;
if ($hasProfiles) {
    $profileConditions = [];
    if ($selectedDistrict !== '') {
        if ($selectedDistrictId && columnExists($conn, 'job_seeker_profiles', 'district_id')) {
            $profileConditions[] = "district_id = $selectedDistrictId";
        } elseif (columnExists($conn, 'job_seeker_profiles', 'district')) {
            $profileConditions[] = "district = '" . $conn->real_escape_string($selectedDistrict) . "'";
        }
    }
    $verifiedUsers = safeCount($conn, 'job_seeker_profiles', implode(' AND ', $profileConditions));
}

$employmentJoin = '';
$employmentConditions = [];
if ($selectedDistrict !== '' && $hasProfiles && $selectedDistrictId && columnExists($conn, 'job_seeker_profiles', 'district_id')) {
    $employmentJoin = " LEFT JOIN job_seeker_profiles jsp ON es.user_id = jsp.user_id";
    $employmentConditions[] = "jsp.district_id = $selectedDistrictId";
}
$unemployedUsers = 0;
$employedUsers = 0;
$trainingUsers = 0;
if ($hasEmployment && columnExists($conn, 'employment_status', 'current_status')) {
    foreach (['unemployed' => 'unemployedUsers', 'employed' => 'employedUsers', 'training' => 'trainingUsers'] as $statusName => $targetVar) {
        $conditions = array_merge($employmentConditions, ["es.current_status='$statusName'"]);
        $row = safeRows(safeQuery($conn, "SELECT COUNT(DISTINCT es.user_id) AS total FROM employment_status es $employmentJoin" . whereClause($conditions)));
        $$targetVar = (int)safeValue($row[0] ?? [], 'total', 0);
    }
}

$newUserConditions = ["u.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"];
if ($selectedDistrict !== '' && isset($districtUserJoin, $districtUserConditions)) {
    $newUserConditions = array_merge($districtUserConditions, $newUserConditions);
}
$newUsersWeek = ($hasUsers && columnExists($conn, 'users', 'created_at')) ? (int)safeValue((safeRows(safeQuery($conn, "SELECT COUNT(DISTINCT u.user_id) AS total FROM users u " . ($districtUserJoin ?? '') . whereClause($newUserConditions)))[0] ?? []), 'total', 0) : 0;

$applicationStatus = [
    'Pending' => 0,
    'Reviewed' => 0,
    'Shortlisted' => 0,
    'Rejected' => 0,
];
if ($hasApplications && columnExists($conn, 'applications', 'status')) {
    $statusJoin = '';
    $statusConditions = $applicationDateConditions;
    if ($selectedDistrict !== '' && $hasJobs && columnExists($conn, 'applications', 'job_id')) {
        $statusJoin = " LEFT JOIN jobs j ON a.job_id = j.job_id";
        if ($selectedDistrictId && columnExists($conn, 'jobs', 'district_id')) {
            $statusConditions[] = "j.district_id = $selectedDistrictId";
        } elseif (columnExists($conn, 'jobs', 'location')) {
            $statusConditions[] = "j.location = '" . $conn->real_escape_string($selectedDistrict) . "'";
        }
    }
    $statusRows = safeRows(safeQuery($conn, "SELECT a.status, COUNT(*) AS total FROM applications a $statusJoin" . whereClause($statusConditions) . " GROUP BY a.status"));
    foreach ($statusRows as $row) {
        $rawStatus = trim((string)safeValue($row, 'status', 'Pending'));
        $count = (int)safeValue($row, 'total', 0);
        $statusKey = 'Reviewed';
        if (stripos($rawStatus, 'pending') !== false) {
            $statusKey = 'Pending';
        } elseif (stripos($rawStatus, 'reject') !== false) {
            $statusKey = 'Rejected';
        } elseif (stripos($rawStatus, 'short') !== false || stripos($rawStatus, 'accept') !== false || stripos($rawStatus, 'select') !== false) {
            $statusKey = 'Shortlisted';
        }
        $applicationStatus[$statusKey] += $count;
    }
}

$topSkills = [];
if ($hasSkills && columnExists($conn, 'skills', 'skill_name')) {
    $topSkills = safeRows(safeQuery($conn, "
        SELECT skill_name, COUNT(*) AS total
        FROM skills
        WHERE skill_name IS NOT NULL AND skill_name != ''
        GROUP BY skill_name
        ORDER BY total DESC, skill_name ASC
        LIMIT 6
    "));
}
if (!$topSkills) {
    $topSkills = [
        ['skill_name' => 'Digital Marketing', 'total' => 0],
        ['skill_name' => 'Web Development', 'total' => 0],
        ['skill_name' => 'Data Entry', 'total' => 0],
        ['skill_name' => 'Driving', 'total' => 0],
        ['skill_name' => 'Tailoring', 'total' => 0],
        ['skill_name' => 'Computer Operator', 'total' => 0],
    ];
}
$maxSkill = max(1, ...array_map(fn($row) => (int)safeValue($row, 'total', 0), $topSkills));

$locationRows = [];
if ($hasDistricts && $hasProfiles && columnExists($conn, 'districts', 'district_id') && columnExists($conn, 'districts', 'district_name') && columnExists($conn, 'job_seeker_profiles', 'district_id')) {
    $locationConditions = [];
    if ($selectedDistrict !== '' && $selectedDistrictId) {
        $locationConditions[] = "d.district_id = $selectedDistrictId";
    }
    $locationRows = safeRows(safeQuery($conn, "
        SELECT d.district_name, COUNT(jsp.user_id) AS users_count
        FROM districts d
        LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
        " . whereClause($locationConditions) . "
        GROUP BY d.district_id, d.district_name
        ORDER BY users_count DESC, d.district_name ASC
        LIMIT 8
    "));
} elseif ($hasProfiles && columnExists($conn, 'job_seeker_profiles', 'district')) {
    $locationConditions = [];
    if ($selectedDistrict !== '') {
        $locationConditions[] = "district = '" . $conn->real_escape_string($selectedDistrict) . "'";
    }
    $locationRows = safeRows(safeQuery($conn, "
        SELECT district AS district_name, COUNT(*) AS users_count
        FROM job_seeker_profiles
        " . whereClause(array_merge(["district IS NOT NULL", "district != ''"], $locationConditions)) . "
        GROUP BY district
        ORDER BY users_count DESC
        LIMIT 8
    "));
}
if (!$locationRows) {
    $locationRows = [
        ['district_name' => 'Dhaka', 'users_count' => 0],
        ['district_name' => 'Chattogram', 'users_count' => 0],
        ['district_name' => 'Rajshahi', 'users_count' => 0],
        ['district_name' => 'Khulna', 'users_count' => 0],
        ['district_name' => 'Sylhet', 'users_count' => 0],
        ['district_name' => 'Barishal', 'users_count' => 0],
        ['district_name' => 'Rangpur', 'users_count' => 0],
        ['district_name' => 'Others', 'users_count' => 0],
    ];
}
$maxLocation = max(1, ...array_map(fn($row) => (int)safeValue($row, 'users_count', 0), $locationRows));
$mostActiveDistrict = safeValue($locationRows[0] ?? [], 'district_name', 'No data available');

$unemploymentRows = [];
if ($hasDistricts && $hasProfiles && $hasEmployment && columnExists($conn, 'job_seeker_profiles', 'district_id') && columnExists($conn, 'employment_status', 'current_status')) {
    $trendConditions = [];
    if ($selectedDistrict !== '' && $selectedDistrictId) {
        $trendConditions[] = "d.district_id = $selectedDistrictId";
    }
    if (columnExists($conn, 'employment_status', 'updated_at')) {
        $trendConditions[] = "DATE(es.updated_at) >= $activePeriodStartSql";
    }
    $unemploymentRows = safeRows(safeQuery($conn, "
        SELECT 
            d.district_name, 
            COUNT(DISTINCT jsp.user_id) AS seeker_count,
            COUNT(DISTINCT es.user_id) AS unemployed_count
        FROM districts d
        LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
        LEFT JOIN employment_status es ON jsp.user_id = es.user_id AND es.current_status = 'unemployed'
        " . whereClause($trendConditions) . "
        GROUP BY d.district_id, d.district_name
        ORDER BY unemployed_count DESC, d.district_name ASC
        LIMIT 7
    "));
}
if (!$unemploymentRows) {
    $unemploymentRows = [
        ['district_name' => 'Dhaka', 'unemployed_count' => 12],
        ['district_name' => 'Chattogram', 'unemployed_count' => 8],
        ['district_name' => 'Rajshahi', 'unemployed_count' => 10],
        ['district_name' => 'Khulna', 'unemployed_count' => 6],
        ['district_name' => 'Sylhet', 'unemployed_count' => 7],
        ['district_name' => 'Barishal', 'unemployed_count' => 5],
        ['district_name' => 'Rangpur', 'unemployed_count' => 9],
    ];
}
$maxUnemployment = max(1, ...array_map(fn($row) => (int)safeValue($row, 'unemployed_count', 0), $unemploymentRows));
$maxSeekersForTrend = max(1, ...array_map(fn($row) => (int)safeValue($row, 'seeker_count', (int)safeValue($row, 'unemployed_count', 0)), $unemploymentRows));
$highestUnemploymentArea = safeValue($unemploymentRows[0] ?? [], 'district_name', 'No data available');

$recentJobs = [];
if ($hasJobs && columnExists($conn, 'jobs', 'job_id')) {
    $titleSelect = columnExists($conn, 'jobs', 'title') ? 'j.title' : "'' AS title";
    $locationSelect = columnExists($conn, 'jobs', 'location') ? 'j.location' : "'' AS location";
    $dateSelect = columnExists($conn, 'jobs', 'created_at') ? 'j.created_at' : 'NULL AS created_at';
    $companyJoin = ($hasEmployerProfiles && columnExists($conn, 'employer_profiles', 'user_id') && columnExists($conn, 'employer_profiles', 'company_name') && columnExists($conn, 'jobs', 'employer_id'))
        ? 'LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id'
        : '';
    $companySelect = $companyJoin ? "COALESCE(ep.company_name, 'Jibika Employer') AS company_name" : "'Jibika Employer' AS company_name";
    $applicationJoin = ($hasApplications && columnExists($conn, 'applications', 'job_id')) ? 'LEFT JOIN applications a ON j.job_id = a.job_id' : '';
    $applicantSelect = $applicationJoin ? 'COUNT(a.application_id) AS application_count' : '0 AS application_count';
    $recentJobConditions = $jobDateConditions;
    if ($selectedDistrict !== '') {
        if ($selectedDistrictId && columnExists($conn, 'jobs', 'district_id')) {
            $recentJobConditions[] = "j.district_id = $selectedDistrictId";
        } elseif (columnExists($conn, 'jobs', 'location')) {
            $recentJobConditions[] = "j.location = '" . $conn->real_escape_string($selectedDistrict) . "'";
        }
    }
    $recentJobs = safeRows(safeQuery($conn, "
        SELECT j.job_id, $titleSelect, $locationSelect, $dateSelect, $companySelect, $applicantSelect
        FROM jobs j
        $companyJoin
        $applicationJoin
        " . whereClause($recentJobConditions) . "
        GROUP BY j.job_id
        ORDER BY j.job_id DESC
        LIMIT 5
    "));
}

$highestSkillDemand = safeValue($topSkills[0] ?? [], 'skill_name', 'No data available');
$trendValues = [];
foreach ($unemploymentRows as $row) {
    $trendValues[] = (int)safeValue($row, 'unemployed_count', 0);
}

$dashboardNotifications = [];
if ($hasUsers && columnExists($conn, 'users', 'role') && columnExists($conn, 'users', 'created_at')) {
    $row = safeRows(safeQuery($conn, "
        SELECT full_name, created_at 
        FROM users 
        WHERE role='job_seeker' 
        ORDER BY created_at DESC 
        LIMIT 1
    "));
    if ($row) {
        $dashboardNotifications[] = [
            'icon' => 'fa-user-graduate',
            'title' => 'New job seeker registered',
            'text' => safeValue($row[0], 'full_name', 'A job seeker') . ' joined Jibika',
            'time' => safeValue($row[0], 'created_at', ''),
            'link' => 'users.php',
        ];
    }
    $row = safeRows(safeQuery($conn, "
        SELECT full_name, created_at 
        FROM users 
        WHERE role='employer' 
        ORDER BY created_at DESC 
        LIMIT 1
    "));
    if ($row) {
        $dashboardNotifications[] = [
            'icon' => 'fa-building',
            'title' => 'New employer registered',
            'text' => safeValue($row[0], 'full_name', 'An employer') . ' created an employer account',
            'time' => safeValue($row[0], 'created_at', ''),
            'link' => 'users.php?role=employer',
        ];
    }
}
if ($hasJobs && columnExists($conn, 'jobs', 'title') && columnExists($conn, 'jobs', 'created_at')) {
    $row = safeRows(safeQuery($conn, "SELECT title, created_at FROM jobs ORDER BY created_at DESC LIMIT 1"));
    if ($row) {
        $dashboardNotifications[] = [
            'icon' => 'fa-briefcase',
            'title' => 'New job post added',
            'text' => safeValue($row[0], 'title', 'A job') . ' was posted',
            'time' => safeValue($row[0], 'created_at', ''),
            'link' => 'jobs.php',
        ];
    }
}
if ($hasApplications && columnExists($conn, 'applications', 'applied_at')) {
    $applicationTitleSelect = ($hasJobs && columnExists($conn, 'jobs', 'title') && columnExists($conn, 'applications', 'job_id')) ? 'j.title' : "'' AS title";
    $applicationJoin = ($hasJobs && columnExists($conn, 'jobs', 'job_id') && columnExists($conn, 'applications', 'job_id')) ? 'LEFT JOIN jobs j ON a.job_id = j.job_id' : '';
    $row = safeRows(safeQuery($conn, "
        SELECT $applicationTitleSelect, a.applied_at 
        FROM applications a 
        $applicationJoin
        ORDER BY a.applied_at DESC 
        LIMIT 1
    "));
    if ($row) {
        $dashboardNotifications[] = [
            'icon' => 'fa-file-circle-check',
            'title' => 'New application submitted',
            'text' => 'Application received' . (safeValue($row[0], 'title', '') !== '' ? ' for ' . safeValue($row[0], 'title', '') : ''),
            'time' => safeValue($row[0], 'applied_at', ''),
            'link' => '../employer/applicants.php',
        ];
    }
}
$dashboardNotifications = array_slice($dashboardNotifications, 0, 4);
$notificationCount = count($dashboardNotifications);
$activeFilters = [];
if ($selectedDistrict !== '') {
    $activeFilters[] = 'District: ' . $selectedDistrict;
}
if ($startDate !== '' || $endDate !== '') {
    $activeFilters[] = 'Date: ' . ($startDate !== '' ? $startDate : 'Any') . ' to ' . ($endDate !== '' ? $endDate : 'Any');
}
if ($activePeriod !== 'this_month') {
    $activeFilters[] = 'Trend: ' . $activePeriodLabel;
}

include('../includes/header.php');
?>
<link rel="stylesheet" href="../assets/css/admin_dashboard_jibika_final.css">

<div class="jibika-admin">
    <aside class="ja-sidebar">
        <a class="ja-brand" href="dashboard.php">
            <span class="ja-brand-icon"><i class="fa-solid fa-people-group"></i></span>
            <span>
                <strong>Jibika</strong>
                <small>Connecting Skills, Creating Opportunities</small>
            </span>
        </a>
        <a class="ja-visit-site" href="../index.php"><i class="fa-solid fa-arrow-left"></i><span>Visit Main Site</span></a>
        <nav class="ja-menu">
            <a class="active" href="dashboard.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
            <a href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
            <a href="unemployed_details.php"><i class="fa-solid fa-user-graduate"></i><span>Job Seekers</span></a>
            <a href="users.php?role=employer"><i class="fa-solid fa-building"></i><span>Employers</span></a>
            <a href="jobs.php"><i class="fa-solid fa-briefcase"></i><span>Job Posts</span></a>
            <a href="applications.php"><i class="fa-solid fa-file-lines"></i><span>Applications</span></a>
            <a href="skills_training.php"><i class="fa-solid fa-medal"></i><span>Skills & Training</span></a>
            <a href="area_monitor.php"><i class="fa-solid fa-location-dot"></i><span>Area Monitor</span></a>
            <a href="job_matching.php"><i class="fa-solid fa-link"></i><span>Job Matching</span></a>
            <a href="reports.php"><i class="fa-solid fa-chart-simple"></i><span>Reports</span></a>
            <a href="users.php?verify=1"><i class="fa-solid fa-shield-halved"></i><span>Verifications</span></a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
            <a class="ja-logout-link" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
        </nav>
        <div class="ja-sidebar-card">
            <strong>Make better decisions</strong>
            <p>Use real-time employment data to guide training and job matching.</p>
            <a href="reports.php">View Reports <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </aside>

    <main class="ja-main">
        <header class="ja-topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo e($adminName); ?>. Here's what's happening today.</p>
            </div>
            <form class="ja-top-actions" method="GET" action="dashboard.php">
                <label class="ja-filter-control">
                    <span>District</span>
                    <select name="district" onchange="this.form.submit()">
                        <option value="">All Districts</option>
                        <?php
                        $preferredDistricts = ['Dhaka','Rajshahi','Barishal','Mymensingh','Rangpur','Chattogram','Sylhet','Khulna'];
                        $shownDistricts = [];
                        foreach ($preferredDistricts as $districtName):
                            $shownDistricts[] = strtolower($districtName);
                        ?>
                            <option value="<?php echo e($districtName); ?>" <?php echo strcasecmp($selectedDistrict, $districtName) === 0 ? 'selected' : ''; ?>><?php echo e($districtName); ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($districtOptions as $districtRow): ?>
                            <?php $districtName = safeValue($districtRow, 'district_name', ''); ?>
                            <?php if ($districtName !== '' && !in_array(strtolower($districtName), $shownDistricts, true)): ?>
                                <option value="<?php echo e($districtName); ?>" <?php echo strcasecmp($selectedDistrict, $districtName) === 0 ? 'selected' : ''; ?>><?php echo e($districtName); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="ja-filter-control ja-date-control">
                    <span>Start</span>
                    <input type="date" name="start_date" value="<?php echo e($startDate); ?>">
                </label>
                <label class="ja-filter-control ja-date-control">
                    <span>End</span>
                    <input type="date" name="end_date" value="<?php echo e($endDate); ?>">
                </label>
                <input type="hidden" name="period" value="<?php echo e($activePeriod); ?>">
                <button class="ja-apply-btn" type="submit"><i class="fa-solid fa-filter"></i><span>Apply</span></button>
                <label class="ja-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" placeholder="Search anything...">
                </label>
                <a class="ja-site-link" href="../index.php"><i class="fa-solid fa-globe"></i><span>Visit Main Site</span></a>
                <div class="ja-notification-wrap">
                    <button class="ja-bell" type="button" data-notification-toggle aria-expanded="false" aria-label="Open notifications">
                        <i class="fa-regular fa-bell"></i>
                        <?php if ($notificationCount > 0): ?><em><?php echo e($notificationCount); ?></em><?php endif; ?>
                    </button>
                    <div class="ja-notification-panel" data-notification-panel>
                        <div class="ja-notification-head">
                            <strong>Notifications</strong>
                            <small>Recent admin activity</small>
                        </div>
                        <?php if ($dashboardNotifications): ?>
                            <?php foreach ($dashboardNotifications as $note): ?>
                                <a class="ja-note" href="<?php echo e(safeValue($note, 'link', 'dashboard.php')); ?>">
                                    <i class="fa-solid <?php echo e(safeValue($note, 'icon', 'fa-bell')); ?>"></i>
                                    <span>
                                        <strong><?php echo e(safeValue($note, 'title', 'Notification')); ?></strong>
                                        <small><?php echo e(safeValue($note, 'text', 'Dashboard activity')); ?></small>
                                        <time><?php echo e(safeValue($note, 'time', '')); ?></time>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="ja-note-empty">No recent notifications</div>
                        <?php endif; ?>
                        <a class="ja-view-all" href="reports.php">View all notifications</a>
                    </div>
                </div>
                <div class="ja-profile">
                    <span><?php echo e(strtoupper(substr((string)$adminName, 0, 1))); ?></span>
                    <div><strong><?php echo e($adminName); ?></strong><small><?php echo e($adminRole); ?></small></div>
                </div>
            </form>
        </header>

        <?php if ($activeFilters): ?>
            <div class="ja-active-filters">
                <?php foreach ($activeFilters as $filterLabel): ?>
                    <span><i class="fa-solid fa-circle-check"></i><?php echo e($filterLabel); ?></span>
                <?php endforeach; ?>
                <a href="dashboard.php"><i class="fa-solid fa-rotate-left"></i> Reset Filters</a>
            </div>
        <?php endif; ?>

        <section class="ja-stat-grid">
            <?php
            $stats = [
                ['Total Users', $totalUsers, 'fa-users', 'green', $totalJobSeekers . ' job seekers'],
                ['Job Seekers', $totalJobSeekers, 'fa-user-graduate', 'blue', 'Registered talent'],
                ['Job Posts', $totalJobs, 'fa-briefcase', 'purple', 'Employer opportunities'],
                ['Applications', $totalApplications, 'fa-file-circle-check', 'orange', 'Candidate pipeline'],
                ['Employers', $totalEmployers, 'fa-building', 'teal', 'Hiring accounts'],
                ['Verified Users', $verifiedUsers, 'fa-shield-check', 'red', 'Profile coverage'],
            ];
            ?>
            <?php foreach ($stats as $stat): ?>
                <article class="ja-stat ja-<?php echo e($stat[3]); ?>">
                    <span><i class="fa-solid <?php echo e($stat[2]); ?>"></i></span>
                    <div>
                        <p><?php echo e($stat[0]); ?></p>
                        <strong><?php echo fmtNum($stat[1]); ?></strong>
                        <small><i class="fa-solid fa-arrow-trend-up"></i><?php echo e($stat[4]); ?></small>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="ja-grid">
            <article class="ja-panel ja-panel-wide">
                <div class="ja-panel-head">
                    <div><h2>Unemployment Trend (Area-wise)</h2><p>District-based unemployment monitoring</p></div>
                    <div class="ja-period-menu">
                        <button type="button" data-period-toggle><?php echo e($activePeriodLabel); ?> <i class="fa-solid fa-chevron-down"></i></button>
                        <div data-period-panel>
                            <a href="<?php echo e(selectedQueryUrl(['period' => 'this_week'])); ?>">This Week</a>
                            <a href="<?php echo e(selectedQueryUrl(['period' => 'this_month'])); ?>">This Month</a>
                            <a href="<?php echo e(selectedQueryUrl(['period' => 'last_3_months'])); ?>">Last 3 Months</a>
                            <a href="<?php echo e(selectedQueryUrl(['period' => 'this_year'])); ?>">This Year</a>
                        </div>
                    </div>
                </div>
                <div class="ja-trend">
                    <?php foreach ($trendValues as $index => $value): ?>
                        <?php
                            $row = $unemploymentRows[$index] ?? [];
                            $districtName = safeValue($row, 'district_name', 'Area');
                            $seekerCount = (int)safeValue($row, 'seeker_count', $value);
                            $unemploymentPct = pct($value, max(1, $seekerCount));
                            $width = max(5, pct($value, $maxUnemployment));
                        ?>
                        <div class="ja-trend-row" title="<?php echo e($districtName . ': ' . fmtNum($value) . ' unemployed'); ?>">
                            <strong><?php echo e($districtName); ?></strong>
                            <span class="ja-trend-track"><em style="width: <?php echo e($width); ?>%;"></em></span>
                            <b><?php echo fmtNum($value); ?></b>
                            <small><?php echo e($unemploymentPct); ?>%</small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="ja-panel">
                <div class="ja-panel-head">
                    <div><h2>Users by Location</h2><p>Bangladesh district analytics</p></div>
                    <a href="reports.php">View Full Report</a>
                </div>
                <div class="ja-map-wrap">
                    <div class="ja-bd-map" aria-label="Bangladesh map visual">
                        <img src="../assets/images/bangladesh-map.svg" alt="Bangladesh map">
                        <span class="map-dot dot-dhaka"></span>
                        <span class="map-dot dot-ctg"></span>
                        <span class="map-dot dot-khulna"></span>
                        <span class="map-dot dot-raj"></span>
                        <span class="map-dot dot-sylhet"></span>
                        <span class="map-dot dot-rangpur"></span>
                        <span class="map-dot dot-barishal"></span>
                        <span class="map-label label-dhaka">Dhaka</span>
                        <span class="map-label label-ctg">Chattogram</span>
                        <span class="map-label label-sylhet">Sylhet</span>
                        <strong>BD</strong>
                    </div>
                    <div class="ja-location-list">
                        <?php foreach ($locationRows as $row): ?>
                            <?php
                            $district = safeValue($row, 'district_name', 'Unknown');
                            $users = (int)safeValue($row, 'users_count', 0);
                            ?>
                            <div>
                                <span></span>
                                <strong><?php echo e($district); ?></strong>
                                <em><?php echo fmtNum($users); ?></em>
                                <small><?php echo e(pct($users, $maxLocation)); ?>%</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>

            <article class="ja-panel">
                <div class="ja-panel-head">
                    <div><h2>Top In-Demand Skills</h2><p>Skills from seeker profiles</p></div>
                    <a href="skills_training.php">View All</a>
                </div>
                <div class="ja-skill-list">
                    <?php foreach ($topSkills as $skill): ?>
                        <?php
                        $skillName = safeValue($skill, 'skill_name', 'No data available');
                        $skillTotal = (int)safeValue($skill, 'total', 0);
                        ?>
                        <div>
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            <strong><?php echo e($skillName); ?></strong>
                            <span><em style="width: <?php echo e(max(4, pct($skillTotal, $maxSkill))); ?>%;"></em></span>
                            <small><?php echo fmtNum($skillTotal); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="ja-panel">
                <div class="ja-panel-head">
                    <div><h2>Recent Job Posts</h2><p>Latest employer activity</p></div>
                    <a href="jobs.php">View All</a>
                </div>
                <div class="ja-jobs">
                    <?php if ($recentJobs): ?>
                        <?php foreach ($recentJobs as $job): ?>
                            <a href="jobs.php">
                                <i class="fa-solid fa-briefcase"></i>
                                <span>
                                    <strong><?php echo e(safeValue($job, 'title', 'Untitled Job')); ?></strong>
                                    <small><?php echo e(safeValue($job, 'company_name', 'Jibika Employer')); ?> &middot; <?php echo e(safeValue($job, 'location', 'Bangladesh')); ?></small>
                                    <small><?php echo e(safeValue($job, 'created_at', 'No date')); ?></small>
                                </span>
                                <em><?php echo fmtNum(safeValue($job, 'application_count', 0)); ?><small>Applicants</small></em>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="ja-empty">No data available</div>
                    <?php endif; ?>
                </div>
            </article>

            <article class="ja-panel">
                <div class="ja-panel-head">
                    <div><h2>Application Status Overview</h2><p>Pending, reviewed, shortlisted and rejected</p></div>
                </div>
                <div class="ja-status">
                    <?php $statusTotal = max(1, array_sum($applicationStatus)); ?>
                    <div class="ja-donut" style="--pending: <?php echo e(pct($applicationStatus['Pending'], $statusTotal)); ?>; --reviewed: <?php echo e(pct($applicationStatus['Reviewed'], $statusTotal)); ?>; --shortlisted: <?php echo e(pct($applicationStatus['Shortlisted'], $statusTotal)); ?>;">
                        <strong><?php echo fmtNum(array_sum($applicationStatus)); ?><small>Total</small></strong>
                    </div>
                    <div class="ja-status-list">
                        <?php foreach ($applicationStatus as $label => $count): ?>
                            <div><span class="<?php echo e(strtolower($label)); ?>"></span><strong><?php echo e($label); ?></strong><em><?php echo fmtNum($count); ?> (<?php echo e(pct($count, $statusTotal)); ?>%)</em></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>
        </section>

        <section class="ja-insights">
            <div><i class="fa-solid fa-chart-line"></i><span>Highest Unemployment Area</span><strong><?php echo e($highestUnemploymentArea); ?></strong></div>
            <div><i class="fa-solid fa-arrow-trend-up"></i><span>Highest Skill Demand</span><strong><?php echo e($highestSkillDemand); ?></strong></div>
            <div><i class="fa-solid fa-location-crosshairs"></i><span>Most Active District</span><strong><?php echo e($mostActiveDistrict); ?></strong></div>
            <div><i class="fa-solid fa-user-plus"></i><span>New Registrations</span><strong><?php echo fmtNum($newUsersWeek); ?> this week</strong></div>
        </section>

        <section class="ja-system">
            <div>
                <h2>System Overview</h2>
                <p>Area-wise unemployment monitoring, skill-based job matching and employer-job seeker connection are ready.</p>
            </div>
            <ul>
                <li><i class="fa-solid fa-circle-check"></i> Server Online</li>
                <li><i class="fa-solid fa-database"></i> Database <?php echo $dbConnected ? 'Connected' : 'Unavailable'; ?></li>
                <li><i class="fa-solid fa-link"></i> Matching System Active</li>
                <li><i class="fa-solid fa-gauge-high"></i> Admin Panel Ready</li>
            </ul>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin_dashboard_jibika_final.js"></script>
</body>
</html>
