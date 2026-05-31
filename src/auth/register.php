<?php
session_start();
include('../assets/config/db.php');

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'job_seeker') {
        header("Location: ../jobseeker/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'employer') {
        header("Location: ../employer/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}

$message = "";
$error = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $check = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $check_result = $conn->query($check);

    if ($check_result && $check_result->num_rows > 0) {
        $error = "Email already exists. Please use another email.";
    } else {
        $sql = "INSERT INTO users (full_name, email, phone, password, role)
                VALUES ('$name', '$email', '$phone', '$hashed', '$role')";

      if ($conn->query($sql)) {

    $new_user_id = $conn->insert_id;

    if ($role == 'job_seeker') {
        $conn->query("
            INSERT INTO employment_status (user_id, current_status, remarks)
            VALUES ('$new_user_id', 'unemployed', 'Default status after registration')
        ");
    }

    echo "<script>alert('Registration Successful'); window.location='login.php';</script>";
    exit();
} else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow p-4">
                    <h3 class="text-center mb-4">Register in Jibika</h3>

                    <?php if ($error != ""): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($message != ""): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Register As</label>
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="job_seeker">Job Seeker</option>
                                <option value="employer">Employer</option>
                            </select>
                        </div>

                        <button type="submit" name="register" class="btn btn-success w-100">
                            Register
                        </button>

                    </form>

                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login</a>
                    </p>

                </div>

            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>