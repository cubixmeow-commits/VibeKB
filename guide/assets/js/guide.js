/* VibeKB guide — light progressive enhancement (jQuery), matching the homepage.
   The guide is fully functional without JavaScript; this only adds the mobile
   nav toggle and auto-submits the functionality filters on change. */
(function (window, document, $) {
    'use strict';

    if (!$ || !$.fn) {
        return;
    }

    $(function () {
        // Mobile navigation toggle.
        var $toggle = $('.nav-toggle');
        var $nav = $('#primary-nav');
        if ($toggle.length && $nav.length) {
            $toggle.prop('hidden', false);
            $toggle.on('click', function () {
                var open = $nav.toggleClass('is-open').hasClass('is-open');
                $toggle.attr('aria-expanded', open ? 'true' : 'false');
            });
        }

        // Auto-submit the functionality filters when a select changes.
        // The form still works without JavaScript (it has a Filter button).
        var $filters = $('.filters');
        if ($filters.length) {
            $filters.find('select').on('change', function () {
                $filters.trigger('submit');
            });
        }
    });
})(window, document, window.jQuery);
