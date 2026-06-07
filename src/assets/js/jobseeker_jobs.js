// Disable default scroll restoration
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

// Fetch and render filtered jobs dynamically
function loadJobs(url, updateUrl = true) {
    const resultsDiv = document.getElementById('jobResults');
    const viewMode = document.querySelector('input[name="view"]').value || 'grid';

    // Inject skeleton loader for dynamic feedback
    if (resultsDiv) {
        let skeletons = '';
        if (viewMode === 'grid') {
            skeletons = '<div class="job-grid">';
            for (let i = 0; i < 6; i++) {
                skeletons += `
                    <div class="skeleton-card">
                        <div class="skeleton-thumb"></div>
                        <div class="skeleton-text skeleton-title"></div>
                        <div class="skeleton-text skeleton-subtitle"></div>
                        <div class="skeleton-pills">
                            <div class="skeleton-pill"></div>
                            <div class="skeleton-pill"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2" style="border-top: 1px dashed var(--border-light);">
                            <div class="skeleton-salary"></div>
                            <div class="skeleton-btn"></div>
                        </div>
                    </div>`;
            }
            skeletons += '</div>';
        } else {
            skeletons = '<div class="d-flex flex-column gap-3">';
            for (let i = 0; i < 4; i++) {
                skeletons += `
                    <div class="skeleton-list-card">
                        <div class="skeleton-list-thumb"></div>
                        <div class="list-content-container">
                            <div class="d-flex justify-content-between">
                                <div class="skeleton-list-badge"></div>
                                <div class="skeleton-list-save"></div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="skeleton-list-logo"></div>
                                <div class="d-flex flex-column gap-2" style="flex: 1;">
                                    <div class="skeleton-text skeleton-list-title"></div>
                                    <div class="skeleton-text skeleton-list-company"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="skeleton-list-meta">
                                    <div class="skeleton-list-meta-item"></div>
                                    <div class="skeleton-list-meta-item"></div>
                                    <div class="skeleton-list-meta-item"></div>
                                </div>
                                <div class="skeleton-list-btn"></div>
                            </div>
                        </div>
                    </div>`;
            }
            skeletons += '</div>';
        }
        resultsDiv.innerHTML = skeletons;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // 1. Update Job Results
            const newResults = doc.getElementById('jobResults');
            if (newResults && resultsDiv) {
                resultsDiv.innerHTML = newResults.innerHTML;
                resultsDiv.style.opacity = '1';
                
                // Re-trigger stagger animation
                document.querySelectorAll('.job-card, .job-list-card').forEach(function(card, i) {
                    card.style.animationDelay = (i * 50) + 'ms';
                });
            }

            // 2. Update Jobs Count
            const countDiv = document.querySelector('.jobs-count');
            const newCountDiv = doc.querySelector('.jobs-count');
            if (countDiv && newCountDiv) {
                countDiv.innerHTML = newCountDiv.innerHTML;
            }

            // 3. Update Top Category Pills
            const pillsDiv = document.querySelector('.top-category-pills');
            const newPillsDiv = doc.querySelector('.top-category-pills');
            if (pillsDiv && newPillsDiv) {
                pillsDiv.innerHTML = newPillsDiv.innerHTML;
            }

            // 4. Update Filter Sidebar
            const sidebarDiv = document.querySelector('.filter-sidebar');
            const newSidebarDiv = doc.querySelector('.filter-sidebar');
            if (sidebarDiv && newSidebarDiv) {
                sidebarDiv.innerHTML = newSidebarDiv.innerHTML;
            }

            // 5. Update Quick Filters
            const quickDiv = document.getElementById('quickFilters');
            const newQuickDiv = doc.getElementById('quickFilters');
            if (quickDiv && newQuickDiv) {
                quickDiv.innerHTML = newQuickDiv.innerHTML;
            }

            // 6. Sync Form State
            const form = document.getElementById('mainJobSearchForm');
            const newForm = doc.getElementById('mainJobSearchForm');
            if (form && newForm) {
                form.querySelector('input[name="view"]').value = newForm.querySelector('input[name="view"]').value;
                form.querySelector('select[name="job_type"]').value = newForm.querySelector('select[name="job_type"]').value;
                form.querySelector('select[name="district_id"]').value = newForm.querySelector('select[name="district_id"]').value;
                form.querySelector('input[name="search"]').value = newForm.querySelector('input[name="search"]').value;
                
                // Sync sort dropdown in sidebar
                const newSortVal = newForm.querySelector('select[name="sort_by"]').value;
                form.querySelector('select[name="sort_by"]').value = newSortVal;
                
                // Sync mobile sort dropdown if it exists
                const mobileSort = form.querySelector('select[name="sort_by_mobile"]');
                if (mobileSort) {
                    mobileSort.value = newSortVal;
                }
            }

            if (updateUrl) {
                window.history.pushState({ path: url }, '', url);
            }
        })
        .catch(err => {
            console.error('Error fetching jobs:', err);
            if (resultsDiv) resultsDiv.style.opacity = '1';
        });
}

// Sidebar Checkboxes & Text Input trigger submit
function submitWithScroll() {
    const form = document.getElementById('mainJobSearchForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    const url = 'jobs.php?' + params.toString();
    loadJobs(url);
}

document.addEventListener('DOMContentLoaded', function() {
    // Intercept View Mode Buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.view-toggle a, .view-btn-toggle');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            loadJobs(url);
        }
    });

    // Intercept Quick Filter Pills
    document.addEventListener('click', function(e) {
        const pill = e.target.closest('#quickFilters .filter-pill');
        if (pill) {
            e.preventDefault();
            const type = pill.getAttribute('data-type');
            const isClear = pill.getAttribute('data-clear');
            const form = document.getElementById('mainJobSearchForm');
            const sel = form.querySelector('select[name="job_type"]');

            if (isClear) {
                loadJobs('jobs.php?view=' + form.querySelector('input[name="view"]').value);
                return;
            }

            if (sel.value === type) {
                sel.value = '';
            } else {
                sel.value = type;
            }

            submitWithScroll();
        }
    });

    // Intercept search form submit
    document.getElementById('mainJobSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        const url = 'jobs.php?' + params.toString();
        loadJobs(url);
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        loadJobs(window.location.href, false);
    });

    // Stagger animation on initial load
    document.querySelectorAll('.job-card, .job-list-card').forEach(function(card, i) {
        card.style.animationDelay = (i * 50) + 'ms';
    });
});
