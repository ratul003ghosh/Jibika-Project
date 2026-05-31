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
    .tip-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-top: 4px solid #006a4e;
        transition: transform 0.2s;
    }
    .tip-card:hover {
        transform: translateY(-5px);
    }
    .tip-icon {
        font-size: 2.5rem;
        color: #006a4e;
        margin-bottom: 15px;
    }
</style>

<div class="resource-header">
    <div class="container-fluid px-4 px-xl-5">
        <h1 class="fw-bold"><i class="fa-solid fa-microphone-lines me-3"></i>ইন্টারভিউ টিপস (Interview Tips)</h1>
        <p class="fs-5 opacity-75 mb-0">Master the art of interviewing and secure your dream job.</p>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    
    <div class="mb-5">
        <h3 class="fw-bold border-bottom pb-2 text-dark">Before the Interview (ইন্টারভিউয়ের আগে)</h3>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h5 class="fw-bold">Research the Company</h5>
                <p class="text-muted">Spend time reading about the company's products, services, culture, and recent news. Understanding their goals will help you answer questions more effectively.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-solid fa-shirt"></i></div>
                <h5 class="fw-bold">Dress Professionally</h5>
                <p class="text-muted">Choose attire that aligns with the company's dress code. When in doubt, it is always better to overdress slightly than to underdress. Ensure your clothes are neat and ironed.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <div class="tip-icon"><i class="fa-regular fa-clock"></i></div>
                <h5 class="fw-bold">Punctuality is Key</h5>
                <p class="text-muted">Aim to arrive at least 15 minutes before the scheduled interview time. If it is an online interview, log in 5 minutes early to test your audio and video setup.</p>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h3 class="fw-bold border-bottom pb-2 text-dark">During the Interview (ইন্টারভিউ চলাকালীন)</h3>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-eye"></i></div>
                <h5 class="fw-bold">Maintain Body Language</h5>
                <p class="text-muted">Offer a firm handshake (if in person), maintain good eye contact, and sit up straight. Positive body language shows confidence and engagement.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-bullseye"></i></div>
                <h5 class="fw-bold">Use the STAR Method</h5>
                <p class="text-muted">Answer behavioral questions using the STAR technique: Situation, Task, Action, and Result. This keeps your answers structured, concise, and impactful.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card" style="border-top-color: #f42a41;">
                <div class="tip-icon" style="color: #f42a41;"><i class="fa-solid fa-circle-question"></i></div>
                <h5 class="fw-bold">Ask Questions</h5>
                <p class="text-muted">At the end of the interview, when asked if you have questions, always have 1-2 thoughtful questions ready about the role or company culture.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-4 shadow-sm p-5 border text-center">
        <h4 class="fw-bold mb-3">Need Personalized Help?</h4>
        <p class="text-muted mb-4">Book a session with one of our career experts to do a mock interview and improve your confidence.</p>
        <a href="/career_counseling.php" class="btn btn-success btn-lg px-5 rounded-pill fw-bold" style="background-color: #006a4e;">Book Mock Interview</a>
    </div>

</div>

<?php include('includes/footer.php'); ?>
