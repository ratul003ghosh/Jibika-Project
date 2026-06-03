<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<?php
$ccText = [
    'bn' => [
        'title' => 'ক্যারিয়ার কাউন্সেলিং (Career Counseling)',
        'subtitle' => 'আপনার ক্যারিয়ার পথ পরিচালনা করতে এবং সঠিক পেশাদার সিদ্ধান্ত নিতে বিশেষজ্ঞ দিকনির্দেশনা পান।',
        'why_title' => 'ক্যারিয়ার কাউন্সেলিং কেন প্রয়োজন?',
        'why_desc' => 'আপনি কোন শিল্পে যোগ দেবেন তা নিয়ে বিভ্রান্ত সদ্য স্নাতক হন বা ক্যারিয়ার পরিবর্তনের সন্ধানকারী অভিজ্ঞ পেশাদারই হন না কেন, আমাদের কাউন্সেলিং প্রোগ্রাম আপনাকে শিল্প বিশেষজ্ঞদের সাথে সংযুক্ত করে যারা স্পষ্টতা, দিকনির্দেশনা এবং বাস্তবসম্মত পরামর্শ প্রদান করতে পারেন।',
        'point1' => 'আপনার মূল শক্তি এবং দুর্বলতাগুলো চিহ্নিত করুন।',
        'point2' => 'একটি কাস্টমাইজড ক্যারিয়ার রোডম্যাপ পান।',
        'point3' => 'শিল্প-নির্দিষ্ট অন্তর্দৃষ্টি এবং বেতনের প্রত্যাশা।',
        'point4' => 'আপনার সিভি এবং পোর্টফোলিও পর্যালোচনা।',
        'available_title' => 'উপলব্ধ কাউন্সেলরগণ',
        'specialty_it' => 'বিশেষত্ব: আইটি এবং ইঞ্জিনিয়ারিং সেক্টর',
        'specialty_hr' => 'বিশেষত্ব: কর্পোরেট ব্যবসা ও এইচআর',
        'book_btn' => 'অ্যাপয়েন্টমেন্ট বুক করুন',
        'form_title' => 'ফ্রি পরামর্শের জন্য অনুরোধ করুন',
        'form_name' => 'পূর্ণ নাম',
        'form_name_ph' => 'আপনার নাম লিখুন',
        'form_email' => 'ইমেল ঠিকানা',
        'form_status' => 'বর্তমান অবস্থা',
        'status_student' => 'শিক্ষার্থী / সদ্য স্নাতক',
        'status_employed' => 'চাকরিজীবী (পরিবর্তন করতে চান)',
        'status_unemployed' => 'বেকার',
        'form_goals' => 'সংক্ষেপে আপনার ক্যারিয়ার লক্ষ্য বর্ণনা করুন',
        'submit_btn' => 'অনুরোধ জমা দিন',
        'alert_msg' => 'আপনার অনুরোধ সফলভাবে জমা দেওয়া হয়েছে। একজন কাউন্সেলর শীঘ্রই আপনার সাথে যোগাযোগ করবেন।',
    ],
    'en' => [
        'title' => 'ক্যারিয়ার কাউন্সেলিং (Career Counseling)',
        'subtitle' => 'Get expert guidance to navigate your career path and make informed professional choices.',
        'why_title' => 'Why Career Counseling?',
        'why_desc' => 'Whether you are a fresh graduate confused about which industry to join, or an experienced professional looking to switch careers, our counseling program connects you with industry veterans who can provide clarity, direction, and actionable advice.',
        'point1' => 'Identify your core strengths and weaknesses.',
        'point2' => 'Get a customized career roadmap.',
        'point3' => 'Industry-specific insights and salary expectations.',
        'point4' => 'Review of your CV and Portfolio.',
        'available_title' => 'Available Counselors',
        'specialty_it' => 'Specialty: IT & Engineering Sectors',
        'specialty_hr' => 'Specialty: Corporate Business & HR',
        'book_btn' => 'Book Appointment',
        'form_title' => 'Request a Free Consultation',
        'form_name' => 'Full Name',
        'form_name_ph' => 'Enter your name',
        'form_email' => 'Email Address',
        'form_status' => 'Current Status',
        'status_student' => 'Student / Fresh Graduate',
        'status_employed' => 'Employed (Looking to Switch)',
        'status_unemployed' => 'Unemployed',
        'form_goals' => 'Briefly describe your career goals',
        'submit_btn' => 'Submit Request',
        'alert_msg' => 'Your request has been submitted successfully. A counselor will contact you soon.',
    ]
];
$ct = $ccText[$lang];
?>

<style>
    body { background-color: #f8f9fa; }
    .resource-header {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }
    .counselor-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .counselor-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #006a4e;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
    }
</style>

<div class="resource-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold"><i class="fa-solid fa-compass me-3"></i><?php echo $ct['title']; ?></h1>
        <p class="fs-5 opacity-75 mb-0"><?php echo $ct['subtitle']; ?></p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    <div class="row g-5">
        <div class="col-lg-7">
            <h3 class="fw-bold mb-4 text-dark"><?php echo $ct['why_title']; ?></h3>
            <p class="text-muted mb-4 fs-5"><?php echo $ct['why_desc']; ?></p>
            
            <ul class="list-unstyled mb-5">
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> <?php echo $ct['point1']; ?></li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> <?php echo $ct['point2']; ?></li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> <?php echo $ct['point3']; ?></li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> <?php echo $ct['point4']; ?></li>
            </ul>

            <h4 class="fw-bold mb-4 text-dark"><?php echo $ct['available_title']; ?></h4>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="counselor-card">
                        <div class="counselor-img">S</div>
                        <div>
                            <h5 class="fw-bold mb-1">Dr. Selim Rahman</h5>
                            <p class="text-muted mb-2 small"><?php echo $ct['specialty_it']; ?></p>
                            <button class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3"><?php echo $ct['book_btn']; ?></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="counselor-card">
                        <div class="counselor-img bg-primary">F</div>
                        <div>
                            <h5 class="fw-bold mb-1">Farhana Islam</h5>
                            <p class="text-muted mb-2 small"><?php echo $ct['specialty_hr']; ?></p>
                            <button class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3"><?php echo $ct['book_btn']; ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 border-0">
                    <h5 class="fw-bold mb-0"><?php echo $ct['form_title']; ?></h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['form_name']; ?></label>
                            <input type="text" class="form-control" placeholder="<?php echo $ct['form_name_ph']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['form_email']; ?></label>
                            <input type="email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['form_status']; ?></label>
                            <select class="form-select">
                                <option><?php echo $ct['status_student']; ?></option>
                                <option><?php echo $ct['status_employed']; ?></option>
                                <option><?php echo $ct['status_unemployed']; ?></option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted"><?php echo $ct['form_goals']; ?></label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                        <button type="button" class="btn btn-success w-100 btn-lg fw-bold rounded-pill" style="background-color: #006a4e;" onclick="alert('<?php echo addslashes($ct['alert_msg']); ?>')"><?php echo $ct['submit_btn']; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
