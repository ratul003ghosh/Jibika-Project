document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const noticeRows = document.querySelectorAll('.notice-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filterBtns.forEach(b => {
                b.classList.remove('bg-success', 'text-white');
                const badge = b.querySelector('.badge');
                badge.classList.remove('bg-light', 'text-success');
                badge.classList.add('bg-secondary');
            });
            btn.classList.add('bg-success', 'text-white');
            const activeBadge = btn.querySelector('.badge');
            activeBadge.classList.remove('bg-secondary');
            activeBadge.classList.add('bg-light', 'text-success');

            const category = btn.getAttribute('data-category');

            // Filter rows
            noticeRows.forEach(row => {
                if (category === 'All' || row.getAttribute('data-category') === category) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
