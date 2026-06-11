<?php
session_start();
include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$loginText = [
    'bn' => [
        'title' => 'জীবিকায় লগইন করুন',
        'email_label' => 'ইমেইল',
        'password_label' => 'পাসওয়ার্ড',
        'login_btn' => 'লগইন করুন',
        'no_account' => 'অ্যাকাউন্ট নেই?',
        'register_link' => 'নিবন্ধন করুন',
        'admin_label' => 'অ্যাডমিন?',
        'admin_login_link' => 'অ্যাডমিন লগইন',
        'err_admin_page' => 'অনুগ্রহ করে অ্যাডমিন লগইন পেজ ব্যবহার করুন।',
        'err_invalid_role' => 'অবৈধ ব্যবহারকারীর ভূমিকা!',
        'err_wrong_pass' => 'ভুল পাসওয়ার্ড!',
        'err_user_not_found' => 'ব্যবহারকারী পাওয়া যায়নি!',
    ],
    'en' => [
        'title' => 'Login to Jibika',
        'email_label' => 'Email',
        'password_label' => 'Password',
        'login_btn' => 'Login',
        'no_account' => "Don't have an account?",
        'register_link' => 'Register',
        'admin_label' => 'Admin?',
        'admin_login_link' => 'Admin Login',
        'err_admin_page' => 'Please use the admin login page.',
        'err_invalid_role' => 'Invalid user role!',
        'err_wrong_pass' => 'Wrong password!',
        'err_user_not_found' => 'User not found!',
    ]
];
$ct = $loginText[$lang];

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

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($user['role'] == 'admin') {
            $error = $ct['err_admin_page'];
        } elseif (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'job_seeker') {

                header("Location: ../jobseeker/dashboard.php");
                exit();

            } elseif ($user['role'] == 'employer') {
                header("Location: ../employer/dashboard.php");
                exit();

            } else {
                $error = $ct['err_invalid_role'];
            }

        } else {
            $error = $ct['err_wrong_pass'];
        }
    } else {
        $error = $ct['err_user_not_found'];
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
                    <h3 class="text-center mb-4"><?php echo $ct['title']; ?></h3>

                    <?php if ($error != ""): ?>
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

                        <button type="submit" name="login" class="btn btn-success w-100">
                            <?php echo $ct['login_btn']; ?>
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-1">
                        <?php echo $ct['no_account']; ?>
                        <a href="register.php"><?php echo $ct['register_link']; ?></a>
                    </p>

                    <p class="text-center mb-0">
                        <?php echo $ct['admin_label']; ?> <a href="../admin_login.php"><?php echo $ct['admin_login_link']; ?></a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>