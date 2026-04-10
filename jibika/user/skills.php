<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker'){
    header("Location: ../login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];
$message = "";

// Add skill
if(isset($_POST['add_skill'])){
    $skill_name = trim($_POST['skill_name']);

    if($skill_name != ""){
        $sql = "INSERT INTO skills (user_id, skill_name) VALUES ('$user_id', '$skill_name')";
        if($conn->query($sql)){
            $message = "Skill added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Skill field cannot be empty!";
    }
}

// Delete skill
if(isset($_GET['delete'])){
    $skill_id = $_GET['delete'];
    $conn->query("DELETE FROM skills WHERE skill_id='$skill_id' AND user_id='$user_id'");
    header("Location: skills.php");
    exit();
}

// Fetch skills
$result = $conn->query("SELECT * FROM skills WHERE user_id='$user_id' ORDER BY skill_id DESC");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">My Skills</h2>

        <?php if($message != ""): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-10 mb-2 mb-md-0">
                    <input type="text" name="skill_name" class="form-control" placeholder="Enter a skill (e.g. HTML, Driving, Tailoring)" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_skill" class="btn btn-success w-100">Add Skill</button>
                </div>
            </div>
        </form>

        <h5 class="mb-3">Skill List</h5>

        <?php if($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th>Skill Name</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td><?php echo htmlspecialchars($row['skill_name']); ?></td>
                            <td>
                                <a href="skills.php?delete=<?php echo $row['skill_id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this skill?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning mb-0">No skills added yet.</div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>