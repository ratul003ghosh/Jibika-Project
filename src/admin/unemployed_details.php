<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

// District dropdown
$districts = $conn->query("
    SELECT * 
    FROM districts 
    ORDER BY district_name ASC
");

$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;

$status_filter = isset($_GET['status']) 
    ? trim($_GET['status']) 
    : '';

$search = isset($_GET['search']) 
    ? trim($_GET['search']) 
    : '';

$where = [];

$where[] = "u.role='job_seeker'";

if($district_id > 0){
    $where[] = "jsp.district_id='$district_id'";
}

if(!empty($status_filter)){
    $safe_status = $conn->real_escape_string($status_filter);
    $where[] = "es.current_status='$safe_status'";
}

if(!empty($search)){

    $safe_search = $conn->real_escape_string($search);

    $where[] = "
        (
            u.full_name LIKE '%$safe_search%'
            OR u.email LIKE '%$safe_search%'
            OR u.phone LIKE '%$safe_search%'
        )
    ";
}

$where_sql = implode(" AND ", $where);

$sql = "
    SELECT 
        u.user_id,
        u.full_name,
        u.email,
        u.phone,

        jsp.nid,
        jsp.education,

        d.district_name,
        up.upazila_name,
        w.ward_name,

        es.current_status,
        es.remarks,
        es.updated_at

    FROM users u

    LEFT JOIN job_seeker_profiles jsp
        ON u.user_id = jsp.user_id

    LEFT JOIN employment_status es
        ON u.user_id = es.user_id

    LEFT JOIN districts d
        ON jsp.district_id = d.district_id

    LEFT JOIN upazilas up
        ON jsp.upazila_id = up.upazila_id

    LEFT JOIN wards w
        ON jsp.ward_id = w.ward_id

    WHERE $where_sql

    ORDER BY 
        d.district_name ASC,
        u.full_name ASC
";

$result = $conn->query($sql);

// Summary analytics
$total_unemployed = $conn->query("
    SELECT COUNT(*) AS total
    FROM employment_status
    WHERE current_status='unemployed'
")->fetch_assoc()['total'] ?? 0;

$total_employed = $conn->query("
    SELECT COUNT(*) AS total
    FROM employment_status
    WHERE current_status='employed'
")->fetch_assoc()['total'] ?? 0;

$total_training = $conn->query("
    SELECT COUNT(*) AS total
    FROM employment_status
    WHERE current_status='training'
")->fetch_assoc()['total'] ?? 0;

$total_self_employed = $conn->query("
    SELECT COUNT(*) AS total
    FROM employment_status
    WHERE current_status='self_employed'
")->fetch_assoc()['total'] ?? 0;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <div>
            <h2 class="mb-1 fw-bold">
                Employment Monitoring System
            </h2>

            <p class="text-muted mb-0">
                Monitor employment conditions, area-wise unemployment and workforce analytics.
            </p>
        </div>

        <a href="reports.php" class="btn btn-secondary">
            Back to Reports
        </a>

    </div>

    <!-- SUMMARY -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Unemployed</h6>
                <h3 class="text-danger">
                    <?php echo $total_unemployed; ?>
                </h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Employed</h6>
                <h3 class="text-success">
                    <?php echo $total_employed; ?>
                </h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Training</h6>
                <h3 class="text-warning">
                    <?php echo $total_training; ?>
                </h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6>Self Employed</h6>
                <h3 class="text-info">
                    <?php echo $total_self_employed; ?>
                </h3>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="card shadow border-0 p-4 mb-4">

        <form method="GET">

            <div class="row g-3 align-items-end">

                <div class="col-md-4">

                    <label class="form-label">
                        Search User
                    </label>

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Name / Email / Phone"
                           value="<?php echo htmlspecialchars($search); ?>">

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Employment Status
                    </label>

                    <select name="status" class="form-select">

                        <option value="">All Status</option>

                        <option value="unemployed"
                            <?php echo ($status_filter == 'unemployed') ? 'selected' : ''; ?>>
                            Unemployed
                        </option>

                        <option value="employed"
                            <?php echo ($status_filter == 'employed') ? 'selected' : ''; ?>>
                            Employed
                        </option>

                        <option value="training"
                            <?php echo ($status_filter == 'training') ? 'selected' : ''; ?>>
                            Training
                        </option>

                        <option value="self_employed"
                            <?php echo ($status_filter == 'self_employed') ? 'selected' : ''; ?>>
                            Self Employed
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        District
                    </label>

                    <select name="district_id" class="form-select">

                        <option value="">All Districts</option>

                        <?php
                        if($districts && $districts->num_rows > 0){

                            while($row = $districts->fetch_assoc()){

                                $selected = ($district_id == $row['district_id'])
                                    ? 'selected'
                                    : '';

                                echo "
                                    <option value='{$row['district_id']}' $selected>
                                        ".htmlspecialchars($row['district_name'])."
                                    </option>
                                ";
                            }
                        }
                        ?>

                    </select>

                </div>

                <div class="col-md-2 d-grid">

                    <button type="submit"
                            class="btn btn-primary">

                        Apply Filter

                    </button>

                </div>

            </div>

        </form>

    </div>

    <!-- TABLE -->
    <div class="card shadow border-0 p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h4 class="mb-0">
                Workforce Monitoring Table
            </h4>

            <span class="badge bg-dark">
                Total Records:
                <?php echo ($result) ? $result->num_rows : 0; ?>
            </span>

        </div>

        <?php if($result && $result->num_rows > 0): ?>

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>
                            <th>User</th>
                            <th>Area</th>
                            <th>Education</th>
                            <th>Skills</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Updated</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php $count = 1; ?>

                        <?php while($row = $result->fetch_assoc()): ?>

                            <?php
                            $skills_result = $conn->query("
                                SELECT skill_name
                                FROM skills
                                WHERE user_id='{$row['user_id']}'
                            ");

                            $skills = [];

                            if($skills_result && $skills_result->num_rows > 0){

                                while($skill = $skills_result->fetch_assoc()){

                                    $skills[] = $skill['skill_name'];
                                }
                            }
                            ?>

                            <tr>

                                <td>
                                    <?php echo $count++; ?>
                                </td>

                                <td>

                                    <strong>
                                        <?php echo htmlspecialchars($row['full_name']); ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </small>

                                    <br>

                                    <small>
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </small>

                                </td>

                                <td>

                                    <strong>
                                        <?php echo htmlspecialchars($row['district_name'] ?? 'N/A'); ?>
                                    </strong>

                                    <br>

                                    <small>
                                        <?php echo htmlspecialchars($row['upazila_name'] ?? ''); ?>
                                    </small>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['education'] ?? 'N/A'); ?>
                                </td>

                                <td>

                                    <?php
                                    if(!empty($skills)){

                                        foreach($skills as $skill){

                                            echo "
                                                <span class='badge bg-secondary me-1 mb-1'>
                                                    ".htmlspecialchars($skill)."
                                                </span>
                                            ";
                                        }

                                    } else {

                                        echo "
                                            <span class='text-muted'>
                                                No Skills
                                            </span>
                                        ";
                                    }
                                    ?>

                                </td>

                                <td>

                                    <?php
                                    $status = $row['current_status'];

                                    if($status == 'employed'){

                                        echo "<span class='badge bg-success'>Employed</span>";

                                    } elseif($status == 'training'){

                                        echo "<span class='badge bg-warning text-dark'>Training</span>";

                                    } elseif($status == 'self_employed'){

                                        echo "<span class='badge bg-info text-dark'>Self Employed</span>";

                                    } else {

                                        echo "<span class='badge bg-danger'>Unemployed</span>";
                                    }
                                    ?>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['remarks'] ?? ''); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['updated_at'] ?? 'N/A'); ?>
                                </td>

                                <td>

                                    <a href="update_status.php?email=<?php echo urlencode($row['email']); ?>"
                                       class="btn btn-primary btn-sm">

                                        Update Status

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="alert alert-warning mb-0">

                No workforce records found.

            </div>

        <?php endif; ?>

    </div>

</div>

<?php include('../includes/footer.php'); ?>