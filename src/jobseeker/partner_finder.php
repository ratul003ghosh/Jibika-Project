<?php
session_start();
include('../includes/header.php');
include('../includes/navbar.php');

$lang = $_SESSION['lang'] ?? 'bn';

$pfText = [
    'bn' => [
        'module' => 'উদ্যোক্তা মডিউল',
        'title' => 'উদ্যোক্তাদের জন্য পার্টনার ফাইন্ডার',
        'subtitle' => 'একত্রে একটি ছোট ব্যবসা শুরু করতে পরিপূরক দক্ষতা, ভাগ করা আগ্রহ বা বিনিয়োগ সহায়তা সম্পন্ন মানুষ খুঁজুন।',
        'form_title' => 'আপনার ব্যবসার ধারণা শেয়ার করুন',
        'label_idea' => 'ব্যবসার ধারণা',
        'placeholder_idea' => 'আপনার ব্যবসার ধারণা বর্ণনা করুন...',
        'label_skills' => 'আপনার দক্ষতাসমূহ',
        'placeholder_skills' => 'উদাহরণ: রান্না, মার্কেটিং, ডিজাইন',
        'skills_note' => 'কমা দিয়ে একাধিক দক্ষতা আলাদা করুন।',
        'label_location' => 'পছন্দসই অবস্থান',
        'placeholder_location' => 'উদাহরণ: ঢাকা',
        'label_partner_type' => 'আপনি কী ধরনের পার্টনার খুঁজছেন?',
        'complementary' => 'পরিপূরক দক্ষতা',
        'similar' => 'অনুরূপ আগ্রহ',
        'investment' => 'বিনিয়োগ অংশীদার',
        'technical' => 'প্রযুক্তিগত দক্ষতা',
        'btn_submit' => 'পার্টনার খুঁজুন',
        'result_title' => 'সম্ভাব্য অংশীদারগণ',
        'result_count' => 'টি ফলাফল পাওয়া গেছে',
        'sample_sugg' => 'নমুনা পার্টনারদের সুপারিশ দেখানো হচ্ছে',
        'connect_btn' => 'যোগাযোগ করুন',
        'empty_title' => 'কোনো অংশীদার পাওয়া যায়নি',
        'empty_desc' => 'আরও ফলাফল দেখতে দক্ষতা, অবস্থান বা অংশীদারের পছন্দ পরিবর্তন করে চেষ্টা করুন।',
        'loc_label' => 'অবস্থান:',
        'idea_label' => 'ব্যবসার ধারণা:',
        'skills_label' => 'দক্ষতাসমূহ:',
    ],
    'en' => [
        'module' => 'Entrepreneurs Module',
        'title' => 'Partner Finder for Entrepreneurs',
        'subtitle' => 'Find people with complementary skills, shared interests, or investment support to start a small business together.',
        'form_title' => 'Share Your Business Idea',
        'label_idea' => 'Business Idea',
        'placeholder_idea' => 'Describe your business idea...',
        'label_skills' => 'Your Skills',
        'placeholder_skills' => 'Example: cooking, marketing, design',
        'skills_note' => 'Separate multiple skills with commas.',
        'label_location' => 'Preferred Location',
        'placeholder_location' => 'Example: Dhaka',
        'label_partner_type' => 'What kind of partner are you looking for?',
        'complementary' => 'Complementary Skills',
        'similar' => 'Similar Interests',
        'investment' => 'Investment Partner',
        'technical' => 'Technical Expertise',
        'btn_submit' => 'Find Partners',
        'result_title' => 'Potential Partners',
        'result_count' => 'result(s) found',
        'sample_sugg' => 'Showing sample partner suggestions',
        'connect_btn' => 'Connect',
        'empty_title' => 'No partners found',
        'empty_desc' => 'Try changing skills, location, or partner preferences to see more matches.',
        'loc_label' => 'Location:',
        'idea_label' => 'Business Idea:',
        'skills_label' => 'Skills:',
    ]
];
$ct = $pfText[$lang];

$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$businessIdea = trim($_POST['business_idea'] ?? '');
$skillsInput = trim($_POST['skills'] ?? '');
$location = trim($_POST['location'] ?? '');
$preferences = $_POST['preferences'] ?? [];

$allPartners = [
    [
        'name' => $lang == 'bn' ? 'রহিম উদ্দিন' : 'Rahim Uddin',
        'location' => $lang == 'bn' ? 'ঢাকা' : 'Dhaka',
        'idea' => $lang == 'bn' ? 'খাবার সরবরাহ এবং হোম কিচেন ব্যবসা' : 'Food delivery and home kitchen business',
        'skills' => $lang == 'bn' ? ['মার্কেটিং', 'বিক্রয়', 'ডেলিভারি ব্যবস্থাপনা'] : ['Marketing', 'Sales', 'Delivery Management'],
        'preference_tags' => ['complementary', 'investment']
    ],
    [
        'name' => $lang == 'bn' ? 'নুসরাত জাহান' : 'Nusrat Jahan',
        'location' => $lang == 'bn' ? 'চট্টগ্রাম' : 'Chattogram',
        'idea' => $lang == 'bn' ? 'অনলাইন কাপড়ের দোকান ও বুটিক হাউস' : 'Online clothing and boutique store',
        'skills' => $lang == 'bn' ? ['ডিজাইন', 'ফেসবুক মার্কেটিং', 'কাস্টমার হ্যান্ডলিং'] : ['Design', 'Facebook Marketing', 'Customer Handling'],
        'preference_tags' => ['similar', 'complementary']
    ],
    [
        'name' => $lang == 'bn' ? 'সাব্বির হাসান' : 'Sabbir Hasan',
        'location' => $lang == 'bn' ? 'খুলনা' : 'Khulna',
        'idea' => $lang == 'bn' ? 'কৃষিভিত্তিক ক্ষুদ্র ব্যবসা' : 'Agro-based small business',
        'skills' => $lang == 'bn' ? ['সাপ্লাই চেইন', 'মাঠ পর্যায়ের কার্যক্রম', 'বিনিয়োগ পরিকল্পনা'] : ['Supply Chain', 'Field Operations', 'Investment Planning'],
        'preference_tags' => ['investment', 'similar']
    ],
    [
        'name' => $lang == 'bn' ? 'তানিয়া আক্তার' : 'Tania Akter',
        'location' => $lang == 'bn' ? 'রাজশাহী' : 'Rajshahi',
        'idea' => $lang == 'bn' ? 'ডিজিটাল সেবা স্টার্টআপ' : 'Digital service startup',
        'skills' => $lang == 'bn' ? ['ওয়েব ডেভেলপমেন্ট', 'ইউআই ডিজাইন', 'টেকনিক্যাল সাপোর্ট'] : ['Web Development', 'UI Design', 'Technical Support'],
        'preference_tags' => ['technical', 'complementary']
    ]
];

