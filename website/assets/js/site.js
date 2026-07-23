/*
 * site.js: tiny, dependency-free page interactions.
 * Theme toggle (Light / Dark) + copy-to-clipboard for command blocks.
 * Everything works without it: the no-flash <head> script sets the initial
 * theme, and command blocks are still readable and selectable.
 */
(function () {
  "use strict";

  /* ---- Theme toggle ---------------------------------------------------- */
  var root = document.documentElement;
  var STORE = "vibekb-theme";

  function current() {
    return root.getAttribute("data-theme") === "dark" ? "dark" : "light";
  }

  function syncButton(btn) {
    var isDark = current() === "dark";
    btn.setAttribute("aria-pressed", isDark ? "true" : "false");
    var label = isDark ? "Switch to light theme" : "Switch to dark theme";
    btn.setAttribute("aria-label", label);
    btn.setAttribute("title", label);
  }

  function initTheme() {
    var btn = document.querySelector("[data-theme-toggle]");
    if (!btn) return;
    // Ensure the attribute exists even if the inline head script was blocked.
    if (current() !== "dark") root.setAttribute("data-theme", current());
    syncButton(btn);

    btn.addEventListener("click", function () {
      var next = current() === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      try { localStorage.setItem(STORE, next); } catch (e) {}
      syncButton(btn);
    });

    // Follow the system theme until the user makes a manual choice.
    if (window.matchMedia) {
      var mq = window.matchMedia("(prefers-color-scheme: dark)");
      var onChange = function (e) {
        var stored;
        try { stored = localStorage.getItem(STORE); } catch (err) { stored = null; }
        if (stored === "light" || stored === "dark") return;
        root.setAttribute("data-theme", e.matches ? "dark" : "light");
        syncButton(btn);
      };
      if (mq.addEventListener) mq.addEventListener("change", onChange);
      else if (mq.addListener) mq.addListener(onChange);
    }
  }

  if (document.readyState !== "loading") initTheme();
  else document.addEventListener("DOMContentLoaded", initTheme);

  function copy(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    // Legacy fallback.
    return new Promise(function (resolve, reject) {
      try {
        var ta = document.createElement("textarea");
        ta.value = text;
        ta.setAttribute("readonly", "");
        ta.style.position = "absolute";
        ta.style.left = "-9999px";
        document.body.appendChild(ta);
        ta.select();
        document.execCommand("copy");
        document.body.removeChild(ta);
        resolve();
      } catch (e) {
        reject(e);
      }
    });
  }

  document.addEventListener("click", function (ev) {
    var btn = ev.target.closest ? ev.target.closest(".cmd-copy") : null;
    if (!btn) return;
    var sel = btn.getAttribute("data-copy");
    var target = sel && document.querySelector(sel);
    if (!target) return;
    copy(target.textContent.trim()).then(
      function () {
        var prev = btn.textContent;
        btn.textContent = "Copied";
        btn.classList.add("is-copied");
        setTimeout(function () {
          btn.textContent = prev;
          btn.classList.remove("is-copied");
        }, 1600);
      },
      function () {
        btn.textContent = "Copy failed";
        setTimeout(function () { btn.textContent = "Copy"; }, 1600);
      }
    );
  });
})();
