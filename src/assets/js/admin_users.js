var currentFilter = 'all';

function setFilter(role, btn) {
    currentFilter = role;
    document.querySelectorAll('.filter-pill').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    filterUsers();
}

function filterUsers() {
    const query = document.getElementById('userSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#usersBody tr');
    let visible = 0;
    rows.forEach(function(row) {
        const data   = row.getAttribute('data-search') || '';
        const role   = row.getAttribute('data-role') || '';
        const matchQ = !query || data.includes(query);
        const matchR = currentFilter === 'all' || role === currentFilter;
        if (matchQ && matchR) {
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
