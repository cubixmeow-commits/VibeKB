/**
 * VibeKB layered homepage interactions.
 * Progressive enhancement only — hidden/collapsed states apply after init.
 */
(function (window, document, $) {
  'use strict';

  if (!$ || !$.fn) {
    return;
  }

  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function isTyping(el) {
    if (!el || !el.tagName) {
      return false;
    }
    var tag = el.tagName.toLowerCase();
    return tag === 'input' || tag === 'textarea' || tag === 'select' || !!el.isContentEditable;
  }

  /**
   * Accessible tab/step activator shared by homepage widgets.
   */
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

    // Ensure initial ARIA/hidden sync without wiping no-JS content before this ran.
    activate(0, false);

    return {
      activate: activate,
      count: $tabs.length
    };
  }

  window.VibeKBHomepage = {
    init: function () {
      document.documentElement.classList.add('js');
      this.bindLayerControls();
      this.bindStoryJourney();
      this.bindGuidePreview();
      this.bindPipeline();
      this.bindDepthSelector();
      this.bindAccordions();
      this.bindHeroFlow();
      this.bindManifesto();
      this.bindCompare();
      this.bindRelevance();
      this.bindRepoMap();
      this.restoreState();
      this.bindHashNav();
    },

    bindLayerControls: function () {
      bindTabGroup({
        root: '[data-tabs="outcomes"]',
        tab: '[data-tab]',
        panel: '[data-tab-panel]',
        attr: 'data-tab'
      });
    },

    bindStoryJourney: function () {
      var $root = $('[data-story-journey]');
      if (!$root.length) {
        return;
      }

      var $panels = $root.find('[data-story-panel]');
      var $dots = $('[data-story-nav] [data-story-dot]');

      function show(index) {
        if (index < 0 || index >= $panels.length) {
          return;
        }
        $panels.removeClass('is-active').eq(index).addClass('is-active');
        $dots.removeClass('is-active').removeAttr('aria-current').eq(index)
          .addClass('is-active').attr('aria-current', 'step');
        if (!reducedMotion && $panels.eq(index)[0]) {
          $panels.eq(index)[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }

      $dots.on('click', function () {
        var index = parseInt($(this).attr('data-story-dot'), 10);
        if (!isNaN(index)) {
          show(index);
        }
      });

      show(0);
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

    bindPipeline: function () {
      bindTabGroup({
        root: '[data-pipeline]',
        tab: '[data-pipe]',
        panel: '[data-pipe-panel]',
        attr: 'data-pipe'
      });
    },

    bindDepthSelector: function () {
      bindTabGroup({
        root: '[data-depth-selector]',
        tab: '[data-depth]',
        panel: '[data-depth-panel]',
        attr: 'data-depth'
      });
    },

    bindAccordions: function () {
      // Native <details> remain; ensure Escape does not trap focus oddly.
      $(document).on('keydown', function (event) {
        if (event.key !== 'Escape' || isTyping(event.target)) {
          return;
        }
        var $open = $('details.hp-details[open]');
        if ($open.length) {
          $open.prop('open', false);
        }
      });
    },

    bindHeroFlow: function () {
      var $items = $('[data-hero-flow] > li').not('.hp-mini-flow-arrow');
      if (!$items.length || reducedMotion) {
        $items.addClass('is-active');
        return;
      }

      var i = 0;
      function tick() {
        $items.removeClass('is-active');
        $items.eq(i % $items.length).addClass('is-active');
        i += 1;
        window.setTimeout(tick, 1600);
      }
      tick();
    },

    bindManifesto: function () {
      var $root = $('[data-manifesto]');
      if (!$root.length) {
        return;
      }

      var $items = $root.find('[data-manifesto-item]');
      var index = 0;

      function show(i) {
        if (i < 0 || i >= $items.length) {
          return;
        }
        index = i;
        $items.each(function (n) {
          var on = n === i;
          $(this).toggleClass('is-active', on).prop('hidden', !on);
        });
        $root.find('[data-manifesto-current]').text(String(i + 1));
        $root.find('[data-manifesto-prev]').prop('disabled', i <= 0);
        $root.find('[data-manifesto-next]').prop('disabled', i >= $items.length - 1);
      }

      $root.on('click', '[data-manifesto-prev]', function () {
        show(index - 1);
      });
      $root.on('click', '[data-manifesto-next]', function () {
        show(index + 1);
      });

      show(0);
    },

    bindCompare: function () {
      bindTabGroup({
        root: '[data-compare]',
        tab: '[data-cmp]',
        panel: '[data-cmp-panel]',
        attr: 'data-cmp'
      });
    },

    bindRelevance: function () {
      bindTabGroup({
        root: '[data-relevance]',
        tab: '[data-rel]',
        panel: '[data-rel-panel]',
        attr: 'data-rel'
      });
    },

    bindRepoMap: function () {
      bindTabGroup({
        root: '[data-repo-map]',
        tab: '[data-repo]',
        panel: '[data-repo-panel]',
        attr: 'data-repo'
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
        // Defer scroll so layout is ready.
        window.setTimeout(function () {
          if (!reducedMotion && $target[0].scrollIntoView) {
            $target[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
          } else if ($target[0].scrollIntoView) {
            $target[0].scrollIntoView(true);
          }
        }, 50);
      }
    },

    bindHashNav: function () {
      $(document).on('click', 'a[href^="#"]', function () {
        var href = $(this).attr('href') || '';
        var id = href.slice(1);
        if (!id) {
          return;
        }
        // Let the browser handle navigation; optionally sync state.
        window.VibeKBHomepage.updateUrlState(id);
      });
    }
  };

  $(function () {
    window.VibeKBHomepage.init();
  });
})(window, document, window.jQuery);
