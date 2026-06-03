<?php
session_start();
include('assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$alText = [
    'bn' => [
        'title' => 'অ্যাডমিন লগইন',
        'email_label' => 'অ্যাডমিন ইমেইল',
        'password_label' => 'পাসওয়ার্ড',
        'login_btn' => 'অ্যাডমিন হিসেবে লগইন করুন',
        'back_to_login' => 'ফিরে যান',
        'user_login_link' => 'ব্যবহারকারী লগইন',
        'err_wrong_pass' => 'ভুল পাসওয়ার্ড!',
        'err_not_found' => 'অ্যাডমিন অ্যাকাউন্ট পাওয়া যায়নি!',
    ],
    'en' => [
        'title' => 'Admin Login',
        'email_label' => 'Admin Email',
        'password_label' => 'Password',
        'login_btn' => 'Login as Admin',
        'back_to_login' => 'Back to',
        'user_login_link' => 'User Login',
        'err_wrong_pass' => 'Wrong password!',
        'err_not_found' => 'Admin account not found!',
    ]
];
$ct = $alText[$lang];

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
            $error = $ct['err_wrong_pass'];
        }
    } else {
        $error = $ct['err_not_found'];
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
                    <h3 class="text-center mb-4"><?php echo $ct['title']; ?></h3>

                    <?php if($error != ""): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $ct['email_label']; ?></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?php echo $ct['password_label']; ?></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-dark w-100">
                            <?php echo $ct['login_btn']; ?>
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        <?php echo $ct['back_to_login']; ?> <a href="auth/login.php"><?php echo $ct['user_login_link']; ?></a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>