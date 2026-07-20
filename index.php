<?php

declare(strict_types=1);

/**
 * VibeKB homepage — the previous layered, jQuery-enhanced homepage structure,
 * with copy rewritten to represent the actual V1 product and the guide-preview
 * carousel driven by real functionality records from `.vibekb/`.
 *
 * Interactions live in assets/js/homepage.js (progressive enhancement over
 * jQuery); styling in assets/css/homepage.css. All widgets degrade to their
 * .hp-*-fallback content without JavaScript.
 */

require_once __DIR__ . '/guide/lib/helpers.php';
require_once __DIR__ . '/guide/lib/Content.php';

// Revalidate the HTML each load so freshly versioned asset URLs are picked up.
if (!headers_sent()) {
    header('Cache-Control: no-cache, must-revalidate');
}

function hp_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Cache-busting URL for a homepage asset (?v=<file-mtime>). */
function hp_asset(string $rel): string
{
    $rel = ltrim($rel, '/');
    $fsPath = __DIR__ . '/' . $rel;
    $version = is_file($fsPath) ? (string) filemtime($fsPath) : '1';
    return $rel . '?v=' . $version;
}

/** Site-root-relative link into a guide view (homepage is above the guide dir). */
function hp_guide(string $view = '', array $params = []): string
{
    $q = [];
    if ($view !== '' && $view !== 'overview') {
        $q['view'] = $view;
    }
    foreach ($params as $k => $v) {
        $q[$k] = $v;
    }
    return 'guide/' . ($q === [] ? '' : '?' . http_build_query($q));
}

/** Pull the numbered "Step-by-step flow" list out of a functionality body. */
function hp_flow_steps(string $body): array
{
    if (!preg_match('/^##\s*Step-by-step flow\s*$(.*?)(?=^##\s|\z)/ms', $body, $m)) {
        return [];
    }
    $steps = [];
    foreach (preg_split('/\r?\n/', $m[1]) as $line) {
        if (preg_match('/^\s*\d+\.\s+(.*)$/', $line, $lm)) {
            // Strip simple inline Markdown (code ticks, bold) for plain display.
            $step = str_replace(['`', '**'], '', trim($lm[1]));
            $steps[] = $step;
        }
    }
    return $steps;
}

/** Map a functionality status to an hp-status tone modifier. */
function hp_status_tone(string $status): string
{
    $tone = status_tone($status);
    return $tone === 'unknown' ? 'muted' : $tone;
}

$guideUrl = 'guide/';
$repoUrl = 'https://github.com/cubixmeow-commits/VibeKB';

$content = new Content(__DIR__ . '/.vibekb');
$loaded = false;
try {
    $content->load();
    $loaded = true;
} catch (Throwable $e) {
    $loaded = false;
}

$manifest = $loaded ? $content->manifest() : [];
$example = is_array($manifest['example_project'] ?? null) ? $manifest['example_project'] : [];
$identity = $loaded ? $content->projectDoc('identity') : null;
$sampleName = (string) ($identity['meta']['title'] ?? ($example['name'] ?? 'the example project'));
$sampleTagline = (string) ($identity['meta']['one_liner'] ?? '');
$sampleRepo = (string) ($example['source_repository'] ?? '');
$statusCounts = $loaded ? $content->statusCounts() : [];

// Live metrics computed from the actual loaded content (never invented).
$groups = $loaded ? $content->functionalityGroups() : [];
$allFunc = $loaded ? $content->allFunctionality() : [];
$funcCount = count($allFunc);
$groupCount = count($groups);
$warnCount = $loaded ? count($content->memoryByType('warnings')) : 0;
$currentWork = $loaded ? $content->currentWork() : null;
$verifiedFromSource = 0;
foreach ($allFunc as $rec) {
    if (($rec['meta']['verification'] ?? '') === 'verified-from-source') {
        $verifiedFromSource++;
    }
}
$verifiedPct = $funcCount > 0 ? (int) round($verifiedFromSource / $funcCount * 100) : 0;

