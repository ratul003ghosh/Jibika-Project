<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';

$e_text = [
    'en' => [
        'title' => 'National E-Services Portal',
        'sub' => 'Access essential government digital services seamlessly.',
        'btn' => 'Access Service',
        'modal_title' => 'Secure Redirection',
        'modal_h' => 'Connecting to',
        'modal_sub' => 'You are being redirected to the secure official government portal for this service. Please ensure you have your credentials ready.',
        'modal_cancel' => 'Cancel',
        'modal_proceed' => 'Proceed to Portal'
    ],
    'bn' => [
        'title' => 'জাতীয় ই-সেবা পোর্টাল',
        'sub' => 'গুরুত্বপূর্ণ সরকারি ডিজিটাল সেবাসমূহ নির্বিঘ্নে গ্রহণ করুন।',
        'btn' => 'সেবা গ্রহণ করুন',
        'modal_title' => 'নিরাপদ রিডাইরেকশন',
        'modal_h' => 'সংযুক্ত হচ্ছে',
        'modal_sub' => 'আপনাকে এই সেবার জন্য সরকারি নিরাপদ পোর্টালে রিডাইরেক্ট করা হচ্ছে। অনুগ্রহ করে আপনার ক্রেডেনশিয়াল প্রস্তুত রাখুন।',
        'modal_cancel' => 'বাতিল করুন',
        'modal_proceed' => 'পোর্টালে যান'
    ]
];
$et = $e_text[$lang];
?>

?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>
<link rel="stylesheet" href="assets/css/eservices.css">

<div class="container py-5 mt-4 mb-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold" style="color: #006a4e;"><?php echo $et['title']; ?></h2>
            <p class="text-muted fs-5"><?php echo $et['sub']; ?></p>
            <div style="width: 80px; height: 4px; background-color: #f42a41; margin: 0 auto;"></div>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $services = [];
        $res = $conn->query("SELECT * FROM eservices");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $services[] = [
                    'icon' => $row['icon'],
                    'title' => ($lang == 'bn') ? $row['title_bn'] : $row['title_en'],
                    'desc' => ($lang == 'bn') ? $row['desc_bn'] : $row['desc_en']
                ];
            }
        }

        foreach($services as $srv):
        ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fa-solid <?php echo $srv['icon']; ?> text-success fs-1"></i>
                    </div>
                    <h5 class="fw-bold"><?php echo $srv['title']; ?></h5>
                    <p class="text-muted small"><?php echo $srv['desc']; ?></p>
                    <button onclick="showServiceModal('<?php echo htmlspecialchars($srv['title'], ENT_QUOTES); ?>')" class="btn btn-outline-success btn-sm rounded-pill px-4 mt-2" style="border-color: #006a4e; color: #006a4e;"><?php echo $et['btn']; ?></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>



<!-- Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white" style="background-color: #006a4e !important;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-shield-halved me-2"></i><?php echo $et['modal_title']; ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="spinner-border text-success mb-3" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="fw-bold text-dark mb-2"><?php echo $et['modal_h']; ?> <span id="modalServiceName" class="text-success"></span></h5>
        <p class="text-muted small"><?php echo $et['modal_sub']; ?></p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal"><?php echo $et['modal_cancel']; ?></button>
        <button type="button" class="btn btn-success rounded-pill px-4" style="background-color: #006a4e;" onclick="window.location.reload();"><?php echo $et['modal_proceed']; ?></button>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/eservices.js"></script>

<?php include('includes/footer.php'); ?>
