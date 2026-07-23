/*
 * map.js — the Live Repository Map.
 *
 * A progressive enhancement, not a requirement. The homepage already ships an
 * accessible list of every functional area and capability (the ".map-fallback"
 * block). This script reads the real model in model.js and, on screens wide
 * enough to explore it, builds an interactive SVG map on top of that list:
 * functional areas radiating from the application, real relationships drawn
 * between them, capabilities revealed on selection, and a side panel that shows
 * the same record data the guide would.
 *
 * No framework, no build step, no external calls — the map is generated from
 * data already in the page. If JavaScript is off, the small screen, or the data
 * is missing, the fallback list is the experience.
 */
(function () {
  "use strict";

  var SVGNS = "http://www.w3.org/2000/svg";
  var MIN_WIDTH = 720; // below this, the accessible fallback is the experience.

  function ready(fn) {
    if (document.readyState !== "loading") {
      fn();
    } else {
      document.addEventListener("DOMContentLoaded", fn);
    }
  }

  function el(tag, attrs, text) {
    var node = document.createElementNS(SVGNS, tag);
    if (attrs) {
      for (var k in attrs) {
        if (Object.prototype.hasOwnProperty.call(attrs, k)) {
          node.setAttribute(k, attrs[k]);
        }
      }
    }
    if (text != null) node.textContent = text;
    return node;
  }

  function h(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (text != null) node.textContent = text;
    return node;
  }

  // Wrap a label into <= maxChars lines and emit tspans centred on (x, y).
  function label(parent, str, x, y, maxChars, lineHeight) {
    var words = str.split(" ");
    var lines = [];
    var line = "";
    for (var i = 0; i < words.length; i++) {
      var test = line ? line + " " + words[i] : words[i];
      if (test.length > maxChars && line) {
        lines.push(line);
        line = words[i];
      } else {
        line = test;
      }
    }
    if (line) lines.push(line);
    var startY = y - ((lines.length - 1) * lineHeight) / 2;
    for (var j = 0; j < lines.length; j++) {
      var t = el("text", {
        x: x,
        y: startY + j * lineHeight,
        "text-anchor": "middle",
        "dominant-baseline": "middle",
        class: "map-node-label",
      });
      t.textContent = lines[j];
      parent.appendChild(t);
    }
  }

  function verificationLabel(v) {
    switch (v) {
      case "verified-from-source": return "Verified from source";
      case "verified-manually": return "Verified manually";
      case "verified-by-test": return "Verified by test";
      case "reported-by-developer": return "Reported by developer";
      case "inferred-from-source": return "Inferred from source";
      case "not-verified": return "Not verified";
      default: return v || "Unverified";
    }
  }

  function statusLabel(s) {
    return (s || "unknown").replace(/-/g, " ");
  }

  function build(container, model) {
    var areas = model.areas || [];
    var edges = model.edges || [];
    var meta = model.meta || {};

    // ---- geometry ---------------------------------------------------------
    var W = 1000;
    var H = 760;
    var cx = W / 2;
    var cy = H / 2;
    var Rx = 320;
    var Ry = 268;

    var svg = el("svg", {
      viewBox: "0 0 " + W + " " + H,
      class: "map-svg",
      role: "img",
      "aria-label":
        "Interactive map of " +
        areas.length +
        " functional areas in the VibeKB repository. A list of the same areas and capabilities follows.",
    });

    var gEdges = el("g", { class: "map-edges" });
    var gCaps = el("g", { class: "map-caps" });
    var gNodes = el("g", { class: "map-nodes" });
    svg.appendChild(gEdges);
    svg.appendChild(gCaps);
    svg.appendChild(gNodes);

    // ---- place area nodes on an ellipse ----------------------------------
    var positions = {};
    var order = areas.slice();
    // Seat the hub area (the one everything leans on) at the top of the ring
    // so the busiest spoke reads cleanly.
    order.sort(function (a, b) {
      return (b.hub ? 1 : 0) - (a.hub ? 1 : 0);
    });
    var n = areas.length;
    for (var i = 0; i < n; i++) {
      var area = areas[i];
      var ang = (-Math.PI / 2) + (i * 2 * Math.PI) / n;
      positions[area.id] = {
        x: cx + Rx * Math.cos(ang),
        y: cy + Ry * Math.sin(ang),
        ang: ang,
      };
    }

    // ---- edges: cross-area relationships (curved, behind nodes) ----------
    edges.forEach(function (e) {
      var a = positions[e.from];
      var b = positions[e.to];
      if (!a || !b) return;
      var mx = (a.x + b.x) / 2;
      var my = (a.y + b.y) / 2;
      // Bow each edge slightly toward the centre so the web reads as a system.
      var bow = 0.16;
      var qx = mx + (cx - mx) * bow;
      var qy = my + (cy - my) * bow;
      var path = el("path", {
        d: "M" + a.x + "," + a.y + " Q" + qx + "," + qy + " " + b.x + "," + b.y,
        class: "map-edge",
        "data-from": e.from,
        "data-to": e.to,
      });
      gEdges.appendChild(path);
    });

    // ---- spokes: application -> each area --------------------------------
    areas.forEach(function (area) {
      var p = positions[area.id];
      var spoke = el("line", {
        x1: cx, y1: cy, x2: p.x, y2: p.y,
        class: "map-spoke",
        "data-area": area.id,
      });
      gEdges.appendChild(spoke);
    });

    // ---- central application node ----------------------------------------
    var core = el("g", { class: "map-core", tabindex: "0", role: "button",
      "aria-label": "VibeKB application overview" });
    core.appendChild(el("circle", { cx: cx, cy: cy, r: 62, class: "map-core-halo" }));
    core.appendChild(el("circle", { cx: cx, cy: cy, r: 46, class: "map-core-disc" }));
    label(core, meta.app || "VibeKB", cx, cy - 6, 12, 17);
    var coreSub = el("text", {
      x: cx, y: cy + 16, "text-anchor": "middle", class: "map-core-sub",
    });
    coreSub.textContent = "repository";
    core.appendChild(coreSub);
    gNodes.appendChild(core);

    // ---- area nodes -------------------------------------------------------
    var areaEls = {};
    areas.forEach(function (area, idx) {
      var p = positions[area.id];
      var g = el("g", {
        class: "map-node map-area" + (area.hub ? " is-hub" : ""),
        "data-area": area.id,
        tabindex: "0",
        role: "button",
        "aria-label": area.title + ", " + area.capabilities.length + " capabilities. Select to explore.",
        "aria-expanded": "false",
        style: "--i:" + idx,
      });
      g.appendChild(el("circle", { cx: p.x, cy: p.y, r: 54, class: "map-node-halo" }));
      g.appendChild(el("circle", { cx: p.x, cy: p.y, r: 40, class: "map-node-disc" }));
      // count badge
      g.appendChild(el("circle", { cx: p.x + 30, cy: p.y - 30, r: 13, class: "map-node-badge" }));
      var badge = el("text", { x: p.x + 30, y: p.y - 30, "text-anchor": "middle",
        "dominant-baseline": "central", class: "map-node-badge-text" });
      badge.textContent = String(area.capabilities.length);
      g.appendChild(badge);
      label(g, area.title, p.x, p.y, 13, 15);
      gNodes.appendChild(g);
      areaEls[area.id] = g;
    });

    // ---- side panel -------------------------------------------------------
    var panel = document.getElementById("map-panel");

    function renderDefault() {
      panel.innerHTML = "";
      panel.appendChild(h("p", "map-panel-kicker", "The whole system"));
      panel.appendChild(h("h3", "map-panel-title", meta.app + " — " + (meta.outcome || "")));
      panel.appendChild(
        h(
          "p",
          "map-panel-lead",
          "Every node is a real functional area of this repository. Select one to see its capabilities, the files behind them, and how far each is verified."
        )
      );
      var grid = h("dl", "map-panel-stats");
      (model.stats || []).forEach(function (s) {
        var d = h("div", null);
        d.appendChild(h("dt", null, String(s.value)));
        d.appendChild(h("dd", null, s.label));
        grid.appendChild(d);
      });
      panel.appendChild(grid);
      var hint = h("p", "map-panel-hint", "Tip: use Tab and Enter to explore the map by keyboard.");
      panel.appendChild(hint);
    }

    function renderArea(area) {
      panel.innerHTML = "";
      panel.appendChild(h("p", "map-panel-kicker", "Functional area"));
      panel.appendChild(h("h3", "map-panel-title", area.title));
      panel.appendChild(h("p", "map-panel-lead", area.blurb));
      panel.appendChild(h("p", "map-panel-sub", area.capabilities.length + " capabilities"));
      var list = h("ul", "map-panel-caps");
      area.capabilities.forEach(function (cap) {
        var li = h("li", null);
        var btn = h("button", "map-cap-chip");
        btn.type = "button";
        btn.setAttribute("data-cap", cap.id);
        var dot = h("span", "map-cap-dot status-" + cap.status);
        btn.appendChild(dot);
        btn.appendChild(h("span", "map-cap-name", cap.title));
        btn.addEventListener("click", function () {
          selectCapability(area, cap);
        });
        li.appendChild(btn);
        list.appendChild(li);
      });
      panel.appendChild(list);
      var link = h("a", "map-panel-link", "Open this area in the live guide →");
      link.href = meta.guideBase + "?view=functionality&area=" + encodeURIComponent(area.id);
      link.rel = "noopener";
      panel.appendChild(link);
    }

    function selectCapability(area, cap) {
      panel.innerHTML = "";
      var back = h("button", "map-panel-back", "‹ " + area.title);
      back.type = "button";
      back.addEventListener("click", function () { renderArea(area); });
      panel.appendChild(back);
      panel.appendChild(h("h3", "map-panel-title", cap.title));
      var tags = h("p", "map-panel-tags");
      tags.appendChild(h("span", "map-tag status-" + cap.status, statusLabel(cap.status)));
      tags.appendChild(h("span", "map-tag verify", verificationLabel(cap.verification)));
      panel.appendChild(tags);
      panel.appendChild(h("p", "map-panel-lead", cap.summary));
      if (cap.files && cap.files.length) {
        panel.appendChild(h("p", "map-panel-sub", "Files that matter"));
        var fl = h("ul", "map-panel-files");
        cap.files.forEach(function (f) {
          fl.appendChild(h("li", null, f));
        });
        panel.appendChild(fl);
      }
      var link = h("a", "map-panel-link", "Open this functionality in the live guide →");
      link.href = meta.guideBase + "?view=functionality&id=" + encodeURIComponent(cap.id);
      link.rel = "noopener";
      panel.appendChild(link);
    }

    // ---- selection state --------------------------------------------------
    var selected = null;

    function clearCaps() {
      while (gCaps.firstChild) gCaps.removeChild(gCaps.firstChild);
    }

    function deselect() {
      selected = null;
      svg.classList.remove("has-selection");
      Object.keys(areaEls).forEach(function (id) {
        areaEls[id].classList.remove("is-selected", "is-related");
        areaEls[id].setAttribute("aria-expanded", "false");
      });
      Array.prototype.forEach.call(gEdges.children, function (e) {
        e.classList.remove("is-active");
      });
      clearCaps();
      renderDefault();
    }

    function relatedAreas(id) {
      var rel = {};
      edges.forEach(function (e) {
        if (e.from === id) rel[e.to] = true;
        if (e.to === id) rel[e.from] = true;
      });
      return rel;
    }

    function selectArea(area) {
      if (selected === area.id) {
        deselect();
        return;
      }
      selected = area.id;
      svg.classList.add("has-selection");
      var rel = relatedAreas(area.id);
      Object.keys(areaEls).forEach(function (id) {
        var g = areaEls[id];
        g.classList.toggle("is-selected", id === area.id);
        g.classList.toggle("is-related", !!rel[id]);
        g.setAttribute("aria-expanded", id === area.id ? "true" : "false");
      });
      Array.prototype.forEach.call(gEdges.children, function (e) {
        var f = e.getAttribute("data-from");
        var t = e.getAttribute("data-to");
        var a = e.getAttribute("data-area");
        e.classList.toggle(
          "is-active",
          a === area.id || f === area.id || t === area.id
        );
      });

      // capability satellites around the selected area
      clearCaps();
      var p = positions[area.id];
      var caps = area.capabilities;
      var outward = Math.atan2(p.y - cy, p.x - cx);
      var count = caps.length;
      var spread = Math.min(count * 0.46, 2.6); // radians
      var start = outward - spread / 2;
      var step = count > 1 ? spread / (count - 1) : 0;
      var r = 108;
      caps.forEach(function (cap, k) {
        var a = count > 1 ? start + k * step : outward;
        var sx = p.x + r * Math.cos(a);
        var sy = p.y + r * Math.sin(a);
        var link = el("line", {
          x1: p.x, y1: p.y, x2: sx, y2: sy, class: "map-cap-link", style: "--k:" + k,
        });
        gCaps.appendChild(link);
        var cg = el("g", {
          class: "map-cap-node status-" + cap.status,
          tabindex: "0",
          role: "button",
          "aria-label": cap.title + ". " + statusLabel(cap.status) + ", " + verificationLabel(cap.verification),
          style: "--k:" + k,
        });
        cg.appendChild(el("circle", { cx: sx, cy: sy, r: 8, class: "map-cap-ring" }));
        cg.appendChild(el("circle", { cx: sx, cy: sy, r: 4.5, class: "map-cap-core" }));
        // short caption to the outward side
        var tx = sx + (Math.cos(a) >= 0 ? 13 : -13);
        var anchor = Math.cos(a) >= 0 ? "start" : "end";
        var cap_t = el("text", { x: tx, y: sy, "text-anchor": anchor,
          "dominant-baseline": "central", class: "map-cap-caption" });
        cap_t.textContent = shortTitle(cap.title);
        cg.appendChild(cap_t);
        function pick() { selectCapability(area, cap); }
        cg.addEventListener("click", pick);
        cg.addEventListener("keydown", function (ev) {
          if (ev.key === "Enter" || ev.key === " ") { ev.preventDefault(); pick(); }
        });
        gCaps.appendChild(cg);
      });

      renderArea(area);
    }

    function shortTitle(t) {
      if (t.length <= 24) return t;
      return t.slice(0, 22).replace(/\s+\S*$/, "") + "…";
    }

    // ---- wire interactions ------------------------------------------------
    areas.forEach(function (area) {
      var g = areaEls[area.id];
      g.addEventListener("click", function () { selectArea(area); });
      g.addEventListener("keydown", function (ev) {
        if (ev.key === "Enter" || ev.key === " ") { ev.preventDefault(); selectArea(area); }
      });
      g.addEventListener("mouseenter", function () {
        if (!selected) g.classList.add("is-hover");
      });
      g.addEventListener("mouseleave", function () { g.classList.remove("is-hover"); });
    });
    core.addEventListener("click", deselect);
    core.addEventListener("keydown", function (ev) {
      if (ev.key === "Enter" || ev.key === " ") { ev.preventDefault(); deselect(); }
    });
    svg.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape") deselect();
    });

    // ---- mount ------------------------------------------------------------
    container.innerHTML = "";
    container.appendChild(svg);
    renderDefault();

    // gentle staggered reveal (skipped under reduced-motion via CSS)
    requestAnimationFrame(function () {
      svg.classList.add("is-live");
    });
  }

  ready(function () {
    var container = document.getElementById("map-canvas");
    var model = window.VIBEKB_MODEL;
    if (!container || !model || !model.areas) return; // fallback stays.

    var mq = window.matchMedia("(min-width: " + MIN_WIDTH + "px)");
    var built = false;
    function attempt() {
      if (mq.matches && !built) {
        try {
          build(container, model);
          document.body.classList.add("map-live");
          built = true;
        } catch (err) {
          // Any failure leaves the accessible fallback in place.
          document.body.classList.remove("map-live");
        }
      }
    }
    attempt();
    if (mq.addEventListener) {
      mq.addEventListener("change", attempt);
    } else if (mq.addListener) {
      mq.addListener(attempt);
    }
  });
})();
