<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

// District dropdown
$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");

$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;

$sql = "
    SELECT 
        u.full_name,
        u.email,
        u.phone,
        jsp.nid,
        jsp.education,
        jsp.skills,
        d.district_name,
        up.upazila_name,
        w.ward_name,
        es.current_status,
        es.remarks,
        es.updated_at
    FROM users u
    INNER JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
    INNER JOIN employment_status es ON u.user_id = es.user_id
    LEFT JOIN districts d ON jsp.district_id = d.district_id
    LEFT JOIN upazilas up ON jsp.upazila_id = up.upazila_id
    LEFT JOIN wards w ON jsp.ward_id = w.ward_id
    WHERE u.role = 'job_seeker'
    AND es.current_status = 'unemployed'
";

if($district_id > 0){
    $sql .= " AND jsp.district_id = '$district_id'";
}

$sql .= " ORDER BY d.district_name ASC, up.upazila_name ASC, w.ward_name ASC, u.full_name ASC";

$result = $conn->query($sql);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Unemployed People Details</h2>
            <p class="text-muted mb-0">View area-based details of currently unemployed job seekers.</p>
        </div>
        <a href="reports.php" class="btn btn-secondary">Back to Reports</a>
    </div>

    <div class="card shadow p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-10">
                <label class="form-label">Filter by District</label>
                <select name="district_id" class="form-control">
                    <option value="">All Districts</option>
                    <?php
                    if($districts && $districts->num_rows > 0){
                        while($row = $districts->fetch_assoc()){
                            $selected = ($district_id == $row['district_id']) ? 'selected' : '';
                            echo "<option value='".$row['district_id']."' $selected>".htmlspecialchars($row['district_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div class="card shadow p-4">
        <?php if($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>NID</th>
                            <th>District</th>
                            <th>Upazila</th>
                            <th>Ward</th>
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
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['nid']); ?></td>
                                <td><?php echo htmlspecialchars($row['district_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['upazila_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['ward_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['education']); ?></td>
                                <td><?php echo htmlspecialchars($row['skills']); ?></td>
                                <td><span class="badge bg-danger">Unemployed</span></td>
                                <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                                <td>
                                    <a href="update_status.php?email=<?php echo urlencode($row['email']); ?>" class="btn btn-sm btn-primary">
                                        Update Status
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mb-0">No unemployed people found for the selected area.</div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>