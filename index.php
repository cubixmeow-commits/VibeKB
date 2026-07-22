<?php

declare(strict_types=1);

/**
 * VibeKB homepage — a story-driven landing page for AI-assisted developers.
 * The copy follows the emotional arc of the product story: open-source excitement,
 * AI-assisted building, growing confusion, fear of change, then VibeKB as the
 * missing understanding layer, ending in confidence. Live metrics and the
 * guide-preview carousel are driven by real `.vibekb/` records (never invented).
 *
 * Structure: hero → developer journey → uncertainty → the gap → VibeKB → what it
 * shows → live proof → before/after → workflow → repository model → CTA.
 *
 * Interactions live in assets/js/homepage.js; styling in assets/css/homepage.css.
 * All widgets degrade to their .hp-*-fallback content without JavaScript.
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

/** Comic-inspired story beats — emotional pacing, not a product feature list. */
$storyBeats = [
    [
        'mood' => 'excited',
        'step' => '1',
        'kicker' => 'Open source still feels incredible',
        'title' => 'You find something brilliant on GitHub.',
        'body' => 'Someone built something useful and gave it away. You can see how it works — at least, you think you can — and you already have ideas for what to build on top of it.',
        'aside' => 'Someone built this and released it for free. Brilliant.',
    ],
    [
        'mood' => 'building',
        'step' => '2',
        'kicker' => 'AI makes building easy',
        'title' => 'Cursor. Claude Code. Copy, modify, ship.',
        'body' => 'You fork the repo, describe what you want, accept a few diffs, and suddenly you have a working app that is yours. The velocity is real. Shipping feels closer than it has ever been.',
        'aside' => 'New idea. New features. Let&#39;s go.',
    ],
    [
        'mood' => 'confused',
        'step' => '3',
        'kicker' => 'Then reality hits',
        'title' => 'It works. You don&#39;t actually understand it.',
        'body' => 'The tests pass. The demo runs. But the codebase is no longer the project you mentally modelled — it is a blend of the original author&#39;s intent, six agent sessions, and decisions you never consciously made.',
        'aside' => 'It runs. I don&#39;t get it.',
    ],
    [
        'mood' => 'afraid',
        'step' => '4',
        'kicker' => 'Confidence disappears',
        'title' => 'Every change feels dangerous.',
        'body' => 'You hesitate before editing a file you did not write. You re-read the same modules before every session. You ask the agent the same architecture questions again because yesterday&#39;s answer is buried in chat history.',
        'aside' => 'One wrong move and it all breaks.',
    ],
    [
        'mood' => 'relief',
        'step' => '5',
        'kicker' => 'The missing layer',
        'title' => 'VibeKB is the understanding layer your repo is missing.',
        'body' => 'Not another coding agent. Not documentation. Not a knowledge base. A living model in <code>.vibekb/</code> that explains what your software is doing right now — architecture, relationships, key files, active work, and what is actually verified.',
        'aside' => 'AI helped you build it. VibeKB helps you understand it.',
    ],
    [
        'mood' => 'confident',
        'step' => '6',
        'kicker' => 'Understanding returns',
        'title' => 'You know what you can safely change.',
        'body' => 'You open the guide before you edit. You see which functionality owns a behaviour, which files participate, what data moves, and what the last agent was trying to do. Changes feel deliberate again.',
        'aside' => 'Calm. Confident. In control.',
    ],
];

