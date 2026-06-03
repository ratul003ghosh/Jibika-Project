<?php
session_start();
include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$regText = [
    'bn' => [
        'title' => 'জীবিকায় নিবন্ধন করুন',
        'name_label' => 'পূর্ণ নাম',
        'email_label' => 'ইমেইল',
        'phone_label' => 'ফোন নম্বর',
        'password_label' => 'পাসওয়ার্ড',
        'role_label' => 'নিবন্ধন করুন হিসেবে',
        'select_role' => 'ভূমিকা নির্বাচন করুন',
        'job_seeker' => 'চাকরিপ্রার্থী',
        'employer' => 'নিয়োগকর্তা',
        'register_btn' => 'নিবন্ধন করুন',
        'already_account' => 'ইতিমধ্যে অ্যাকাউন্ট আছে?',
        'login_link' => 'লগইন করুন',
        'err_email_exists' => 'ইমেইল ইতিমধ্যে বিদ্যমান। অনুগ্রহ করে অন্য ইমেইল ব্যবহার করুন।',
        'alert_success' => 'নিবন্ধন সফল হয়েছে!',
        'msg_error' => 'ত্রুটি: ',
    ],
    'en' => [
        'title' => 'Register in Jibika',
        'name_label' => 'Full Name',
        'email_label' => 'Email',
        'phone_label' => 'Phone',
        'password_label' => 'Password',
        'role_label' => 'Register As',
        'select_role' => 'Select Role',
        'job_seeker' => 'Job Seeker',
        'employer' => 'Employer',
        'register_btn' => 'Register',
        'already_account' => 'Already have an account?',
        'login_link' => 'Login',
        'err_email_exists' => 'Email already exists. Please use another email.',
        'alert_success' => 'Registration Successful',
        'msg_error' => 'Error: ',
    ]
];
$ct = $regText[$lang];

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
        $error = $ct['err_email_exists'];
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

            $alert_msg = addslashes($ct['alert_success']);
            echo "<script>alert('$alert_msg'); window.location='login.php';</script>";
            exit();
        } else {
            $error = $ct['msg_error'] . $conn->error;
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
                    <h3 class="text-center mb-4"><?php echo $ct['title']; ?></h3>

                    <?php if ($error != ""): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($message != ""): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label><?php echo $ct['name_label']; ?></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label><?php echo $ct['email_label']; ?></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label><?php echo $ct['phone_label']; ?></label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label><?php echo $ct['password_label']; ?></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label><?php echo $ct['role_label']; ?></label>
                            <select name="role" class="form-control" required>
                                <option value=""><?php echo $ct['select_role']; ?></option>
                                <option value="job_seeker"><?php echo $ct['job_seeker']; ?></option>
                                <option value="employer"><?php echo $ct['employer']; ?></option>
                            </select>
                        </div>

                        <button type="submit" name="register" class="btn btn-success w-100">
                            <?php echo $ct['register_btn']; ?>
                        </button>

                    </form>

                    <p class="text-center mt-3">
                        <?php echo $ct['already_account']; ?> <a href="login.php"><?php echo $ct['login_link']; ?></a>
                    </p>

                </div>

            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>