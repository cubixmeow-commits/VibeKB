<?php

declare(strict_types=1);

function homepage_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$guideUrl = 'guide/';
$repoUrl = 'https://github.com/cubixmeow-commits/VibeKB';
$sampleDisplayName = 'Weekend SaaS Demo';
$sampleRealName = 'SaaS Idea Manager';

$previewStems = [
    '01-what-is-this',
    '03-save-flow',
    '04-simplicity',
    '06-problems',
    '07-change-safely',
];

$previewChapters = [];
foreach ($previewStems as $stem) {
    $path = __DIR__ . '/.vibekb/guide/chapters/' . $stem . '.json';
    if (!is_file($path)) {
        continue;
    }
    $data = json_decode((string) file_get_contents($path), true);
    if (!is_array($data)) {
        continue;
    }

    $statement = '';
    $devPoints = [];
    $flowSteps = [];
    foreach (($data['scenes'] ?? []) as $scene) {
        if (!is_array($scene)) {
            continue;
        }
        $type = (string) ($scene['type'] ?? '');
        if ($type === 'statement' && $statement === '') {
            $statement = (string) ($scene['headline'] ?? $scene['body'] ?? '');
        }
        if ($type === 'developer-detail' && $devPoints === []) {
            foreach (($scene['points'] ?? []) as $point) {
                if (is_array($point)) {
                    $devPoints[] = [
                        'title' => (string) ($point['title'] ?? ''),
                        'text' => (string) ($point['text'] ?? ''),
                    ];
                }
            }
        }
        if ($type === 'flow' && $flowSteps === []) {
            foreach (($scene['steps'] ?? []) as $step) {
                if (is_array($step)) {
                    $flowSteps[] = [
                        'title' => (string) ($step['title'] ?? ''),
                        'text' => (string) ($step['text'] ?? ''),
                    ];
                }
            }
        }
        if ($type === 'interactive-cards' && $statement === '') {
            $cards = $scene['cards'] ?? [];
            if (isset($cards[1]) && is_array($cards[1])) {
                $statement = (string) ($cards[1]['title'] ?? '') . ': ' . (string) ($cards[1]['teaser'] ?? '');
                foreach (($cards[1]['points'] ?? []) as $p) {
                    $devPoints[] = ['title' => '', 'text' => (string) $p];
                }
            }
        }
        if ($type === 'problem-path' && $flowSteps === []) {
            $problems = $scene['problems'] ?? [];
            if (isset($problems[0]) && is_array($problems[0])) {
                $statement = (string) ($problems[0]['summary'] ?? '');
                foreach (($problems[0]['steps'] ?? []) as $step) {
                    if (is_array($step)) {
                        $flowSteps[] = [
                            'title' => (string) ($step['title'] ?? ''),
                            'text' => (string) ($step['text'] ?? ''),
                        ];
                    }
                }
            }
        }
        if ($type === 'checklist' && $devPoints === []) {
            $items = $scene['items'] ?? [];
            if (isset($items[0]) && is_array($items[0])) {
                $statement = (string) ($items[0]['intro'] ?? '');
                foreach (($items[0]['checks'] ?? $items[0]['affects'] ?? []) as $check) {
                    $devPoints[] = ['title' => '', 'text' => (string) $check];
                }
            }
        }
    }

    $previewChapters[] = [
        'id' => (string) ($data['id'] ?? $stem),
        'question' => (string) ($data['question'] ?? $data['title'] ?? ''),
        'summary' => (string) ($data['summary'] ?? ''),
        'statement' => $statement,
        'dev_points' => $devPoints,
        'flow_steps' => $flowSteps,
        'hash' => (string) ($data['id'] ?? $stem),
    ];
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — Code is being generated faster than humans can understand it</title>
    <meta name="description" content="For the first time in software history, code is being generated faster than humans can understand it. VibeKB closes that gap—with a living Project Guide that grows alongside AI-assisted development.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homepage.css">
</head>
<body class="hp-body" data-homepage>
    <a class="hp-skip" href="#main">Skip to content</a>

    <header class="hp-top">
        <div class="hp-wrap hp-top-inner">
            <a class="hp-wordmark" href="./">VibeKB</a>
            <nav class="hp-nav" aria-label="Primary">
                <a href="#shift">The shift</a>
                <a class="hp-nav-cta" href="<?= homepage_e($guideUrl) ?>">Project Guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero: the new reality, not the product -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">A new bottleneck in software</p>
                    <h1 id="hero-title">Code is being generated faster than humans can understand it.</h1>
                    <p class="hp-hero-support">
                        For decades, software was limited by how fast people could write code.
                        Tools like Cursor, Claude Code, Windsurf, Copilot, and Gemini CLI flipped that.
                        People aren&#39;t blocked by writing software anymore. They&#39;re blocked by understanding it.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="#shift">See how the gap opens</a>
                        <a class="hp-btn hp-btn-ghost" href="#solution">How VibeKB closes it</a>
                    </div>
                </div>
                <figure class="hp-hero-visual" aria-labelledby="hero-flow-label">
                    <figcaption id="hero-flow-label" class="visually-hidden">
                        AI accelerates code generation while human understanding lags behind, creating a gap.
                    </figcaption>
                    <ol class="hp-mini-flow" data-hero-flow>
                        <li class="is-active"><span>AI writes fast</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Understanding lags</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>The gap grows</span></li>
                    </ol>
                </figure>
            </div>
        </section>

        <!-- 2. The shift / story -->
        <section class="hp-section" id="shift" aria-labelledby="shift-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">The shift</p>
                <h2 id="shift-title">Software is being generated faster than humans can absorb it.</h2>
                <p class="hp-lead">
                    That is not a documentation problem. It is a new phenomenon:
                    AI can expand a codebase faster than a person can continuously build a mental model of it.
                </p>

                <div class="hp-stepper" data-stepper="problem" id="problem">
                    <div class="hp-stepper-tabs" role="tablist" aria-label="How the understanding gap opens">
                        <button type="button" class="hp-step-tab is-active" role="tab" id="prob-tab-0" aria-selected="true" aria-controls="prob-panel-0" data-step="0">1. You start building</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-1" aria-selected="false" aria-controls="prob-panel-1" data-step="1" tabindex="-1">2. The app explodes</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-2" aria-selected="false" aria-controls="prob-panel-2" data-step="2" tabindex="-1">3. The chats vanish</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-3" aria-selected="false" aria-controls="prob-panel-3" data-step="3" tabindex="-1">4. The code remains</button>
                    </div>
                    <div class="hp-stepper-panels">
                        <div class="hp-step-panel is-active" role="tabpanel" id="prob-panel-0" aria-labelledby="prob-tab-0" data-step-panel="0">
                            <p>Someone starts a new project. They build rapidly with AI. It feels like leverage. It is.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-1" aria-labelledby="prob-tab-1" data-step-panel="1" hidden>
                            <p>Hundreds of lines become thousands, then tens of thousands—often in days instead of months. The system grows faster than any continuous human walkthrough of it.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-2" aria-labelledby="prob-tab-2" data-step-panel="2" hidden>
                            <p>Every conversation held architectural decisions, trade-offs, rejected approaches, dependencies, assumptions, and future plans. Those conversations disappear. The understanding disappears with them.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-3" aria-labelledby="prob-tab-3" data-step-panel="3" hidden>
                            <p>The software still works. The developer is not inexperienced—they simply could not absorb the system at the speed it was generated. That is the gap.</p>
                        </div>
                    </div>
                    <div class="hp-stepper-fallback">
                        <ol>
                            <li><strong>You start building</strong> — rapidly, with AI coding tools.</li>
                            <li><strong>The app explodes</strong> — thousands of lines in days, not months.</li>
                            <li><strong>The chats vanish</strong> — decisions, trade-offs, and assumptions evaporate with the sessions.</li>
                            <li><strong>The code remains</strong> — working software without a durable mental model.</li>
                        </ol>
                    </div>
                </div>

                <p class="hp-thesis">
                    For the first time in software history, code is being generated faster than humans can understand it.
                    That is why a new category of software exists.
                </p>

                <details class="hp-details">
                    <summary>Why “I&#39;ll catch up later” doesn&#39;t close the gap</summary>
                    <ul class="hp-plain-list">
                        <li>Later arrives after the system is already too large to casually relearn.</li>
                        <li>Agent chats are temporary. They do not ship with the repo.</li>
                        <li>READMEs cover setup, not the living mental model.</li>
                        <li>Source shows what runs—not why it was shaped that way.</li>
                        <li>Reconstruction costs more than preserving understanding while you build.</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 3. Introduce VibeKB -->
        <section class="hp-section hp-surface" id="solution" aria-labelledby="solution-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Closing the gap</p>
                <h2 id="solution-title">VibeKB preserves understanding while the software is being built.</h2>
                <p class="hp-lead">
                    Not documentation after the fact. Not a report you generate when you&#39;re already lost.
                    A living Project Guide that begins on day one and evolves with the repository—
                    so neither you nor the next AI session has to reconstruct months of conversations.
                </p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="How VibeKB closes the understanding gap">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">Understanding grows with the code</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">Decisions stay in the repo</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">Change without archaeology</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Debug from memory, not guesswork</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">The Project Guide keeps pace with the product: what it does, who it&#39;s for, how someone uses it—updated as features land.</p>
                            <p class="hp-example"><strong>In the sample:</strong> plain-language product truth before any file dive.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#what-is-this">See “What is this project?”</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Trade-offs, rejected approaches, and “why it&#39;s shaped this way” stop living only in chats. They become repository knowledge.</p>
                            <p class="hp-example"><strong>In the sample:</strong> save path, SQLite choice, no-auth boundaries—recorded where the code lives.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#save-flow">See the save path</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Dependencies, invariants, and side effects are available before the next refactor prompt—not after something breaks.</p>
                            <p class="hp-example"><strong>In the sample:</strong> adding a field means migration, forms, write, read, and production apply—together.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#change-safely">See change-safety guides</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">When you solve a failure once, the path stays with the project—so the gap doesn&#39;t reopen every late night.</p>
                            <p class="hp-example"><strong>In the sample:</strong> blank list → file exists? PHP can read it? Query returns rows?</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#problems">See troubleshooting sequences</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article>
                            <h3>Understanding grows with the code</h3>
                            <p>Product truth stays current as features land.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#what-is-this">Open “What is this project?”</a>
                        </article>
                        <article>
                            <h3>Decisions stay in the repo</h3>
                            <p>Trade-offs and intent leave the chat and enter Git.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#save-flow">Open the save path</a>
                        </article>
                        <article>
                            <h3>Change without archaeology</h3>
                            <p>Invariants and impact before you edit.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#change-safely">Open change-safety guides</a>
                        </article>
                        <article>
                            <h3>Debug from memory, not guesswork</h3>
                            <p>Failure paths accumulate instead of disappearing.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#problems">Open troubleshooting</a>
                        </article>
                    </div>
                </div>

                <p class="hp-thesis hp-thesis-soft">
                    Documentation describes software. VibeKB preserves understanding. Those are different jobs.
                </p>
            </div>
        </section>

        <!-- 4. Sample: what preserved understanding looks like -->
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What closing the gap looks like</p>
                <h2 id="sample-title"><?= homepage_e($sampleDisplayName) ?></h2>
                <p class="hp-lead">
                    A living Project Guide for a real build—simple first, technical when you dig in.
                    This is what it looks like when understanding is not left behind in expired chats.
                    <span class="hp-sample-aside">Inside the guide, the project is <?= homepage_e($sampleRealName) ?>: a small PHP and SQLite app explained end to end.</span>
                </p>

                <div class="hp-guide-preview" data-guide-preview>
                    <div class="hp-guide-chapters" role="tablist" aria-label="Sample chapters">
                        <?php foreach ($previewChapters as $i => $chapter): ?>
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
                                <?= homepage_e($chapter['question']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="hp-guide-stage">
                        <?php foreach ($previewChapters as $i => $chapter): ?>
                            <div
                                class="hp-guide-panel<?= $i === 0 ? ' is-active' : '' ?>"
                                role="tabpanel"
                                id="guide-panel-<?= (int) $i ?>"
                                aria-labelledby="guide-tab-<?= (int) $i ?>"
                                data-guide-panel="<?= (int) $i ?>"
                                <?= $i === 0 ? '' : 'hidden' ?>
                            >
                                <p class="hp-guide-summary"><?= homepage_e($chapter['summary']) ?></p>
                                <?php if ($chapter['statement'] !== ''): ?>
                                    <p class="hp-guide-statement"><?= homepage_e($chapter['statement']) ?></p>
                                <?php endif; ?>

                                <?php if ($chapter['flow_steps'] !== []): ?>
                                    <ol class="hp-guide-flow">
                                        <?php foreach (array_slice($chapter['flow_steps'], 0, 5) as $step): ?>
                                            <li>
                                                <strong><?= homepage_e($step['title']) ?></strong>
                                                <span><?= homepage_e($step['text']) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>

                                <?php if ($chapter['dev_points'] !== []): ?>
                                    <details class="hp-details hp-guide-dev">
                                        <summary>What should I know before I change this?</summary>
                                        <ul class="hp-plain-list">
                                            <?php foreach (array_slice($chapter['dev_points'], 0, 4) as $point): ?>
                                                <li>
                                                    <?php if ($point['title'] !== ''): ?>
                                                        <strong><?= homepage_e($point['title']) ?>.</strong>
                                                    <?php endif; ?>
                                                    <?= homepage_e($point['text']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </details>
                                <?php endif; ?>

                                <p class="hp-guide-open">
                                    <a class="hp-btn hp-btn-secondary" href="<?= homepage_e($guideUrl . '#' . $chapter['hash']) ?>">
                                        Open this chapter
                                    </a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="hp-guide-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-guide-prev disabled>Previous</button>
                        <p class="hp-guide-status" aria-live="polite">
                            Chapter <span data-guide-current>1</span> of <?= count($previewChapters) ?>
                        </p>
                        <button type="button" class="hp-btn hp-btn-primary" data-guide-next>Next</button>
                    </div>

                    <p class="hp-guide-complete">
                        <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">Explore the complete Project Guide</a>
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. Depths -->
        <section class="hp-section hp-surface" id="depths" aria-labelledby="depths-title">
            <div class="hp-wrap">
                <p class="hp-kicker">One mental model, three depths</p>
                <h2 id="depths-title">Absorb what you need now. Dig deeper when the gap would reopen.</h2>
                <p class="hp-lead">
                    AI accelerates generation. Humans still need a paced explanation.
                    Same facts—Understand, Work on it, Reference—so you are not force-fed a schema dump when you only needed the story.
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
                                <li><strong>Purpose:</strong> Capture an idea so it survives beyond a chat.</li>
                                <li><strong>User journey:</strong> Submit a form and see it on the list.</li>
                                <li><strong>Mental model:</strong> Browser → PHP → SQLite → screen.</li>
                                <li><strong>Relationships:</strong> The list shows what actually got stored.</li>
                            </ul>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-1" aria-labelledby="depth-tab-1" data-depth-panel="1" hidden>
                            <h3>Work on it</h3>
                            <ul>
                                <li><strong>Responsibilities:</strong> Validate, write, redirect, reload.</li>
                                <li><strong>Data touched:</strong> Title, notes, status, timestamps in SQLite.</li>
                                <li><strong>Invariant:</strong> Read and write paths must stay aligned.</li>
                                <li><strong>Change impact:</strong> New fields need migration, forms, write, read, and a production apply.</li>
                            </ul>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-2" aria-labelledby="depth-tab-2" data-depth-panel="2" hidden>
                            <h3>Reference</h3>
                            <ul>
                                <li><strong>Important areas:</strong> Ideas CRUD, database layer, templates.</li>
                                <li><strong>Decisions:</strong> Manual migrations; SQLite; no auth; no uploads.</li>
                                <li><strong>Debugging:</strong> Fields vanish when write/read diverge.</li>
                                <li><strong>History:</strong> Assumptions and notes living in <code>.vibekb/</code>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-depth-fallback">
                        <h3>Understand</h3>
                        <p>Purpose and mental model—at human absorption speed.</p>
                        <h3>Work on it</h3>
                        <p>Responsibilities, invariants, risks—before you edit.</p>
                        <h3>Reference</h3>
                        <p>Decisions, debugging notes, history—accumulated as you go.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Workflow: never open the gap -->
        <section class="hp-section" id="workflow" aria-labelledby="workflow-title">
            <div class="hp-wrap">
                <p class="hp-kicker">The new workflow</p>
                <h2 id="workflow-title">Don&#39;t reconstruct understanding later. Grow it from day one.</h2>
                <p class="hp-lead">
                    AI is accelerating software development. Human understanding is not accelerating at the same rate.
                    VibeKB closes that gap by putting a living Project Guide in the repository at the start—and keeping it current as you build.
                </p>

                <div class="hp-timeline" data-pipeline data-timeline>
                    <ol class="hp-timeline-track" aria-hidden="true">
                        <li>New project</li>
                        <li>Initialize VibeKB</li>
                        <li>Build with AI tools</li>
                        <li>Understanding stays</li>
                        <li>Months later…</li>
                    </ol>

                    <div class="hp-pipeline-stages hp-timeline-stages" role="tablist" aria-label="Closing the understanding gap from day one">
                        <button type="button" class="hp-pipe-stage is-active" role="tab" id="pipe-tab-0" aria-selected="true" aria-controls="pipe-panel-0" data-pipe="0">1. New project</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-1" aria-selected="false" aria-controls="pipe-panel-1" data-pipe="1" tabindex="-1">2. Initialize VibeKB</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-2" aria-selected="false" aria-controls="pipe-panel-2" data-pipe="2" tabindex="-1">3. Build normally</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-3" aria-selected="false" aria-controls="pipe-panel-3" data-pipe="3" tabindex="-1">4. Understanding stays</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-4" aria-selected="false" aria-controls="pipe-panel-4" data-pipe="4" tabindex="-1">5. Months later…</button>
                    </div>
                    <div class="hp-pipeline-panels">
                        <div class="hp-pipe-panel is-active" role="tabpanel" id="pipe-panel-0" aria-labelledby="pipe-tab-0" data-pipe-panel="0">
                            <p><span class="hp-badge hp-badge-now">Same starting line</span></p>
                            <p>You open a new AI-assisted project the way you already do. The difference is what you refuse to leave behind.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-1" aria-labelledby="pipe-tab-1" data-pipe-panel="1" hidden>
                            <p><span class="hp-badge hp-badge-now">Before the gap opens</span></p>
                            <p>Initialize VibeKB immediately. The companion belongs in the repo before hundreds of lines become tens of thousands.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-2" aria-labelledby="pipe-tab-2" data-pipe-panel="2" hidden>
                            <p><span class="hp-badge hp-badge-now">Keep your tools</span></p>
                            <p>Continue with Cursor, Claude Code, Windsurf, Copilot, Gemini CLI—whatever you use. Generation stays fast. Understanding no longer has to lag by default.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-3" aria-labelledby="pipe-tab-3" data-pipe-panel="3" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span> <span class="hp-badge hp-badge-later">Deepens over time</span></p>
                            <p>
                                As the software evolves, so does the Project Guide: decisions, risks, dependencies, assumptions, workflows, and debugging discoveries
                                land in the repository while you work.
                                Version 1 is a working guide system maintained with the project (including by your coding agent following the protocol).
                                Richer automation is architecture direction—not a finished claim.
                            </p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-4" aria-labelledby="pipe-tab-4" data-pipe-panel="4" hidden>
                            <p><span class="hp-badge hp-badge-now">The outcome</span></p>
                            <p>You still understand what you built. The next session starts from the repo&#39;s mental model—not from reconstructing months of chats or reverse-engineering the tree.</p>
                        </div>
                    </div>
                    <div class="hp-pipeline-fallback">
                        <ol>
                            <li><strong>New project</strong> — start an AI-assisted build.</li>
                            <li><strong>Initialize VibeKB</strong> — before the understanding gap opens.</li>
                            <li><strong>Build normally</strong> — keep your coding tools.</li>
                            <li><strong>Understanding stays</strong> — the Project Guide evolves with the code.</li>
                            <li><strong>Months later…</strong> — you still know the system.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details" id="how-it-works">
                    <summary>Version 1, honestly</summary>
                    <p>
                        Version 1 proves the category: repository-owned knowledge, a guided Project Guide, and a sample that shows preserved understanding.
                        No accounts. No cloud AI APIs. No claim that every fact extracts itself without curation.
                        The premise is the point—close the gap between generation speed and human absorption.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. What to preserve -->
        <section class="hp-section hp-surface" id="relevance" aria-labelledby="relevance-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What belongs in the mental model</p>
                <h2 id="relevance-title">Keep what would otherwise die in a conversation.</h2>
                <p class="hp-lead">Not every file. The facts that stop understanding from falling behind generation.</p>

                <div class="hp-filter" data-relevance>
                    <div class="hp-filter-list" role="tablist" aria-label="What to preserve while building">
                        <button type="button" class="hp-filter-btn is-active" role="tab" id="rel-tab-0" aria-selected="true" aria-controls="rel-panel-0" data-rel="0">Does it improve understanding?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-1" aria-selected="false" aria-controls="rel-panel-1" data-rel="1" tabindex="-1">Need it before the next edit?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-2" aria-selected="false" aria-controls="rel-panel-2" data-rel="2" tabindex="-1">Could skipping it cause harm?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-3" aria-selected="false" aria-controls="rel-panel-3" data-rel="3" tabindex="-1">Would this shorten a future debug?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-4" aria-selected="false" aria-controls="rel-panel-4" data-rel="4" tabindex="-1">Will the next session forget this?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-5" aria-selected="false" aria-controls="rel-panel-5" data-rel="5" tabindex="-1">Does it explain architectural intent?</button>
                    </div>
                    <div class="hp-filter-panels">
                        <div class="hp-filter-panel is-active" role="tabpanel" id="rel-panel-0" aria-labelledby="rel-tab-0" data-rel-panel="0">
                            <p><strong>Preserve:</strong> “Ideas are first-class records with status and timestamps.”</p>
                            <p>Product truth written when it becomes true—so absorption doesn&#39;t wait for archaeology.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-1" aria-labelledby="rel-tab-1" data-rel-panel="1" hidden>
                            <p><strong>Preserve:</strong> “Read and write paths must stay aligned when fields change.”</p>
                            <p>Otherwise the next generated change updates only half the system.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-2" aria-labelledby="rel-tab-2" data-rel-panel="2" hidden>
                            <p><strong>Preserve:</strong> “A login page alone does not make this safely multi-user.”</p>
                            <p>Boundaries belong in the repo before speed outruns judgment.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-3" aria-labelledby="rel-tab-3" data-rel-panel="3" hidden>
                            <p><strong>Preserve:</strong> The blank-list order you learned the hard way.</p>
                            <p>Solve it once. Keep the path so understanding compounds.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-4" aria-labelledby="rel-tab-4" data-rel-panel="4" hidden>
                            <p><strong>Preserve:</strong> Why SQLite and manual migrations were chosen.</p>
                            <p>Chats forget. The repository can remember—if you put it there as you decide.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-5" aria-labelledby="rel-tab-5" data-rel-panel="5" hidden>
                            <p><strong>Preserve:</strong> “No uploads is intentional—not unfinished.”</p>
                            <p>Intent stops the next session from inventing a subsystem that reopens the gap.</p>
                        </div>
                    </div>
                    <div class="hp-filter-fallback">
                        <ol>
                            <li>Does it improve understanding?</li>
                            <li>Need it before the next edit?</li>
                            <li>Could skipping it cause harm?</li>
                            <li>Would this shorten a future debug?</li>
                            <li>Will the next session forget this?</li>
                            <li>Does it explain architectural intent?</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>What we intentionally leave out</summary>
                    <ul class="hp-plain-list">
                        <li>Every file</li>
                        <li>Every function</li>
                        <li>Full line-by-line schemas</li>
                        <li>Obvious implementation noise</li>
                        <li>Generic framework tutorials</li>
                        <li>Temporary trivia from a throwaway prompt</li>
                        <li>Exhaustive dependency dumps</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 8. Emotional outcome / audience -->
        <section class="hp-section" id="audience" aria-labelledby="audience-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Who this is for</p>
                <h2 id="audience-title">Not because you already lost the plot. Because you never want to.</h2>
                <p class="hp-lead">
                    If you already feel the gap, the sample guide helps today.
                    The deeper reason VibeKB belongs in every new AI-assisted repo is simpler: you do not want understanding to fall behind generation again.
                </p>

                <div class="hp-compare" data-compare>
                    <div class="hp-compare-tabs" role="tablist" aria-label="Why the gap matters">
                        <button type="button" class="hp-compare-tab is-active" role="tab" id="cmp-tab-0" aria-selected="true" aria-controls="cmp-panel-0" data-cmp="0">Starting the next build</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-1" aria-selected="false" aria-controls="cmp-panel-1" data-cmp="1" tabindex="-1">Shipping at AI speed</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-2" aria-selected="false" aria-controls="cmp-panel-2" data-cmp="2" tabindex="-1">The next AI session</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-3" aria-selected="false" aria-controls="cmp-panel-3" data-cmp="3" tabindex="-1">The next human</button>
                    </div>
                    <div class="hp-compare-panels">
                        <div class="hp-compare-panel is-active" role="tabpanel" id="cmp-panel-0" aria-labelledby="cmp-tab-0" data-cmp-panel="0">
                            <p class="hp-tabpanel-lead">Put understanding in the repo before generation outruns absorption.</p>
                            <ul class="hp-plain-list">
                                <li>Companion present from day one</li>
                                <li>Mental model starts empty and grows honestly</li>
                                <li>No plan to “document it after launch”</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-1" aria-labelledby="cmp-tab-1" data-cmp-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Keep the Project Guide in the same change sets as the architecture.</p>
                            <ul class="hp-plain-list">
                                <li>Decisions recorded while they are still obvious</li>
                                <li>Risks tracked as features land</li>
                                <li>Less fear every time you open the editor</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-2" aria-labelledby="cmp-tab-2" data-cmp-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Hand the next session a repository-owned mental model—not amnesia.</p>
                            <ul class="hp-plain-list">
                                <li>Stable context in Git</li>
                                <li>Fewer guessed assumptions</li>
                                <li>More focused change instructions</li>
                            </ul>
                            <p class="hp-note">Better context reduces ambiguity. It does not make every generated change correct.</p>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-3" aria-labelledby="cmp-tab-3" data-cmp-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Transfer understanding instead of forcing reverse-engineering.</p>
                            <ul class="hp-plain-list">
                                <li>Faster onboarding</li>
                                <li>Clearer boundaries</li>
                                <li>Less “wait, why is it like this?”</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-compare-fallback">
                        <h3>Starting the next build</h3>
                        <p>Close the gap before it opens—initialize VibeKB on day one.</p>
                        <h3>Shipping at AI speed</h3>
                        <p>Keep understanding current in the same change sets as the code.</p>
                        <h3>The next AI session</h3>
                        <p>Repository-owned context instead of expired chats.</p>
                        <h3>The next human</h3>
                        <p>A living Project Guide beats archaeology.</p>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why preserving once beats re-explaining forever</summary>
                    <p>
                        The expensive work is creating structured understanding as you go.
                        After that, Understand / Work on it / Reference views reuse the same source—
                        instead of regenerating the whole story every time generation gets ahead of absorption again.
                    </p>
                </details>
            </div>
        </section>

        <!-- 9. Repository -->
        <section class="hp-section hp-surface" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Where understanding lives</p>
                <h2 id="arch-title">Conversations are temporary. The repository can remember the project.</h2>
                <p class="hp-lead">
                    VibeKB is repository-native from the beginning: continuously maintained, versioned with the software,
                    and always available when the next session starts.
                </p>

                <div class="hp-repo" data-repo-map>
                    <div class="hp-repo-tree" role="tablist" aria-label=".vibekb structure">
                        <button type="button" class="hp-repo-item is-active" role="tab" id="repo-tab-0" aria-selected="true" aria-controls="repo-panel-0" data-repo="0"><code>.vibekb/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-1" aria-selected="false" aria-controls="repo-panel-1" data-repo="1" tabindex="-1"><code>project.json</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-2" aria-selected="false" aria-controls="repo-panel-2" data-repo="2" tabindex="-1"><code>guide/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-3" aria-selected="false" aria-controls="repo-panel-3" data-repo="3" tabindex="-1"><code>decisions/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-4" aria-selected="false" aria-controls="repo-panel-4" data-repo="4" tabindex="-1"><code>risks/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-5" aria-selected="false" aria-controls="repo-panel-5" data-repo="5" tabindex="-1"><code>debugging/</code></button>
                        <button type="button" class="hp-repo-item" role="tab" id="repo-tab-6" aria-selected="false" aria-controls="repo-panel-6" data-repo="6" tabindex="-1"><code>glossary/</code></button>
                    </div>
                    <div class="hp-repo-panels">
                        <div class="hp-repo-panel is-active" role="tabpanel" id="repo-panel-0" aria-labelledby="repo-tab-0" data-repo-panel="0">
                            <p>Where the project&#39;s mental model lives—initialized early, grown continuously, deployed with the site.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-1" aria-labelledby="repo-tab-1" data-repo-panel="1" hidden>
                            <p>Identity that stays accurate as generation accelerates: name, stack, constraints, audience.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-2" aria-labelledby="repo-tab-2" data-repo-panel="2" hidden>
                            <p>Chapter JSON for the living Project Guide—the human-paced explanation of a fast-growing system.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-3" aria-labelledby="repo-tab-3" data-repo-panel="3" hidden>
                            <p>Decisions written when you make them—so trade-offs do not evaporate with the chat.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-4" aria-labelledby="repo-tab-4" data-repo-panel="4" hidden>
                            <p>Risks tracked as the architecture grows—before the gap turns into an incident.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-5" aria-labelledby="repo-tab-5" data-repo-panel="5" hidden>
                            <p>Debugging paths captured the first time—so you do not relearn them at generation speed.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-6" aria-labelledby="repo-tab-6" data-repo-panel="6" hidden>
                            <p>Shared vocabulary that accumulates—so every session means the same words.</p>
                        </div>
                    </div>
                    <div class="hp-repo-fallback">
                        <ul>
                            <li><code>.vibekb/</code> — project mental model root</li>
                            <li><code>project.json</code> — identity and constraints</li>
                            <li><code>guide/</code> — living Project Guide chapters</li>
                            <li><code>decisions/</code> — choices recorded as you make them</li>
                            <li><code>risks/</code> — failure modes tracked as you learn them</li>
                            <li><code>debugging/</code> — investigation paths that stick</li>
                            <li><code>glossary/</code> — shared vocabulary</li>
                        </ul>
                    </div>
                </div>

                <ul class="hp-plain-list hp-arch-points">
                    <li>Present from the beginning. Continuously maintained. Versioned with the software.</li>
                    <li>When architecture changes, update the guide in the same change set.</li>
                    <li>The website is one view of preserved understanding—not the understanding itself.</li>
                </ul>
            </div>
        </section>

        <!-- 10. Principles -->
        <section class="hp-section" id="principles" aria-labelledby="principles-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Principles</p>
                <h2 id="principles-title">Rules for keeping understanding ahead of the gap.</h2>

                <div class="hp-manifesto" data-manifesto>
                    <div class="hp-manifesto-stage" aria-live="polite">
                        <p class="hp-manifesto-index">Principle <span data-manifesto-current>1</span> of 7</p>
                        <p class="hp-manifesto-text is-active" data-manifesto-item="0">Generation got faster. Understanding must keep up.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="1" hidden>Preserve understanding while you build—not after you&#39;re lost.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="2" hidden>The Project Guide is a living companion, not a post-mortem.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="3" hidden>Documentation describes. VibeKB preserves understanding.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="4" hidden>Record once. Explain at human absorption speed.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="5" hidden>The repository is the source of truth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="6" hidden>Never make the next session reconstruct the mental model.</p>
                    </div>
                    <div class="hp-manifesto-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-manifesto-prev disabled>Previous</button>
                        <button type="button" class="hp-btn hp-btn-secondary" data-manifesto-next>Next principle</button>
                    </div>
                    <div class="hp-manifesto-fallback">
                        <ol>
                            <li>Generation got faster. Understanding must keep up.</li>
                            <li>Preserve understanding while you build—not after you&#39;re lost.</li>
                            <li>The Project Guide is a living companion, not a post-mortem.</li>
                            <li>Documentation describes. VibeKB preserves understanding.</li>
                            <li>Record once. Explain at human absorption speed.</li>
                            <li>The repository is the source of truth.</li>
                            <li>Never make the next session reconstruct the mental model.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">See what it looks like when understanding keeps up.</h2>
                <p>
                    Open the <?= homepage_e($sampleDisplayName) ?> Project Guide.
                    Then put the same idea in your next AI-assisted repo on day one—
                    so you never want to lose control of the project you&#39;re about to build.
                </p>
                <p class="hp-thesis">
                    For the first time in software history, code is being generated faster than humans can understand it.
                    VibeKB exists to close that gap.
                </p>
                <div class="hp-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">Explore the complete Project Guide</a>
                    <a class="hp-btn hp-btn-ghost" href="<?= homepage_e($repoUrl) ?>" rel="noopener noreferrer">View the repository</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="hp-footer">
        <div class="hp-wrap hp-footer-inner">
            <p><strong>VibeKB.</strong> Close the gap between generation speed and human understanding.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo from day one · <a href="<?= homepage_e($guideUrl) ?>">Project Guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="assets/js/homepage.js" defer></script>
</body>
</html>
