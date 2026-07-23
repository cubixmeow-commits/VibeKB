# VibeKB public website (`/website/`)

A self-contained marketing site for VibeKB. It is **static** — plain HTML, CSS,
and vanilla JavaScript with no build step, no framework, and no server code — so
it deploys anywhere and keeps every guardrail the product itself honours (PHP 8.2
shared hosting, subfolder-safe, usable without JavaScript, no external API for
core rendering).

## What's here

```
website/
  index.html                     the whole homepage
  assets/
    css/site.css                 the design system (ink hero + warm paper body)
    js/map.js                    the Live Repository Map (progressive enhancement)
    js/site.js                   copy-to-clipboard for command blocks
    data/model.js                the real .vibekb model the map is drawn from
```

## The signature visual: the Live Repository Map

The hero centrepiece is an interactive SVG map of VibeKB's own functional areas,
drawn from **real repository data** in `assets/data/model.js`. That data is
transcribed from VibeKB's living model:

- `.vibekb/functionality/index.json` and `functionality/records/*.md` — the 8
  functional areas and their 23 capabilities (title, summary, status, files,
  verification), and
- `.vibekb/diagrams/topology/vibekb-architecture.json` — the real cross-area
  relationships, derived from the `depends_on` links between records.

It is a **progressive enhancement**. The page always ships an accessible list of
the same areas and capabilities (`.map-fallback`). On screens ≥ 720px with
JavaScript, `map.js` builds the interactive map on top of that list; otherwise
the list is the experience. Nothing is invented — if the model changes, refresh
`model.js` from it (the same discipline the product follows).

### Keeping `model.js` honest

`model.js` is a hand-maintained snapshot, not a live read of `.vibekb/`. When
VibeKB's own functionality, areas, files, or verification states change, update
`model.js` and the proof/stat figures in `index.html` to match. The figures in
`index.html` are the real, current totals at the recorded source commit
(`fd08afa`, analysed 2026-07-23).

## Deploy

The site is a folder of static files. Any of these work with no changes:

- **GitHub Pages** — publish the repository (or a `gh-pages` branch) and serve
  `/website/`, or copy its contents to the Pages root. All asset links are
  relative, so it works at a web root or under a repository subpath.
- **cPanel / Apache / Nginx** — copy `website/` into the public folder (or a
  subfolder). No PHP, rewrite rules, or database required.
- **Netlify / Cloudflare Pages / any static host** — set the publish directory
  to `website/`.

To preview locally:

```bash
php -S localhost:8080 -t website     # or: python3 -m http.server -d website 8080
```

Then open <http://localhost:8080/>.

## Relationship to the existing homepage

The repository already ships a PHP homepage at the repository root
(`index.php`), which reads `.vibekb/` live. This `/website/` folder is a
separate, fully static presentation of the product, built around the
Functionality Map as VibeKB's signature visual. It does not replace `index.php`;
choose whichever deployment target fits the host.

## Accessibility & performance notes

- Semantic landmarks (`header`/`main`/`footer`/`nav`), a skip link, and a single
  `<h1>`.
- The map's SVG nodes are focusable buttons with `aria-label`s; the whole map
  degrades to a linked list without JavaScript.
- Colour is never the only signal — capability status and verification are always
  stated in text.
- Respects `prefers-reduced-motion` (load and idle animations collapse).
- No external JavaScript/CSS dependencies. The only network request is Google
  Fonts, which falls back to system fonts if blocked.
- No horizontal overflow at 320px and up; command blocks scroll within their own
  box.
