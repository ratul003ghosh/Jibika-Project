<?php
session_start();
include('../assets/config/db.php');

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'job_seeker') {

        $user_id = $_SESSION['user_id'];
        $check_profile = $conn->query("SELECT profile_id FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");

        if ($check_profile && $check_profile->num_rows > 0) {
            header("Location: ../jobseeker/dashboard.php");
        } else {
            header("Location: ../jobseeker/profile.php");
        }
        exit();

    } elseif ($_SESSION['role'] == 'employer') {
        header("Location: ../employer/dashboard.php");
        exit();

    } elseif ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($user['role'] == 'admin') {
            $error = "Please use the admin login page.";
        } elseif (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'job_seeker') {

                $user_id = $user['user_id'];
                $check_profile = $conn->query("SELECT profile_id FROM job_seeker_profiles WHERE user_id='$user_id' LIMIT 1");

                if ($check_profile && $check_profile->num_rows > 0) {
                    header("Location: ../jobseeker/dashboard.php");
                } else {
                    header("Location: ../jobseeker/profile.php");
                }
                exit();

            } elseif ($user['role'] == 'employer') {
                header("Location: ../employer/dashboard.php");
                exit();

            } else {
                $error = "Invalid user role!";
            }

        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "User not found!";
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
                    <h3 class="text-center mb-4">Login to Jibika</h3>

                    <?php if ($error != ""): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-success w-100">
                            Login
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-1">
                        Don't have an account?
                        <a href="register.php">Register</a>
                    </p>

                    <p class="text-center mb-0">
                        Admin? <a href="../admin_login.php">Admin Login</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>