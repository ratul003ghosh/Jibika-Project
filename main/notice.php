<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
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
    <div class="notice-ticker-label">LATEST UPDATES</div>
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
            <h2 class="fw-bold" style="color: #006a4e; margin-bottom: 5px;">Official Notice Board</h2>
            <div style="width: 60px; height: 4px; background-color: #f42a41; margin-bottom: 15px;"></div>
            <p class="text-muted fs-5">Browse the latest government circulars, policy updates, and training announcements.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Categories -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light fw-bold py-3">Notice Categories</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-success text-white">
                        All Notices <span class="badge bg-light text-success rounded-pill">124</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Employment <span class="badge bg-secondary rounded-pill">45</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Training <span class="badge bg-secondary rounded-pill">32</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Policy & Guidelines <span class="badge bg-secondary rounded-pill">18</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        General Announcements <span class="badge bg-secondary rounded-pill">29</span>
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
                                <th class="ps-4 py-3" style="width: 15%;">Date</th>
                                <th class="py-3" style="width: 55%;">Notice Title</th>
                                <th class="py-3" style="width: 15%;">Category</th>
                                <th class="text-end pe-4 py-3" style="width: 15%;">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $notices = [
                                ["date" => "May 28, 2026", "title" => "Circular for 3rd Phase Govt. Employment Scheme", "cat" => "Employment", "new" => true],
                                ["date" => "May 20, 2026", "title" => "Update to Employer Registration Guidelines (v2.1)", "cat" => "Policy", "new" => true],
                                ["date" => "May 15, 2026", "title" => "List of Approved IT Training Centers 2026-2027", "cat" => "Training", "new" => false],
                                ["date" => "May 02, 2026", "title" => "Holiday Notice for Eid-ul-Adha", "cat" => "General", "new" => false],
                                ["date" => "Apr 25, 2026", "title" => "Revised Minimum Wage Structure for Garments Sector", "cat" => "Policy", "new" => false],
                                ["date" => "Apr 10, 2026", "title" => "Call for Applications: Freelancing Masterclass", "cat" => "Training", "new" => false],
                                ["date" => "Mar 28, 2026", "title" => "Annual Employment Statistics Report 2025", "cat" => "General", "new" => false],
                            ];

                            foreach ($notices as $n):
                            ?>
                            <tr>
                                <td class="ps-4 fw-medium text-secondary"><?php echo $n['date']; ?></td>
                                <td class="text-dark">
                                    <a href="#" class="text-decoration-none text-dark fw-bold hover-success"><?php echo $n['title']; ?></a>
                                    <?php if($n['new']) echo '<span class="badge bg-danger ms-2" style="font-size:0.65rem;">NEW</span>'; ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?php echo $n['cat']; ?></span></td>
                                <td class="text-end pe-4">
                                    <a href="#" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fa-solid fa-file-pdf me-1"></i> PDF</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link bg-success border-success" href="#">1</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#">2</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#">3</a></li>
                            <li class="page-item"><a class="page-link text-success" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-success:hover {
        color: #006a4e !important;
        text-decoration: underline !important;
    }
</style>

<?php include('includes/footer.php'); ?>
