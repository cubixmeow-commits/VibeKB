/* VibeKB guide — light progressive enhancement only.
   The guide is fully functional without JavaScript; this just adds a mobile
   nav toggle and auto-submits the functionality filters on change. */
(function () {
    'use strict';

    // Mobile navigation toggle.
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('primary-nav');
    if (toggle && nav) {
        toggle.hidden = false;
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Auto-submit filters when a select changes (form still works without JS).
    var filterForm = document.querySelector('.filters');
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }
})();
