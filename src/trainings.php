<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');

$lang = $_SESSION['lang'] ?? 'bn';

// Helper function to translate English numbers and months/units to Bengali
if (!function_exists('translateNumber')) {
    function translateNumber($num, $lang) {
        if ($lang == 'bn') {
            $eng_nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bng_nums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            $num = str_replace($eng_nums, $bng_nums, (string)$num);
            
            $en_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'AM', 'PM', 'Weeks', 'Week'];
            $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'এএম', 'পিএম', 'সপ্তাহ', 'সপ্তাহ'];
            $num = str_replace($en_months, $bn_months, $num);
        }
        return $num;
    }
}

$trText = [
    'bn' => [
        'hero_title' => 'জাতীয় দক্ষতা উন্নয়ন কার্যক্রম',
        'hero_sub' => 'উন্নত এবং শিল্প-উপযোগী দক্ষতার সাথে জনশক্তিকে শক্তিশালী করতে সরকারি উদ্যোগ।',
        'search_placeholder' => 'প্রশিক্ষণ প্রোগ্রাম খুঁজুন...',
        'search_btn' => 'খুঁজুন',
        'filter_all' => 'সব প্রোগ্রাম',
        'filter_it' => 'আইটি ও টেক',
        'filter_industry' => 'ইন্ডাস্ট্রি ও উৎপাদন',
        'filter_eng' => 'ইঞ্জিনিয়ারিং',
        'filter_biz' => 'ব্যবসা',
        'filter_health' => 'স্বাস্থ্যসেবা',
        'apply_btn' => 'আবেদন করুন',
        'app_modal_title' => 'প্রশিক্ষণের আবেদন',
        'app_modal_heading' => 'আপনি আবেদন করছেন:',
        'app_modal_nid_notice' => 'দয়া করে মনে রাখবেন যে সমস্ত সরকারি প্রশিক্ষণ প্রোগ্রামের জন্য এনআইডি যাচাইকরণ প্রয়োজন। এগিয়ে যাওয়ার মাধ্যমে, আপনি আপনার জীবিকা প্রোফাইলের বিবরণ প্রশিক্ষণ কর্তৃপক্ষের সাথে শেয়ার করতে সম্মত হচ্ছেন।',
        'app_modal_confirm' => 'আমি নিশ্চিত করছি যে আমি এই প্রোগ্রামের জন্য ন্যূনতম শিক্ষাগত যোগ্যতা পূরণ করি।',
        'app_modal_cancel' => 'বাতিল',
        'app_modal_submit' => 'আবেদন জমা দিন'
    ],
    'en' => [
        'hero_title' => 'National Skill Development Programs',
        'hero_sub' => 'Official government initiatives to empower the workforce with advanced, industry-ready skills.',
        'search_placeholder' => 'Search training programs...',
        'search_btn' => 'Find',
        'filter_all' => 'All Programs',
        'filter_it' => 'IT & Tech',
        'filter_industry' => 'Industry & Mfg',
        'filter_eng' => 'Engineering',
        'filter_biz' => 'Business',
        'filter_health' => 'Healthcare',
        'apply_btn' => 'Apply for Program',
        'app_modal_title' => 'Training Application',
        'app_modal_heading' => 'You are applying for:',
        'app_modal_nid_notice' => 'Please note that all government training programs require NID verification. By proceeding, you agree to share your Jibika profile details with the training authority.',
        'app_modal_confirm' => 'I confirm that I meet the minimum educational requirements for this program.',
        'app_modal_cancel' => 'Cancel',
        'app_modal_submit' => 'Submit Application'
    ]
];
$t = $trText[$lang];
?>

<link rel="stylesheet" href="assets/css/trainings.css">

