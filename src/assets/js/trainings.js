document.addEventListener("DOMContentLoaded", function() {
    // Category Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.training-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state of buttons
            filterBtns.forEach(b => {
                b.classList.remove('btn-success', 'fw-bold');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success', 'fw-bold');

            const filterValue = btn.getAttribute('data-filter');

            // Show/Hide cards
            items.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else {
                    if (item.getAttribute('data-group') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    });

    // Real-time Search Filtering
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        items.forEach(item => {
            const title = item.querySelector('.training-title-text').textContent.toLowerCase();
            const desc = item.querySelector('p').textContent.toLowerCase();
            const matches = title.includes(query) || desc.includes(query);

            // Respect both current category filter and search query
            const activeFilter = document.querySelector('.filter-btn.btn-success').getAttribute('data-filter');
            const matchesCategory = (activeFilter === 'all') || (item.getAttribute('data-group') === activeFilter);

            if (matches && matchesCategory) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
    }
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
});

function showTrainingModal(courseName) {
    document.getElementById('modalCourseName').textContent = courseName;
    var myModal = new bootstrap.Modal(document.getElementById('trainingModal'));
    myModal.show();
}
