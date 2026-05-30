<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');
?>

<div class="container-fluid px-4 px-lg-5 py-5 mt-3 mb-5">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold" style="color: #006a4e; margin-bottom: 5px;">Contact Support</h2>
            <div style="width: 60px; height: 4px; background-color: #f42a41; margin-bottom: 15px;"></div>
            <p class="text-muted fs-5">Get in touch with the Jibika Support Team for assistance with registration, services, or training programs.</p>
        </div>
    </div>

    <div class="row g-5">
        <!-- Contact Info & Map -->
        <div class="col-lg-5">
            <div class="bg-success text-white p-5 rounded-4 mb-4 shadow-sm" style="background: linear-gradient(135deg, #006a4e, #198754) !important;">
                <h3 class="fw-bold mb-4">Official Head Office</h3>
                
                <div class="d-flex mb-4 align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-location-dot fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Address</h5>
                        <p class="mb-0 text-white-50">Jibika Bhaban, 123 E-Service Road,<br>Agargaon, Dhaka-1207, Bangladesh</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4 align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-phone fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Phone (Toll Free)</h5>
                        <p class="mb-0 text-white-50">333 or 16122</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Email Support</h5>
                        <p class="mb-0 text-white-50">support@jibika.gov.bd</p>
                    </div>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div class="rounded-4 overflow-hidden shadow-sm border border-light" style="height: 300px;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.139626388484!2d90.3752541153629!3d23.77804478457585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c0b05b63391d%3A0xc62cb17f1a0af7d6!2sAgargaon%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1689255648752!5m2!1sen!2sbd" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border border-light shadow-sm p-4 p-md-5 rounded-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-regular fa-paper-plane fs-3 text-success me-3"></i>
                    <h3 class="fw-bold text-dark mb-0">Send an Inquiry</h3>
                </div>
                <hr class="mb-4 bg-light">
                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" placeholder="e.g. Hasan Mahmud" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg bg-light border-0" placeholder="hasan@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Phone Number</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" placeholder="017XXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Department <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg bg-light border-0" required>
                                <option value="" selected disabled>Select Department...</option>
                                <option>Technical Support</option>
                                <option>Employer Registration</option>
                                <option>Training & Certification</option>
                                <option>Report Fraud</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light border-0" rows="6" placeholder="Please describe your issue in detail..." required></textarea>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                            <button type="button" onclick="submitContactForm()" class="btn btn-success btn-lg px-5 fw-bold rounded-pill shadow-sm" style="background-color: #006a4e; border: none;"><i class="fa-solid fa-paper-plane me-2"></i> Submit Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Contact Success Modal -->
<div class="modal fade" id="contactSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-body text-center p-5">
        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-check fs-1"></i>
        </div>
        <h4 class="fw-bold text-dark mb-3">Message Sent Successfully!</h4>
        <p class="text-muted mb-4">Thank you for reaching out. Your inquiry has been forwarded to the respective department. A support representative will get back to you within 24-48 working hours.</p>
        <button type="button" class="btn btn-success rounded-pill px-5 fw-bold" style="background-color: #006a4e;" onclick="window.location.reload();">Done</button>
      </div>
    </div>
  </div>
</div>

<script>
function submitContactForm() {
    var myModal = new bootstrap.Modal(document.getElementById('contactSuccessModal'));
    myModal.show();
}
</script>

<?php include('includes/footer.php'); ?>
