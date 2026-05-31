<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

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
        <h1 class="fw-bold"><i class="fa-solid fa-compass me-3"></i>ক্যারিয়ার কাউন্সেলিং (Career Counseling)</h1>
        <p class="fs-5 opacity-75 mb-0">Get expert guidance to navigate your career path and make informed professional choices.</p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    <div class="row g-5">
        <div class="col-lg-7">
            <h3 class="fw-bold mb-4 text-dark">Why Career Counseling?</h3>
            <p class="text-muted mb-4 fs-5">Whether you are a fresh graduate confused about which industry to join, or an experienced professional looking to switch careers, our counseling program connects you with industry veterans who can provide clarity, direction, and actionable advice.</p>
            
            <ul class="list-unstyled mb-5">
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> Identify your core strengths and weaknesses.</li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> Get a customized career roadmap.</li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> Industry-specific insights and salary expectations.</li>
                <li class="mb-3 fs-5"><i class="fa-solid fa-circle-check text-success me-3"></i> Review of your CV and Portfolio.</li>
            </ul>

            <h4 class="fw-bold mb-4 text-dark">Available Counselors</h4>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="counselor-card">
                        <div class="counselor-img">S</div>
                        <div>
                            <h5 class="fw-bold mb-1">Dr. Selim Rahman</h5>
                            <p class="text-muted mb-2 small">Specialty: IT & Engineering Sectors</p>
                            <button class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">Book Appointment</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="counselor-card">
                        <div class="counselor-img bg-primary">F</div>
                        <div>
                            <h5 class="fw-bold mb-1">Farhana Islam</h5>
                            <p class="text-muted mb-2 small">Specialty: Corporate Business & HR</p>
                            <button class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">Book Appointment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 border-0">
                    <h5 class="fw-bold mb-0">Request a Free Consultation</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Email Address</label>
                            <input type="email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Current Status</label>
                            <select class="form-select">
                                <option>Student / Fresh Graduate</option>
                                <option>Employed (Looking to Switch)</option>
                                <option>Unemployed</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Briefly describe your career goals</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                        <button type="button" class="btn btn-success w-100 btn-lg fw-bold rounded-pill" style="background-color: #006a4e;" onclick="alert('Your request has been submitted successfully. A counselor will contact you soon.')">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
