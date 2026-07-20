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
    <title>VibeKB — Build with AI. Keep the understanding.</title>
    <meta name="description" content="Start every AI-assisted project with VibeKB. As you build with Cursor or Claude Code, the Project Guide grows with the repo—so months later you still understand what you shipped.">
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
                <a href="#workflow">How it works</a>
                <a class="hp-nav-cta" href="<?= homepage_e($guideUrl) ?>">Project Guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">Infrastructure for AI-assisted building</p>
                    <h1 id="hero-title">Build with AI. Keep the understanding.</h1>
                    <p class="hp-hero-support">
                        Start every AI-assisted project with VibeKB already in the repository.
                        As you build with Cursor, Claude Code, Windsurf, Copilot, or Gemini CLI,
                        the Project Guide grows with the software—decisions, risks, dependencies, and mental models,
                        recorded while you work. Not a cleanup pass. A development companion.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">See a living Project Guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#workflow">See the day-one workflow</a>
                    </div>
                </div>
                <figure class="hp-hero-visual" aria-labelledby="hero-flow-label">
                    <figcaption id="hero-flow-label" class="visually-hidden">
                        Initialize VibeKB, build with your coding tools, and the Project Guide grows with the project.
                    </figcaption>
                    <ol class="hp-mini-flow" data-hero-flow>
                        <li class="is-active"><span>New project</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Initialize VibeKB</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Guide grows with you</span></li>
                    </ol>
                </figure>
            </div>
        </section>

        <!-- 2. The trap you avoid -->
        <section class="hp-section" id="problem" aria-labelledby="problem-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">The trap</p>
                <h2 id="problem-title">You don&#39;t have to end up afraid of your own repo.</h2>
                <p class="hp-lead">
                    AI coding makes it easy to ship faster than you can absorb.
                    Without a place for understanding to land, chats expire, decisions vanish,
                    and six months later you&#39;re reverse-engineering yourself.
                    VibeKB is how you never get stuck there.
                </p>

                <div class="hp-stepper" data-stepper="problem">
                    <div class="hp-stepper-tabs" role="tablist" aria-label="What happens without a living guide">
                        <button type="button" class="hp-step-tab is-active" role="tab" id="prob-tab-0" aria-selected="true" aria-controls="prob-panel-0" data-step="0">1. Without a companion</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-1" aria-selected="false" aria-controls="prob-panel-1" data-step="1" tabindex="-1">2. Understanding leaks</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-2" aria-selected="false" aria-controls="prob-panel-2" data-step="2" tabindex="-1">3. With VibeKB from day one</button>
                    </div>
                    <div class="hp-stepper-panels">
                        <div class="hp-step-panel is-active" role="tabpanel" id="prob-panel-0" aria-labelledby="prob-tab-0" data-step-panel="0">
                            <p>You open a repo and start prompting. Features land. The tool explains itself in a chat that won&#39;t outlive the week. The code is real. The story is rented.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-1" aria-labelledby="prob-tab-1" data-step-panel="1" hidden>
                            <p>Architectural choices, weird bugs you already fixed, and “don&#39;t touch this” assumptions evaporate with the session. The next you—or the next agent—starts from scratch.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-2" aria-labelledby="prob-tab-2" data-step-panel="2" hidden>
                            <p>Initialize VibeKB when the project starts. As you build, the Project Guide evolves in the same repo. Understanding compounds instead of resetting.</p>
                        </div>
                    </div>
                    <div class="hp-stepper-fallback">
                        <ol>
                            <li><strong>Without a companion.</strong> Code ships; the story stays in temporary chats.</li>
                            <li><strong>Understanding leaks.</strong> Decisions and risks vanish between sessions.</li>
                            <li><strong>With VibeKB from day one.</strong> The Project Guide grows with the software.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why “I&#39;ll document it later” fails</summary>
                    <ul class="hp-plain-list">
                        <li>Later never comes—the next prompt always feels more urgent.</li>
                        <li>Chats are temporary. They don&#39;t ship with the repo.</li>
                        <li>READMEs cover setup, not the living mental model.</li>
                        <li>Code shows what runs, not why it was shaped that way.</li>
                        <li>Reconstruction costs more than recording decisions as you go.</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 3. Product outcomes -->
        <section class="hp-section hp-surface" id="product" aria-labelledby="product-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What grows with the project</p>
                <h2 id="product-title">A living Project Guide—not a post-mortem report.</h2>
                <p class="hp-lead">
                    While you build, VibeKB is where durable understanding lands:
                    what the app does, how the pieces connect, what&#39;s risky to change, and where bugs usually start.
                    Always available. Always in the repo.
                </p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="What the Project Guide keeps current">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">What this app does</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">How the system works</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">What&#39;s safe to change</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Where problems start</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">Product truth stays current as features land—so you never have to rediscover what you shipped.</p>
                            <p class="hp-example"><strong>In the sample:</strong> one person collecting software ideas, explained in plain language first.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#what-is-this">See “What is this project?”</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Request paths and data flow get recorded when they&#39;re still obvious—not six months later from archaeology.</p>
                            <p class="hp-example"><strong>In the sample:</strong> form → PHP validation → SQLite write → reload → updated list.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#save-flow">See the save path</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Assumptions, invariants, and side effects travel with the code—ready before the next refactor prompt.</p>
                            <p class="hp-example"><strong>In the sample:</strong> a new field means migration, forms, write path, read path, and production apply—together.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#change-safely">See change-safety guides</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Debugging discoveries stick in the repo the first time you solve them—so you don&#39;t relearn them.</p>
                            <p class="hp-example"><strong>In the sample:</strong> blank list → file exists? PHP can read it? Query returns rows?</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#problems">See troubleshooting sequences</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article>
                            <h3>What this app does</h3>
                            <p>Product understanding that stays current as you build.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#what-is-this">Open “What is this project?”</a>
                        </article>
                        <article>
                            <h3>How the system works</h3>
                            <p>Request and data paths recorded while they&#39;re still fresh.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#save-flow">Open the save path</a>
                        </article>
                        <article>
                            <h3>What&#39;s safe to change</h3>
                            <p>Invariants and change impact that grow with the architecture.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#change-safely">Open change-safety guides</a>
                        </article>
                        <article>
                            <h3>Where problems start</h3>
                            <p>Debugging paths that accumulate instead of disappearing.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#problems">Open troubleshooting</a>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Interactive sample preview -->
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What a living guide looks like</p>
                <h2 id="sample-title"><?= homepage_e($sampleDisplayName) ?></h2>
                <p class="hp-lead">
                    This is the shape of understanding that grows alongside a real build—
                    chapters that start simple and open into developer detail when you need it.
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
                <p class="hp-kicker">One living source</p>
                <h2 id="depths-title">Simple while you&#39;re shipping. Technical when you dig in.</h2>
                <p class="hp-lead">
                    Record the fact once as the project evolves.
                    Explain it at the depth you need today—without regenerating the whole story from chat history.
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
                        <p>Purpose and mental model—kept current as the product changes.</p>
                        <h3>Work on it</h3>
                        <p>Responsibilities, invariants, risks—ready when you edit.</p>
                        <h3>Reference</h3>
                        <p>Decisions, debugging notes, history—accumulated as you go.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Central workflow timeline -->
        <section class="hp-section" id="workflow" aria-labelledby="workflow-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Day-one workflow</p>
                <h2 id="workflow-title">Start with VibeKB. Keep building. Still understand it later.</h2>
                <p class="hp-lead">
                    AI coding is becoming normal. Project understanding should evolve alongside the code.
                    That&#39;s the job.
                </p>

                <div class="hp-timeline" data-pipeline data-timeline>
                    <ol class="hp-timeline-track" aria-hidden="true">
                        <li>New project</li>
                        <li>Initialize VibeKB</li>
                        <li>Build normally</li>
                        <li>Guide grows</li>
                        <li>Months later…</li>
                    </ol>

                    <div class="hp-pipeline-stages hp-timeline-stages" role="tablist" aria-label="VibeKB day-one workflow">
                        <button type="button" class="hp-pipe-stage is-active" role="tab" id="pipe-tab-0" aria-selected="true" aria-controls="pipe-panel-0" data-pipe="0">1. New project</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-1" aria-selected="false" aria-controls="pipe-panel-1" data-pipe="1" tabindex="-1">2. Initialize VibeKB</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-2" aria-selected="false" aria-controls="pipe-panel-2" data-pipe="2" tabindex="-1">3. Build with your tools</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-3" aria-selected="false" aria-controls="pipe-panel-3" data-pipe="3" tabindex="-1">4. Guide grows with it</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-4" aria-selected="false" aria-controls="pipe-panel-4" data-pipe="4" tabindex="-1">5. Months later…</button>
                    </div>
                    <div class="hp-pipeline-panels">
                        <div class="hp-pipe-panel is-active" role="tabpanel" id="pipe-panel-0" aria-labelledby="pipe-tab-0" data-pipe-panel="0">
                            <p><span class="hp-badge hp-badge-now">The starting point</span></p>
                            <p>You spin up a new AI-assisted project—the same way you already do. Empty repo. Fresh energy. No requirement to “finish first.”</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-1" aria-labelledby="pipe-tab-1" data-pipe-panel="1" hidden>
                            <p><span class="hp-badge hp-badge-now">From day one</span></p>
                            <p>Install or initialize VibeKB immediately. The companion belongs in the repository before the first feature lands—not after you&#39;re lost.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-2" aria-labelledby="pipe-tab-2" data-pipe-panel="2" hidden>
                            <p><span class="hp-badge hp-badge-now">Keep your workflow</span></p>
                            <p>Continue building normally with Cursor, Claude Code, Windsurf, Copilot, Gemini CLI, or whatever you use. VibeKB doesn&#39;t replace your coding tools. It rides alongside them.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-3" aria-labelledby="pipe-tab-3" data-pipe-panel="3" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span> <span class="hp-badge hp-badge-later">Deepens over time</span></p>
                            <p>
                                As the software evolves, so does the Project Guide: decisions, risks, dependencies, assumptions, workflows, and debugging discoveries
                                land in repository-owned knowledge while you build.
                                Version 1 is a working guide system maintained with the project (including by your coding agent following the protocol).
                                Richer automation is architecture direction—not something we pretend is finished.
                            </p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-4" aria-labelledby="pipe-tab-4" data-pipe-panel="4" hidden>
                            <p><span class="hp-badge hp-badge-now">The point</span></p>
                            <p>You still understand what you built. So does the next session. Neither of you has to reconstruct months of chats or reverse-engineer thousands of lines just to make a safe change.</p>
                        </div>
                    </div>
                    <div class="hp-pipeline-fallback">
                        <ol>
                            <li><strong>New project</strong> — start an AI-assisted build as usual.</li>
                            <li><strong>Initialize VibeKB</strong> — put the companion in the repo from day one.</li>
                            <li><strong>Build with your tools</strong> — Cursor, Claude Code, and friends as normal.</li>
                            <li><strong>Guide grows with it</strong> — understanding recorded alongside the code.</li>
                            <li><strong>Months later…</strong> — you still understand the project.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details" id="how-it-works">
                    <summary>Version 1, honestly</summary>
                    <p>
                        Version 1 proves the companion model: repository-owned knowledge, a guided Project Guide, and a sample that shows the living explanation.
                        No accounts. No cloud AI APIs. No claim that every fact extracts itself without curation.
                        The vision is clear—understanding evolves with the code from day one.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. Relevance -->
        <section class="hp-section hp-surface" id="relevance" aria-labelledby="relevance-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What gets recorded as you build</p>
                <h2 id="relevance-title">Capture what future-you will need—while it&#39;s still obvious.</h2>
                <p class="hp-lead">Not every file. The decisions, risks, and mental models that would otherwise die in a chat.</p>

                <div class="hp-filter" data-relevance>
                    <div class="hp-filter-list" role="tablist" aria-label="What to record while building">
                        <button type="button" class="hp-filter-btn is-active" role="tab" id="rel-tab-0" aria-selected="true" aria-controls="rel-panel-0" data-rel="0">Does it improve understanding?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-1" aria-selected="false" aria-controls="rel-panel-1" data-rel="1" tabindex="-1">Need it before the next edit?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-2" aria-selected="false" aria-controls="rel-panel-2" data-rel="2" tabindex="-1">Could skipping it cause harm?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-3" aria-selected="false" aria-controls="rel-panel-3" data-rel="3" tabindex="-1">Would this shorten a future debug?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-4" aria-selected="false" aria-controls="rel-panel-4" data-rel="4" tabindex="-1">Will the next session forget this?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-5" aria-selected="false" aria-controls="rel-panel-5" data-rel="5" tabindex="-1">Does it explain architectural intent?</button>
                    </div>
                    <div class="hp-filter-panels">
                        <div class="hp-filter-panel is-active" role="tabpanel" id="rel-panel-0" aria-labelledby="rel-tab-0" data-rel-panel="0">
                            <p><strong>Record while building:</strong> “Ideas are first-class records with status and timestamps.”</p>
                            <p>Product truth written down the day it becomes true—not reconstructed later.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-1" aria-labelledby="rel-tab-1" data-rel-panel="1" hidden>
                            <p><strong>Record while building:</strong> “Read and write paths must stay aligned when fields change.”</p>
                            <p>So the next prompt doesn&#39;t “helpfully” update only half the path.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-2" aria-labelledby="rel-tab-2" data-rel-panel="2" hidden>
                            <p><strong>Record while building:</strong> “A login page alone does not make this safely multi-user.”</p>
                            <p>Boundaries belong in the repo before someone half-ships auth.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-3" aria-labelledby="rel-tab-3" data-rel-panel="3" hidden>
                            <p><strong>Record while building:</strong> The blank-list order you just learned the hard way.</p>
                            <p>Solve it once. Keep the path for every future night.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-4" aria-labelledby="rel-tab-4" data-rel-panel="4" hidden>
                            <p><strong>Record while building:</strong> Why SQLite and manual migrations were chosen.</p>
                            <p>Chats forget. The repository can remember—if you put it there as you decide.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-5" aria-labelledby="rel-tab-5" data-rel-panel="5" hidden>
                            <p><strong>Record while building:</strong> “No uploads is intentional—not unfinished.”</p>
                            <p>Intent prevents the next session from inventing a subsystem you never wanted.</p>
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

        <!-- 8. Scenarios -->
        <section class="hp-section" id="audience" aria-labelledby="audience-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Who it&#39;s for</p>
                <h2 id="audience-title">Anyone who plans to still care about this repo next month.</h2>

                <div class="hp-compare" data-compare>
                    <div class="hp-compare-tabs" role="tablist" aria-label="Companion scenarios">
                        <button type="button" class="hp-compare-tab is-active" role="tab" id="cmp-tab-0" aria-selected="true" aria-controls="cmp-panel-0" data-cmp="0">Starting a new build</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-1" aria-selected="false" aria-controls="cmp-panel-1" data-cmp="1" tabindex="-1">Shipping features weekly</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-2" aria-selected="false" aria-controls="cmp-panel-2" data-cmp="2" tabindex="-1">Handing off to another AI</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-3" aria-selected="false" aria-controls="cmp-panel-3" data-cmp="3" tabindex="-1">Handing off to a human</button>
                    </div>
                    <div class="hp-compare-panels">
                        <div class="hp-compare-panel is-active" role="tabpanel" id="cmp-panel-0" aria-labelledby="cmp-tab-0" data-cmp-panel="0">
                            <p class="hp-tabpanel-lead">Initialize VibeKB with the repo—before the first feature makes the story hard to recover.</p>
                            <ul class="hp-plain-list">
                                <li>Companion present from commit one</li>
                                <li>Understanding starts empty and grows honestly</li>
                                <li>No “we&#39;ll document it after launch” lie</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-1" aria-labelledby="cmp-tab-1" data-cmp-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Keep the guide current as architecture changes—same change set as the code.</p>
                            <ul class="hp-plain-list">
                                <li>Decisions and risks updated while they&#39;re fresh</li>
                                <li>Change-safety notes before the scary refactor</li>
                                <li>Less fear every time you open the editor</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-2" aria-labelledby="cmp-tab-2" data-cmp-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Give the next session repository-owned context—not amnesia and a prayer.</p>
                            <ul class="hp-plain-list">
                                <li>Stable project memory in Git</li>
                                <li>Fewer guessed assumptions</li>
                                <li>More focused change instructions</li>
                            </ul>
                            <p class="hp-note">Better context reduces ambiguity. It doesn&#39;t magically make every generated change correct.</p>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-3" aria-labelledby="cmp-tab-3" data-cmp-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Hand over a living explanation instead of forcing archaeology.</p>
                            <ul class="hp-plain-list">
                                <li>Faster onboarding</li>
                                <li>Clearer boundaries</li>
                                <li>Less “wait, why is it like this?”</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-compare-fallback">
                        <h3>Starting a new build</h3>
                        <p>Initialize VibeKB from day one so understanding can grow with the code.</p>
                        <h3>Shipping features weekly</h3>
                        <p>Update the guide in the same change sets as the architecture.</p>
                        <h3>Handing off to another AI</h3>
                        <p>Repository-owned context instead of expired chats.</p>
                        <h3>Handing off to a human</h3>
                        <p>A living Project Guide beats reverse-engineering.</p>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why recording once beats re-explaining forever</summary>
                    <p>
                        The expensive work is creating structured understanding as you go.
                        After that, Understand / Work on it / Reference views reuse the same source—
                        instead of regenerating the whole story in every new session.
                    </p>
                </details>
            </div>
        </section>

        <!-- 9. Repository architecture -->
        <section class="hp-section hp-surface" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Repository-native</p>
                <h2 id="arch-title">Your coding tools remember chats. Your repo should remember the project.</h2>
                <p class="hp-lead">
                    VibeKB is present from the beginning, continuously maintained, and versioned with the software.
                    The Project Guide is one living view of that knowledge.
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
                            <p>Project memory root—initialized early, deployed with the site, grown continuously. Do not exclude it from rsync.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-1" aria-labelledby="repo-tab-1" data-repo-panel="1" hidden>
                            <p>Identity that stays accurate as the product changes: name, stack, constraints, audience.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-2" aria-labelledby="repo-tab-2" data-repo-panel="2" hidden>
                            <p>Chapter JSON for the living Project Guide. Presentation under <code>guide/</code>; the evolving story stays here.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-3" aria-labelledby="repo-tab-3" data-repo-panel="3" hidden>
                            <p>Accepted decisions written when you make them—SQLite, no auth, no uploads, manual migrations—and why they stand.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-4" aria-labelledby="repo-tab-4" data-repo-panel="4" hidden>
                            <p>Risks tracked as the architecture grows: schema drift, half-shipped auth, losing the plot as features pile up.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-5" aria-labelledby="repo-tab-5" data-repo-panel="5" hidden>
                            <p>Debugging paths captured the first time you solve them—blank lists, vanishing fields, deploy mismatch.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-6" aria-labelledby="repo-tab-6" data-repo-panel="6" hidden>
                            <p>Shared vocabulary that accumulates with the project—so every session means the same words.</p>
                        </div>
                    </div>
                    <div class="hp-repo-fallback">
                        <ul>
                            <li><code>.vibekb/</code> — project memory that grows from day one</li>
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
                    <li>The website is one view of the living explanation—not the explanation itself.</li>
                </ul>
            </div>
        </section>

        <!-- 10. Principles -->
        <section class="hp-section" id="principles" aria-labelledby="principles-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Principles</p>
                <h2 id="principles-title">How a development companion behaves.</h2>

                <div class="hp-manifesto" data-manifesto>
                    <div class="hp-manifesto-stage" aria-live="polite">
                        <p class="hp-manifesto-index">Principle <span data-manifesto-current>1</span> of 7</p>
                        <p class="hp-manifesto-text is-active" data-manifesto-item="0">Start with understanding in the repo—not after you&#39;re lost.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="1" hidden>The Project Guide evolves with the project.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="2" hidden>Record once. Explain at the right depth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="3" hidden>The repository is the source of truth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="4" hidden>Explain intent, not only implementation.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="5" hidden>Never make the next session guess.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="6" hidden>Curated understanding, not automated exhaust.</p>
                    </div>
                    <div class="hp-manifesto-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-manifesto-prev disabled>Previous</button>
                        <button type="button" class="hp-btn hp-btn-secondary" data-manifesto-next>Next principle</button>
                    </div>
                    <div class="hp-manifesto-fallback">
                        <ol>
                            <li>Start with understanding in the repo—not after you&#39;re lost.</li>
                            <li>The Project Guide evolves with the project.</li>
                            <li>Record once. Explain at the right depth.</li>
                            <li>The repository is the source of truth.</li>
                            <li>Explain intent, not only implementation.</li>
                            <li>Never make the next session guess.</li>
                            <li>Curated understanding, not automated exhaust.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">See what a living Project Guide looks like.</h2>
                <p>
                    Open the <?= homepage_e($sampleDisplayName) ?> guide.
                    Imagine this growing in your next repo from day one—
                    so months later, you still know what you built.
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
            <p><strong>VibeKB.</strong> Build with AI. Keep the understanding.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo from day one · <a href="<?= homepage_e($guideUrl) ?>">Project Guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="assets/js/homepage.js" defer></script>
</body>
</html>
