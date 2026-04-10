<?php
session_start();
include('config/db.php');

$error = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='admin' LIMIT 1";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Admin account not found!";
    }
}
?>

<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow p-4">
                    <h3 class="text-center mb-4">Admin Login</h3>

                    <?php if($error != ""): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Admin Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-dark w-100">
                            Login as Admin
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Back to <a href="login.php">User Login</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>