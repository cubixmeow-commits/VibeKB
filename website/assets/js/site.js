/*
 * site.js: tiny, dependency-free page interactions.
 * Copy-to-clipboard for command blocks. Everything works without it.
 */
(function () {
  "use strict";

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