<!-- Hero Section -->
<div class="training-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?php echo $t['hero_title']; ?></h1>
        <p class="lead mb-4" style="opacity: 0.9;"><?php echo $t['hero_sub']; ?></p>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <input type="text" id="searchInput" class="form-control border-0 px-4" placeholder="<?php echo $t['search_placeholder']; ?>">
                    <button class="btn btn-light px-4 text-success fw-bold" type="button" id="searchBtn"><i class="fa-solid fa-search me-2"></i><?php echo $t['search_btn']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mb-5">
    
    <!-- Filter Tabs -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
        <button class="btn btn-success rounded-pill px-4 fw-bold filter-btn" data-filter="all"><?php echo $t['filter_all']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="IT"><?php echo $t['filter_it']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Industry"><?php echo $t['filter_industry']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Engineering"><?php echo $t['filter_eng']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Business"><?php echo $t['filter_biz']; ?></button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Healthcare"><?php echo $t['filter_health']; ?></button>
    </div>

    <div class="row g-4" id="trainingsGrid">
        <?php
        $trainings = [];
        $res = $conn->query("SELECT * FROM trainings");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $trainings[] = [
                    'icon' => $row['icon'],
                    'date' => $row['start_date'],
                    'cat_bn' => $row['cat_bn'],
                    'cat_en' => $row['cat_en'],
                    'group' => $row['group_name'],
                    'title_bn' => $row['title_bn'],
                    'title_en' => $row['title_en'],
                    'loc_bn' => $row['loc_bn'],
                    'loc_en' => $row['loc_en'],
                    'duration_bn' => $row['duration_bn'],
                    'duration_en' => $row['duration_en'],
                    'desc_bn' => $row['desc_bn'],
                    'desc_en' => $row['desc_en']
                ];
            }
        }

        foreach($trainings as $t_item):
            $cat = ($lang == 'bn') ? $t_item['cat_bn'] : $t_item['cat_en'];
            $title = ($lang == 'bn') ? $t_item['title_bn'] : $t_item['title_en'];
            $desc = ($lang == 'bn') ? $t_item['desc_bn'] : $t_item['desc_en'];
            $loc = ($lang == 'bn') ? $t_item['loc_bn'] : $t_item['loc_en'];
            $duration = ($lang == 'bn') ? translateNumber($t_item['duration_bn'], 'bn') : $t_item['duration_en'];
            $date = ($lang == 'bn') ? translateNumber($t_item['date'], 'bn') : $t_item['date'];
        ?>
        <div class="col-xl-4 col-md-6 training-item" data-group="<?php echo $t_item['group']; ?>">
            <div class="card training-card h-100 border-0 shadow-sm position-relative">
                <div class="card-body p-4 p-xl-5">
                    
                    <!-- Top Ribbon -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <i class="fa-solid <?php echo $t_item['icon']; ?> fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-bold shadow-sm"><?php echo htmlspecialchars($cat); ?></span>
                    </div>
                    
                    <!-- Content -->
                    <h5 class="fw-bold text-dark mb-3 training-title-text" style="line-height: 1.4;"><?php echo htmlspecialchars($title); ?></h5>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;"><?php echo htmlspecialchars($desc); ?></p>
                    
                    <!-- Meta Info Grid -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-clock text-warning me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo htmlspecialchars($duration); ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-calendar-day text-primary me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo htmlspecialchars($date); ?></span>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="d-flex align-items-center text-muted small bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-location-dot me-2 text-danger"></i> <span class="text-truncate fw-medium"><?php echo htmlspecialchars($loc); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="card-footer bg-white border-top-0 px-4 px-xl-5 pb-4 pt-0 mt-auto">
                    <button onclick="showTrainingModal('<?php echo htmlspecialchars($title, ENT_QUOTES); ?>')" class="btn btn-outline-success w-100 rounded-pill fw-bold" style="border-width: 2px;"><?php echo $t['apply_btn']; ?></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="assets/js/trainings.js"></script>

<!-- Training Application Modal -->
<div class="modal fade" id="trainingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background-color: #006a4e !important;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-pen me-2"></i><?php echo $t['app_modal_title']; ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <h6 class="fw-bold text-dark mb-3"><?php echo $t['app_modal_heading']; ?> <br><span id="modalCourseName" class="text-success fs-5"></span></h6>
        <p class="text-muted small mb-4"><?php echo $t['app_modal_nid_notice']; ?></p>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
          <label class="form-check-label text-dark small fw-medium" for="flexCheckDefault">
            <?php echo $t['app_modal_confirm']; ?>
          </label>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-between">
        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal"><?php echo $t['app_modal_cancel']; ?></button>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" style="background-color: #006a4e;" onclick="window.location.reload();"><?php echo $t['app_modal_submit']; ?></button>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
