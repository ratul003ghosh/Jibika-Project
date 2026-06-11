document.addEventListener('DOMContentLoaded', function () {
    const notificationToggle = document.querySelector('[data-notification-toggle]');
    const notificationPanel = document.querySelector('[data-notification-panel]');
    const periodToggle = document.querySelector('[data-period-toggle]');
    const periodPanel = document.querySelector('[data-period-panel]');

    function closePanels() {
        if (notificationPanel) {
            notificationPanel.classList.remove('open');
        }
        if (notificationToggle) {
            notificationToggle.setAttribute('aria-expanded', 'false');
        }
        if (periodPanel) {
            periodPanel.classList.remove('open');
        }
    }

    if (notificationToggle && notificationPanel) {
        notificationToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = notificationPanel.classList.toggle('open');
            notificationToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (periodPanel) {
                periodPanel.classList.remove('open');
            }
        });

        notificationPanel.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }

    if (periodToggle && periodPanel) {
        periodToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            periodPanel.classList.toggle('open');
            if (notificationPanel) {
                notificationPanel.classList.remove('open');
            }
        });

        periodPanel.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }

    document.addEventListener('click', closePanels);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePanels();
        }
    });
});
