<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}
$lang = $_SESSION['lang'] ?? 'bn';

$n_text = [
    'en' => [
        'ticker_lbl' => 'LATEST UPDATES',
        'title' => 'Official Notice Board',
        'sub' => 'Browse the latest government circulars, policy updates, and training announcements.',
        'cat_title' => 'Notice Categories',
        'cat_all' => 'All Notices',
        'cat_emp' => 'Employment',
        'cat_train' => 'Training',
        'cat_pol' => 'Policy & Guidelines',
        'cat_gen' => 'General Announcements',
        'th_date' => 'Date',
        'th_title' => 'Notice Title',
        'th_cat' => 'Category',
        'th_dl' => 'Download',
        'new_badge' => 'NEW',
        'prev' => 'Previous',
        'next' => 'Next'
    ],
    'bn' => [
        'ticker_lbl' => 'সর্বশেষ আপডেট',
        'title' => 'অফিসিয়াল নোটিশ বোর্ড',
        'sub' => 'সর্বশেষ সরকারি সার্কুলার, নীতি আপডেট এবং প্রশিক্ষণ ঘোষণা ব্রাউজ করুন।',
        'cat_title' => 'নোটিশ ক্যাটাগরি',
        'cat_all' => 'সকল নোটিশ',
        'cat_emp' => 'কর্মসংস্থান',
        'cat_train' => 'প্রশিক্ষণ',
        'cat_pol' => 'নীতি ও নির্দেশিকা',
        'cat_gen' => 'সাধারণ ঘোষণা',
        'th_date' => 'তারিখ',
        'th_title' => 'নোটিশের শিরোনাম',
        'th_cat' => 'ক্যাটাগরি',
        'th_dl' => 'ডাউনলোড',
        'new_badge' => 'নতুন',
        'prev' => 'পূর্ববর্তী',
        'next' => 'পরবর্তী'
    ]
];
$nt = $n_text[$lang];

include('includes/header.php');
include('includes/navbar.php');
?>

