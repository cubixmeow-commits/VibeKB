# VibeKB

Understand your AI-built projects.

VibeKB keeps an explanation of your project inside the repository (`.vibekb/`) and publishes it as a website, making it easy to understand what your coding agent has built.

## Version 1

- Landing page: `/index.php`
- Primary sample: `/guide/` — **SaaS Idea Manager Project Guide** (guided presentation)
- Technical reference: `/edition/` — full structured articles from `.vibekb/` knowledge files
- Content system: `.vibekb/` (JSON metadata + Markdown + guide chapters)

## Local preview

```bash
php -S localhost:8080 -t .
```

Then open:

- http://localhost:8080/
- http://localhost:8080/guide/
- http://localhost:8080/edition/

## Content layout

```
.vibekb/
  project.json
  guide/
    guide.json
    chapters/
  edition.json
  homepage.json
  collections.json
  overview/
  decisions/
  risks/
  mental-models/
  warnings/
  assumptions/
  debugging/
  modules/
  glossary/
  editorial/
```

The Project Guide engine under `guide/` renders chapter JSON as an interactive presentation.
The edition engine under `edition/` remains the complete technical reference.
Do not bury long-form knowledge only inside templates or JavaScript.

## Documentation

- [Project Guide V1 plan](./docs/PROJECT_GUIDE_V1_PLAN.md)
- [Project Guide engine](./docs/PROJECT_GUIDE_ENGINE.md)
- [Content rules](./docs/PROJECT_GUIDE_CONTENT_RULES.md)

## Deployment

Production target: `/home/iainmcok/public_html/vibekb/`

See `.cpanel.yml` and [DEPLOYMENT.md](./DEPLOYMENT.md). The rsync deploy includes the hidden `.vibekb/` knowledge directory (including `.vibekb/guide/`) required by both engines.
