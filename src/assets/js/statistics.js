document.addEventListener("DOMContentLoaded", function() {
    const colorEmerald = '#129B6F';
    const colorNavy = '#152334';
    const colorAmber = '#F59E0B';
    const colorRed = '#dc2626';

    // Translation data is passed via a global variable set by the PHP page
    const t = window.statsTranslations || {};

    const ctxTrends = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctxTrends, {
        type: 'line',
        data: {
            labels: t.months,
            datasets: [
                {
                    label: t.c_post,
                    data: [2500, 3200, 3800, 4100, 3900, 5200],
                    borderColor: colorNavy,
                    backgroundColor: colorNavy,
                    borderWidth: 2,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: t.c_place,
                    data: [1200, 1500, 2100, 2800, 3100, 4000],
                    borderColor: colorEmerald,
                    backgroundColor: 'rgba(18, 155, 111, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: { type: 'linear', display: true, position: 'left', beginAtZero: true } }
        }
    });

    const ctxGeo = document.getElementById('geoChart').getContext('2d');
    new Chart(ctxGeo, {
        type: 'bar',
        data: {
            labels: [t.d_dhaka, t.d_ctg, t.d_raj, t.d_khu, t.d_syl, t.d_bar, t.d_rng, t.d_mym],
            datasets: [
                {
                    label: t.c_act,
                    data: [6500, 3200, 1800, 1500, 1200, 900, 1100, 800],
                    backgroundColor: colorNavy,
                    borderRadius: 4
                },
                {
                    label: t.c_seek,
                    data: [45000, 25000, 15000, 12000, 10000, 8000, 9000, 6000],
                    backgroundColor: colorEmerald,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    const ctxType = document.getElementById('jobTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'doughnut',
        data: {
            labels: [t.t_ft, t.t_pt, t.t_in, t.t_rm, t.t_dl],
            datasets: [{
                data: [55, 15, 10, 12, 8],
                backgroundColor: [colorNavy, colorEmerald, colorAmber, '#3b82f6', colorRed],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    const ctxSkills = document.getElementById('skillsChart').getContext('2d');
    new Chart(ctxSkills, {
        type: 'bar',
        data: {
            labels: t.sk_list,
            datasets: [{
                label: t.c_dem,
                data: [95, 88, 82, 75, 70, 68, 65, 55, 50, 45],
                backgroundColor: colorEmerald,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
});
