<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../admin_login.php");
    exit();
}

include('../config/db.php');

// DELETE USER
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id = '$id'");
    header("Location: users.php");
    exit();
}

// FETCH USERS
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Users</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow p-4">
        <?php if($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo ($row['role']=='admin') ? 'dark' : 
                                         (($row['role']=='employer') ? 'success' : 'primary'); 
                                ?>">
                                    <?php echo $row['role']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <?php if($row['role'] != 'admin'): ?>
                                    <a href="?delete=<?php echo $row['user_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this user?')">
                                       Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">No users found</div>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>