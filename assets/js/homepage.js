/**
 * VibeKB homepage — guide carousel + copyable install commands.
 * Progressive enhancement: commands remain readable without JavaScript.
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

  function resolveCopyText($btn) {
    var target = $btn.attr('data-copy-target');
    if (target) {
      var $el = $(target);
      if ($el.length) {
        return $.trim($el.text());
      }
    }
    return $.trim($btn.attr('data-copy') || '');
  }

  function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.setAttribute('aria-hidden', 'true');
    ta.style.position = 'fixed';
    ta.style.top = '0';
    ta.style.left = '0';
    ta.style.width = '1px';
    ta.style.height = '1px';
    ta.style.padding = '0';
    ta.style.border = 'none';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    ta.setSelectionRange(0, text.length);
    var ok = false;
    try {
      ok = document.execCommand('copy');
    } catch (err) {
      ok = false;
    }
    document.body.removeChild(ta);
    return ok;
  }

  function selectTargetText(selector) {
    try {
      var node = document.querySelector(selector);
      if (!node) {
        return;
      }
      var range = document.createRange();
      range.selectNodeContents(node);
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(range);
    } catch (err) {
      // Commands remain visible for manual copy.
    }
  }

  function flashCopied($btn) {
    var original = $btn.data('copy-label') || $btn.text();
    $btn.data('copy-label', original);
    $btn.addClass('is-copied').attr('aria-live', 'polite').text('Copied');
    window.setTimeout(function () {
      $btn.removeClass('is-copied').text(original);
    }, 1600);
  }

  function copyText(text, onSuccess, onFail) {
    var clip = window.navigator && window.navigator.clipboard;
    if (clip && typeof clip.writeText === 'function') {
      clip.writeText(text).then(onSuccess).catch(function () {
        if (fallbackCopy(text)) {
          onSuccess();
        } else {
          onFail();
        }
      });
      return;
    }
    if (fallbackCopy(text)) {
      onSuccess();
    } else {
      onFail();
    }
  }

  window.VibeKBHomepage = {
    init: function () {
      document.documentElement.classList.add('js');
      this.bindGuidePreview();
      this.bindCopyButtons();
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

    bindCopyButtons: function () {
      $(document).on('click', '[data-copy-target], [data-copy]', function (event) {
        event.preventDefault();
        var $btn = $(this);
        var text = resolveCopyText($btn);
        if (!text) {
          return;
        }

        copyText(text, function () {
          flashCopied($btn);
        }, function () {
          selectTargetText($btn.attr('data-copy-target') || '');
        });
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