// Build the preview carousel from real functionality records (index order).
$previewItems = [];
if ($loaded) {
    foreach ($content->functionalityGroups() as $group) {
        foreach ($group['records'] as $rec) {
            $m = $rec['meta'];
            $previewItems[] = [
                'id' => (string) ($m['id'] ?? ''),
                'title' => (string) ($m['title'] ?? $m['id'] ?? ''),
                'summary' => (string) ($m['summary'] ?? ''),
                'status' => (string) ($m['status'] ?? 'unknown'),
                'verification' => (string) ($m['verification'] ?? ''),
                'trigger' => (string) ($m['trigger'] ?? ''),
                'flow' => hp_flow_steps((string) ($rec['body'] ?? '')),
            ];
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — Understand what your software is doing</title>
    <meta name="description" content="VibeKB gives AI-assisted developers a living explanation of their application's current functionality, how it works, what AI is changing, and why.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= hp_e(hp_asset('assets/css/homepage.css')) ?>">
</head>
<body class="hp-body" data-homepage>
    <a class="hp-skip" href="#main">Skip to content</a>

    <header class="hp-top">
        <div class="hp-wrap hp-top-inner">
            <a class="hp-wordmark" href="./">VibeKB</a>
            <nav class="hp-nav" aria-label="Primary">
                <a href="#what">What it shows</a>
                <a class="hp-nav-cta" href="<?= hp_e($guideUrl) ?>">Open the guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">A living explanation for AI-assisted software</p>
                    <h1 id="hero-title">Understand what your software is doing.</h1>
                    <p class="hp-hero-support">
                        VibeKB gives AI-assisted developers a living explanation of their application&#39;s
                        current functionality — how it works, what AI is changing, and why. It lives in your
                        repository and is organized around what your software actually does.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#what">See what it shows</a>
                    </div>
                </div>
                <?php if ($loaded): ?>
                <aside class="hp-example-card" aria-label="Live software example">
                    <p class="hp-example-label">Live software example</p>
                    <h2 class="hp-example-name"><?= hp_e($sampleName) ?></h2>
                    <p class="hp-example-desc"><?= hp_e($sampleTagline !== '' ? $sampleTagline : 'A platform for running step-by-step AI workflows using the AI subscriptions people already have.') ?></p>
                    <dl class="hp-example-metrics">
                        <div><dt><?= (int) $funcCount ?></dt><dd>functions modelled</dd></div>
                        <div><dt><?= (int) $groupCount ?></dt><dd>capability groups</dd></div>
                        <div><dt><?= (int) $verifiedPct ?>%</dt><dd>verified from source</dd></div>
                        <div><dt><?= (int) $warnCount ?></dt><dd>active warnings</dd></div>
                    </dl>
                    <?php if ($currentWork !== null): ?>
                        <p class="hp-example-work"><span class="hp-status hp-status--info">AI now</span> <?= hp_e((string) ($currentWork['meta']['title'] ?? '')) ?></p>
                    <?php endif; ?>
                    <div class="hp-example-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the <?= hp_e($sampleName) ?> guide</a>
                        <?php if ($sampleRepo !== ''): ?>
                            <a class="hp-btn hp-btn-ghost" href="<?= hp_e($sampleRepo) ?>" rel="noopener noreferrer">View the repository</a>
                        <?php endif; ?>
                    </div>
                    <p class="hp-example-note">VibeKB is the product. <?= hp_e($sampleName) ?> is the real application it is explaining.</p>
                </aside>
                <?php endif; ?>
            </div>
        </section>

        <!-- 2. The questions VibeKB answers -->
        <section class="hp-section" id="answers" aria-labelledby="answers-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Why it exists</p>
                <h2 id="answers-title">AI can change six files faster than you can rebuild your mental model.</h2>
                <p class="hp-lead">
                    You&#39;re not blocked by writing code anymore — you&#39;re blocked by understanding what the
                    agent just built. VibeKB answers the questions you&#39;d otherwise have to reverse-engineer.
                </p>

                <div class="hp-stepper" data-stepper="problem" id="problem">
                    <div class="hp-stepper-tabs" role="tablist" aria-label="Questions VibeKB answers">
                        <button type="button" class="hp-step-tab is-active" role="tab" id="prob-tab-0" aria-selected="true" aria-controls="prob-panel-0" data-step="0">What does it do now?</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-1" aria-selected="false" aria-controls="prob-panel-1" data-step="1" tabindex="-1">How does this work?</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-2" aria-selected="false" aria-controls="prob-panel-2" data-step="2" tabindex="-1">What is AI changing?</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-3" aria-selected="false" aria-controls="prob-panel-3" data-step="3" tabindex="-1">Is it actually done?</button>
                    </div>
                    <div class="hp-stepper-panels">
                        <div class="hp-step-panel is-active" role="tabpanel" id="prob-panel-0" aria-labelledby="prob-tab-0" data-step-panel="0">
                            <p>Every behaviour the software implements, listed with its real status — implemented, partial, experimental, planned, deprecated, or broken. No guessing what&#39;s actually there.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-1" aria-labelledby="prob-tab-1" data-step-panel="1" hidden>
                            <p>Any action explained end to end: a plain-language step-by-step flow from trigger to result, the files that participate, and the data it reads and writes.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-2" aria-labelledby="prob-tab-2" data-step-panel="2" hidden>
                            <p>The current AI objective: what was asked, which functionality it affects, which files are expected to change, the data impact, and the risks — before, during, and after.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-3" aria-labelledby="prob-tab-3" data-step-panel="3" hidden>
                            <p>Intended, implemented, and verified are kept distinct. Every record carries a provenance state, so you can tell what&#39;s confirmed from what an agent merely claimed.</p>
                        </div>
                    </div>
                    <div class="hp-stepper-fallback">
                        <ol>
                            <li><strong>What does it do now?</strong> — every behaviour with its real status.</li>
                            <li><strong>How does this work?</strong> — step-by-step flow, files, and data.</li>
                            <li><strong>What is AI changing?</strong> — the current objective, impact, and risks.</li>
                            <li><strong>Is it actually done?</strong> — intended vs implemented vs verified.</li>
                        </ol>
                    </div>
                </div>

                <p class="hp-thesis">
                    VibeKB is not a documentation dump, a code browser, or a chat log.
                    It is a living explanation of what the software is doing right now.
                </p>
            </div>
        </section>

        <!-- 3. What VibeKB shows -->
        <section class="hp-section hp-surface" id="what" aria-labelledby="what-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What VibeKB shows you</p>
                <h2 id="what-title">One model. Four things you can finally see.</h2>
                <p class="hp-lead">
                    The primary unit is <strong>functionality</strong> — the things your software does — and
                    everything else connects back to it. Each lens is a real view in the guide.
                </p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="What VibeKB shows you">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">What it does now</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">How it works</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">What AI is changing</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Why it works this way</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">Every behaviour, grouped by purpose, with a real status and verification state. Filter by status, area, or whether it&#39;s user-facing.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('functionality')) ?>">Open the functionality index</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">The mental model, major components, and the request and data lifecycles — a paced, system-level explanation.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('how-it-works')) ?>">Open how it works</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">A structured view of the active change: objective, affected functionality, expected files, data impact, risks, and progress.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('current-work')) ?>">Open current AI work</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Decisions, constraints, assumptions, and warnings — each connected to the functionality it explains.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('why')) ?>">Open why it works this way</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article><h3>What it does now</h3><p>Every behaviour with a real status.</p><a href="<?= hp_e(hp_guide('functionality')) ?>">Functionality index</a></article>
                        <article><h3>How it works</h3><p>Mental model, components, flows.</p><a href="<?= hp_e(hp_guide('how-it-works')) ?>">How it works</a></article>
                        <article><h3>What AI is changing</h3><p>The active objective and impact.</p><a href="<?= hp_e(hp_guide('current-work')) ?>">Current AI work</a></article>
                        <article><h3>Why it works this way</h3><p>Decisions, constraints, warnings.</p><a href="<?= hp_e(hp_guide('why')) ?>">Why it works this way</a></article>
                    </div>
                </div>

                <p class="hp-thesis hp-thesis-soft">
                    Intended, implemented, and verified are different things. VibeKB keeps them distinct.
                </p>
            </div>
        </section>

        <!-- 4. Real functionality carousel -->
        <?php if ($previewItems !== []): ?>
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">A real application, explained by VibeKB</p>
                <h2 id="sample-title">Real functionality from <?= hp_e($sampleName) ?>.</h2>
                <p class="hp-lead">
                    <?= hp_e($sampleName) ?> is a real PHP application; VibeKB is the product explaining it.
                    Every slide below is an actual record derived from the <?= hp_e($sampleName) ?> source —
                    with its honest status and how it was verified — linking to a full explanation of the
                    flow, files, data, failure cases, and what&#39;s safe to change.
                </p>

                <div class="hp-guide-preview" data-guide-preview>
                    <div class="hp-guide-chapters" role="tablist" aria-label="Example functionality">
                        <?php foreach ($previewItems as $i => $item): ?>
                            <button
                                type="button"
                                class="hp-guide-chapter<?= $i === 0 ? ' is-active' : '' ?>"
                                role="tab"
                                id="guide-tab-<?= (int) $i ?>"
                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                aria-controls="guide-panel-<?= (int) $i ?>"
                                data-guide-chapter="<?= (int) $i ?>"
                                <?= $i === 0 ? '' : 'tabindex="-1"' ?>
                            >
                                <?= hp_e($item['title']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="hp-guide-stage">
                        <?php foreach ($previewItems as $i => $item): ?>
                            <div
                                class="hp-guide-panel<?= $i === 0 ? ' is-active' : '' ?>"
                                role="tabpanel"
                                id="guide-panel-<?= (int) $i ?>"
                                aria-labelledby="guide-tab-<?= (int) $i ?>"
                                data-guide-panel="<?= (int) $i ?>"
                                <?= $i === 0 ? '' : 'hidden' ?>
                            >
                                <p class="hp-statusbar">
                                    <span class="hp-status hp-status--<?= hp_e(hp_status_tone($item['status'])) ?>"><?= hp_e(status_label($item['status'])) ?></span>
                                    <?php if ($item['verification'] !== ''): ?>
                                        <span class="hp-status hp-status--<?= hp_e(verification_tone($item['verification']) === 'unknown' ? 'muted' : verification_tone($item['verification'])) ?>"><?= hp_e(verification_label($item['verification'])) ?></span>
                                    <?php endif; ?>
                                </p>
                                <p class="hp-guide-summary"><?= hp_e($item['summary']) ?></p>
                                <?php if ($item['trigger'] !== ''): ?>
                                    <p class="hp-guide-statement"><strong>Trigger:</strong> <?= hp_e($item['trigger']) ?></p>
                                <?php endif; ?>

                                <?php if ($item['flow'] !== []): ?>
                                    <ol class="hp-guide-flow">
                                        <?php foreach (array_slice($item['flow'], 0, 6) as $step): ?>
                                            <li><span><?= hp_e($step) ?></span></li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>

                                <p class="hp-guide-open">
                                    <a class="hp-btn hp-btn-secondary" href="<?= hp_e(hp_guide('functionality', ['id' => $item['id']])) ?>">
                                        Open this functionality
                                    </a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="hp-guide-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-guide-prev disabled>Previous</button>
                        <p class="hp-guide-status" aria-live="polite">
                            <span data-guide-current>1</span> of <?= count($previewItems) ?>
                        </p>
                        <button type="button" class="hp-btn hp-btn-primary" data-guide-next>Next</button>
                    </div>

                    <p class="hp-guide-complete">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e(hp_guide('functionality')) ?>">Browse all functionality</a>
                    </p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- 5. Depths -->
        <section class="hp-section hp-surface" id="depths" aria-labelledby="depths-title">
            <div class="hp-wrap">
                <p class="hp-kicker">The same content, at the depth you need</p>
                <h2 id="depths-title">Absorb the story now. Dig into the detail when you&#39;re about to change something.</h2>
                <p class="hp-lead">
                    The guide renders one repository-owned model as many views — so you&#39;re not force-fed a
                    schema dump when you only needed the overview.
                </p>

                <div class="hp-depth" data-depth-selector>
                    <div class="hp-depth-tabs" role="tablist" aria-label="Information depth">
                        <button type="button" class="hp-depth-tab is-active" role="tab" id="depth-tab-0" aria-selected="true" aria-controls="depth-panel-0" data-depth="0">Understand</button>
                        <button type="button" class="hp-depth-tab" role="tab" id="depth-tab-1" aria-selected="false" aria-controls="depth-panel-1" data-depth="1" tabindex="-1">Work on it</button>
                        <button type="button" class="hp-depth-tab" role="tab" id="depth-tab-2" aria-selected="false" aria-controls="depth-panel-2" data-depth="2" tabindex="-1">Reference</button>
                    </div>
                    <div class="hp-depth-panels">
                        <div class="hp-depth-panel is-active" role="tabpanel" id="depth-panel-0" aria-labelledby="depth-tab-0" data-depth-panel="0">
                            <h3>Understand</h3>
                            <ul>
                                <li><strong>Overview:</strong> what the software is doing right now, at a glance.</li>
                                <li><strong>Status:</strong> how much is implemented, partial, or unverified.</li>
                                <li><strong>Mental model:</strong> the simplest true picture of how it fits together.</li>
                            </ul>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('overview')) ?>">Open the overview</a>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-1" aria-labelledby="depth-tab-1" data-depth-panel="1" hidden>
                            <h3>Work on it</h3>
                            <ul>
                                <li><strong>Functionality detail:</strong> the step-by-step flow, data, and dependencies.</li>
                                <li><strong>Files that matter:</strong> a curated list with safety levels.</li>
                                <li><strong>Safe to change / use caution:</strong> what to test before you touch it.</li>
                            </ul>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('files')) ?>">Open files that matter</a>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-2" aria-labelledby="depth-tab-2" data-depth-panel="2" hidden>
                            <h3>Reference</h3>
                            <ul>
                                <li><strong>Data &amp; storage:</strong> what&#39;s stored and which functionality touches it.</li>
                                <li><strong>Changes &amp; handoff:</strong> what changed and what the next session must know.</li>
                                <li><strong>Content model:</strong> record types and live validation diagnostics.</li>
                            </ul>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('reference')) ?>">Open the reference</a>
                        </div>
                    </div>
                    <div class="hp-depth-fallback">
                        <h3>Understand</h3><p>Overview, status, and the mental model.</p>
                        <h3>Work on it</h3><p>Functionality detail, files, and safety guidance.</p>
                        <h3>Reference</h3><p>Data, changes, handoff, and the content model.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. How it stays current -->
        <section class="hp-section" id="workflow" aria-labelledby="workflow-title">
            <div class="hp-wrap">
                <p class="hp-kicker">How it stays accurate</p>
                <h2 id="workflow-title">The explanation stays current because updating it is part of the change.</h2>
                <p class="hp-lead">
                    Coding agents follow a documented workflow: understand the software before changing it, then
                    update the model after. So the guide describes the current software — not a past version.
                </p>

                <div class="hp-timeline" data-pipeline data-timeline>
                    <ol class="hp-timeline-track" aria-hidden="true">
                        <li>Understand</li>
                        <li>Record the work</li>
                        <li>Implement</li>
                        <li>Verify</li>
                        <li>Update &amp; hand off</li>
                    </ol>

                    <div class="hp-pipeline-stages hp-timeline-stages" role="tablist" aria-label="How the model stays current">
                        <button type="button" class="hp-pipe-stage is-active" role="tab" id="pipe-tab-0" aria-selected="true" aria-controls="pipe-panel-0" data-pipe="0">1. Understand</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-1" aria-selected="false" aria-controls="pipe-panel-1" data-pipe="1" tabindex="-1">2. Record the work</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-2" aria-selected="false" aria-controls="pipe-panel-2" data-pipe="2" tabindex="-1">3. Implement</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-3" aria-selected="false" aria-controls="pipe-panel-3" data-pipe="3" tabindex="-1">4. Verify</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-4" aria-selected="false" aria-controls="pipe-panel-4" data-pipe="4" tabindex="-1">5. Update &amp; hand off</button>
                    </div>
                    <div class="hp-pipeline-panels">
                        <div class="hp-pipe-panel is-active" role="tabpanel" id="pipe-panel-0" aria-labelledby="pipe-tab-0" data-pipe-panel="0">
                            <p>The agent reads the affected functionality, files, constraints, and warnings — and can state what the software currently does before touching it.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-1" aria-labelledby="pipe-tab-1" data-pipe-panel="1" hidden>
                            <p>Before implementing, the current-work record captures the requested outcome, proposed behaviour, expected files, data impact, and a verification plan.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-2" aria-labelledby="pipe-tab-2" data-pipe-panel="2" hidden>
                            <p>The code changes are made — without silently contradicting the product intent or the active constraints.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-3" aria-labelledby="pipe-tab-3" data-pipe-panel="3" hidden>
                            <p>The real behaviour is tested where possible. What passed, what failed, and what remains unverified is recorded honestly — code written is not the same as work done.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-4" aria-labelledby="pipe-tab-4" data-pipe-panel="4" hidden>
                            <p>The functionality records, flows, files, statuses, and verification states are updated to match the new reality — and the handoff tells the next session exactly where things stand.</p>
                        </div>
                    </div>
                    <div class="hp-pipeline-fallback">
                        <ol>
                            <li><strong>Understand</strong> — read the model before changing it.</li>
                            <li><strong>Record the work</strong> — capture the plan in the current-work record.</li>
                            <li><strong>Implement</strong> — make the change within the constraints.</li>
                            <li><strong>Verify</strong> — test the real behaviour; record the honest state.</li>
                            <li><strong>Update &amp; hand off</strong> — bring the model current and hand off.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details" id="how-it-works">
                    <summary>Version 1, honestly</summary>
                    <p>
                        V1 is a working, functionality-first living software model with eleven connected views over
                        one repository-owned content set. Extraction is agent-maintained, not automatic. No accounts,
                        no database, no external or AI API, no build step. It runs as plain PHP and deploys in a subfolder.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. Every functionality page answers -->
        <section class="hp-section hp-surface" id="relevance" aria-labelledby="relevance-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Inside a functionality record</p>
                <h2 id="relevance-title">Every functionality page answers the questions you&#39;d otherwise ask the code.</h2>
                <p class="hp-lead">Readable without opening the source. The code references support the explanation — they don&#39;t replace it.</p>

                <div class="hp-filter" data-relevance>
                    <div class="hp-filter-list" role="tablist" aria-label="What a functionality record answers">
                        <button type="button" class="hp-filter-btn is-active" role="tab" id="rel-tab-0" aria-selected="true" aria-controls="rel-panel-0" data-rel="0">What does it do?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-1" aria-selected="false" aria-controls="rel-panel-1" data-rel="1" tabindex="-1">What happens step by step?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-2" aria-selected="false" aria-controls="rel-panel-2" data-rel="2" tabindex="-1">Which files implement it?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-3" aria-selected="false" aria-controls="rel-panel-3" data-rel="3" tabindex="-1">What data does it touch?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-4" aria-selected="false" aria-controls="rel-panel-4" data-rel="4" tabindex="-1">What could go wrong?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-5" aria-selected="false" aria-controls="rel-panel-5" data-rel="5" tabindex="-1">What&#39;s safe to change?</button>
                    </div>
                    <div class="hp-filter-panels">
                        <div class="hp-filter-panel is-active" role="tabpanel" id="rel-panel-0" aria-labelledby="rel-tab-0" data-rel-panel="0">
                            <p><strong>In one sentence, then in full:</strong> what the user experiences and what the software actually does now — with its status and how it was verified.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-1" aria-labelledby="rel-tab-1" data-rel-panel="1" hidden>
                            <p><strong>A readable flow</strong> from trigger to result — e.g. paste a response → <code>RunnerController::paste</code> → sanitise untrusted input → store an immutable artifact version → back to the review step.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-2" aria-labelledby="rel-tab-2" data-rel-panel="2" hidden>
                            <p><strong>An implementation map:</strong> the controllers, services, and templates that participate — each linked to the Files that matter view with a safety level.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-3" aria-labelledby="rel-tab-3" data-rel-panel="3" hidden>
                            <p><strong>Data in and out:</strong> the inputs, the tables or files it reads and writes, and the configuration that affects it.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-4" aria-labelledby="rel-tab-4" data-rel-panel="4" hidden>
                            <p><strong>Failure cases:</strong> what can go wrong and what the user would experience — plus the warnings that apply to this behaviour.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-5" aria-labelledby="rel-tab-5" data-rel-panel="5" hidden>
                            <p><strong>Safe to change vs use caution:</strong> what you can likely edit freely, what to test, and the high-impact areas to treat carefully — with the reasoning behind them.</p>
                        </div>
                    </div>
                    <div class="hp-filter-fallback">
                        <ol>
                            <li>What does it do?</li>
                            <li>What happens step by step?</li>
                            <li>Which files implement it?</li>
                            <li>What data does it touch?</li>
                            <li>What could go wrong?</li>
                            <li>What&#39;s safe to change?</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Who / when -->
        <section class="hp-section" id="audience" aria-labelledby="audience-title">
            <div class="hp-wrap">
                <p class="hp-kicker">When it earns its place</p>
                <h2 id="audience-title">Whether you&#39;re mid-build or handing off.</h2>
                <p class="hp-lead">The same living model serves the moment you&#39;re in.</p>

                <div class="hp-compare" data-compare>
                    <div class="hp-compare-tabs" role="tablist" aria-label="When VibeKB helps">
                        <button type="button" class="hp-compare-tab is-active" role="tab" id="cmp-tab-0" aria-selected="true" aria-controls="cmp-panel-0" data-cmp="0">Starting a build</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-1" aria-selected="false" aria-controls="cmp-panel-1" data-cmp="1" tabindex="-1">Shipping at AI speed</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-2" aria-selected="false" aria-controls="cmp-panel-2" data-cmp="2" tabindex="-1">The next AI session</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-3" aria-selected="false" aria-controls="cmp-panel-3" data-cmp="3" tabindex="-1">The next human</button>
                    </div>
                    <div class="hp-compare-panels">
                        <div class="hp-compare-panel is-active" role="tabpanel" id="cmp-panel-0" aria-labelledby="cmp-tab-0" data-cmp-panel="0">
                            <p class="hp-tabpanel-lead">Put the model in the repo from day one and let it grow as the software does.</p>
                            <ul class="hp-plain-list">
                                <li>Functionality captured as it&#39;s built</li>
                                <li>Status starts honest and stays honest</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-1" aria-labelledby="cmp-tab-1" data-cmp-panel="1" hidden>
                            <p class="hp-tabpanel-lead">See the change coming before you approve it.</p>
                            <ul class="hp-plain-list">
                                <li>Current AI work shows affected functionality and risks</li>
                                <li>Verify what&#39;s real before you ship it</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-2" aria-labelledby="cmp-tab-2" data-cmp-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Hand the next session a current model instead of amnesia.</p>
                            <ul class="hp-plain-list">
                                <li>A handoff with the exact next recommended action</li>
                                <li>Constraints and warnings it must not violate</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-3" aria-labelledby="cmp-tab-3" data-cmp-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Explain the software without a walkthrough meeting.</p>
                            <ul class="hp-plain-list">
                                <li>Plain-language functionality, not just source</li>
                                <li>Why it works this way, recorded</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-compare-fallback">
                        <h3>Starting a build</h3><p>Grow the model with the code.</p>
                        <h3>Shipping at AI speed</h3><p>See and verify the change first.</p>
                        <h3>The next AI session</h3><p>A current handoff, not amnesia.</p>
                        <h3>The next human</h3><p>Plain-language understanding.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. Repository -->
        <section class="hp-section hp-surface" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Where it lives</p>
                <h2 id="arch-title">Repository-owned content, versioned with your software.</h2>
                <p class="hp-lead">
                    The model is plain files under <code>.vibekb/</code> — human-readable, AI-editable, and
                    deployed with the site. The website is one view of it, not the source of truth.
                </p>

                <div class="hp-repo" data-repo-map>
                    <div class="hp-repo-tree" role="tablist" aria-label=".vibekb structure">
                        <button type="button" class="hp-repo-item is-active" role="tab" id="repo-tab-0" aria-selected="true" aria-controls="repo-panel-0" data-repo="0"><code>.vibekb/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-1" aria-selected="false" aria-controls="repo-panel-1" data-repo="1" tabindex="-1"><code>project/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-2" aria-selected="false" aria-controls="repo-panel-2" data-repo="2" tabindex="-1"><code>functionality/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-3" aria-selected="false" aria-controls="repo-panel-3" data-repo="3" tabindex="-1"><code>system/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-4" aria-selected="false" aria-controls="repo-panel-4" data-repo="4" tabindex="-1"><code>files/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-5" aria-selected="false" aria-controls="repo-panel-5" data-repo="5" tabindex="-1"><code>memory/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-6" aria-selected="false" aria-controls="repo-panel-6" data-repo="6" tabindex="-1"><code>work/</code></button>
                    </div>
                    <div class="hp-repo-panels">
                        <div class="hp-repo-panel is-active" role="tabpanel" id="repo-panel-0" aria-labelledby="repo-tab-0" data-repo-panel="0">
                            <p>The living software model root — Markdown records plus small JSON manifests, deployed with the site and read by the guide.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-1" aria-labelledby="repo-tab-1" data-repo-panel="1" hidden>
                            <p>Identity, intent, current state, and constraints — what the software is and the boundaries it&#39;s built inside.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-2" aria-labelledby="repo-tab-2" data-repo-panel="2" hidden>
                            <p>The primary unit: one record per behaviour, plus an index that groups and orders them.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-3" aria-labelledby="repo-tab-3" data-repo-panel="3" hidden>
                            <p>The system-level explanation: mental model, components, request and data flows, storage, and deployment.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-4" aria-labelledby="repo-tab-4" data-repo-panel="4" hidden>
                            <p>A curated list of important files, each with a purpose, a safety level, and what to test after changing it.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-5" aria-labelledby="repo-tab-5" data-repo-panel="5" hidden>
                            <p>Repository memory — decisions, constraints, assumptions, warnings, discoveries, and changes — each linked to functionality.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-6" aria-labelledby="repo-tab-6" data-repo-panel="6" hidden>
                            <p>Current AI work, the handoff, and session history — what&#39;s being changed and what the next session must know.</p>
                        </div>
                    </div>
                    <div class="hp-repo-fallback">
                        <ul>
                            <li><code>.vibekb/</code> — living software model root</li>
                            <li><code>project/</code> — identity, intent, state, constraints</li>
                            <li><code>functionality/</code> — the primary unit (records + index)</li>
                            <li><code>system/</code> — mental model, components, flows, storage</li>
                            <li><code>files/</code> — curated important files</li>
                            <li><code>memory/</code> — decisions, warnings, assumptions, changes</li>
                            <li><code>work/</code> — current work, handoff, sessions</li>
                        </ul>
                    </div>
                </div>

                <ul class="hp-plain-list hp-arch-points">
                    <li>PHP 8.2 on ordinary shared hosting · deploys in a subfolder · no rewrite rules.</li>
                    <li>No database, no external or AI API, no Node, no build step.</li>
                    <li>Works without JavaScript · mobile-first · deep links work.</li>
                </ul>
            </div>
        </section>

        <!-- 10. Principles -->
        <section class="hp-section" id="principles" aria-labelledby="principles-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Principles</p>
                <h2 id="principles-title">What keeps VibeKB honest.</h2>

                <div class="hp-manifesto" data-manifesto>
                    <div class="hp-manifesto-stage" aria-live="polite">
                        <p class="hp-manifesto-index">Principle <span data-manifesto-current>1</span> of 7</p>
                        <p class="hp-manifesto-text is-active" data-manifesto-item="0">Functionality is the primary unit — not files, decisions, or AI sessions.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="1" hidden>Understand what the software does before you change it.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="2" hidden>Intended, implemented, and verified are different things.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="3" hidden>Repository memory exists to keep the explanation accurate.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="4" hidden>Show uncertainty — never hide what isn&#39;t verified.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="5" hidden>The repository is the source of truth; the website is a view of it.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="6" hidden>Keep the model current as the software changes.</p>
                    </div>
                    <div class="hp-manifesto-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-manifesto-prev disabled>Previous</button>
                        <button type="button" class="hp-btn hp-btn-secondary" data-manifesto-next>Next principle</button>
                    </div>
                    <div class="hp-manifesto-fallback">
                        <ol>
                            <li>Functionality is the primary unit.</li>
                            <li>Understand what the software does before you change it.</li>
                            <li>Intended, implemented, and verified are different things.</li>
                            <li>Repository memory exists to keep the explanation accurate.</li>
                            <li>Show uncertainty — never hide what isn&#39;t verified.</li>
                            <li>The repository is the source of truth.</li>
                            <li>Keep the model current as the software changes.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">See what <?= hp_e($sampleName) ?> is doing.</h2>
                <p>
                    Open the guide and read the live model of <?= hp_e($sampleName) ?>, a real application —
                    then put VibeKB in your own repo and keep its model current as you build.
                </p>
                <p class="hp-thesis">
                    Understand what your software is doing — the current functionality, how it works,
                    what AI is changing, and why.
                </p>
                <div class="hp-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the <?= hp_e($sampleName) ?> guide</a>
                    <?php if ($sampleRepo !== ''): ?>
                        <a class="hp-btn hp-btn-ghost" href="<?= hp_e($sampleRepo) ?>" rel="noopener noreferrer">View the <?= hp_e($sampleName) ?> repository</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="hp-footer">
        <div class="hp-wrap hp-footer-inner">
            <p><strong>VibeKB.</strong> Understand what your software is doing.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= hp_e(hp_asset('assets/js/homepage.js')) ?>" defer></script>
</body>
</html>
