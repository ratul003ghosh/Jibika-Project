<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';

$c_text = [
    'en' => [
        'title' => 'Contact Support',
        'sub' => 'Get in touch with the Jibika Support Team for assistance with registration, services, or training programs.',
        'hq' => 'Official Head Office',
        'addr_t' => 'Address',
        'addr_v' => 'Jibika Bhaban, 123 E-Service Road,<br>Agargaon, Dhaka-1207, Bangladesh',
        'phone_t' => 'Phone (Toll Free)',
        'phone_v' => '333 or 16122',
        'email_t' => 'Email Support',
        'email_v' => 'support@jibika.gov.bd',
        'form_t' => 'Send an Inquiry',
        'f_name' => 'Full Name',
        'f_email' => 'Email Address',
        'f_phone' => 'Phone Number',
        'f_dept' => 'Department',
        'f_dept_ph' => 'Select Department...',
        'd1' => 'Technical Support',
        'd2' => 'Employer Registration',
        'd3' => 'Training & Certification',
        'd4' => 'Report Fraud',
        'f_msg' => 'Message',
        'btn_sub' => 'Submit Message',
        'modal_t' => 'Message Sent Successfully!',
        'modal_sub' => 'Thank you for reaching out. Your inquiry has been forwarded to the respective department. A support representative will get back to you within 24-48 working hours.',
        'modal_btn' => 'Done',
        'ph_name' => 'e.g. Hasan Mahmud',
        'ph_email' => 'hasan@example.com',
        'ph_phone' => '017XXXXXXXX',
        'ph_msg' => 'Please describe your issue in detail...'
    ],
    'bn' => [
        'title' => 'যোগাযোগ করুন',
        'sub' => 'নিবন্ধন, পরিষেবা বা প্রশিক্ষণ কর্মসূচির সহায়তার জন্য জীবিকা সাপোর্ট টিমের সাথে যোগাযোগ করুন।',
        'hq' => 'প্রধান কার্যালয়',
        'addr_t' => 'ঠিকানা',
        'addr_v' => 'জীবিকা ভবন, ১২৩ ই-সার্ভিস রোড,<br>আগারগাঁও, ঢাকা-১২০৭, বাংলাদেশ',
        'phone_t' => 'ফোন (টোল ফ্রি)',
        'phone_v' => '৩৩৩ বা ১৬১২২',
        'email_t' => 'ইমেইল সাপোর্ট',
        'email_v' => 'support@jibika.gov.bd',
        'form_t' => 'একটি জিজ্ঞাসা পাঠান',
        'f_name' => 'পুরো নাম',
        'f_email' => 'ইমেইল ঠিকানা',
        'f_phone' => 'ফোন নম্বর',
        'f_dept' => 'বিভাগ',
        'f_dept_ph' => 'বিভাগ নির্বাচন করুন...',
        'd1' => 'কারিগরি সহায়তা',
        'd2' => 'নিয়োগকর্তা নিবন্ধন',
        'd3' => 'প্রশিক্ষণ ও সার্টিফিকেশন',
        'd4' => 'প্রতারণা রিপোর্ট করুন',
        'f_msg' => 'বার্তা',
        'btn_sub' => 'বার্তা জমা দিন',
        'modal_t' => 'সফলভাবে বার্তা পাঠানো হয়েছে!',
        'modal_sub' => 'যোগাযোগ করার জন্য ধন্যবাদ। আপনার জিজ্ঞাসাটি সংশ্লিষ্ট বিভাগে ফরোয়ার্ড করা হয়েছে। ২৪-৪৮ কর্মঘণ্টার মধ্যে একজন সাপোর্ট প্রতিনিধি আপনার সাথে যোগাযোগ করবেন।',
        'modal_btn' => 'সম্পন্ন',
        'ph_name' => 'উদাঃ হাসান মাহমুদ',
        'ph_email' => 'hasan@example.com',
        'ph_phone' => '০১৭১২XXXXXX',
        'ph_msg' => 'অনুগ্রহ করে আপনার সমস্যা বিস্তারিত বর্ণনা করুন...'
    ]
];
$c = $c_text[$lang];

include('includes/header.php');
include('includes/navbar.php');
?>

<div class="container-fluid px-4 px-lg-5 py-5 mt-3 mb-5">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold" style="color: #006a4e; margin-bottom: 5px;"><?php echo $c['title']; ?></h2>
            <div style="width: 60px; height: 4px; background-color: #f42a41; margin-bottom: 15px;"></div>
            <p class="text-muted fs-5"><?php echo $c['sub']; ?></p>
        </div>
    </div>

    <div class="row g-5">
        <!-- Contact Info & Map -->
        <div class="col-lg-5">
            <div class="bg-success text-white p-5 rounded-4 mb-4 shadow-sm" style="background: linear-gradient(135deg, #006a4e, #198754) !important;">
                <h3 class="fw-bold mb-4"><?php echo $c['hq']; ?></h3>
                
                <div class="d-flex mb-4 align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-location-dot fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1"><?php echo $c['addr_t']; ?></h5>
                        <p class="mb-0 text-white-50"><?php echo $c['addr_v']; ?></p>
                    </div>
                </div>
                
                <div class="d-flex mb-4 align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-phone fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1"><?php echo $c['phone_t']; ?></h5>
                        <p class="mb-0 text-white-50"><?php echo $c['phone_v']; ?></p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-white text-success rounded-circle d-flex justify-content-center align-items-center me-3 mt-1" style="width: 40px; height: 40px; flex-shrink:0;">
                        <i class="fa-solid fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1"><?php echo $c['email_t']; ?></h5>
                        <p class="mb-0 text-white-50"><?php echo $c['email_v']; ?></p>
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
                    <h3 class="fw-bold text-dark mb-0"><?php echo $c['form_t']; ?></h3>
                </div>
                <hr class="mb-4 bg-light">
                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase"><?php echo $c['f_name']; ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" placeholder="<?php echo $c['ph_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase"><?php echo $c['f_email']; ?> <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg bg-light border-0" placeholder="<?php echo $c['ph_email']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase"><?php echo $c['f_phone']; ?></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" placeholder="<?php echo $c['ph_phone']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase"><?php echo $c['f_dept']; ?> <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg bg-light border-0" required>
                                <option value="" selected disabled><?php echo $c['f_dept_ph']; ?></option>
                                <option><?php echo $c['d1']; ?></option>
                                <option><?php echo $c['d2']; ?></option>
                                <option><?php echo $c['d3']; ?></option>
                                <option><?php echo $c['d4']; ?></option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase"><?php echo $c['f_msg']; ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light border-0" rows="6" placeholder="<?php echo $c['ph_msg']; ?>" required></textarea>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                            <button type="button" onclick="submitContactForm()" class="btn btn-success btn-lg px-5 fw-bold rounded-pill shadow-sm" style="background-color: #006a4e; border: none;"><i class="fa-solid fa-paper-plane me-2"></i> <?php echo $c['btn_sub']; ?></button>
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
        <h4 class="fw-bold text-dark mb-3"><?php echo $c['modal_t']; ?></h4>
        <p class="text-muted mb-4"><?php echo $c['modal_sub']; ?></p>
        <button type="button" class="btn btn-success rounded-pill px-5 fw-bold" style="background-color: #006a4e;" onclick="window.location.reload();"><?php echo $c['modal_btn']; ?></button>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/contact.js"></script>

<?php include('includes/footer.php'); ?>
