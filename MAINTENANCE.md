# MAINTENANCE.md — Changing a feature with VibeKB

This is the complete workflow for a meaningful change. It keeps the living
software model accurate as the software evolves. Follow every step; skipping
verification or the model update is how VibeKB drifts.

Worked example: **adding a `priority` reorder UI to the sample app.**

## 1. Describe current behavior

Read the affected functionality record(s) and be able to state what the
software does today. For the example: `browse-ideas` currently sorts by
`priority ASC` but has **no UI to change priority after creation** — that's why
its status is `partial`.

## 2. Record the requested change

Update `.vibekb/work/current.md`:
- requested outcome ("let me reorder ideas from the list"),
- current behaviour,
- proposed behaviour,
- affected functionality (`browse-ideas`, maybe a new `reorder-ideas`),
- expected files,
- data impact (writes `priority`),
- risks,
- verification plan.

## 3. Identify affected functionality

List every functionality record the change touches, and check `depends_on` /
derived dependents. Reordering writes `priority`, which `browse-ideas` reads —
so both are in scope. Check active warnings: `read-write-path-drift` applies.

## 4. Implement

Make the code changes, respecting constraints (PHP 8.2, no build step,
single-user) and warnings (change read and write paths together).

## 5. Verify

Exercise the real behaviour: reorder ideas, reload the list, confirm order
persists. Record what you tested and how. Set the honest verification state
(`verified-manually` or `verified-by-test`). If something is untested, say so.

## 6. Update functionality records

- Update `browse-ideas`: change status from `partial` to `implemented` **only
  if** reordering now exists and is verified; update the current-behavior,
  flow, data-used, and safe-to-change sections.
- If you added `reorder-ideas`, create its record with all sections and add it
  to `functionality/index.json`.
- Update `files/important-files.json` for any new or changed file.

## 7. Update repository memory

Add only meaningful records:
- a `change` record (before/after/impact) linked to the functionality,
- a `decision` if you chose between real alternatives,
- a `warning` or `discovery` if you learned something risky.
Do not log every edit.

## 8. Update the handoff

Update `.vibekb/work/handoff.md`: current functionality state, completed work,
verification done, what's still open, active warnings, and the exact next
recommended action. Clear or update `.vibekb/work/current.md` when the work is
done.

## After every change: run the checks

```bash
# Syntax
find guide -name '*.php' -print0 | xargs -0 -n1 php -l

# Load the guide and read the Reference view's validation section
VIBEKB_DEV=1 php -S 127.0.0.1:8080 -t .
# open http://127.0.0.1:8080/guide/?view=reference
```

The Reference view must show no validation errors. Every relationship you added
must resolve (no ⚠ broken chips).
