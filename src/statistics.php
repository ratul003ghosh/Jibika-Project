<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('assets/config/db.php');
include('includes/header.php');
include('includes/navbar.php');
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-4 px-lg-5 py-5 mt-3 mb-5">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold" style="color: #006a4e; margin-bottom: 5px;">National Employment Statistics</h2>
            <div style="width: 60px; height: 4px; background-color: #f42a41; margin-bottom: 15px;"></div>
            <p class="text-muted fs-5">Real-time data visualization of jobs, skills, and platform engagement across Bangladesh.</p>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-success border-4 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Registered Seekers</p>
                            <h3 class="fw-bold text-dark mb-0">125,430</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3 text-success">
                            <i class="fa-solid fa-users fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-primary border-4 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Verified Employers</p>
                            <h3 class="fw-bold text-dark mb-0">8,450</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-primary">
                            <i class="fa-solid fa-building fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-danger border-4 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Active Job Postings</p>
                            <h3 class="fw-bold text-dark mb-0">34,102</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded p-3 text-danger">
                            <i class="fa-solid fa-briefcase fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-warning border-4 py-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Successful Placements</p>
                            <h3 class="fw-bold text-dark mb-0">62,890</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-3 text-warning">
                            <i class="fa-solid fa-handshake fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <!-- Bar Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Employment by Sector (2026)</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="sectorChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Doughnut Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Job Seekers by Division</h6>
                </div>
                <div class="card-body p-4 d-flex justify-content-center align-items-center">
                    <canvas id="regionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Data Section -->
    <div class="row g-4">
        <!-- Line Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Monthly Job Postings Trend</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="trendChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Industries Table -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Top Hiring Industries</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Industry</th>
                                    <th class="py-3">Active Employers</th>
                                    <th class="text-end pe-4 py-3">Total Hires</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="fa-solid fa-shirt text-warning me-2"></i>Garments & Textile</td>
                                    <td>1,240</td>
                                    <td class="text-end pe-4 text-success fw-bold">25,430</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="fa-solid fa-laptop-code text-primary me-2"></i>IT & Software</td>
                                    <td>850</td>
                                    <td class="text-end pe-4 text-success fw-bold">15,200</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="fa-solid fa-trowel-bricks text-secondary me-2"></i>Construction</td>
                                    <td>420</td>
                                    <td class="text-end pe-4 text-success fw-bold">12,100</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="fa-solid fa-tractor text-success me-2"></i>Agriculture</td>
                                    <td>310</td>
                                    <td class="text-end pe-4 text-success fw-bold">8,050</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><i class="fa-solid fa-stethoscope text-danger me-2"></i>Healthcare</td>
                                    <td>280</td>
                                    <td class="text-end pe-4 text-success fw-bold">6,120</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Bar Chart (Employment by Sector)
        const ctxSector = document.getElementById('sectorChart').getContext('2d');
        new Chart(ctxSector, {
            type: 'bar',
            data: {
                labels: ['Garments', 'IT & Tech', 'Agriculture', 'Construction', 'Healthcare', 'Education'],
                datasets: [{
                    label: 'Number of Jobs Filled',
                    data: [25000, 15000, 8000, 12000, 6000, 4500],
                    backgroundColor: [
                        'rgba(0, 106, 78, 0.7)',  // Gov Green
                        'rgba(244, 42, 65, 0.7)', // Gov Red
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Doughnut Chart (Seekers by Division)
        const ctxRegion = document.getElementById('regionChart').getContext('2d');
        new Chart(ctxRegion, {
            type: 'doughnut',
            data: {
                labels: ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Sylhet', 'Others'],
                datasets: [{
                    data: [40, 20, 15, 10, 10, 5],
                    backgroundColor: [
                        '#006a4e',
                        '#f42a41',
                        '#2c3e50',
                        '#f39c12',
                        '#2980b9',
                        '#bdc3c7'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Line Chart (Monthly Trends)
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Job Postings',
                    data: [4500, 5200, 4800, 6100, 5900, 7200],
                    borderColor: '#f42a41',
                    backgroundColor: 'rgba(244, 42, 65, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>

<?php include('includes/footer.php'); ?>
