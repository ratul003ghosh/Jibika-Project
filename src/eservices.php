<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');
?>

<div class="container py-5 mt-4 mb-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold" style="color: #006a4e;">National E-Services Portal</h2>
            <p class="text-muted fs-5">Access essential government digital services seamlessly.</p>
            <div style="width: 80px; height: 4px; background-color: #f42a41; margin: 0 auto;"></div>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $services = [
            ["icon" => "fa-file-shield", "title" => "Online Police Clearance", "desc" => "Apply for an official police clearance certificate required for employment."],
            ["icon" => "fa-file-invoice", "title" => "e-Trade License", "desc" => "Apply for a new trade license or renew your existing business license online."],
            ["icon" => "fa-file-signature", "title" => "Online TIN Certificate", "desc" => "Register for a new Taxpayer Identification Number or download your certificate."],
            ["icon" => "fa-passport", "title" => "e-Passport Portal", "desc" => "Apply for a new machine-readable e-Passport or check application status."],
            ["icon" => "fa-plane-departure", "title" => "Expatriate Clearance (BMET)", "desc" => "Register for overseas employment and obtain BMET clearance securely."],
            ["icon" => "fa-building", "title" => "Employer Registration", "desc" => "Register your company to legally hire employees through government portals."],
            ["icon" => "fa-graduation-cap", "title" => "Skill Certification Check", "desc" => "Verify authenticity of vocational and technical training certificates."],
            ["icon" => "fa-file-invoice-dollar", "title" => "Online Tax Return Filing", "desc" => "Submit your annual income tax return digitally to the NBR."]
        ];

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
                    <button onclick="showServiceModal('<?php echo $srv['title']; ?>')" class="btn btn-outline-success btn-sm rounded-pill px-4 mt-2" style="border-color: #006a4e; color: #006a4e;">Access Service</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .btn-outline-success:hover {
        background-color: #006a4e !important;
        color: white !important;
    }
</style>

<!-- Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white" style="background-color: #006a4e !important;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-shield-halved me-2"></i>Secure Redirection</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="spinner-border text-success mb-3" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="fw-bold text-dark mb-2">Connecting to <span id="modalServiceName" class="text-success"></span></h5>
        <p class="text-muted small">You are being redirected to the secure official government portal for this service. Please ensure you have your credentials ready.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success rounded-pill px-4" style="background-color: #006a4e;" onclick="window.location.reload();">Proceed to Portal</button>
      </div>
    </div>
  </div>
</div>

<script>
function showServiceModal(serviceName) {
    document.getElementById('modalServiceName').textContent = serviceName;
    var myModal = new bootstrap.Modal(document.getElementById('serviceModal'));
    myModal.show();
}
</script>

<?php include('includes/footer.php'); ?>
