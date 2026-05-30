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
    .support-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    .support-icon {
        background-color: #e6f0ed;
        color: #006a4e;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }
</style>

<div class="resource-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold"><i class="fa-solid fa-rocket me-3"></i>উদ্যোক্তা সহায়তা (Entrepreneur Support)</h1>
        <p class="fs-5 opacity-75 mb-0">Empowering the next generation of business leaders with resources, funding, and mentorship.</p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <h4 class="fw-bold mb-3">SME Loan Assistance</h4>
                <p class="text-muted mb-4">Learn about government-backed SME loans, low-interest funding options, and application processes for your startup or small business.</p>
                <a href="#" class="btn btn-outline-success rounded-pill fw-bold">Learn More</a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-file-contract"></i></div>
                <h4 class="fw-bold mb-3">Legal & Registration</h4>
                <p class="text-muted mb-4">Get step-by-step guidance on how to acquire Trade Licenses, TIN, VAT registration, and company incorporation in Bangladesh.</p>
                <a href="/eservices.php" class="btn btn-outline-success rounded-pill fw-bold">Access E-Services</a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="support-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                <h4 class="fw-bold mb-3">Partner & Co-founder</h4>
                <p class="text-muted mb-4">Looking for someone with technical skills or investment capital? Use our Partner Finder to connect with potential co-founders.</p>
                <a href="/jobseeker/partner_finder.php" class="btn btn-outline-success rounded-pill fw-bold">Find a Partner</a>
            </div>
        </div>
    </div>

    <div class="bg-dark text-white rounded-4 p-5 shadow-lg position-relative overflow-hidden">
        <div class="row align-items-center position-relative" style="z-index: 1;">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">Join the Jibika Startup Incubator</h2>
                <p class="fs-5 opacity-75 mb-4">We select 50 promising startups every year for intensive mentorship, office space allocation, and direct connections to venture capitalists.</p>
                <button class="btn btn-warning btn-lg px-5 fw-bold rounded-pill text-dark" onclick="alert('Applications for the next cohort will open in January 2027.')">Apply for Incubation</button>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <i class="fa-solid fa-lightbulb text-warning" style="font-size: 8rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>

</div>

<?php include('includes/footer.php'); ?>
