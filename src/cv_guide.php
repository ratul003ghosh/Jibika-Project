<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<style>
    body { background-color: #f4f7f6; }
    
    .resource-hero {
        background: linear-gradient(135deg, #00563f 0%, #006a4e 100%);
        padding: 70px 0;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .resource-hero::after {
        content: '\f15c'; /* fa-file-lines */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        right: -50px;
        bottom: -50px;
        font-size: 20rem;
        color: rgba(255, 255, 255, 0.05);
        transform: rotate(-15deg);
    }
    
    .timeline {
        position: relative;
        padding-left: 40px;
        margin-top: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 10px;
        bottom: 0;
        width: 3px;
        background-color: #d1d5db;
        border-radius: 3px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 35px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -34.5px;
        top: 25px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: #006a4e;
        border: 4px solid #fff;
        box-shadow: 0 0 0 3px #006a4e;
        z-index: 2;
    }
    .timeline-content {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }
    .timeline-content:hover {
        transform: translateX(10px);
        border-color: #006a4e;
        box-shadow: 0 10px 25px rgba(0,106,78,0.1);
    }
    
    /* Visual CV Mockup */
    .cv-mockup-container {
        position: sticky;
        top: 30px;
    }
    .cv-mockup {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    .mock-line {
        height: 10px;
        background: #e2e8f0;
        border-radius: 5px;
        margin-bottom: 12px;
    }
    .mock-line.short { width: 35%; }
    .mock-line.medium { width: 65%; }
    .mock-line.long { width: 100%; }
    
    .mock-title {
        height: 20px;
        width: 50%;
        background: #006a4e;
        border-radius: 10px;
        margin: 0 auto 10px auto;
    }
    .mock-subtitle {
        height: 12px;
        width: 30%;
        background: #94a3b8;
        border-radius: 6px;
        margin: 0 auto 30px auto;
    }
    
    .mock-section {
        border: 2px dashed #cbd5e1;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        position: relative;
        background: #f8fafc;
        transition: border-color 0.3s;
    }
    .mock-section:hover {
        border-color: #006a4e;
    }
    .mock-badge {
        position: absolute;
        top: -12px;
        left: 20px;
        background: #006a4e;
        color: white;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: bold;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .mistake-card {
        background: #fff5f5;
        border-left: 4px solid #f42a41;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .mistake-icon {
        color: #f42a41;
        font-size: 1.5rem;
    }
</style>

<!-- Hero Section -->
<div class="resource-hero mb-5">
    <div class="container-fluid px-4 px-xl-5">
        <div class="row align-items-center">
            <div class="col-lg-8" style="z-index: 2;">
                <span class="badge bg-white text-success rounded-pill px-3 py-2 mb-3 fw-bold">Jibika Resources</span>
                <h1 class="fw-bold display-5 mb-3">The Ultimate CV Writing Guide</h1>
                <p class="fs-5 opacity-75 mb-0">Your CV is your first impression. Learn the exact structure and techniques used by professionals to get hired in today's competitive job market.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" style="z-index: 2;">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-light btn-lg px-4 fw-bold text-success shadow-sm" onclick="alert('Downloading Standard Jibika Template...')"><i class="fa-solid fa-download me-2"></i>Download Word Template</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-xl-5 pb-5">
    <div class="row g-5">
        
        <!-- Left Column: The Timeline Steps -->
        <div class="col-lg-7">
            <h3 class="fw-bold text-dark border-bottom pb-3 mb-4">Step-by-Step CV Architecture</h3>
            
            <div class="timeline">
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-address-card"></i>
                            </div>
                            <h4 class="fw-bold mb-0">1. Contact Header</h4>
                        </div>
                        <p class="text-muted mb-0">This must be the very first thing employers see. Include your full name (large font), a professional email, phone number, and LinkedIn URL. <strong class="text-dark">Never</strong> use unprofessional emails like <em>coolboy99@email.com</em>.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h4 class="fw-bold mb-0">2. Professional Summary</h4>
                        </div>
                        <p class="text-muted mb-0">A powerful 3-line paragraph summarizing who you are and what value you bring. Do not use generic statements like "I am a hardworking person." Instead use facts: "Digital Marketer with 3 years experience increasing ROI by 40%."</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <h4 class="fw-bold mb-0">3. Work Experience</h4>
                        </div>
                        <p class="text-muted mb-0">List your previous jobs in <strong>reverse-chronological order</strong>. Use bullet points. Start each bullet point with an action verb (e.g., Developed, Managed, Designed). Focus on achievements and numbers, not just daily duties.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 text-info rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <h4 class="fw-bold mb-0">4. Education</h4>
                        </div>
                        <p class="text-muted mb-0">Mention your highest degrees first. Include the university/college, graduation year, and major. If you have graduated from university, you do not need to include your SSC/HSC details unless specifically asked.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 text-danger rounded p-2 me-3 fs-4">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <h4 class="fw-bold mb-0">5. Core Skills</h4>
                        </div>
                        <p class="text-muted mb-0">List 5-8 highly relevant skills for the job you are applying for. Mix hard skills (e.g., Python, Data Entry, AutoCAD) and soft skills (e.g., Leadership, Communication). Keep them punchy and easily scannable.</p>
                    </div>
                </div>

            </div>

            <div class="mt-5 pt-4 border-top">
                <h3 class="fw-bold text-dark mb-4">Critical CV Mistakes to Avoid</h3>
                
                <div class="mistake-card d-flex">
                    <div class="mistake-icon me-3"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Including Unnecessary Personal Info</h6>
                        <p class="text-muted small mb-0">Do not include your religion, marital status, blood group, or parents' names unless explicitly requested in a government format. Standard corporate CVs do not need this.</p>
                    </div>
                </div>

                <div class="mistake-card d-flex">
                    <div class="mistake-icon me-3"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Using Crazy Fonts or Colors</h6>
                        <p class="text-muted small mb-0">Stick to standard, readable fonts like Arial, Calibri, or Roboto. Using bright red or pink text makes the CV look unprofessional.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Visual CV Mockup -->
        <div class="col-lg-5">
            <div class="cv-mockup-container d-none d-md-block">
                <h5 class="fw-bold text-muted mb-3 text-center text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Visual Anatomy of a Perfect CV</h5>
                <div class="cv-mockup">
                    
                    <!-- Header Mock -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <div class="mock-title"></div>
                        <div class="mock-subtitle"></div>
                        <div class="d-flex justify-content-center gap-2">
                            <div class="mock-line" style="width: 20px;"></div>
                            <div class="mock-line" style="width: 40px;"></div>
                            <div class="mock-line" style="width: 30px;"></div>
                        </div>
                    </div>

                    <!-- Summary Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-primary">Summary</div>
                        <div class="mock-line long"></div>
                        <div class="mock-line long"></div>
                        <div class="mock-line medium"></div>
                    </div>

                    <!-- Experience Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-warning text-dark">Experience</div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 20%; background: #cbd5e1;"></div>
                        </div>
                        <ul class="ps-3 mb-4">
                            <li><div class="mock-line long mt-2"></div></li>
                            <li><div class="mock-line medium"></div></li>
                        </ul>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 20%; background: #cbd5e1;"></div>
                        </div>
                        <ul class="ps-3 mb-0">
                            <li><div class="mock-line long mt-2"></div></li>
                        </ul>
                    </div>

                    <!-- Education Mock -->
                    <div class="mock-section">
                        <div class="mock-badge bg-info text-dark">Education</div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="mock-line short bg-secondary"></div>
                            <div class="mock-line" style="width: 15%; background: #cbd5e1;"></div>
                        </div>
                        <div class="mock-line medium mb-0"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include('includes/footer.php'); ?>
