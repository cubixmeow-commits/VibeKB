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
    <title>VibeKB — Your AI built the app. Now understand what you shipped.</title>
    <meta name="description" content="Built something with Cursor, Claude Code, or Copilot that you&#39;re afraid to touch? VibeKB turns the repo into a Project Guide so you can understand what you actually shipped.">
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
                <a href="#how-it-works">How it works</a>
                <a class="hp-nav-cta" href="<?= homepage_e($guideUrl) ?>">Project Guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. Hero -->
        <section class="hp-section hp-hero" id="top" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">For people building with AI coding tools</p>
                    <h1 id="hero-title">Your AI built the app. Now understand what you actually shipped.</h1>
                    <p class="hp-hero-support">
                        Cursor, Claude Code, Windsurf, Copilot—they&#39;re great at generating software.
                        They&#39;re less great at leaving you with a mental model.
                        VibeKB turns the repo into a guided Project Guide: what it does, how it works,
                        what depends on what, where it usually breaks, and what to know before you change anything.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">Open a sample Project Guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#problem">That&#39;s… me</a>
                    </div>
                </div>
                <figure class="hp-hero-visual" aria-labelledby="hero-flow-label">
                    <figcaption id="hero-flow-label" class="visually-hidden">
                        An AI-built repository becomes a Project Guide, which becomes confidence to change the software.
                    </figcaption>
                    <ol class="hp-mini-flow" data-hero-flow>
                        <li class="is-active"><span>AI-built repo</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Project Guide</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>You get it</span></li>
                    </ol>
                </figure>
            </div>
        </section>

        <!-- 2. Problem -->
        <section class="hp-section" id="problem" aria-labelledby="problem-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">The problem</p>
                <h2 id="problem-title">You successfully built software faster than you could understand it.</h2>

                <div class="hp-stepper" data-stepper="problem">
                    <div class="hp-stepper-tabs" role="tablist" aria-label="How understanding falls behind">
                        <button type="button" class="hp-step-tab is-active" role="tab" id="prob-tab-0" aria-selected="true" aria-controls="prob-panel-0" data-step="0">1. It ships fast</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-1" aria-selected="false" aria-controls="prob-panel-1" data-step="1" tabindex="-1">2. The chat evaporates</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-2" aria-selected="false" aria-controls="prob-panel-2" data-step="2" tabindex="-1">3. You&#39;re scared to touch it</button>
                    </div>
                    <div class="hp-stepper-panels">
                        <div class="hp-step-panel is-active" role="tabpanel" id="prob-panel-0" aria-labelledby="prob-tab-0" data-step-panel="0">
                            <p>One late night. A few prompts. Suddenly you have screens, a database, and something that looks suspiciously like a product. It works. You&#39;re proud. Also slightly panicked.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-1" aria-labelledby="prob-tab-1" data-step-panel="1" hidden>
                            <p>The tool that built it explained itself in a conversation you&#39;ll never fully recover. Decisions live in expired sessions. Six weeks later, the repo is real—and the story is gone.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-2" aria-labelledby="prob-tab-2" data-step-panel="2" hidden>
                            <p>Now every change feels like defusing a bomb you assembled yourself. You&#39;re not stuck because you can&#39;t code. You&#39;re stuck because you don&#39;t trust what you shipped.</p>
                        </div>
                    </div>
                    <div class="hp-stepper-fallback">
                        <ol>
                            <li><strong>It ships fast.</strong> Prompts turn into a real app before you have a durable mental model.</li>
                            <li><strong>The chat evaporates.</strong> Decisions disappear into sessions you can&#39;t reopen.</li>
                            <li><strong>You&#39;re scared to touch it.</strong> The software works—and every change feels risky.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why the README (and the chat) aren&#39;t enough</summary>
                    <ul class="hp-plain-list">
                        <li>READMEs are usually about setup, not how the system thinks.</li>
                        <li>The code shows what runs—not why it was shaped that way.</li>
                        <li>Commits are fragments. They rarely narrate the mental model.</li>
                        <li>Agent chats are temporary. They don&#39;t ship with the repo.</li>
                        <li>Filenames rarely admit the assumptions you&#39;re relying on.</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 3. Product outcomes -->
        <section class="hp-section hp-surface" id="product" aria-labelledby="product-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What you get</p>
                <h2 id="product-title">Answers you wish you had before the next prompt.</h2>
                <p class="hp-lead">VibeKB helps you answer the questions that keep you from opening the editor. The Project Guide is where those answers live—pulled from knowledge kept inside the repository.</p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="Questions VibeKB helps answer">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">What does this app do?</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">What happens on Save?</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">What breaks if I change this?</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Where do I start debugging?</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">Plain language first: what it is, who it&#39;s for, how someone uses it—before you drown in files.</p>
                            <p class="hp-example"><strong>In the sample:</strong> one person collecting software ideas, not a multi-tenant mystery box.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#what-is-this">See “What is this project?”</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">Follow the click: form → validation → database → reload → screen. The path you need before you “just add a field.”</p>
                            <p class="hp-example"><strong>In the sample:</strong> PHP checks input, writes SQLite, then shows the saved idea again.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#save-flow">See the save path</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Dependencies, assumptions, and side effects—so a refactor doesn&#39;t become an archaeology dig.</p>
                            <p class="hp-example"><strong>In the sample:</strong> a new field means migration, forms, write path, read path, and production apply—together.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#change-safely">See change-safety guides</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Known failure paths and a starting order—so you&#39;re not guessing which file to open at 1am.</p>
                            <p class="hp-example"><strong>In the sample:</strong> blank list → does the DB file exist? Can PHP read it? Does the query return rows?</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#problems">See troubleshooting sequences</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article>
                            <h3>What does this app do?</h3>
                            <p>Plain-language product understanding before file diving.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#what-is-this">Open “What is this project?”</a>
                        </article>
                        <article>
                            <h3>What happens on Save?</h3>
                            <p>Follow the request path end to end.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#save-flow">Open the save path</a>
                        </article>
                        <article>
                            <h3>What breaks if I change this?</h3>
                            <p>Dependencies, assumptions, and change impact.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#change-safely">Open change-safety guides</a>
                        </article>
                        <article>
                            <h3>Where do I start debugging?</h3>
                            <p>Known failure paths and investigation order.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#problems">Open troubleshooting</a>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Interactive sample preview -->
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Sample Project Guide</p>
                <h2 id="sample-title"><?= homepage_e($sampleDisplayName) ?></h2>
                <p class="hp-lead">
                    Here&#39;s what your project could look like once you actually understand it.
                    Flip through chapters the way you&#39;d open a mysterious weekend build and finally ask: wait—what did we ship?
                    <span class="hp-note" style="display:block;margin-top:0.5rem;">Inside the guide, the real project name is <?= homepage_e($sampleRealName) ?>—a small PHP and SQLite app.</span>
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
                                        <summary>What should I know before I mess with this?</summary>
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
                <p class="hp-kicker">One source, multiple depths</p>
                <h2 id="depths-title">Simple first. Technical when you&#39;re ready.</h2>
                <p class="hp-lead">Same fact, three depths—so you&#39;re not force-fed a schema dump when you only wanted “what happens when I click Save?”</p>

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
                        <p>Purpose, journey, mental model—enough to stop panicking.</p>
                        <h3>Work on it</h3>
                        <p>Responsibilities, data, invariants, risks—enough to edit safely.</p>
                        <h3>Reference</h3>
                        <p>Decisions, debugging notes, assumptions, history—when you need the receipts.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. How it works -->
        <section class="hp-section" id="how-it-works" aria-labelledby="how-title">
            <div class="hp-wrap">
                <p class="hp-kicker">How VibeKB works</p>
                <h2 id="how-title">Understanding that ships with the repo—not another chat you&#39;ll lose.</h2>

                <div class="hp-pipeline" data-pipeline>
                    <div class="hp-pipeline-stages" role="tablist" aria-label="VibeKB pipeline">
                        <button type="button" class="hp-pipe-stage is-active" role="tab" id="pipe-tab-0" aria-selected="true" aria-controls="pipe-panel-0" data-pipe="0">Your repo</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-1" aria-selected="false" aria-controls="pipe-panel-1" data-pipe="1" tabindex="-1">What matters</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-2" aria-selected="false" aria-controls="pipe-panel-2" data-pipe="2" tabindex="-1">Structured knowledge</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-3" aria-selected="false" aria-controls="pipe-panel-3" data-pipe="3" tabindex="-1">Project Guide</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-4" aria-selected="false" aria-controls="pipe-panel-4" data-pipe="4" tabindex="-1">You + the next session</button>
                    </div>
                    <div class="hp-pipeline-panels">
                        <div class="hp-pipe-panel is-active" role="tabpanel" id="pipe-panel-0" aria-labelledby="pipe-tab-0" data-pipe-panel="0">
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>The software you already built—and the project knowledge beside it under <code>.vibekb/</code>. Same repo. Same Git history.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-1" aria-labelledby="pipe-tab-1" data-pipe-panel="1" hidden>
                            <p><span class="hp-badge hp-badge-later">Architecture direction</span></p>
                            <p>Figure out what&#39;s worth remembering. Version 1 is curated knowledge files you maintain with the project. Deeper automatic extraction is a direction—not a magic black box we pretend exists today.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-2" aria-labelledby="pipe-tab-2" data-pipe-panel="2" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>Facts, risks, intent, and change impact live in structured files—separate from how they&#39;re shown on screen.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-3" aria-labelledby="pipe-tab-3" data-pipe-panel="3" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>That knowledge becomes a guided Project Guide: chapters, scenes, and developer detail when you ask for it.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-4" aria-labelledby="pipe-tab-4" data-pipe-panel="4" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>You get a mental model you can trust. The next coding session—human or agent—starts from the repo, not from amnesia.</p>
                        </div>
                    </div>
                    <div class="hp-pipeline-fallback">
                        <ol>
                            <li><strong>Your repo</strong> — code plus project knowledge in Git. <em>Version 1</em></li>
                            <li><strong>What matters</strong> — curated now; deeper extraction later. <em>Architecture direction</em></li>
                            <li><strong>Structured knowledge</strong> — facts separate from presentation. <em>Version 1</em></li>
                            <li><strong>Project Guide</strong> — guided explanation you can actually read. <em>Version 1</em></li>
                            <li><strong>You + the next session</strong> — continuity instead of re-guessing. <em>Version 1</em></li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Version 1, honestly</summary>
                    <p>
                        One working content system. One fully published sample.
                        No accounts. No cloud AI APIs. No search product.
                        Just proof that a project can explain itself from knowledge that lives in the repo.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. Relevance -->
        <section class="hp-section hp-surface" id="relevance" aria-labelledby="relevance-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What belongs in VibeKB?</p>
                <h2 id="relevance-title">The stuff you&#39;d beg past-you to write down.</h2>
                <p class="hp-lead">Not every file. Not every function. The facts that stop you from breaking your own app.</p>

                <div class="hp-filter" data-relevance>
                    <div class="hp-filter-list" role="tablist" aria-label="Inclusion tests">
                        <button type="button" class="hp-filter-btn is-active" role="tab" id="rel-tab-0" aria-selected="true" aria-controls="rel-panel-0" data-rel="0">Would this help me understand it?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-1" aria-selected="false" aria-controls="rel-panel-1" data-rel="1" tabindex="-1">Do I need this before I edit?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-2" aria-selected="false" aria-controls="rel-panel-2" data-rel="2" tabindex="-1">Could skipping it wreck data?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-3" aria-selected="false" aria-controls="rel-panel-3" data-rel="3" tabindex="-1">Would this shorten a 2am debug?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-4" aria-selected="false" aria-controls="rel-panel-4" data-rel="4" tabindex="-1">Will the next chat forget this?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-5" aria-selected="false" aria-controls="rel-panel-5" data-rel="5" tabindex="-1">Does this explain why it&#39;s shaped this way?</button>
                    </div>
                    <div class="hp-filter-panels">
                        <div class="hp-filter-panel is-active" role="tabpanel" id="rel-panel-0" aria-labelledby="rel-tab-0" data-rel-panel="0">
                            <p><strong>Keep:</strong> “Ideas are first-class records with status and timestamps.”</p>
                            <p>That&#39;s a product truth—not a folder tour.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-1" aria-labelledby="rel-tab-1" data-rel-panel="1" hidden>
                            <p><strong>Keep:</strong> “Read and write paths must stay aligned when fields change.”</p>
                            <p>Otherwise you update the form, hit Save, and watch the field ghost itself on reload.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-2" aria-labelledby="rel-tab-2" data-rel-panel="2" hidden>
                            <p><strong>Keep:</strong> “A login page alone does not make this safely multi-user.”</p>
                            <p>Half-shipping auth is how you invent a data leak and call it a feature.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-3" aria-labelledby="rel-tab-3" data-rel-panel="3" hidden>
                            <p><strong>Keep:</strong> Blank-list order: file → permissions → query → template.</p>
                            <p>Beats randomly rewriting the homepage at midnight.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-4" aria-labelledby="rel-tab-4" data-rel-panel="4" hidden>
                            <p><strong>Keep:</strong> Why SQLite and manual migrations were chosen.</p>
                            <p>Chats forget. The repo can remember—if you put it there.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-5" aria-labelledby="rel-tab-5" data-rel-panel="5" hidden>
                            <p><strong>Keep:</strong> “No uploads is intentional—not unfinished.”</p>
                            <p>So the next agent doesn&#39;t “helpfully” invent a storage subsystem for you.</p>
                        </div>
                    </div>
                    <div class="hp-filter-fallback">
                        <ol>
                            <li>Would this help me understand the project?</li>
                            <li>Do I need this before I edit?</li>
                            <li>Could skipping it cause bugs or data loss?</li>
                            <li>Would this shorten debugging?</li>
                            <li>Will the next chat forget this?</li>
                            <li>Does this explain architectural intent?</li>
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
                        <li>Temporary trivia from last Tuesday&#39;s prompt</li>
                        <li>Exhaustive dependency dumps</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 8. Scenarios -->
        <section class="hp-section" id="audience" aria-labelledby="audience-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Familiar situations</p>
                <h2 id="audience-title">You built it. Now you have to live with it.</h2>

                <div class="hp-compare" data-compare>
                    <div class="hp-compare-tabs" role="tablist" aria-label="Familiar situations">
                        <button type="button" class="hp-compare-tab is-active" role="tab" id="cmp-tab-0" aria-selected="true" aria-controls="cmp-panel-0" data-cmp="0">I inherited my own code</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-1" aria-selected="false" aria-controls="cmp-panel-1" data-cmp="1" tabindex="-1">I&#39;m scared to refactor</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-2" aria-selected="false" aria-controls="cmp-panel-2" data-cmp="2" tabindex="-1">I&#39;m handing this to another AI</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-3" aria-selected="false" aria-controls="cmp-panel-3" data-cmp="3" tabindex="-1">I&#39;m handing this to a human</button>
                    </div>
                    <div class="hp-compare-panels">
                        <div class="hp-compare-panel is-active" role="tabpanel" id="cmp-panel-0" aria-labelledby="cmp-tab-0" data-cmp-panel="0">
                            <p class="hp-tabpanel-lead">Understand what already exists—without replaying every chat that created it.</p>
                            <ul class="hp-plain-list">
                                <li>See the product story before the file tree</li>
                                <li>Recover decisions that only lived in prompts</li>
                                <li>Stop reverse-engineering your own weekend</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-1" aria-labelledby="cmp-tab-1" data-cmp-panel="1" hidden>
                            <p class="hp-tabpanel-lead">See dependencies before you change anything.</p>
                            <ul class="hp-plain-list">
                                <li>Change-safety checklists for fields, auth, uploads, engines</li>
                                <li>Invariants called out in plain language</li>
                                <li>Risks that show up before the breakage does</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-2" aria-labelledby="cmp-tab-2" data-cmp-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Give the next session real project context—not vibes and a prayer.</p>
                            <ul class="hp-plain-list">
                                <li>Stable, repository-owned context</li>
                                <li>Fewer guessed assumptions</li>
                                <li>More focused change instructions</li>
                            </ul>
                            <p class="hp-note">Better context reduces ambiguity. It doesn&#39;t magically make every generated change correct.</p>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-3" aria-labelledby="cmp-tab-3" data-cmp-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Preserve the mental model instead of forcing someone to archaeology your repo.</p>
                            <ul class="hp-plain-list">
                                <li>Faster onboarding</li>
                                <li>Clearer boundaries</li>
                                <li>Less “wait, why is it like this?”</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-compare-fallback">
                        <h3>I inherited my own code</h3>
                        <p>Understand what already exists without replaying every chat.</p>
                        <h3>I&#39;m scared to refactor</h3>
                        <p>See dependencies and change impact first.</p>
                        <h3>I&#39;m handing this to another AI</h3>
                        <p>Give it repository-owned context instead of amnesia.</p>
                        <h3>I&#39;m handing this to a human</h3>
                        <p>Preserve the mental model so they don&#39;t reverse-engineer everything.</p>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why this is cheaper than re-explaining the project every time</summary>
                    <p>
                        The expensive work is creating the structured understanding once.
                        After that, Understand / Work on it / Reference views reuse the same source—
                        instead of regenerating the whole story in every new session.
                    </p>
                </details>
            </div>
        </section>

        <!-- 9. Repository architecture -->
        <section class="hp-section hp-surface" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Memory</p>
                <h2 id="arch-title">Tools remember conversations. Repositories should remember projects.</h2>
                <p class="hp-lead">VibeKB keeps understanding next to the code—so the story survives the chat that wrote it.</p>

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
                            <p>Where project memory lives. Deploys with the site. Do not exclude it from rsync—or from your brain&#39;s backup plan.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-1" aria-labelledby="repo-tab-1" data-repo-panel="1" hidden>
                            <p>Identity card: name, stack, constraints, audience. Shared by the Guide and the deeper reference.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-2" aria-labelledby="repo-tab-2" data-repo-panel="2" hidden>
                            <p>Chapter JSON for the guided tour. Presentation code lives under <code>guide/</code>; the story stays here.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-3" aria-labelledby="repo-tab-3" data-repo-panel="3" hidden>
                            <p>Choices you made on purpose—SQLite, no auth, no uploads, manual migrations—and why you shouldn&#39;t casually undo them.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-4" aria-labelledby="repo-tab-4" data-repo-panel="4" hidden>
                            <p>The failure modes that bite vibe-built apps: schema drift, half-shipped auth, losing the plot as features pile up.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-5" aria-labelledby="repo-tab-5" data-repo-panel="5" hidden>
                            <p>Ordered “start here” guides for blank lists, vanishing fields, and “worked locally, died after deploy.”</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-6" aria-labelledby="repo-tab-6" data-repo-panel="6" hidden>
                            <p>Shared words—idea, manual migration, ownership validation—so you and the next session mean the same thing.</p>
                        </div>
                    </div>
                    <div class="hp-repo-fallback">
                        <ul>
                            <li><code>.vibekb/</code> — project memory root</li>
                            <li><code>project.json</code> — identity and constraints</li>
                            <li><code>guide/</code> — Project Guide chapters</li>
                            <li><code>decisions/</code> — choices and reasons</li>
                            <li><code>risks/</code> — what usually bites</li>
                            <li><code>debugging/</code> — where to look first</li>
                            <li><code>glossary/</code> — shared vocabulary</li>
                        </ul>
                    </div>
                </div>

                <ul class="hp-plain-list hp-arch-points">
                    <li>The website is one view of that memory—not the memory itself.</li>
                    <li>When the architecture changes, update the guide in the same change set.</li>
                    <li>If it&#39;s in Git with the software, the next you can find it.</li>
                </ul>
            </div>
        </section>

        <!-- 10. Principles -->
        <section class="hp-section" id="principles" aria-labelledby="principles-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Principles</p>
                <h2 id="principles-title">Rules for not gaslighting your future self.</h2>

                <div class="hp-manifesto" data-manifesto>
                    <div class="hp-manifesto-stage" aria-live="polite">
                        <p class="hp-manifesto-index">Principle <span data-manifesto-current>1</span> of 7</p>
                        <p class="hp-manifesto-text is-active" data-manifesto-item="0">Curated understanding, not automated exhaust.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="1" hidden>Extract once. Explain at the right depth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="2" hidden>The repository is the source of truth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="3" hidden>Explain intent, not only implementation.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="4" hidden>Simple first. Technical when you&#39;re ready.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="5" hidden>Never make the next you guess.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="6" hidden>The guide should evolve with the project.</p>
                    </div>
                    <div class="hp-manifesto-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-manifesto-prev disabled>Previous</button>
                        <button type="button" class="hp-btn hp-btn-secondary" data-manifesto-next>Next principle</button>
                    </div>
                    <div class="hp-manifesto-fallback">
                        <ol>
                            <li>Curated understanding, not automated exhaust.</li>
                            <li>Extract once. Explain at the right depth.</li>
                            <li>The repository is the source of truth.</li>
                            <li>Explain intent, not only implementation.</li>
                            <li>Simple first. Technical when you&#39;re ready.</li>
                            <li>Never make the next you guess.</li>
                            <li>The guide should evolve with the project.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">See what understanding looks like on a real weekend build.</h2>
                <p>
                    Open the <?= homepage_e($sampleDisplayName) ?> Project Guide.
                    Start with what the app does. Dig into risks when you&#39;re ready.
                    Imagine this existed for the project you&#39;re afraid to open.
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
            <p><strong>VibeKB.</strong> Understand the software you shipped before you break it.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo · <a href="<?= homepage_e($guideUrl) ?>">Project Guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="assets/js/homepage.js" defer></script>
</body>
</html>
