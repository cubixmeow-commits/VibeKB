/* VibeKB guide — light progressive enhancement (jQuery).
   Core navigation and content remain usable without JavaScript:
   - Without JS the sidebar flows in document order on small screens.
   - With JS it becomes a drawer opened by the Menu button. */
(function (window, document, $) {
    'use strict';

    document.documentElement.classList.remove('no-js');
    document.body.classList.add('js-ready');

    if (!$ || !$.fn) {
        return;
    }

    $(function () {
        var $toggle = $('.nav-toggle');
        var $sidebar = $('#guide-sidebar');
        var $backdrop = $('#nav-backdrop');
        var mq = window.matchMedia('(max-width: 900px)');

        function setOpen(open) {
            $sidebar.toggleClass('is-open', open);
            $toggle.attr('aria-expanded', open ? 'true' : 'false');
            $backdrop.prop('hidden', !open).toggleClass('is-visible', open);
            document.body.classList.toggle('nav-open', open);
        }

        function closeNav() {
            setOpen(false);
        }

        if ($toggle.length && $sidebar.length) {
            $toggle.prop('hidden', false);

            $toggle.on('click', function () {
                if (!mq.matches) {
                    return;
                }
                setOpen(!$sidebar.hasClass('is-open'));
            });

            $backdrop.on('click', closeNav);

            $sidebar.on('click', 'a', function () {
                if (mq.matches) {
                    closeNav();
                }
            });

            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && $sidebar.hasClass('is-open')) {
                    closeNav();
                    $toggle.trigger('focus');
                }
            });

            if (typeof mq.addEventListener === 'function') {
                mq.addEventListener('change', function (e) {
                    if (!e.matches) {
                        closeNav();
                    }
                });
            } else if (typeof mq.addListener === 'function') {
                mq.addListener(function (e) {
                    if (!e.matches) {
                        closeNav();
                    }
                });
            }
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
