<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: ../auth/login.php");
    exit();
}

include('../assets/config/db.php');

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'bn';

$skText = [
    'bn' => [
        'title' => 'আমার দক্ষতাসমূহ',
        'subtitle' => 'চাকরির মিল এবং সুপারিশ উন্নত করতে আপনার দক্ষতা যুক্ত করুন।',
        'back_btn' => 'ড্যাশবোর্ডে ফিরে যান',
        'summary_title' => 'দক্ষতার সারসংক্ষেপ',
        'total_skills' => 'মোট যুক্ত করা দক্ষতা',
        'summary_note' => 'আরও প্রাসঙ্গিক দক্ষতা নিয়োগকর্তাদের আপনাকে দ্রুত খুঁজে পেতে সাহায্য করে।',
        'suggested' => 'প্রস্তাবিত দক্ষতাসমূহ',
        'add_new' => 'নতুন দক্ষতা যুক্ত করুন',
        'placeholder' => 'একটি দক্ষতা লিখুন যেমন HTML, ড্রাইভিং, সেলাই',
        'add_btn' => 'দক্ষতা যুক্ত করুন',
        'list_title' => 'আপনার দক্ষতার তালিকা',
        'th_serial' => '#',
        'th_name' => 'দক্ষতার নাম',
        'th_action' => 'পদক্ষেপ',
        'empty_msg' => 'এখনও কোনো দক্ষতা যুক্ত করা হয়নি। মিল উন্নত করতে আপনার প্রথম দক্ষতাটি যুক্ত করুন।',
        'btn_delete' => 'মুছে ফেলুন',
        'confirm_delete' => 'আপনি কি নিশ্চিত যে এই দক্ষতাটি মুছে ফেলতে চান?',
        'err_empty' => 'দক্ষতার ক্ষেত্রটি খালি হতে পারে না!',
        'err_exists' => 'এই দক্ষতাটি ইতিমধ্যে বিদ্যমান আছে।',
        'success_added' => 'দক্ষতা সফলভাবে যুক্ত হয়েছে!',
        'sug_comp' => 'কম্পিউটার',
        'sug_driv' => 'ড্রাইভিং',
        'sug_tail' => 'সেলাই',
        'sug_mark' => 'মার্কেটিং',
        'sug_data' => 'ডাটা এন্ট্রি',
        'sug_comm' => 'যোগাযোগ',
    ],
    'en' => [
        'title' => 'My Skills',
        'subtitle' => 'Add your skills to improve job matching and recommendations.',
        'back_btn' => 'Back Dashboard',
        'summary_title' => 'Skill Summary',
        'total_skills' => 'Total skills added',
        'summary_note' => 'More relevant skills help employers find you faster.',
        'suggested' => 'Suggested Skills',
        'add_new' => 'Add New Skill',
        'placeholder' => 'Enter a skill e.g. HTML, Driving, Tailoring',
        'add_btn' => 'Add Skill',
        'list_title' => 'Your Skill List',
        'th_serial' => '#',
        'th_name' => 'Skill Name',
        'th_action' => 'Action',
        'empty_msg' => 'No skills added yet. Add your first skill to improve matching.',
        'btn_delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this skill?',
        'err_empty' => 'Skill field cannot be empty!',
        'err_exists' => 'This skill already exists.',
        'success_added' => 'Skill added successfully!',
        'sug_comp' => 'Computer',
        'sug_driv' => 'Driving',
        'sug_tail' => 'Tailoring',
        'sug_mark' => 'Marketing',
        'sug_data' => 'Data Entry',
        'sug_comm' => 'Communication',
    ]
];
$ct = $skText[$lang];

$message = "";
$message_type = "info";

// Add skill
if (isset($_POST['add_skill'])) {
    $skill_name = trim($_POST['skill_name']);

    if ($skill_name == "") {
        $message = $ct['err_empty'];
        $message_type = "warning";
    } else {
        $safe_skill = $conn->real_escape_string($skill_name);

        $check = $conn->query("SELECT skill_id FROM skills WHERE user_id='$user_id' AND LOWER(skill_name)=LOWER('$safe_skill') LIMIT 1");

        if ($check && $check->num_rows > 0) {
            $message = $ct['err_exists'];
            $message_type = "warning";
        } else {
            $sql = "INSERT INTO skills (user_id, skill_name) VALUES ('$user_id', '$safe_skill')";

            if ($conn->query($sql)) {
                $message = $ct['success_added'];
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
            <h2 class="fw-bold mb-1"><?php echo $ct['title']; ?></h2>
            <p class="text-muted mb-0">
                <?php echo $ct['subtitle']; ?>
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-dark">
            <?php echo $ct['back_btn']; ?>
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
                <h5 class="fw-bold mb-2"><?php echo $ct['summary_title']; ?></h5>
                <p class="text-muted mb-3"><?php echo $ct['total_skills']; ?></p>

                <h1 class="fw-bold text-success">
                    <?php echo $total_skills; ?>
                </h1>

                <p class="mb-0 text-muted">
                    <?php echo $ct['summary_note']; ?>
                </p>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3"><?php echo $ct['suggested']; ?></h5>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_comp']; ?></span>
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_driv']; ?></span>
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_tail']; ?></span>
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_mark']; ?></span>
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_data']; ?></span>
                    <span class="badge bg-light text-dark border"><?php echo $ct['sug_comm']; ?></span>
                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm p-4 mb-4">

                <h5 class="fw-bold mb-3"><?php echo $ct['add_new']; ?></h5>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <input 
                                type="text" 
                                name="skill_name" 
                                class="form-control" 
                                placeholder="<?php echo $ct['placeholder']; ?>"
                                required
                            >
                        </div>

                        <div class="col-md-3 d-grid">
                            <button type="submit" name="add_skill" class="btn btn-success">
                                <?php echo $ct['add_btn']; ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            <div class="card border-0 shadow-sm p-4">

                <h5 class="fw-bold mb-3"><?php echo $ct['list_title']; ?></h5>

                <?php if ($result && $result->num_rows > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 10%;"><?php echo $ct['th_serial']; ?></th>
                                    <th><?php echo $ct['th_name']; ?></th>
                                    <th style="width: 20%;"><?php echo $ct['th_action']; ?></th>
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
                                               onclick="return confirm('<?php echo addslashes($ct['confirm_delete']); ?>')">
                                                <?php echo $ct['btn_delete']; ?>
                                            </a>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>
                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="alert alert-warning mb-0">
                        <?php echo $ct['empty_msg']; ?>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>