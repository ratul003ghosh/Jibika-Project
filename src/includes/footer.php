<?php
$lang = $_SESSION['lang'] ?? 'bn';
if (!isset($path_prefix)) {
    $current_script = $_SERVER['SCRIPT_NAME'];
    $src_pos = strrpos($current_script, '/src/');
    if ($src_pos !== false) {
        $sub_path = substr($current_script, $src_pos + 5);
        $slash_count = substr_count($sub_path, '/');
        $path_prefix = str_repeat('../', $slash_count);
    } else {
        $slash_count = substr_count(ltrim($current_script, '/'), '/');
        $path_prefix = str_repeat('../', $slash_count);
    }
}
$footer_text = [
    'bn' => [
        'desc' => 'এলাকাভিত্তিক বেকারত্ব পর্যবেক্ষণ ও স্মার্ট কর্মসংস্থান প্ল্যাটফর্ম। Connecting talent with opportunity across Bangladesh.',
        'quick_links' => 'দ্রুত লিঙ্ক',
        'home' => 'হোম',
        'about' => 'আমাদের সম্পর্কে',
        'find_job' => 'চাকরি খুঁজুন',
        'register' => 'রেজিস্ট্রেশন',
        'services' => 'আমাদের পেজসমূহ',
        'eservices' => 'ই-সার্ভিস',
        'trainings' => 'প্রশিক্ষণ',
        'notice' => 'নোটিশ বোর্ড',
        'stats' => 'পরিসংখ্যান',
        'cv_guide' => 'সিভি গাইড',
        'contact' => 'যোগাযোগ',
        'stay_updated' => 'আপডেট থাকুন',
        'stay_updated_sub' => 'সর্বশেষ চাকরির অ্যালার্ট এবং ক্যারিয়ার টিপস আপনার ইনবক্সে পান।',
        'subscribe' => 'সাবস্ক্রাইব করুন',
        'email_placeholder' => 'আপনার ইমেইল ঠিকানা...',
        'copyright' => '© 2026 Jibika — জীবিকা। সর্বস্বত্ব সংরক্ষিত। গণপ্রজাতন্ত্রী বাংলাদেশ সরকার।',
        'made_in' => 'Made in Bangladesh'
    ],
    'en' => [
        'desc' => 'Area-based unemployment monitoring and smart employment platform. Connecting talent with opportunity across Bangladesh.',
        'quick_links' => 'Quick Links',
        'home' => 'Home',
        'about' => 'About Us',
        'find_job' => 'Find Jobs',
        'register' => 'Register',
        'services' => 'Our Pages',
        'eservices' => 'E-Services',
        'trainings' => 'Trainings',
        'notice' => 'Notice Board',
        'stats' => 'Statistics',
        'cv_guide' => 'CV Guide',
        'contact' => 'Contact',
        'stay_updated' => 'Stay Updated',
        'stay_updated_sub' => 'Get the latest job alerts and career tips straight to your inbox.',
        'subscribe' => 'Subscribe',
        'email_placeholder' => 'Your email address...',
        'copyright' => '© 2026 Jibika — জীবিকা. All rights reserved. Government of Bangladesh.',
        'made_in' => 'Made in Bangladesh'
    ]
];
$ft = $footer_text[$lang];
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/footer.css">
<footer style="background-color: #0f172a; color: #f8fafc; padding: 4rem 0 2rem;">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row gy-5">
            <div class="col-lg-4">
                <h3 class="fw-bold mb-3"><span style="color: #10B981;">Jibika</span> <span style="color: #f8fafc;">জীবিকা</span></h3>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 2rem;"><?php echo $ft['desc']; ?></p>
                <div class="d-flex gap-3">
                    <a href="#" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: background 0.3s;"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: background 0.3s;"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: background 0.3s;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: background 0.3s;"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-bold mb-4"><?php echo $ft['quick_links']; ?></h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li><a href="<?php echo $path_prefix; ?>index.php" class="footer-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>"><?php echo $ft['home']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>about.php" class="footer-link <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>"><?php echo $ft['about']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>jobseeker/jobs.php" class="footer-link <?php echo in_array($currentPage, ['jobs.php', 'manage_jobs.php']) ? 'active' : ''; ?>"><?php echo $ft['find_job']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>auth/register.php" class="footer-link <?php echo $currentPage == 'register.php' ? 'active' : ''; ?>"><?php echo $ft['register']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>contact.php" class="footer-link <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>"><?php echo $ft['contact']; ?></a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-bold mb-4"><?php echo $ft['services']; ?></h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li><a href="<?php echo $path_prefix; ?>eservices.php" class="footer-link <?php echo $currentPage == 'eservices.php' ? 'active' : ''; ?>"><?php echo $ft['eservices']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>trainings.php" class="footer-link <?php echo $currentPage == 'trainings.php' ? 'active' : ''; ?>"><?php echo $ft['trainings']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>notice.php" class="footer-link <?php echo $currentPage == 'notice.php' ? 'active' : ''; ?>"><?php echo $ft['notice']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>statistics.php" class="footer-link <?php echo $currentPage == 'statistics.php' ? 'active' : ''; ?>"><?php echo $ft['stats']; ?></a></li>
                    <li><a href="<?php echo $path_prefix; ?>cv_guide.php" class="footer-link <?php echo $currentPage == 'cv_guide.php' ? 'active' : ''; ?>"><?php echo $ft['cv_guide']; ?></a></li>
                </ul>
            </div>
            
            <div class="col-lg-4">
                <h5 class="fw-bold mb-4"><?php echo $ft['stay_updated']; ?></h5>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 1.5rem;"><?php echo $ft['stay_updated_sub']; ?></p>
                <form class="d-flex mb-2" id="subscribeForm" onsubmit="handleSubscribe(event)">
                    <input type="email" id="subscribeEmail" required class="form-control me-2" placeholder="<?php echo $ft['email_placeholder']; ?>" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px;">
                    <button type="submit" class="btn px-4" style="background-color: #10B981; color: white; border-radius: 8px; font-weight: 600;"><?php echo $ft['subscribe']; ?></button>
                </form>
                <div id="subscribeMessage" class="mb-3 small fw-bold" style="display:none;"></div>
                <div style="color: #94a3b8; font-size: 0.9rem; display: flex; gap: 15px; flex-wrap: wrap;">
                    <span><i class="fa-solid fa-location-dot me-2" style="color: #e2e8f0;"></i> Dhaka, Bangladesh</span>
                    <span>|</span>
                    <span><i class="fa-solid fa-envelope me-2" style="color: #e2e8f0;"></i> info@jibika.gov.bd</span>
                    <span>|</span>
                    <span><i class="fa-solid fa-phone me-2" style="color: #e2e8f0;"></i> +880-1234-567890</span>
                </div>
            </div>
        </div>
        
        <div class="mt-5 pt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0" style="color: #94a3b8; font-size: 0.95rem;"><?php echo $ft['copyright']; ?></p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <p class="mb-0" style="color: #94a3b8; font-size: 0.95rem;"><?php echo $ft['made_in']; ?></p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $path_prefix; ?>assets/js/main.js"></script>
<script>window.jibikaPathPrefix = '<?php echo $path_prefix; ?>';</script>
<script src="<?php echo $path_prefix; ?>assets/js/footer.js"></script>
</body>
</html>