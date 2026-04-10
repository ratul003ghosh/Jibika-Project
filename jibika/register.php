<?php 
include('includes/header.php'); 
include('includes/navbar.php'); 
include('config/db.php');

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, phone, password, role)
            VALUES ('$name', '$email', '$phone', '$hashed', '$role')";

    if($conn->query($sql)){
        echo "<script>alert('Registration Successful');</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');</script>";
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow p-4">
                    <h3 class="text-center mb-4">Register in Jibika</h3>

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

<?php include('includes/footer.php'); ?>