<?php

declare(strict_types=1);

/**
 * VibeKB homepage — a landing page for the product, positioned as a
 * repository-native understanding layer for AI-assisted development. The copy is
 * organized around one spine ("AI can change six files faster than you can
 * rebuild your mental model") and the guide-preview carousel + hero metrics are
 * driven by real functionality records from `.vibekb/` (never invented).
 *
 * Structure is seven sections: hero, the problem, what VibeKB adds, live proof,
 * the AI-assisted workflow, why the repository-owned model matters, final CTA.
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
$provenance = is_array($manifest['provenance'] ?? null) ? $manifest['provenance'] : [];
$identity = $loaded ? $content->projectDoc('identity') : null;
$selfHosted = !empty($manifest['self_hosted']) || (($example['is_sample'] ?? true) === false);
$sampleName = (string) ($identity['meta']['title'] ?? ($example['name'] ?? 'the example project'));
// Prefer a short one_liner; fall back to summary (same pattern as guide overview).
$sampleTagline = (string) ($identity['meta']['one_liner'] ?? $identity['meta']['summary'] ?? '');
if ($sampleTagline === '') {
    $sampleTagline = 'A living software model that explains what your application is currently doing — organized around functionality.';
}
$sampleRepo = (string) ($example['source_repository'] ?? $provenance['source_repository'] ?? $repoUrl);
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
    <title>VibeKB — Understand the software AI helped you build</title>
    <meta name="description" content="VibeKB adds a repository-native understanding layer for AI-assisted software, connecting current functionality, files, data, active work, decisions, warnings, and verification — so every human and coding agent starts from the current reality.">
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
                <a href="#what">What it records</a>
                <a href="#workflow">How it works</a>
                <a class="hp-nav-cta" href="<?= hp_e($guideUrl) ?>">Open the guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero — define the category -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">Repository understanding for AI-assisted development</p>
                    <h1 id="hero-title">Understand the software AI helped you build.</h1>
                    <p class="hp-hero-support">
                        VibeKB adds a structured understanding layer to your Git repository. It records what
                        the software does, how its functionality connects to files and data, what AI is
                        changing, and what has actually been verified — so every new session starts from the
                        current reality instead of reconstructing the project from scratch.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the live guide</a>
                        <a class="hp-btn hp-btn-ghost" href="<?= hp_e($repoUrl) ?>" rel="noopener noreferrer">View the GitHub repository</a>
                    </div>
                </div>
                <?php if ($loaded): ?>
                <aside class="hp-example-card" aria-label="<?= $selfHosted ? 'VibeKB analyzing its own repository' : 'Live software example' ?>">
                    <p class="hp-example-label"><?= $selfHosted ? 'VibeKB analyzing its own repository' : 'Live software example' ?></p>
                    <h2 class="hp-example-name"><?= hp_e($sampleName) ?></h2>
                    <p class="hp-example-desc"><?= hp_e($sampleTagline) ?></p>
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
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the guide</a>
                        <?php if ($sampleRepo !== ''): ?>
                            <a class="hp-btn hp-btn-ghost" href="<?= hp_e($sampleRepo) ?>" rel="noopener noreferrer">View the repository</a>
                        <?php endif; ?>
                    </div>
                    <p class="hp-example-note"><?php if ($selfHosted): ?>
                        These numbers come from VibeKB&#39;s own repository-owned model — VibeKB explaining VibeKB, on a real repository.
                    <?php else: ?>
                        These numbers come from the repository-owned VibeKB model of <?= hp_e($sampleName) ?>, a real application.
                    <?php endif; ?></p>
                </aside>
                <?php endif; ?>
            </div>
        </section>

        <!-- 2. The problem — the spine of the page -->
        <section class="hp-section" id="problem" aria-labelledby="problem-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Why it exists</p>
                <h2 id="problem-title">AI can change six files faster than you can rebuild your mental model.</h2>
                <p class="hp-lead">
                    Writing code stopped being the bottleneck. Understanding what the agent just built is the
                    new one. One session implements a feature, another revises it, the decisions stay buried in
                    chat history — and the code works, but the project gets harder to explain with every change.
                </p>
                <p class="hp-lead">
                    Each new session then spends its first minutes rediscovering behaviour, dependencies, and
                    risks that a previous session already knew. That cost repeats whether the next session is a
                    coding agent or a human coming back to their own project.
                </p>

                <p class="hp-thesis">
                    VibeKB keeps that understanding in the repository — instead of leaving it scattered across
                    code, prompts, and old conversations.
                </p>

                <p class="hp-note">
                    Written for people who build with coding agents — Claude Code, Cursor, Codex, Copilot,
                    Gemini CLI — from solo developers shipping fast to experienced developers inheriting an
                    AI-generated codebase. The software may run fine; what&#39;s missing is a dependable model
                    of the whole project. VibeKB restores it.
                </p>
            </div>
        </section>

        <!-- 3. What VibeKB adds to the repository -->
        <section class="hp-section hp-surface" id="what" aria-labelledby="what-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What VibeKB adds to the repository</p>
                <h2 id="what-title">One model. Four things it keeps current.</h2>
                <p class="hp-lead">
                    The primary unit is <strong>functionality</strong> — the things your software does — and
                    everything else connects back to it. These are four parts of one coherent model, and each
                    is a real view in the guide.
                </p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="What VibeKB adds to the repository">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">Current functionality</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">How it works</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">Active AI work</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Repository memory</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">What the software does now, grouped by purpose, each behaviour with an honest status — implemented, partial, experimental, planned, deprecated, or broken — and how it was verified.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('functionality')) ?>">Open the functionality index</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Readable flows from trigger to result, connected to the files, data, dependencies, and failure cases involved — plus the mental model and request and data lifecycles.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('how-it-works')) ?>">Open how it works</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">What the current agent was asked to change: the objective, the functionality it affects, the files expected to change, the data impact, the risks, and how the result should be verified.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('current-work')) ?>">Open current AI work</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Decisions, constraints, assumptions, warnings, and discoveries — each connected to the functionality it affects, and to the handoff the next session needs.</p>
                            <a class="hp-text-link" href="<?= hp_e(hp_guide('why')) ?>">Open why it works this way</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article><h3>Current functionality</h3><p>What the software does now, with an honest status and how it was verified.</p><a href="<?= hp_e(hp_guide('functionality')) ?>">Functionality index</a></article>
                        <article><h3>How it works</h3><p>Readable flows connected to files, data, dependencies, and failure cases.</p><a href="<?= hp_e(hp_guide('how-it-works')) ?>">How it works</a></article>
                        <article><h3>Active AI work</h3><p>The current objective, its impact, the risks, and how to verify it.</p><a href="<?= hp_e(hp_guide('current-work')) ?>">Current AI work</a></article>
                        <article><h3>Repository memory</h3><p>Decisions, constraints, warnings, and handoff — tied to functionality.</p><a href="<?= hp_e(hp_guide('why')) ?>">Why it works this way</a></article>
                    </div>
                </div>

                <p class="hp-thesis hp-thesis-soft">
                    Intended, implemented, and verified are different things. VibeKB keeps them distinct — and
                    a functionality record answers what it does, step by step, which files implement it, what
                    data it touches, what could go wrong, and what&#39;s safe to change.
                </p>
            </div>
        </section>

        <!-- 4. Live proof — VibeKB explains itself -->
        <?php if ($previewItems !== []): ?>
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">A real repository, explained by VibeKB</p>
                <h2 id="sample-title">See VibeKB explain its own functionality.</h2>
                <p class="hp-lead">
                    <?php if ($selfHosted): ?>
                        The records below come directly from this repository&#39;s <code>.vibekb/</code> model.
                        Each one describes real behaviour in the codebase — its current status, verification
                        state, flow, relevant files, data, and failure cases. This is not a hand-written
                        product tour; it is the same repository-owned model a developer or coding agent uses
                        to understand the project.
                    <?php else: ?>
                        The records below come directly from the <?= hp_e($sampleName) ?> repository&#39;s
                        <code>.vibekb/</code> model. Each one describes real behaviour in that codebase — its
                        current status, verification state, flow, relevant files, data, and failure cases —
                        the same repository-owned model a developer or coding agent uses to understand the
                        project.
                    <?php endif; ?>
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

        <!-- 5. How it fits into AI-assisted development -->
        <section class="hp-section hp-surface" id="workflow" aria-labelledby="workflow-title">
            <div class="hp-wrap">
                <p class="hp-kicker">How it fits into AI-assisted development</p>
                <h2 id="workflow-title">Understand before changing. Update after.</h2>
                <p class="hp-lead">
                    VibeKB is used <em>with</em> coding agents. The human or agent reads the model before a
                    change and brings it current after — so the guide describes the current software, not a
                    past version.
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
                            <p>The human or agent reads the affected functionality, files, constraints, and warnings — and can state what the software currently does before touching it.</p>
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
                        In V1, coding agents maintain the model as part of the development workflow. VibeKB
                        provides the structure, validation, relationships, and guide — it does not pretend that
                        repository understanding appears automatically without review. VibeKB detects that code
                        changed; interpreting what a change <em>means</em> is the agent&#39;s job, following the
                        documented workflow. <code>updates_automatically</code> is <code>false</code> and stays
                        that way.
                    </p>
                </details>
            </div>
        </section>

        <!-- 6. Why the repository-owned model matters -->
        <section class="hp-section" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Why the repository-owned model matters</p>
                <h2 id="arch-title">Chat context expires. Repository context remains.</h2>
                <p class="hp-lead">
                    The model is plain files under <code>.vibekb/</code>, committed with your code. Changes are
                    reviewed in Git, humans and agents can read and update it, and the generated guide is a
                    presentation layer — the repository stays the source of truth, with uncertainty and
                    verification kept explicit.
                </p>

                <ul class="hp-plain-list hp-arch-points">
                    <li><strong>Functionality first.</strong> The model is organized around what the software does — not files, decisions, or AI sessions.</li>
                    <li><strong>Evidence and uncertainty stay visible.</strong> Every record carries a status and a verification state; what isn&#39;t confirmed is never hidden.</li>
                    <li><strong>The model changes with the software.</strong> Updating it is part of the change, so the explanation keeps describing the current reality.</li>
                </ul>

                <p class="hp-thesis hp-thesis-soft">
                    The repository is the source of truth; the website is a view of it.
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
                            <p>The living software model root — Markdown records plus small JSON manifests, committed with the code and read by the guide.</p>
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

                <details class="hp-details">
                    <summary>How this reference implementation runs</summary>
                    <p>
                        This guide is a plain PHP&nbsp;8.2 app that reads <code>.vibekb/</code> live, with a
                        static snapshot generator for any host. It runs on ordinary shared hosting, deploys in
                        a subfolder with no rewrite rules, works without JavaScript, and needs no database, no
                        external or AI API, and no build step. Those are properties of <em>this</em>
                        implementation — useful for contributors — not a requirement of the repositories VibeKB
                        can describe.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">Keep the understanding with the code.</h2>
                <p>
                    <?php if ($selfHosted): ?>
                        Explore the live guide to see VibeKB working on a real repository — its own — then add
                        the model to your own project so the next human or AI session can understand what
                        exists before changing it.
                    <?php else: ?>
                        Explore the live guide to see VibeKB working on <?= hp_e($sampleName) ?>, a real
                        repository — then add the model to your own project so the next human or AI session can
                        understand what exists before changing it.
                    <?php endif; ?>
                </p>
                <p class="hp-thesis">
                    AI helped you build it. VibeKB helps you understand it.
                </p>
                <div class="hp-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the live guide</a>
                    <?php if ($sampleRepo !== ''): ?>
                        <a class="hp-btn hp-btn-ghost" href="<?= hp_e($sampleRepo) ?>" rel="noopener noreferrer">View VibeKB on GitHub</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="hp-footer">
        <div class="hp-wrap hp-footer-inner">
            <p><strong>VibeKB.</strong> Understand the software AI helped you build.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= hp_e(hp_asset('assets/js/homepage.js')) ?>" defer></script>
</body>
</html>
