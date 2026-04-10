<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$message = "";

// Dropdown data
$districts = $conn->query("SELECT * FROM districts ORDER BY district_name ASC");
$upazilas = $conn->query("SELECT * FROM upazilas ORDER BY upazila_name ASC");
$wards = $conn->query("SELECT * FROM wards ORDER BY ward_name ASC");

if(isset($_POST['post_job'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $salary = $_POST['salary'];
    $location = $_POST['location'];

    $district_id = !empty($_POST['district_id']) ? intval($_POST['district_id']) : "NULL";
    $upazila_id  = !empty($_POST['upazila_id']) ? intval($_POST['upazila_id']) : "NULL";
    $ward_id     = !empty($_POST['ward_id']) ? intval($_POST['ward_id']) : "NULL";

    $employer_id = $_SESSION['user_id'];

    $sql = "INSERT INTO jobs (employer_id, title, description, location, salary, district_id, upazila_id, ward_id) 
            VALUES ('$employer_id', '$title', '$description', '$location', '$salary', $district_id, $upazila_id, $ward_id)";

    if($conn->query($sql)){
        $message = "Job posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Post a Job</h2>
                <p class="text-muted mb-0">Create a location-based job opportunity.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if($message != ""): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Job Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label>District</label>
                <select name="district_id" class="form-control">
                    <option value="">Select District</option>
                    <?php
                    if($districts && $districts->num_rows > 0){
                        while($row = $districts->fetch_assoc()){
                            echo "<option value='".$row['district_id']."'>".htmlspecialchars($row['district_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Upazila</label>
                <select name="upazila_id" class="form-control">
                    <option value="">Select Upazila</option>
                    <?php
                    if($upazilas && $upazilas->num_rows > 0){
                        while($row = $upazilas->fetch_assoc()){
                            echo "<option value='".$row['upazila_id']."'>".htmlspecialchars($row['upazila_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Ward</label>
                <select name="ward_id" class="form-control">
                    <option value="">Select Ward</option>
                    <?php
                    if($wards && $wards->num_rows > 0){
                        while($row = $wards->fetch_assoc()){
                            echo "<option value='".$row['ward_id']."'>".htmlspecialchars($row['ward_name'])."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Location Text (Optional)</label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Near Bazar, Road No 2">
            </div>

            <div class="mb-3">
                <label>Salary</label>
                <input type="text" name="salary" class="form-control">
            </div>

            <button type="submit" name="post_job" class="btn btn-success w-100">
                Post Job
            </button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>