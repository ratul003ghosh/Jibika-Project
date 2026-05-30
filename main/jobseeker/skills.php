<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "info";

// Add skill
if (isset($_POST['add_skill'])) {
    $skill_name = trim($_POST['skill_name']);

    if ($skill_name == "") {
        $message = "Skill field cannot be empty!";
        $message_type = "warning";
    } else {
        $safe_skill = $conn->real_escape_string($skill_name);

        $check = $conn->query("SELECT skill_id FROM skills WHERE user_id='$user_id' AND LOWER(skill_name)=LOWER('$safe_skill') LIMIT 1");

        if ($check && $check->num_rows > 0) {
            $message = "This skill already exists.";
            $message_type = "warning";
        } else {
            $sql = "INSERT INTO skills (user_id, skill_name) VALUES ('$user_id', '$safe_skill')";

            if ($conn->query($sql)) {
                $message = "Skill added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
}

// Delete skill
if (isset($_GET['delete'])) {
    $skill_id = intval($_GET['delete']);
    $conn->query("DELETE FROM skills WHERE skill_id='$skill_id' AND user_id='$user_id'");
    header("Location: skills.php");
    exit();
}

// Fetch skills
$result = $conn->query("SELECT * FROM skills WHERE user_id='$user_id' ORDER BY skill_id DESC");

$total_skills = ($result) ? $result->num_rows : 0;
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">My Skills</h2>
            <p class="text-muted mb-0">
                Add your skills to improve job matching and recommendations.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-dark">
            Back Dashboard
        </a>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?> shadow-sm">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-2">Skill Summary</h5>
                <p class="text-muted mb-3">Total skills added</p>

                <h1 class="fw-bold text-success">
                    <?php echo $total_skills; ?>
                </h1>

                <p class="mb-0 text-muted">
                    More relevant skills help employers find you faster.
                </p>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Suggested Skills</h5>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border">Computer</span>
                    <span class="badge bg-light text-dark border">Driving</span>
                    <span class="badge bg-light text-dark border">Tailoring</span>
                    <span class="badge bg-light text-dark border">Marketing</span>
                    <span class="badge bg-light text-dark border">Data Entry</span>
                    <span class="badge bg-light text-dark border">Communication</span>
                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm p-4 mb-4">

                <h5 class="fw-bold mb-3">Add New Skill</h5>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <input 
                                type="text" 
                                name="skill_name" 
                                class="form-control" 
                                placeholder="Enter a skill e.g. HTML, Driving, Tailoring"
                                required
                            >
                        </div>

                        <div class="col-md-3 d-grid">
                            <button type="submit" name="add_skill" class="btn btn-success">
                                Add Skill
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            <div class="card border-0 shadow-sm p-4">

                <h5 class="fw-bold mb-3">Your Skill List</h5>

                <?php if ($result && $result->num_rows > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 10%;">#</th>
                                    <th>Skill Name</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $serial = 1; ?>

                                <?php while ($row = $result->fetch_assoc()): ?>

                                    <tr>
                                        <td><?php echo $serial++; ?></td>

                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo htmlspecialchars($row['skill_name']); ?>
                                            </span>
                                        </td>

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

                    </div>

                <?php else: ?>

                    <div class="alert alert-warning mb-0">
                        No skills added yet. Add your first skill to improve matching.
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>