import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const autoDismissAlerts = document.querySelectorAll('.js-alert-auto');
    autoDismissAlerts.forEach((alertElement) => {
        window.setTimeout(() => {
            alertElement.classList.add('fade-out');
            window.setTimeout(() => alertElement.remove(), 180);
        }, 3200);
    });

    document.querySelectorAll('.js-alert-dismiss').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('.js-alert')?.remove();
        });
    });

    const pageLoadingOverlay = document.querySelector('.js-page-loading-overlay');
    const showPageLoadingOverlay = () => {
        if (!pageLoadingOverlay) {
            return;
        }

        pageLoadingOverlay.classList.remove('hidden');
        pageLoadingOverlay.classList.add('flex');
    };

    document.querySelectorAll('a[data-page-loading-trigger]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented || link.target === '_blank' || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            showPageLoadingOverlay();
        });
    });

    document.querySelectorAll('form[data-page-loading-trigger]').forEach((form) => {
        form.addEventListener('submit', () => {
            showPageLoadingOverlay();
        });
    });

    document.querySelectorAll('form[data-enhanced-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButtons = form.querySelectorAll('[data-submit-button], button[type="submit"], input[type="submit"]');
            submitButtons.forEach((button) => {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-not-allowed');
            });

            form.querySelectorAll('[data-submit-default]').forEach((element) => {
                element.classList.add('hidden');
            });

            form.querySelectorAll('[data-submit-loading]').forEach((element) => {
                element.classList.remove('hidden');
                element.classList.add('inline');
            });
        });
    });
});
