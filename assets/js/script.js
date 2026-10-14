/* Plugiit Rules: acceptance modal and banner behaviour. */
(function () {
    'use strict';

    function init() {
        var modal = document.getElementById('plugiit-rules-modal');

        if (modal) {
            document.documentElement.classList.add('plugiit-rules-locked');

            var check = modal.querySelector('[data-plugiit-rules-check]');
            var submit = modal.querySelector('[data-plugiit-rules-submit]');

            if (check && submit) {
                check.addEventListener('change', function () {
                    submit.disabled = !check.checked;
                });
            }

            var logout = modal.querySelector('[data-plugiit-rules-logout]');
            var logoutForm = document.getElementById('plugiit-rules-logout-form');

            if (logout && logoutForm) {
                logout.addEventListener('click', function (event) {
                    event.preventDefault();
                    logoutForm.submit();
                });
            }

            // Keep the focus inside the dialog.
            var body = modal.querySelector('.plugiit-rules-body');

            if (body) {
                body.focus({ preventScroll: true });
            }

            document.addEventListener('focusin', function (event) {
                if (!modal.contains(event.target) && body) {
                    body.focus({ preventScroll: true });
                }
            });
        }

        var banner = document.getElementById('plugiit-rules-banner');

        if (banner) {
            var key = 'plugiit-rules-banner-' + banner.getAttribute('data-plugiit-rules-version');

            try {
                if (window.sessionStorage.getItem(key)) {
                    banner.classList.add('plugiit-rules-hidden');
                }
            } catch (e) {
                // Storage unavailable: always display the banner.
            }

            var close = banner.querySelector('[data-plugiit-rules-dismiss]');

            if (close) {
                close.addEventListener('click', function () {
                    banner.classList.add('plugiit-rules-hidden');

                    try {
                        window.sessionStorage.setItem(key, '1');
                    } catch (e) {
                        // ignore
                    }
                });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
