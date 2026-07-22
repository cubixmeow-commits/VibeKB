/**
 * VibeKB homepage — guide carousel only. Progressive enhancement.
 */
(function (window, document, $) {
  'use strict';

  if (!$ || !$.fn) {
    return;
  }

  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function bindTabGroup(options) {
    var $root = $(options.root);
    if (!$root.length) {
      return;
    }

    var tabSel = options.tab;
    var panelSel = options.panel;
    var attr = options.attr;
    var $tabs = $root.find(tabSel);
    var $panels = $root.find(panelSel);

    function activate(index, focusTab) {
      if (index < 0 || index >= $tabs.length) {
        return;
      }

      $tabs.each(function (i) {
        var on = i === index;
        $(this)
          .toggleClass('is-active', on)
          .attr('aria-selected', on ? 'true' : 'false')
          .attr('tabindex', on ? 0 : -1);
      });

      $panels.each(function (i) {
        var on = i === index;
        $(this).toggleClass('is-active', on).prop('hidden', !on);
      });

      if (typeof options.onChange === 'function') {
        options.onChange(index, $root);
      }

      if (focusTab) {
        $tabs.eq(index).trigger('focus');
      }
    }

    $root.on('click', tabSel, function (event) {
      event.preventDefault();
      var index = parseInt($(this).attr(attr), 10);
      if (isNaN(index)) {
        index = $tabs.index(this);
      }
      activate(index, false);
    });

    $root.on('keydown', tabSel, function (event) {
      var key = event.key;
      var current = $tabs.index(this);
      var next = current;

      if (key === 'ArrowRight' || key === 'ArrowDown') {
        next = (current + 1) % $tabs.length;
      } else if (key === 'ArrowLeft' || key === 'ArrowUp') {
        next = (current - 1 + $tabs.length) % $tabs.length;
      } else if (key === 'Home') {
        next = 0;
      } else if (key === 'End') {
        next = $tabs.length - 1;
      } else {
        return;
      }

      event.preventDefault();
      activate(next, true);
    });

    activate(0, false);

    return { activate: activate, count: $tabs.length };
  }

  window.VibeKBHomepage = {
    init: function () {
      document.documentElement.classList.add('js');
      this.bindGuidePreview();
      this.restoreState();
      this.bindHashNav();
    },

    bindGuidePreview: function () {
      var $preview = $('[data-guide-preview]');
      if (!$preview.length) {
        return;
      }

      var total = $preview.find('[data-guide-chapter]').length;

      var group = bindTabGroup({
        root: $preview,
        tab: '[data-guide-chapter]',
        panel: '[data-guide-panel]',
        attr: 'data-guide-chapter',
        onChange: function (index) {
          $preview.find('[data-guide-current]').text(String(index + 1));
          $preview.find('[data-guide-prev]').prop('disabled', index <= 0);
          $preview.find('[data-guide-next]').prop('disabled', index >= total - 1);
        }
      });

      $preview.on('click', '[data-guide-prev]', function () {
        var current = parseInt($preview.find('[data-guide-chapter][aria-selected="true"]').attr('data-guide-chapter'), 10) || 0;
        group.activate(current - 1, true);
      });

      $preview.on('click', '[data-guide-next]', function () {
        var current = parseInt($preview.find('[data-guide-chapter][aria-selected="true"]').attr('data-guide-chapter'), 10) || 0;
        group.activate(current + 1, true);
      });
    },

    updateUrlState: function (hash) {
      if (!hash || !window.history || !window.history.replaceState) {
        return;
      }
      var url = window.location.pathname + window.location.search + '#' + hash.replace(/^#/, '');
      window.history.replaceState(null, '', url);
    },

    restoreState: function () {
      var hash = (window.location.hash || '').replace(/^#/, '');
      if (!hash) {
        return;
      }
      var $target = $('#' + hash.replace(/([\\:])/g, '\\$1'));
      if ($target.length) {
        window.setTimeout(function () {
          if ($target[0].scrollIntoView) {
            $target[0].scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
          }
        }, 50);
      }
    },

    bindHashNav: function () {
      $(document).on('click', 'a[href^="#"]', function () {
        var href = $(this).attr('href') || '';
        var id = href.slice(1);
        if (id) {
          window.VibeKBHomepage.updateUrlState(id);
        }
      });
    }
  };

  $(function () {
    window.VibeKBHomepage.init();
  });
})(window, document, window.jQuery);
