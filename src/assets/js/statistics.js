document.addEventListener("DOMContentLoaded", function() {
    const colorEmerald = '#129B6F';
    const colorNavy = '#152334';
    const colorAmber = '#F59E0B';
    const colorRed = '#dc2626';

    const t = window.statsTranslations || {};
    const cd = window.chartData || null;

    // Trends Chart
    let trendsLabels = cd && cd.trends ? cd.trends.labels : t.months;
    let trendsDataApps = cd && cd.trends ? cd.trends.apps : [1200, 1500, 2100, 2800, 3100, 4000];
    let trendsDataPosts = cd && cd.trends ? cd.trends.postings : [2500, 3200, 3800, 4100, 3900, 5200];
    
    // For Employer view we might only care about Apps, but let's keep both for now
    let trendsDatasets = [
        {
            label: t.c_place || 'Applications',
            data: trendsDataApps,
            borderColor: colorEmerald,
            backgroundColor: 'rgba(18, 155, 111, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
        }
    ];
    if (!cd || !cd.trends) {
        trendsDatasets.unshift({
            label: t.c_post || 'Postings',
            data: trendsDataPosts,
            borderColor: colorNavy,
            backgroundColor: colorNavy,
            borderWidth: 2,
            tension: 0.4,
            yAxisID: 'y'
        });
    }

    const ctxTrends = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctxTrends, {
        type: 'line',
        data: {
            labels: trendsLabels,
            datasets: trendsDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: { type: 'linear', display: true, position: 'left', beginAtZero: true } }
        }
    });

    // Geo Chart
    let geoLabels = cd && cd.geo ? cd.geo.labels : [t.d_dhaka, t.d_ctg, t.d_raj, t.d_khu, t.d_syl, t.d_bar, t.d_rng, t.d_mym];
    let geoDataApps = cd && cd.geo ? cd.geo.data : [45000, 25000, 15000, 12000, 10000, 8000, 9000, 6000];
    let geoDatasets = [
        {
            label: t.c_seek || 'Applicants',
            data: geoDataApps,
            backgroundColor: colorEmerald,
            borderRadius: 4
        }
    ];

    if (!cd || !cd.geo) {
        geoDatasets.unshift({
            label: t.c_act || 'Active Jobs',
            data: [6500, 3200, 1800, 1500, 1200, 900, 1100, 800],
            backgroundColor: colorNavy,
            borderRadius: 4
        });
    }

    const ctxGeo = document.getElementById('geoChart').getContext('2d');
    new Chart(ctxGeo, {
        type: 'bar',
        data: {
            labels: geoLabels,
            datasets: geoDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', align: 'end' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Type Chart
    let typeLabels = cd && cd.type && cd.type.labels.length ? cd.type.labels : [t.t_ft, t.t_pt, t.t_in, t.t_rm, t.t_dl];
    let typeData = cd && cd.type && cd.type.data.length ? cd.type.data : [55, 15, 10, 12, 8];

    const ctxType = document.getElementById('jobTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: [colorNavy, colorEmerald, colorAmber, '#3b82f6', colorRed, '#8b5cf6', '#ec4899'],
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

    // Skills Chart
    let skillLabels = cd && cd.skills && cd.skills.labels.length ? cd.skills.labels : t.sk_list;
    let skillData = cd && cd.skills && cd.skills.data.length ? cd.skills.data : [95, 88, 82, 75, 70, 68, 65, 55, 50, 45];

    const ctxSkills = document.getElementById('skillsChart').getContext('2d');
    new Chart(ctxSkills, {
        type: 'bar',
        data: {
            labels: skillLabels,
            datasets: [{
                label: t.c_dem || 'Demand',
                data: skillData,
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