<style>
    .notice-ticker {
        background-color: #006a4e;
        color: white;
        padding: 8px 0;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    .notice-ticker-label {
        background-color: #f42a41;
        color: white;
        padding: 8px 15px;
        font-weight: bold;
        white-space: nowrap;
        z-index: 2;
        position: relative;
    }
</style>

<!-- Scrolling Ticker -->
<div class="d-flex border-bottom mb-4">
    <div class="notice-ticker-label"><?php echo $nt['ticker_lbl']; ?></div>
    <div class="notice-ticker w-100 overflow-hidden">
        <marquee behavior="scroll" direction="left" scrollamount="6">
            <span class="me-5"><i class="fa-solid fa-circle-exclamation text-warning me-2"></i>Registration for the 3rd Phase Govt. Employment Scheme is now open until June 30, 2026.</span>
            <span class="me-5"><i class="fa-solid fa-circle-info text-info me-2"></i>Employers must update their Trade License info in the portal before July 15.</span>
            <span><i class="fa-solid fa-bullhorn text-warning me-2"></i>New Web Development training batch starting in Dhaka IT Park next month.</span>
        </marquee>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold" style="color: #006a4e; margin-bottom: 5px;"><?php echo $nt['title']; ?></h2>
            <div style="width: 60px; height: 4px; background-color: #f42a41; margin-bottom: 15px;"></div>
            <p class="text-muted fs-5"><?php echo $nt['sub']; ?></p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Categories -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light fw-bold py-3"><?php echo $nt['cat_title']; ?></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-success text-white filter-btn" data-category="All" style="cursor: pointer;">
                        <?php echo $nt['cat_all']; ?> <span class="badge bg-light text-success rounded-pill">7</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center filter-btn" data-category="Employment" style="cursor: pointer;">
                        <?php echo $nt['cat_emp']; ?> <span class="badge bg-secondary rounded-pill">1</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center filter-btn" data-category="Training" style="cursor: pointer;">
                        <?php echo $nt['cat_train']; ?> <span class="badge bg-secondary rounded-pill">2</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center filter-btn" data-category="Policy" style="cursor: pointer;">
                        <?php echo $nt['cat_pol']; ?> <span class="badge bg-secondary rounded-pill">2</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center filter-btn" data-category="General" style="cursor: pointer;">
                        <?php echo $nt['cat_gen']; ?> <span class="badge bg-secondary rounded-pill">2</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Notices List -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-4 py-3" style="width: 15%;"><?php echo $nt['th_date']; ?></th>
                                <th class="py-3" style="width: 55%;"><?php echo $nt['th_title']; ?></th>
                                <th class="py-3" style="width: 15%;"><?php echo $nt['th_cat']; ?></th>
                                <th class="text-end pe-4 py-3" style="width: 15%;"><?php echo $nt['th_dl']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $notices_data = [
                                'en' => [
                                    ["date" => "May 28, 2026", "title" => "Circular for 3rd Phase Govt. Employment Scheme", "cat" => "Employment", "new" => true],
                                    ["date" => "May 20, 2026", "title" => "Update to Employer Registration Guidelines (v2.1)", "cat" => "Policy", "new" => true],
                                    ["date" => "May 15, 2026", "title" => "List of Approved IT Training Centers 2026-2027", "cat" => "Training", "new" => false],
                                    ["date" => "May 02, 2026", "title" => "Holiday Notice for Eid-ul-Adha", "cat" => "General", "new" => false],
                                    ["date" => "Apr 25, 2026", "title" => "Revised Minimum Wage Structure for Garments Sector", "cat" => "Policy", "new" => false],
                                    ["date" => "Apr 10, 2026", "title" => "Call for Applications: Freelancing Masterclass", "cat" => "Training", "new" => false],
                                    ["date" => "Mar 28, 2026", "title" => "Annual Employment Statistics Report 2025", "cat" => "General", "new" => false]
                                ],
                                'bn' => [
                                    ["date" => "২৮ মে, ২০২৬", "title" => "৩য় পর্যায় সরকারি কর্মসংস্থান প্রকল্পের সার্কুলার", "cat" => "Employment", "new" => true],
                                    ["date" => "২০ মে, ২০২৬", "title" => "নিয়োগকর্তা নিবন্ধন নির্দেশিকা আপডেট (v2.1)", "cat" => "Policy", "new" => true],
                                    ["date" => "১৫ মে, ২০২৬", "title" => "অনুমোদিত আইটি প্রশিক্ষণ কেন্দ্র ২০২৬-২০২৭ এর তালিকা", "cat" => "Training", "new" => false],
                                    ["date" => "০২ মে, ২০২৬", "title" => "ঈদুল আযহার ছুটির নোটিশ", "cat" => "General", "new" => false],
                                    ["date" => "২৫ এপ্রিল, ২০২৬", "title" => "গার্মেন্টস খাতের সংশোধিত ন্যূনতম মজুরি কাঠামো", "cat" => "Policy", "new" => false],
                                    ["date" => "১০ এপ্রিল, ২০২৬", "title" => "আহ্বান: ফ্রিল্যান্সিং মাস্টারক্লাস", "cat" => "Training", "new" => false],
                                    ["date" => "২৮ মার্চ, ২০২৬", "title" => "বার্ষিক কর্মসংস্থান পরিসংখ্যান প্রতিবেদন ২০২৫", "cat" => "General", "new" => false]
                                ]
                            ];
                            $notices = $notices_data[$lang];

                            $cat_display = [
                                'en' => ['Employment' => 'Employment', 'Policy' => 'Policy', 'Training' => 'Training', 'General' => 'General'],
                                'bn' => ['Employment' => 'কর্মসংস্থান', 'Policy' => 'নীতি', 'Training' => 'প্রশিক্ষণ', 'General' => 'সাধারণ']
                            ];

                            foreach ($notices as $n):
                            ?>
                            <tr class="notice-row" data-category="<?php echo $n['cat']; ?>">
                                <td class="ps-4 fw-medium text-secondary"><?php echo $n['date']; ?></td>
                                <td class="text-dark">
                                    <a href="#" class="text-decoration-none text-dark fw-bold hover-success"><?php echo $n['title']; ?></a>
                                    <?php if($n['new']) echo '<span class="badge bg-danger ms-2" style="font-size:0.65rem;">' . $nt['new_badge'] . '</span>'; ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?php echo $cat_display[$lang][$n['cat']]; ?></span></td>
                                <td class="text-end pe-4">
                                    <a href="data:application/pdf;base64,JVBERi0xLjQKJcOkw7zDtsOfCjIgMCBvYmoKPDwvTGVuZ3RoIDMgMCBSL0ZpbHRlci9GbGF0ZURlY29kZT4+CnN0cmVhbQp4nDPQM1Qo5ypUMFAwALJMLU31jBQsTAz1LBSKijLzElMz81KMDIzMUvNSi0pSi4ozU8v0QGKu+YV5xYk5qSWJRSq6hZl5mSBDjEDmKOSkVlRWKxTl5xdl5qXrFxRlpiYDhSEmAACl4B6KCmVuZHN0cmVhbQplbmRvYmoKCjMgMCBvYmoKOTgKZW5kb2JqCgo0IDAgb2JqCjw8L1R5cGUvUGFnZS9NZWRpYUJveFswIDAgNTk1LjI3NiA4NDEuODldL1Jlc291cmNlczw8L0ZvbnQ8PC9GMCA2IDAgUj4+Pj4vQ29udGVudHMgMiAwIFIvUGFyZW50IDUgMCBSPj4KZW5kb2JqCgo1IDAgb2JqCjw8L1R5cGUvUGFnZXMvS2lkc1s0IDAgUl0vQ291bnQgMT4+CmVuZG9iagoKMSAwIG9iago8PC9UeXBlL0NhdGFsb2cvUGFnZXMgNSAwIFI+PgplbmRvYmoKCjYgMCBvYmoKPDwvVHlwZS9Gb250L1N1YnR5cGUvVHlwZTEvQmFzZUZvbnQvSGVsdmV0aWNhL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZz4+CmVuZG9iagoKeHJlZgowIDcKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMzE4IDAwMDAwIG4gCjAwMDAwMDAwMTUgMDAwMDAgbiAKMDAwMDAwMDE2MiAwMDAwMCBuIAowMDAwMDAwMTgzIDAwMDAwIG4gCjAwMDAwMDAyNzAgMDAwMDAgbiAKMDAwMDAwMDM2NiAwMDAwMCBuIAp0cmFpbGVyCjw8L1NpemUgNy9Sb290IDEgMCBSPj4Kc3RhcnR4cmVmCjQ1NAolJUVPRgo=" download="demo-notice.pdf" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fa-solid fa-file-pdf me-1"></i> PDF</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#"><?php echo $nt['prev']; ?></a></li>
                            <li class="page-item active"><a class="page-link bg-success border-success" href="#">1</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#">2</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#">3</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#"><?php echo $nt['next']; ?></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const noticeRows = document.querySelectorAll('.notice-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filterBtns.forEach(b => {
                b.classList.remove('bg-success', 'text-white');
                const badge = b.querySelector('.badge');
                badge.classList.remove('bg-light', 'text-success');
                badge.classList.add('bg-secondary');
            });
            btn.classList.add('bg-success', 'text-white');
            const activeBadge = btn.querySelector('.badge');
            activeBadge.classList.remove('bg-secondary');
            activeBadge.classList.add('bg-light', 'text-success');

            const category = btn.getAttribute('data-category');

            // Filter rows
            noticeRows.forEach(row => {
                if (category === 'All' || row.getAttribute('data-category') === category) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
    .hover-success:hover {
        color: #006a4e !important;
        text-decoration: underline !important;
    }
</style>

<?php include('includes/footer.php'); ?>