$partners = $allPartners;

if ($submitted) {
    $userSkills = array_filter(array_map('trim', explode(',', strtolower($skillsInput))));

    $partners = array_values(array_filter($allPartners, function ($partner) use ($preferences, $location, $userSkills) {
        $locationMatch = true;
        if ($location !== '') {
            $locationMatch = stripos($partner['location'], $location) !== false;
        }

        $preferenceMatch = true;
        if (!empty($preferences)) {
            $preferenceMatch = count(array_intersect($preferences, $partner['preference_tags'])) > 0;
        }

        $skillMatch = true;
        if (!empty($userSkills)) {
            $partnerSkillsLower = array_map('strtolower', $partner['skills']);
            $skillMatch = count(array_intersect($userSkills, $partnerSkillsLower)) === 0;
        }

        return $locationMatch && $preferenceMatch && $skillMatch;
    }));
}
?>

<link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/partner_finder.css">

<div class="partner-page py-5">
    <div class="container">
        <div class="partner-hero text-center mb-4">
            <span class="partner-badge"><?php echo $ct['module']; ?></span>
            <h1 class="partner-title mt-3"><?php echo $ct['title']; ?></h1>
            <p class="partner-subtitle mb-0"><?php echo $ct['subtitle']; ?></p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="partner-form-card">
                    <h3 class="card-title mb-3"><?php echo $ct['form_title']; ?></h3>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="business-idea" class="form-label"><?php echo $ct['label_idea']; ?></label>
                            <textarea id="business-idea" name="business_idea" class="form-control" rows="4" placeholder="<?php echo $ct['placeholder_idea']; ?>" required><?php echo htmlspecialchars($businessIdea); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="skills" class="form-label"><?php echo $ct['label_skills']; ?></label>
                            <input type="text" id="skills" name="skills" class="form-control" placeholder="<?php echo $ct['placeholder_skills']; ?>" value="<?php echo htmlspecialchars($skillsInput); ?>" required>
                            <div class="form-text"><?php echo $ct['skills_note']; ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label"><?php echo $ct['label_location']; ?></label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="<?php echo $ct['placeholder_location']; ?>" value="<?php echo htmlspecialchars($location); ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block mb-2"><?php echo $ct['label_partner_type']; ?></label>
                            <div class="preference-grid">
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="complementary" <?php echo in_array('complementary', $preferences) ? 'checked' : ''; ?>> <?php echo $ct['complementary']; ?></label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="similar" <?php echo in_array('similar', $preferences) ? 'checked' : ''; ?>> <?php echo $ct['similar']; ?></label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="investment" <?php echo in_array('investment', $preferences) ? 'checked' : ''; ?>> <?php echo $ct['investment']; ?></label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="technical" <?php echo in_array('technical', $preferences) ? 'checked' : ''; ?>> <?php echo $ct['technical']; ?></label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success px-4"><?php echo $ct['btn_submit']; ?></button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="partner-results-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h3 class="card-title mb-0"><?php echo $ct['result_title']; ?></h3>
                        <?php if ($submitted): ?>
                            <span class="result-count"><?php echo htmlspecialchars($partners ? count($partners) : 0) . ' ' . $ct['result_count']; ?></span>
                        <?php else: ?>
                            <span class="result-count"><?php echo $ct['sample_sugg']; ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($partners)): ?>
                        <div class="row g-3">
                            <?php foreach ($partners as $partner): ?>
                                <div class="col-md-6">
                                    <div class="partner-card h-100">
                                        <h4><?php echo htmlspecialchars($partner['name']); ?></h4>
                                        <p><strong><?php echo $ct['loc_label']; ?></strong> <?php echo htmlspecialchars($partner['location']); ?></p>
                                        <p><strong><?php echo $ct['idea_label']; ?></strong> <?php echo htmlspecialchars($partner['idea']); ?></p>
                                        <p><strong><?php echo $ct['skills_label']; ?></strong> <?php echo htmlspecialchars(implode(', ', $partner['skills'])); ?></p>
                                        <button type="button" class="btn btn-outline-primary btn-sm"><?php echo $ct['connect_btn']; ?></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <h4><?php echo $ct['empty_title']; ?></h4>
                            <p class="mb-0"><?php echo $ct['empty_desc']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>