$uncertaintyQuestions = [
    'Why does this file exist?',
    'Why is this dependency here?',
    'What breaks if I change this?',
    'Which files actually matter?',
    'Why is authentication implemented this way?',
    'Why does this feature depend on five other systems?',
];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — AI helped you build it. VibeKB helps you understand it.</title>
    <meta name="description" content="You shipped with Cursor and Claude Code faster than you understood what you built. VibeKB is the missing understanding layer — a living model in your repo that explains architecture, relationships, key files, and active work.">
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
                <a href="#story">The story</a>
                <a href="#solution">Understanding</a>
                <a href="#sample">See it work</a>
                <a class="hp-nav-cta" href="<?= hp_e($guideUrl) ?>">Open the guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero — recognition first -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">For developers who ship with AI</p>
                    <h1 id="hero-title">AI helped you build it. VibeKB helps you understand it.</h1>
                    <p class="hp-hero-support">
                        You found a great repo, extended it with Cursor or Claude Code, and shipped something
                        real. The app runs — but the mental model didn&#39;t keep up. That gap between
                        <em>working</em> and <em>understanding</em> is the problem VibeKB exists to solve.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="#story">That&#39;s exactly what happened to me</a>
                        <a class="hp-btn hp-btn-ghost" href="<?= hp_e($guideUrl) ?>">Open the live guide</a>
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

        <!-- 2. Developer journey — comic-inspired emotional arc -->
        <section class="hp-section hp-journey-section" id="story" aria-labelledby="story-title">
            <div class="hp-wrap">
                <p class="hp-kicker">The story</p>
                <h2 id="story-title">Building software has changed. Understanding didn&#39;t.</h2>
                <p class="hp-lead">
                    This is not a product tour. It is the arc most AI-assisted developers eventually hit —
                    whether they forked open source or generated a codebase from scratch.
                </p>

                <ol class="hp-journey" data-story-journey>
                    <?php foreach ($storyBeats as $i => $beat): ?>
                        <li class="hp-journey-panel hp-journey-panel--<?= hp_e($beat['mood']) ?><?= $i === 0 ? ' is-active' : '' ?>" data-story-panel="<?= (int) $i ?>">
                            <p class="hp-journey-step" aria-hidden="true"><?= hp_e($beat['step']) ?></p>
                            <p class="hp-journey-kicker"><?= hp_e($beat['kicker']) ?></p>
                            <h3 class="hp-journey-title"><?= $beat['title'] ?></h3>
                            <p class="hp-journey-body"><?= $beat['body'] ?></p>
                            <p class="hp-journey-aside"><?= $beat['aside'] ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="hp-journey-nav" data-story-nav aria-label="Story beats">
                    <?php foreach ($storyBeats as $i => $beat): ?>
                        <button
                            type="button"
                            class="hp-journey-dot<?= $i === 0 ? ' is-active' : '' ?>"
                            data-story-dot="<?= (int) $i ?>"
                            aria-label="Step <?= hp_e($beat['step']) ?>: <?= hp_e($beat['kicker']) ?>"
                            <?= $i === 0 ? 'aria-current="step"' : '' ?>
                        ></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 3. Uncertainty — name the feeling, not "documentation" -->
        <section class="hp-section hp-surface hp-mood-confused" id="uncertainty" aria-labelledby="uncertainty-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">The feeling</p>
                <h2 id="uncertainty-title">The app runs. Understanding does not.</h2>
                <p class="hp-lead">
                    This is not a documentation problem. It is uncertainty — guessing which files matter,
                    fearing that the next edit breaks something unrelated, re-asking the same questions every
                    session because nothing durable captured the answers.
                </p>

                <ul class="hp-question-list">
                    <?php foreach ($uncertaintyQuestions as $q): ?>
                        <li><?= hp_e($q) ?></li>
                    <?php endforeach; ?>
                </ul>

                <p class="hp-thesis hp-thesis-warn">
                    You are not blocked on writing code anymore. You are blocked on knowing what the code
                    you have is actually doing.
                </p>
            </div>
        </section>

        <!-- 4. The gap -->
        <section class="hp-section" id="gap" aria-labelledby="gap-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">That&#39;s the real problem</p>
                <h2 id="gap-title">AI made building easy. Nobody made understanding keep up.</h2>
                <p class="hp-lead">
                    Agents are excellent at producing diffs. They are not a substitute for a durable model of
                    the whole application — what each behaviour does, how pieces connect, what is verified,
                    and what the last session changed.
                </p>
                <p class="hp-lead">
                    Without that model, every new session — human or agent — pays the rediscovery tax again.
                    The codebase grows. Confidence shrinks.
                </p>
            </div>
        </section>

        <!-- 5. VibeKB — introduce the product after the problem lands -->
        <section class="hp-section hp-surface hp-mood-relief" id="solution" aria-labelledby="solution-title">
            <div class="hp-wrap">
                <p class="hp-kicker">VibeKB</p>
                <h2 id="solution-title">The missing understanding layer — living in your repository.</h2>
                <p class="hp-lead">
                    VibeKB is not another AI coding tool. It does not replace your agent. It gives you and
                    every future session a structured, honest picture of what the software is doing <em>right
                    now</em> — organized around functionality, not folders.
                </p>

                <div class="hp-understanding-grid">
                    <div>
                        <h3 class="hp-understanding-label">It explains</h3>
                        <ul class="hp-plain-list">
                            <li>Architecture and how components connect</li>
                            <li>Which files implement each behaviour</li>
                            <li>Data flow and dependencies between features</li>
                            <li>What AI is changing in the active work record</li>
                            <li>What is verified — and what is still guessing</li>
                            <li>What is safe to change vs what needs caution</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="hp-understanding-label">It is not</h3>
                        <ul class="hp-plain-list hp-plain-list-muted">
                            <li>A documentation generator you write once and forget</li>
                            <li>A code browser that dumps the file tree</li>
                            <li>An AI chat log or activity archive</li>
                            <li>A magic auto-updater that interprets diffs for you</li>
                        </ul>
                    </div>
                </div>

                <p class="hp-thesis">
                    AI helped you build the application. VibeKB helps you understand the application.
                </p>
            </div>
        </section>

        <!-- 6. What you finally see — proof of the model -->
        <section class="hp-section" id="what" aria-labelledby="what-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Here&#39;s how</p>
                <h2 id="what-title">One model. Four views into what your software is actually doing.</h2>
                <p class="hp-lead">
                    Everything connects back to <strong>functionality</strong> — the things your software does
                    from a user&#39;s or system&#39;s point of view. Each lens below is a real view in the guide,
                    kept current as the code changes.
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

        <!-- 7. Live proof -->
        <?php if ($previewItems !== []): ?>
        <section class="hp-section hp-surface" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">See it on a real repository</p>
                <h2 id="sample-title">This is what understanding looks like — not a marketing slide.</h2>
                <p class="hp-lead">
                    <?php if ($selfHosted): ?>
                        Every record below comes from this repository&#39;s <code>.vibekb/</code> model — real
                        functionality, honest status, verification state, and flows traced from source. Open
                        any one to see the kind of clarity you were missing when the codebase felt fragile.
                    <?php else: ?>
                        Every record below comes from the <?= hp_e($sampleName) ?> repository&#39;s
                        <code>.vibekb/</code> model — real functionality with honest status and verification.
                        Open any one to see the kind of clarity you were missing when the codebase felt fragile.
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

        <!-- 8. Before / after — emotional transformation -->
        <section class="hp-section" id="transform" aria-labelledby="transform-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Build with confidence</p>
                <h2 id="transform-title">From afraid to edit → calm enough to ship.</h2>

                <div class="hp-transform">
                    <article class="hp-transform-card hp-transform-card--before" aria-labelledby="transform-before">
                        <h3 id="transform-before">Before VibeKB</h3>
                        <ul class="hp-plain-list">
                            <li>Confused about what the agent actually built</li>
                            <li>Afraid every edit might break something unrelated</li>
                            <li>Re-reading the same files before every change</li>
                            <li>Asking AI the same architecture questions every session</li>
                            <li>Guessing which parts of the codebase matter</li>
                        </ul>
                    </article>
                    <article class="hp-transform-card hp-transform-card--after" aria-labelledby="transform-after">
                        <h3 id="transform-after">After VibeKB</h3>
                        <ul class="hp-plain-list">
                            <li>Understand the architecture in plain language</li>
                            <li>Know which files implement each behaviour</li>
                            <li>See what the current AI work is changing and why</li>
                            <li>Open the guide before you edit — not after something breaks</li>
                            <li>Ship faster because you understand the system</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <!-- 9. How understanding stays current -->
        <section class="hp-section hp-surface" id="workflow" aria-labelledby="workflow-title">
            <div class="hp-wrap">
                <p class="hp-kicker">How it stays current</p>
                <h2 id="workflow-title">Understanding is part of the change — not an afterthought.</h2>
                <p class="hp-lead">
                    VibeKB works <em>with</em> your coding agents. Read the model before you change the code.
                    Update it after — with honest verification. The guide then describes the software as it is
                    today, not as it was three sessions ago.
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

        <!-- 10. Repository-owned understanding -->
        <section class="hp-section" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Where it lives</p>
                <h2 id="arch-title">Chat context expires. Repository context remains.</h2>
                <p class="hp-lead">
                    The model is plain files under <code>.vibekb/</code>, committed with your code. Reviewed in
                    Git. Readable by humans and agents. The website is a view — the repository stays the source
                    of truth, with uncertainty kept visible.
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

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final hp-mood-confident" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">Stop guessing. Start understanding.</h2>
                <p>
                    <?php if ($selfHosted): ?>
                        Open the live guide to see VibeKB on a real repository — its own — then add
                        <code>.vibekb/</code> to your project so the next session starts from clarity, not fear.
                    <?php else: ?>
                        Open the live guide on <?= hp_e($sampleName) ?>, then add <code>.vibekb/</code> to your
                        own project so the next session starts from clarity, not fear.
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
            <p><strong>VibeKB.</strong> The understanding layer for the application you&#39;re afraid to touch.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= hp_e(hp_asset('assets/js/homepage.js')) ?>" defer></script>
</body>
</html>
