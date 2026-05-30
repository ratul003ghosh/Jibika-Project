<?php
session_start();
include('../includes/header.php');
include('../includes/navbar.php');

$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$businessIdea = trim($_POST['business_idea'] ?? '');
$skillsInput = trim($_POST['skills'] ?? '');
$location = trim($_POST['location'] ?? '');
$preferences = $_POST['preferences'] ?? [];

$allPartners = [
    [
        'name' => 'Rahim Uddin',
        'location' => 'Dhaka',
        'idea' => 'Food delivery and home kitchen business',
        'skills' => ['Marketing', 'Sales', 'Delivery Management'],
        'preference_tags' => ['complementary', 'investment']
    ],
    [
        'name' => 'Nusrat Jahan',
        'location' => 'Chattogram',
        'idea' => 'Online clothing and boutique store',
        'skills' => ['Design', 'Facebook Marketing', 'Customer Handling'],
        'preference_tags' => ['similar', 'complementary']
    ],
    [
        'name' => 'Sabbir Hasan',
        'location' => 'Khulna',
        'idea' => 'Agro-based small business',
        'skills' => ['Supply Chain', 'Field Operations', 'Investment Planning'],
        'preference_tags' => ['investment', 'similar']
    ],
    [
        'name' => 'Tania Akter',
        'location' => 'Rajshahi',
        'idea' => 'Digital service startup',
        'skills' => ['Web Development', 'UI Design', 'Technical Support'],
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

<link rel="stylesheet" href="/assets/css/partner_finder.css">

<div class="partner-page py-5">
    <div class="container">
        <div class="partner-hero text-center mb-4">
            <span class="partner-badge">Entrepreneurs Module</span>
            <h1 class="partner-title mt-3">Partner Finder for Entrepreneurs</h1>
            <p class="partner-subtitle mb-0">Find people with complementary skills, shared interests, or investment support to start a small business together.</p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="partner-form-card">
                    <h3 class="card-title mb-3">Share Your Business Idea</h3>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="business-idea" class="form-label">Business Idea</label>
                            <textarea id="business-idea" name="business_idea" class="form-control" rows="4" placeholder="Describe your business idea..." required><?php echo htmlspecialchars($businessIdea); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="skills" class="form-label">Your Skills</label>
                            <input type="text" id="skills" name="skills" class="form-control" placeholder="Example: cooking, marketing, design" value="<?php echo htmlspecialchars($skillsInput); ?>" required>
                            <div class="form-text">Separate multiple skills with commas.</div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Preferred Location</label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="Example: Dhaka" value="<?php echo htmlspecialchars($location); ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block mb-2">What kind of partner are you looking for?</label>
                            <div class="preference-grid">
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="complementary" <?php echo in_array('complementary', $preferences) ? 'checked' : ''; ?>> Complementary Skills</label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="similar" <?php echo in_array('similar', $preferences) ? 'checked' : ''; ?>> Similar Interests</label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="investment" <?php echo in_array('investment', $preferences) ? 'checked' : ''; ?>> Investment Partner</label>
                                <label class="preference-item"><input type="checkbox" name="preferences[]" value="technical" <?php echo in_array('technical', $preferences) ? 'checked' : ''; ?>> Technical Expertise</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success px-4">Find Partners</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="partner-results-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h3 class="card-title mb-0">Potential Partners</h3>
                        <?php if ($submitted): ?>
                            <span class="result-count"><?php echo count($partners); ?> result(s) found</span>
                        <?php else: ?>
                            <span class="result-count">Showing sample partner suggestions</span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($partners)): ?>
                        <div class="row g-3">
                            <?php foreach ($partners as $partner): ?>
                                <div class="col-md-6">
                                    <div class="partner-card h-100">
                                        <h4><?php echo htmlspecialchars($partner['name']); ?></h4>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($partner['location']); ?></p>
                                        <p><strong>Business Idea:</strong> <?php echo htmlspecialchars($partner['idea']); ?></p>
                                        <p><strong>Skills:</strong> <?php echo htmlspecialchars(implode(', ', $partner['skills'])); ?></p>
                                        <button type="button" class="btn btn-outline-primary btn-sm">Connect</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <h4>No partners found</h4>
                            <p class="mb-0">Try changing skills, location, or partner preferences to see more matches.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>