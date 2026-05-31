<?php
$lang = $_SESSION['lang'] ?? 'bn';
$footer_text = [
    'bn' => [
        'desc' => 'এলাকাভিত্তিক বেকারত্ব পর্যবেক্ষণ ও স্মার্ট কর্মসংস্থান প্ল্যাটফর্ম। গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের ভাবনায় তৈরি।',
        'quick_links' => 'দ্রুত লিঙ্ক',
        'home' => 'হোম',
        'about' => 'আমাদের সম্পর্কে',
        'find_job' => 'চাকরি খুঁজুন',
        'register' => 'রেজিস্ট্রেশন',
        'services' => 'সেবাসমূহ',
        'job_list' => 'চাকরির তালিকা',
        'skill_map' => 'স্কিল ম্যাপিং',
        'partner' => 'পার্টনার ফাইন্ডার',
        'reports' => 'রিপোর্ট ও বিশ্লেষণ',
        'contact' => 'যোগাযোগ',
        'address' => '📍 ঢাকা, বাংলাদেশ',
        'time' => '🕐 রবি - বৃহস্পতি: সকাল ৯টা - বিকাল ৫টা',
        'copyright' => '© 2026 Jibika - জীবিকা। সর্বস্বত্ব সংরক্ষিত। গণপ্রজাতন্ত্রী বাংলাদেশ সরকার।'
    ],
    'en' => [
        'desc' => 'Area-based unemployment monitoring and smart employment platform. Conceived by the Government of Bangladesh.',
        'quick_links' => 'Quick Links',
        'home' => 'Home',
        'about' => 'About Us',
        'find_job' => 'Find a Job',
        'register' => 'Register',
        'services' => 'Services',
        'job_list' => 'Job Listings',
        'skill_map' => 'Skill Mapping',
        'partner' => 'Partner Finder',
        'reports' => 'Reports & Analysis',
        'contact' => 'Contact Us',
        'address' => '📍 Dhaka, Bangladesh',
        'time' => '🕐 Sun - Thu: 9 AM - 5 PM',
        'copyright' => '© 2026 Jibika. All rights reserved. Government of the People\'s Republic of Bangladesh.'
    ]
];
$ft = $footer_text[$lang];
?>
<footer class="site-footer-v2">
    <div class="footer-top">
        <div class="container-fluid px-4 px-xl-5">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <h3><span style="color:#3B82F6;">Jibika</span> <span style="color:#F59E0B;">জীবিকা</span></h3>
                        <p><?php echo $ft['desc']; ?></p>
                        <div class="footer-social">
                            <a href="mailto:info@jibika.gov.bd" title="Email"><i class="fa-solid fa-envelope"></i></a>
                            <a href="https://facebook.com" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://linkedin.com" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="https://jibika.gov.bd" title="Website"><i class="fa-solid fa-globe"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h5><?php echo $ft['quick_links']; ?></h5>
                    <ul>
                        <li><a href="/index.php"><?php echo $ft['home']; ?></a></li>
                        <li><a href="/about.php"><?php echo $ft['about']; ?></a></li>
                        <li><a href="/jobseeker/jobs.php"><?php echo $ft['find_job']; ?></a></li>
                        <li><a href="/auth/register.php"><?php echo $ft['register']; ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h5><?php echo $ft['services']; ?></h5>
                    <ul>
                        <li><a href="/jobseeker/jobs.php"><?php echo $ft['job_list']; ?></a></li>
                        <li><a href="/jobseeker/skills.php"><?php echo $ft['skill_map']; ?></a></li>
                        <li><a href="/jobseeker/partner_finder.php"><?php echo $ft['partner']; ?></a></li>
                        <li><a href="/admin/reports.php"><?php echo $ft['reports']; ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5><?php echo $ft['contact']; ?></h5>
                    <ul>
                        <li><?php echo $ft['address']; ?></li>
                        <li>📞 +880-1234-567890</li>
                        <li>✉️ info@jibika.gov.bd</li>
                        <li><?php echo $ft['time']; ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container-fluid px-4 px-xl-5">
            <div class="row align-items-center">
                <div class="col-md-6"><p><?php echo $ft['copyright']; ?></p></div>
                <div class="col-md-6 text-md-end"><p>DBMS Project | Built with ❤️ for Bangladesh</p></div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>