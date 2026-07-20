# VibeKB

Understand your AI-built projects.

VibeKB keeps an explanation of your project inside the repository (`.vibekb/`) and publishes it as a website, making it easy to understand what your coding agent has built.

## Version 1

- Landing page: `/index.php`
- Live demonstration: `/edition/` — a generated publication for the fictional **SaaS Idea Manager** project
- Content system: `.vibekb/` (JSON metadata + Markdown)

## Local preview

```bash
php -S localhost:8080 -t .
```

Then open:

- http://localhost:8080/
- http://localhost:8080/edition/

## Content layout

```
.vibekb/
  project.json
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

PHP templates under `edition/` read these files and render the publication. Do not duplicate long-form content inside templates.

## Deployment

Production target: `/home/iainmcok/public_html/vibekb/`

See `.cpanel.yml` and [DEPLOYMENT.md](./DEPLOYMENT.md). The rsync deploy includes the hidden `.vibekb/` knowledge directory required by the edition engine.
