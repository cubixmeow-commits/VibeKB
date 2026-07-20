/**
 * VibeKB Project Guide — presentation enhancement
 * Requires jQuery. Progressive enhancement only.
 */
(function ($, window, document) {
  'use strict';

  if (!$ || !$.fn) {
    return;
  }

  var $body = $(document.body);
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var storageKey = $body.data('guide-storage') || 'vibekb-project-guide';
  var $chapters = $('[data-chapter]');
  var $navLinks = $('[data-chapter-link]');
  var $controls = $('[data-guide-controls]');
  var $prev = $('[data-guide-prev]');
  var $next = $('[data-guide-next]');
  var $statusCurrent = $('[data-guide-current]');
  var $live = $('[data-guide-live]');
  var $intro = $('[data-guide-intro]');
  var total = $chapters.length;
  var currentIndex = 0;
  var updatingHash = false;

  if (!total) {
    return;
  }

  function announce(message) {
    $live.text('');
    window.setTimeout(function () {
      $live.text(message);
    }, 20);
  }

  function isTypingTarget(el) {
    if (!el || !el.tagName) {
      return false;
    }
    var tag = el.tagName.toLowerCase();
    if (tag === 'input' || tag === 'textarea' || tag === 'select') {
      return true;
    }
    return !!el.isContentEditable;
  }

  function closeOpenPanels($scope) {
    var $root = $scope && $scope.length ? $scope : $(document);
    $root.find('[data-card-toggle][aria-expanded="true"], [data-problem-toggle][aria-expanded="true"], [data-checklist-toggle][aria-expanded="true"], [data-dev-toggle][aria-expanded="true"], [data-decision-toggle][aria-expanded="true"]').each(function () {
      var $btn = $(this);
      var panelId = $btn.attr('aria-controls');
      $btn.attr('aria-expanded', 'false');
      if (panelId) {
        $('#' + panelId).prop('hidden', true);
      }
      $btn.closest('[data-card]').removeClass('is-open');
    });
  }

  function indexFromHash() {
    var hash = (window.location.hash || '').replace(/^#/, '');
    if (!hash) {
      return -1;
    }
    var $match = $chapters.filter('[data-chapter-hash="' + hash + '"]');
    if (!$match.length) {
      $match = $chapters.filter('#' + hash.replace(/([\\:])/g, '\\$1'));
    }
    if (!$match.length) {
      return -1;
    }
    return parseInt($match.attr('data-chapter-index'), 10);
  }

  function remember(index) {
    try {
      window.localStorage.setItem(storageKey, String(index));
    } catch (err) {
      /* ignore quota / private mode */
    }
  }

  function recalledIndex() {
    try {
      var raw = window.localStorage.getItem(storageKey);
      if (raw === null) {
        return -1;
      }
      var n = parseInt(raw, 10);
      if (isNaN(n) || n < 0 || n >= total) {
        return -1;
      }
      return n;
    } catch (err) {
      return -1;
    }
  }

  function updateNav(index) {
    $navLinks.each(function () {
      var i = parseInt($(this).attr('data-chapter-index'), 10);
      $(this).attr('aria-current', i === index ? 'step' : 'false');
    });
    $statusCurrent.text(String(index + 1));
    $prev.prop('disabled', index <= 0);
    $next.text(index >= total - 1 ? 'Finish' : 'Continue');
  }

  function setHash(index, replace) {
    var hash = $chapters.eq(index).attr('data-chapter-hash');
    if (!hash) {
      return;
    }
    updatingHash = true;
    var url = window.location.pathname + window.location.search + '#' + hash;
    if (replace && window.history.replaceState) {
      window.history.replaceState(null, '', url);
    } else if (window.history.pushState) {
      window.history.pushState(null, '', url);
    } else {
      window.location.hash = hash;
    }
    window.setTimeout(function () {
      updatingHash = false;
    }, 0);
  }

  function showChapter(index, options) {
    options = options || {};
    if (index < 0) {
      index = 0;
    }
    if (index >= total) {
      index = total - 1;
    }

    var previous = currentIndex;
    currentIndex = index;

    $chapters.removeClass('is-active');
    var $active = $chapters.eq(index).addClass('is-active');

    updateNav(index);
    remember(index);

    if (options.updateHash !== false) {
      setHash(index, !!options.replaceHash);
    }

    if (options.focus !== false) {
      var $heading = $active.find('.pg-chapter-title').first();
      if ($heading.length) {
        $heading.trigger('focus');
      }
    }

    if (options.announce !== false && previous !== index) {
      var title = $.trim($active.find('.pg-chapter-title').first().text());
      announce('Chapter ' + (index + 1) + ' of ' + total + ': ' + title);
    }

    if (options.hideIntro) {
      $intro.attr('hidden', true);
    }

    // Soft-init progressive scenes in the active chapter
    initProgression($active);
    initAlignment($active);
    $active.find('[data-timeline]').each(function () {
      $(this).find('[data-timeline-phase]').first().trigger('click');
    });
  }

  function go(delta) {
    showChapter(currentIndex + delta, { hideIntro: true });
  }

  function initProgression($scope) {
    $scope.find('[data-progression]').each(function () {
      var $steps = $(this).find('[data-progression-step]');
      if (!$steps.length || reducedMotion) {
        $steps.addClass('is-current');
        return;
      }
      $steps.removeClass('is-current');
      var i = 0;
      function tick() {
        if (i < $steps.length) {
          $steps.eq(i).addClass('is-current');
          i += 1;
          window.setTimeout(tick, 220);
        }
      }
      tick();
    });
  }

  function initAlignment($scope) {
    $scope.find('[data-alignment]').each(function () {
      var $items = $(this).find('[data-alignment-item]');
      $items.removeClass('is-lit');
      if (reducedMotion) {
        $items.addClass('is-lit');
        return;
      }
      $items.each(function (idx) {
        var $item = $(this);
        window.setTimeout(function () {
          $item.addClass('is-lit');
        }, 120 * idx);
      });
    });
  }

  function bindDisclosure(toggleSel, panelAttr) {
    $(document).on('click', toggleSel, function () {
      var $btn = $(this);
      var expanded = $btn.attr('aria-expanded') === 'true';
      var panelId = $btn.attr('aria-controls');
      var $panel = panelId ? $('#' + panelId) : $btn.siblings('[' + panelAttr + ']').first();
      var next = !expanded;
      $btn.attr('aria-expanded', next ? 'true' : 'false');
      if ($panel.length) {
        $panel.prop('hidden', !next);
        if (next) {
          $panel.trigger('focus');
          if ($panel.is('[data-problem-panel]')) {
            initAlignment($panel);
            initAffects($panel);
          }
          if ($panel.is('[data-checklist-panel]')) {
            initAffects($panel);
          }
        }
      }
      $btn.closest('[data-card]').toggleClass('is-open', next);
    });
  }

  function initAffects($scope) {
    $scope.find('[data-affects-list]').each(function () {
      var $items = $(this).find('[data-affects-item]');
      $items.removeClass('is-lit');
      if (reducedMotion) {
        $items.addClass('is-lit');
        return;
      }
      $items.each(function (idx) {
        var $item = $(this);
        window.setTimeout(function () {
          $item.addClass('is-lit');
        }, 90 * idx);
      });
    });
  }

  function bindTimeline() {
    $('[data-timeline]').each(function () {
      var $timeline = $(this);
      var $phases = $timeline.find('[data-timeline-phase]');
      var $panel = $timeline.find('[data-timeline-panel]');
      var $prev = $timeline.find('[data-timeline-prev]');
      var $next = $timeline.find('[data-timeline-next]');
      var data = [];
      try {
        data = JSON.parse($timeline.find('[data-timeline-data]').text() || '[]');
      } catch (err) {
        data = [];
      }
      var index = 0;

      function render(i) {
        if (!data[i]) {
          return;
        }
        var phase = data[i];
        index = i;
        $phases.removeClass('is-active').attr('aria-selected', 'false').attr('tabindex', '-1');
        $phases.eq(i).addClass('is-active').attr('aria-selected', 'true').attr('tabindex', '0');
        $panel.find('[data-timeline-panel-when]').text(phase.when || '');
        $panel.find('[data-timeline-panel-title]').text(phase.title || '');
        $panel.find('[data-timeline-panel-narrative]').text(phase.narrative || '');
        var $snaps = $panel.find('[data-timeline-snapshots]');
        $snaps.empty();
        if (phase.snapshots && phase.snapshots.length) {
          $snaps.prop('hidden', false);
          phase.snapshots.forEach(function (snap) {
            $snaps.append(
              $('<li/>').append($('<strong/>').text(snap.label || '')).append(document.createTextNode(' ' + (snap.text || '')))
            );
          });
        } else {
          $snaps.prop('hidden', true);
        }
        var $captured = $panel.find('[data-timeline-captured]');
        var $wrap = $panel.find('[data-timeline-captured-wrap]');
        $captured.empty();
        if (phase.captured && phase.captured.length) {
          $wrap.prop('hidden', false);
          phase.captured.forEach(function (item) {
            $captured.append($('<li/>').text(item));
          });
        } else {
          $wrap.prop('hidden', true);
        }
        $prev.prop('disabled', i <= 0);
        $next.prop('disabled', i >= data.length - 1);
      }

      $timeline.on('click', '[data-timeline-phase]', function () {
        render(parseInt($(this).attr('data-phase-index'), 10));
      });
      $prev.on('click', function () {
        render(index - 1);
      });
      $next.on('click', function () {
        render(index + 1);
      });
      if (data.length) {
        render(0);
      }
    });
  }

  function bindEvolution() {
    $('[data-evolution]').each(function () {
      var $evo = $(this);
      var $tabs = $evo.find('[data-evolution-tab]');
      var $panel = $evo.find('[data-evolution-panel]');
      var data = [];
      try {
        data = JSON.parse($evo.find('[data-evolution-data]').text() || '[]');
      } catch (err) {
        data = [];
      }

      function render(i) {
        if (!data[i]) {
          return;
        }
        var snap = data[i];
        $tabs.removeClass('is-active').attr('aria-selected', 'false').attr('tabindex', '-1');
        $tabs.eq(i).addClass('is-active').attr('aria-selected', 'true').attr('tabindex', '0');
        $panel.find('[data-evolution-panel-version]').text(snap.version || '');
        $panel.find('[data-evolution-panel-title]').text(snap.title || '');
        $panel.find('[data-evolution-panel-body]').text(snap.body || '');
        var $changes = $panel.find('[data-evolution-changes]');
        $changes.empty();
        if (snap.changes && snap.changes.length) {
          $changes.prop('hidden', false);
          snap.changes.forEach(function (change) {
            $changes.append($('<li/>').text(change));
          });
        } else {
          $changes.prop('hidden', true);
        }
        var $note = $panel.find('[data-evolution-note]');
        if (snap.note) {
          $note.text(snap.note).prop('hidden', false);
        } else {
          $note.prop('hidden', true);
        }
      }

      $evo.on('click', '[data-evolution-tab]', function () {
        render(parseInt($(this).attr('data-snap-index'), 10));
      });
      if (data.length) {
        render(0);
      }
    });
  }

  function bindAiLoop() {
    $('[data-ai-loop]').each(function () {
      var $loop = $(this);
      var $steps = $loop.find('[data-ailoop-step]');
      var $prev = $loop.find('[data-ailoop-prev]');
      var $next = $loop.find('[data-ailoop-next]');
      var index = 0;

      function activate(i, focusBtn) {
        if (i < 0 || i >= $steps.length) {
          return;
        }
        index = i;
        $steps.removeClass('is-active');
        $steps.find('[data-ailoop-activate]').attr('aria-pressed', 'false');
        var $step = $steps.eq(i).addClass('is-active');
        var $btn = $step.find('[data-ailoop-activate]').attr('aria-pressed', 'true');
        var actor = $.trim($step.find('.pg-ailoop-actor').first().text());
        var title = $.trim($step.find('.pg-ailoop-step-title').first().text());
        var text = $.trim($loop.find('[data-ailoop-step-text="' + i + '"]').text());
        var exampleMatch = text.match(/Example:\s*(.+)$/);
        var bodyText = text;
        var example = '';
        if (exampleMatch) {
          bodyText = $.trim(text.replace(/Example:\s*.+$/, ''));
          example = exampleMatch[1];
        }
        $loop.find('[data-ailoop-panel-actor]').text(actor);
        $loop.find('[data-ailoop-panel-title]').text(title);
        $loop.find('[data-ailoop-panel-text]').text(bodyText);
        var $ex = $loop.find('[data-ailoop-example]');
        if (example) {
          $ex.text(example).prop('hidden', false);
        } else {
          $ex.prop('hidden', true);
        }
        $prev.prop('disabled', i <= 0);
        $next.prop('disabled', i >= $steps.length - 1);
        if (focusBtn) {
          $btn.trigger('focus');
        }
      }

      $loop.on('click', '[data-ailoop-activate]', function () {
        activate(parseInt($(this).closest('[data-ailoop-step]').attr('data-step-index'), 10), false);
      });
      $prev.on('click', function () {
        activate(index - 1, true);
      });
      $next.on('click', function () {
        activate(index + 1, true);
      });
      activate(0, false);
    });
  }

  function bindFlow($root) {
    $root.each(function () {
      var $flow = $(this);
      var $steps = $flow.find('[data-flow-step]');
      var $panelTitle = $flow.find('[data-flow-panel-title]');
      var $panelText = $flow.find('[data-flow-panel-text]');
      var $prevBtn = $flow.find('[data-flow-prev]');
      var $nextBtn = $flow.find('[data-flow-next]');
      var index = 0;

      function activate(i, focusBtn) {
        if (i < 0 || i >= $steps.length) {
          return;
        }
        index = i;
        $steps.removeClass('is-active');
        $steps.find('[data-flow-activate]').attr('aria-pressed', 'false');
        var $step = $steps.eq(i).addClass('is-active');
        var $btn = $step.find('[data-flow-activate]').attr('aria-pressed', 'true');
        var title = $.trim($step.find('.pg-flow-step-title').first().text());
        var text = $.trim($step.find('.pg-flow-step-text').first().text());
        $panelTitle.text(title);
        $panelText.text(text);
        $prevBtn.prop('disabled', i <= 0);
        $nextBtn.prop('disabled', i >= $steps.length - 1);
        if (focusBtn) {
          $btn.trigger('focus');
        }
      }

      $flow.on('click', '[data-flow-activate]', function () {
        var i = parseInt($(this).closest('[data-flow-step]').attr('data-step-index'), 10);
        activate(i, false);
      });

      $prevBtn.on('click', function () {
        activate(index - 1, true);
      });

      $nextBtn.on('click', function () {
        activate(index + 1, true);
      });

      activate(0, false);
    });
  }

  function bindTrouble() {
    $(document).on('click', '[data-trouble-activate]', function () {
      var $step = $(this).closest('[data-trouble-step]');
      var $list = $step.closest('[data-trouble-steps]');
      $list.find('[data-trouble-step]').removeClass('is-active');
      $list.find('[data-trouble-activate]').attr('aria-pressed', 'false');
      $step.addClass('is-active');
      $(this).attr('aria-pressed', 'true');
    });

    $(document).on('click', '[data-trouble-next]', function () {
      var $panel = $(this).closest('[data-problem-panel]');
      var $steps = $panel.find('[data-trouble-step]');
      var $active = $steps.filter('.is-active');
      var i = $steps.index($active);
      if (i < $steps.length - 1) {
        $steps.eq(i + 1).find('[data-trouble-activate]').trigger('click');
      }
      $(this).siblings('[data-trouble-prev]').prop('disabled', false);
      if (i + 1 >= $steps.length - 1) {
        $(this).prop('disabled', true);
      }
    });

    $(document).on('click', '[data-trouble-prev]', function () {
      var $panel = $(this).closest('[data-problem-panel]');
      var $steps = $panel.find('[data-trouble-step]');
      var $active = $steps.filter('.is-active');
      var i = $steps.index($active);
      if (i > 0) {
        $steps.eq(i - 1).find('[data-trouble-activate]').trigger('click');
      }
      $(this).siblings('[data-trouble-next]').prop('disabled', false);
      if (i - 1 <= 0) {
        $(this).prop('disabled', true);
      }
    });
  }

  function bindConceptMap() {
    $(document).on('click', '[data-concept-activate]', function () {
      var $btn = $(this);
      var $map = $btn.closest('[data-concept-map]');
      $map.find('[data-concept-layer]').removeClass('is-active');
      $map.find('[data-concept-activate]').attr('aria-pressed', 'false');
      $btn.closest('[data-concept-layer]').addClass('is-active');
      $btn.attr('aria-pressed', 'true');
      $map.find('[data-concept-detail-title]').text($btn.attr('data-title') || '');
      $map.find('[data-concept-detail-text]').text($btn.attr('data-detail') || $btn.attr('data-text') || '');
      $map.find('[data-concept-detail]').trigger('focus');
    });
  }

  function bindIdeaDemo() {
    $(document).on('submit', '[data-demo-form]', function (event) {
      event.preventDefault();
      var $form = $(this);
      var $demo = $form.closest('[data-idea-demo]');
      var $input = $demo.find('[data-demo-input]');
      var value = $.trim($input.val() || '');
      if (!value) {
        $input.trigger('focus');
        return;
      }
      var $list = $demo.find('[data-demo-list]');
      $list.find('[data-demo-empty]').remove();
      var $item = $('<li/>', {
        'class': 'pg-demo-item is-new',
        text: value
      });
      $list.prepend($item);
      if (reducedMotion) {
        $item.removeClass('is-new');
      } else {
        window.setTimeout(function () {
          $item.removeClass('is-new');
        }, 400);
      }
      $input.val('').trigger('focus');
    });
  }

  function bindCheckboxes() {
    $(document).on('change', '[data-check-box]', function () {
      $(this).closest('[data-check-item]').toggleClass('is-done', this.checked);
    });
  }

  function bindKeyboard() {
    $(document).on('keydown', function (event) {
      if (isTypingTarget(event.target)) {
        return;
      }

      if (event.key === 'Escape') {
        closeOpenPanels($chapters.eq(currentIndex));
        return;
      }

      if (event.key === 'ArrowRight') {
        event.preventDefault();
        if (currentIndex < total - 1) {
          go(1);
        }
        return;
      }

      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        if (currentIndex > 0) {
          go(-1);
        }
      }
    });
  }

  function bindControls() {
    $controls.prop('hidden', false);
    $prev.on('click', function () {
      if (currentIndex > 0) {
        go(-1);
      }
    });
    $next.on('click', function () {
      if (currentIndex < total - 1) {
        go(1);
      } else {
        announce('You finished the Project Guide. Explore developer details or the technical reference.');
        $chapters.eq(currentIndex).find('.pg-chapter-title').trigger('focus');
      }
    });

    $navLinks.on('click', function (event) {
      event.preventDefault();
      var index = parseInt($(this).attr('data-chapter-index'), 10);
      showChapter(index, { hideIntro: true });
    });
  }

  function bindHistory() {
    $(window).on('hashchange popstate', function () {
      if (updatingHash) {
        return;
      }
      var index = indexFromHash();
      if (index >= 0) {
        showChapter(index, { updateHash: false, hideIntro: true });
      }
    });
  }

  function start() {
    $body.addClass('pg-enhanced');
    if (reducedMotion) {
      $body.addClass('pg-reduced');
    }

    bindControls();
    bindHistory();
    bindKeyboard();
    bindDisclosure('[data-card-toggle]', 'data-card-panel');
    bindDisclosure('[data-problem-toggle]', 'data-problem-panel');
    bindDisclosure('[data-checklist-toggle]', 'data-checklist-panel');
    bindDisclosure('[data-dev-toggle]', 'data-dev-panel');
    bindDisclosure('[data-decision-toggle]', 'data-decision-panel');
    bindFlow($('[data-flow]'));
    bindTimeline();
    bindEvolution();
    bindAiLoop();
    bindTrouble();
    bindConceptMap();
    bindIdeaDemo();
    bindCheckboxes();

    var fromHash = indexFromHash();
    var fromMemory = recalledIndex();
    var initial = fromHash >= 0 ? fromHash : (fromMemory >= 0 ? fromMemory : 0);

    showChapter(initial, {
      replaceHash: fromHash < 0,
      hideIntro: initial > 0,
      focus: fromHash >= 0 || fromMemory > 0,
      announce: false
    });
  }

  $(start);
})(window.jQuery, window, document);
