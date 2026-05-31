<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');
?>

<style>
    .training-hero {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        padding: 80px 0;
        color: white;
        text-align: center;
        border-bottom: 5px solid #f42a41;
    }
    .training-card {
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    .training-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }
    .icon-box {
        transition: all 0.3s ease;
    }
    .training-card:hover .icon-box {
        background-color: #006a4e !important;
        color: white !important;
    }
</style>

<!-- Hero Section -->
<div class="training-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">National Skill Development Programs</h1>
        <p class="lead mb-4" style="opacity: 0.9;">Official government initiatives to empower the workforce with advanced, industry-ready skills.</p>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <input type="text" class="form-control border-0 px-4" placeholder="Search training programs...">
                    <button class="btn btn-light px-4 text-success fw-bold" type="button"><i class="fa-solid fa-search me-2"></i>Find</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 mb-5">
    
    <!-- Filter Tabs -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
        <button class="btn btn-success rounded-pill px-4 fw-bold filter-btn" data-filter="all">All Programs</button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="IT">IT & Tech</button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Industry">Industry & Mfg</button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Engineering">Engineering</button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Business">Business</button>
        <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-filter="Healthcare">Healthcare</button>
    </div>

    <div class="row g-4">
        <?php
        $trainings = [
            ["icon" => "fa-cloud", "date" => "Jul 15", "cat" => "IT & Cloud", "group" => "IT", "title" => "SEIP: Advanced Cloud Computing & AWS", "loc" => "BCC IT Hub, Agargaon", "duration" => "12 Weeks", "desc" => "Government-sponsored Skill for Employment Investment Program focusing on cloud architecture and Azure/AWS deployment."],
            ["icon" => "fa-shoe-prints", "date" => "Aug 02", "cat" => "Manufacturing", "group" => "Industry", "title" => "Leather Goods & Footwear Manufacturing", "loc" => "LSC Training Center, Gazipur", "duration" => "8 Weeks", "desc" => "Specialized technical training in leather processing, footwear design, and quality assurance for export-oriented factories."],
            ["icon" => "fa-plane", "date" => "Aug 10", "cat" => "Aviation", "group" => "Engineering", "title" => "Biman Ground Handling & Logistics", "loc" => "Hazrat Shahjalal Airport Admin", "duration" => "6 Weeks", "desc" => "Professional certification in airport ground operations, logistics management, and cargo handling."],
            ["icon" => "fa-shield-halved", "date" => "Sep 01", "cat" => "Cybersecurity", "group" => "IT", "title" => "National Cyber Security Certification", "loc" => "ICT Division Head Office", "duration" => "16 Weeks", "desc" => "Intensive ethical hacking and network security defense course designed for government and banking IT professionals."],
            ["icon" => "fa-ship", "date" => "Sep 15", "cat" => "Marine Eng.", "group" => "Engineering", "title" => "Advanced Shipbuilding & Marine Welding", "loc" => "Chittagong Marine Academy", "duration" => "10 Weeks", "desc" => "High-tier TIG and MIG welding certifications specifically required for the deep-sea shipbuilding industry."],
            ["icon" => "fa-chart-line", "date" => "Oct 05", "cat" => "Business", "group" => "Business", "title" => "BIDA Entrepreneurship Development", "loc" => "BIDA HQ, Dhaka", "duration" => "4 Weeks", "desc" => "Complete training on company formation, tax compliance, and securing government funding for new startups."],
            ["icon" => "fa-robot", "date" => "Oct 20", "cat" => "AI & Robotics", "group" => "IT", "title" => "Hi-Tech Park AI & Machine Learning", "loc" => "Bangabandhu Hi-Tech City", "duration" => "14 Weeks", "desc" => "Cutting-edge bootcamp on Python data science, neural networks, and computer vision algorithms."],
            ["icon" => "fa-truck-monster", "date" => "Nov 02", "cat" => "Infrastructure", "group" => "Engineering", "title" => "Heavy Equipment Operations (RHD)", "loc" => "Roads & Highways Dept, Savar", "duration" => "8 Weeks", "desc" => "Official certification for operating excavators, cranes, and heavy road construction machinery safely."],
            ["icon" => "fa-cart-shopping", "date" => "Nov 15", "cat" => "E-Commerce", "group" => "IT", "title" => "National E-Commerce Management", "loc" => "Online / Virtual Campus", "duration" => "6 Weeks", "desc" => "Comprehensive training on digital storefront management, payment gateways, and supply chain logistics."],
            ["icon" => "fa-shirt", "date" => "Dec 01", "cat" => "Textile", "group" => "Industry", "title" => "Apparel Merchandising & Supply Chain", "loc" => "BGMEA University of Fashion", "duration" => "12 Weeks", "desc" => "Advanced merchandising principles, export compliance, and international buyer management for the RMG sector."],
            ["icon" => "fa-stethoscope", "date" => "Dec 10", "cat" => "Healthcare", "group" => "Healthcare", "title" => "Medical Equipment Troubleshooting", "loc" => "NITOR Campus, Dhaka", "duration" => "10 Weeks", "desc" => "Technical skills for maintaining and repairing hospital machinery, MRI scanners, and life-support systems."],
            ["icon" => "fa-solar-panel", "date" => "Dec 20", "cat" => "Energy", "group" => "Engineering", "title" => "Renewable Energy Grid Installation", "loc" => "SREDA Training Center", "duration" => "8 Weeks", "desc" => "Design, setup, and maintenance of large-scale solar power grids and renewable energy storage solutions."]
        ];

        foreach($trainings as $t):
        ?>
        <div class="col-xl-4 col-md-6 training-item" data-group="<?php echo $t['group']; ?>">
            <div class="card training-card h-100 border-0 shadow-sm position-relative">
                <div class="card-body p-4 p-xl-5">
                    
                    <!-- Top Ribbon -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <i class="fa-solid <?php echo $t['icon']; ?> fs-4"></i>
                        </div>
                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-bold shadow-sm"><?php echo $t['cat']; ?></span>
                    </div>
                    
                    <!-- Content -->
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;"><?php echo $t['title']; ?></h5>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;"><?php echo $t['desc']; ?></p>
                    
                    <!-- Meta Info Grid -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-clock text-warning me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo $t['duration']; ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-calendar-day text-primary me-2"></i>
                                <span class="small fw-bold text-dark"><?php echo explode(" ", $t['date'])[0] . " " . explode(" ", $t['date'])[1]; ?></span>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="d-flex align-items-center text-muted small bg-light p-2 px-3 rounded-3 border">
                                <i class="fa-solid fa-location-dot me-2 text-danger"></i> <span class="text-truncate fw-medium"><?php echo $t['loc']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="card-footer bg-white border-top-0 px-4 px-xl-5 pb-4 pt-0 mt-auto">
                    <button onclick="showTrainingModal('<?php echo htmlspecialchars($t['title'], ENT_QUOTES); ?>')" class="btn btn-outline-success w-100 rounded-pill fw-bold" style="border-width: 2px;">Apply for Program</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.training-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state of buttons
            filterBtns.forEach(b => {
                b.classList.remove('btn-success', 'fw-bold');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success', 'fw-bold');

            const filterValue = btn.getAttribute('data-filter');

            // Show/Hide cards
            items.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else {
                    if (item.getAttribute('data-group') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    });
});

function showTrainingModal(courseName) {
    document.getElementById('modalCourseName').textContent = courseName;
    var myModal = new bootstrap.Modal(document.getElementById('trainingModal'));
    myModal.show();
}
</script>

<!-- Training Application Modal -->
<div class="modal fade" id="trainingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white" style="background-color: #006a4e !important;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-pen me-2"></i>Training Application</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <h6 class="fw-bold text-dark mb-3">You are applying for: <br><span id="modalCourseName" class="text-success fs-5"></span></h6>
        <p class="text-muted small mb-4">Please note that all government training programs require NID verification. By proceeding, you agree to share your Jibika profile details with the training authority.</p>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
          <label class="form-check-label text-dark small fw-medium" for="flexCheckDefault">
            I confirm that I meet the minimum educational requirements for this program.
          </label>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-between">
        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" style="background-color: #006a4e;" onclick="window.location.reload();">Submit Application</button>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
