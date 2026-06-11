function showJobModal(id, title, desc, location, salary, employer, date) {
    document.getElementById('modal-title').textContent    = title;
    document.getElementById('modal-desc').textContent     = desc || '—';
    document.getElementById('modal-location').textContent = location || '—';
    document.getElementById('modal-salary').textContent   = salary || '—';
    document.getElementById('modal-employer').textContent = employer || '—';
    document.getElementById('modal-date').textContent     = date || '—';
    document.getElementById('modal-delete-btn').href = 'jobs.php?delete=' + id;
    var modal = new bootstrap.Modal(document.getElementById('jobModal'));
    modal.show();
}

function filterJobs() {
    const query = document.getElementById('jobSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#jobsBody tr');
    let visible = 0;
    rows.forEach(function(row) {
        const data = row.getAttribute('data-search') || '';
        if (!query || data.includes(query)) {
            row.classList.remove('hidden-row');
            visible++;
        } else {
            row.classList.add('hidden-row');
        }
    });
    const vc = document.getElementById('visibleCount');
    const fv = document.getElementById('footerVisible');
    if (vc) vc.textContent = visible;
    if (fv) fv.textContent = visible;
}
