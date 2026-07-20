<?php

declare(strict_types=1);

function homepage_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$guideUrl = 'guide/';
$repoUrl = 'https://github.com/cubixmeow-commits/VibeKB';

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
    <title>VibeKB — Understand the project before you touch the code</title>
    <meta name="description" content="VibeKB turns repository knowledge into a guided Project Guide that explains what the software does, how it works, what can break, and what to know before changing it.">
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
                    <p class="hp-eyebrow">VibeKB Project Guides</p>
                    <h1 id="hero-title">Understand the project before you touch the code.</h1>
                    <p class="hp-hero-support">
                        VibeKB turns knowledge stored inside a repository into a guided, visual explanation
                        for developers, collaborators, and future AI agents.
                    </p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">Explore the sample Project Guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#how-it-works">See how VibeKB works</a>
                    </div>
                </div>
                <figure class="hp-hero-visual" aria-labelledby="hero-flow-label">
                    <figcaption id="hero-flow-label" class="visually-hidden">
                        Repository knowledge becomes a Project Guide, which creates shared understanding.
                    </figcaption>
                    <ol class="hp-mini-flow" data-hero-flow>
                        <li class="is-active"><span>Repository knowledge</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Project Guide</span></li>
                        <li aria-hidden="true" class="hp-mini-flow-arrow">↓</li>
                        <li><span>Shared understanding</span></li>
                    </ol>
                </figure>
            </div>
        </section>

        <!-- 2. Problem -->
        <section class="hp-section" id="problem" aria-labelledby="problem-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">The problem</p>
                <h2 id="problem-title">Software is being built faster than it is being understood.</h2>

                <div class="hp-stepper" data-stepper="problem">
                    <div class="hp-stepper-tabs" role="tablist" aria-label="How understanding breaks down">
                        <button type="button" class="hp-step-tab is-active" role="tab" id="prob-tab-0" aria-selected="true" aria-controls="prob-panel-0" data-step="0">1. The project grows</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-1" aria-selected="false" aria-controls="prob-panel-1" data-step="1" tabindex="-1">2. Decisions disappear</button>
                        <button type="button" class="hp-step-tab" role="tab" id="prob-tab-2" aria-selected="false" aria-controls="prob-panel-2" data-step="2" tabindex="-1">3. The next person rebuilds the model</button>
                    </div>
                    <div class="hp-stepper-panels">
                        <div class="hp-step-panel is-active" role="tabpanel" id="prob-panel-0" aria-labelledby="prob-tab-0" data-step-panel="0">
                            <p>Features land quickly. Files multiply. The repo becomes real software before anyone has a durable explanation of how the pieces fit.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-1" aria-labelledby="prob-tab-1" data-step-panel="1" hidden>
                            <p>Important decisions disappear into code, chat transcripts, and expired agent sessions. Intent is scattered; assumptions go unrecorded.</p>
                        </div>
                        <div class="hp-step-panel" role="tabpanel" id="prob-panel-2" aria-labelledby="prob-tab-2" data-step-panel="2" hidden>
                            <p>The next developer—or the next AI agent—has to reconstruct the mental model from scratch, often guessing what is safe to change.</p>
                        </div>
                    </div>
                    <div class="hp-stepper-fallback">
                        <ol>
                            <li><strong>The project grows.</strong> Features land quickly before a durable explanation exists.</li>
                            <li><strong>Decisions disappear.</strong> Intent scatters across code, chats, and expired sessions.</li>
                            <li><strong>The next person rebuilds the model.</strong> Humans and agents guess what is safe to change.</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Why READMEs are not enough</summary>
                    <ul class="hp-plain-list">
                        <li>README files are usually setup-oriented.</li>
                        <li>Source code shows implementation but not always intent.</li>
                        <li>Commit history contains fragments, not a coherent mental model.</li>
                        <li>AI conversations are temporary and disconnected.</li>
                        <li>Architectural assumptions are rarely obvious from filenames.</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 3. Product outcomes -->
        <section class="hp-section hp-surface" id="product" aria-labelledby="product-title">
            <div class="hp-wrap">
                <p class="hp-kicker">The product</p>
                <h2 id="product-title">VibeKB creates a guided Project Guide inside the repository.</h2>
                <p class="hp-lead">Four outcomes—open one at a time.</p>

                <div class="hp-outcome" data-tabs="outcomes">
                    <div class="hp-tablist" role="tablist" aria-label="Project Guide outcomes">
                        <button type="button" class="hp-tab is-active" role="tab" id="out-tab-0" aria-selected="true" aria-controls="out-panel-0" data-tab="0">Understand the product</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-1" aria-selected="false" aria-controls="out-panel-1" data-tab="1" tabindex="-1">Follow the system</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-2" aria-selected="false" aria-controls="out-panel-2" data-tab="2" tabindex="-1">Change it safely</button>
                        <button type="button" class="hp-tab" role="tab" id="out-tab-3" aria-selected="false" aria-controls="out-panel-3" data-tab="3" tabindex="-1">Debug it faster</button>
                    </div>
                    <div class="hp-tabpanels">
                        <div class="hp-tabpanel is-active" role="tabpanel" id="out-panel-0" aria-labelledby="out-tab-0" data-tab-panel="0">
                            <p class="hp-tabpanel-lead">What it does, who it is for, and how someone uses it—before any file paths.</p>
                            <p class="hp-example"><strong>Example:</strong> SaaS Idea Manager is a single-operator place to collect and develop software ideas.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#what-is-this">See this in the sample guide</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-1" aria-labelledby="out-tab-1" data-tab-panel="1" hidden>
                            <p class="hp-tabpanel-lead">How actions, data, and major components connect end to end.</p>
                            <p class="hp-example"><strong>Example:</strong> Form submit → PHP validation → SQLite write → reload → updated list.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#save-flow">See the save path</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-2" aria-labelledby="out-tab-2" data-tab-panel="2" hidden>
                            <p class="hp-tabpanel-lead">Dependencies, assumptions, invariants, risks, and side effects before you edit.</p>
                            <p class="hp-example"><strong>Example:</strong> Adding a field means migration, forms, write path, read path, and production apply—together.</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#change-safely">See change-safety guides</a>
                        </div>
                        <div class="hp-tabpanel" role="tabpanel" id="out-panel-3" aria-labelledby="out-tab-3" data-tab-panel="3" hidden>
                            <p class="hp-tabpanel-lead">Known failure paths and where investigation should begin.</p>
                            <p class="hp-example"><strong>Example:</strong> Blank ideas list → file exists? PHP can read it? Query returns rows? Template displays them?</p>
                            <a class="hp-text-link" href="<?= homepage_e($guideUrl) ?>#problems">See troubleshooting sequences</a>
                        </div>
                    </div>
                    <div class="hp-tabs-fallback">
                        <article>
                            <h3>Understand the product</h3>
                            <p>What it does, who it is for, and how someone uses it.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#what-is-this">Sample: What is this project?</a>
                        </article>
                        <article>
                            <h3>Follow the system</h3>
                            <p>How actions, data, and major components connect.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#save-flow">Sample: What happens when an idea is saved?</a>
                        </article>
                        <article>
                            <h3>Change it safely</h3>
                            <p>Dependencies, assumptions, invariants, risks, and side effects.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#change-safely">Sample: What should I know before changing it?</a>
                        </article>
                        <article>
                            <h3>Debug it faster</h3>
                            <p>Known failure paths and where investigation should begin.</p>
                            <a href="<?= homepage_e($guideUrl) ?>#problems">Sample: Where do problems usually happen?</a>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Interactive sample preview -->
        <section class="hp-section" id="sample" aria-labelledby="sample-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Sample Project Guide</p>
                <h2 id="sample-title">SaaS Idea Manager</h2>
                <p class="hp-lead">
                    Explore how VibeKB explains a PHP and SQLite application—from the user journey through developer risks and deployment mistakes.
                    This is a preview; the complete guide remains separate.
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
                                        <summary>What should a developer know?</summary>
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
                        <a class="hp-btn hp-btn-primary" href="<?= homepage_e($guideUrl) ?>">Open the complete Project Guide</a>
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. Depths -->
        <section class="hp-section hp-surface" id="depths" aria-labelledby="depths-title">
            <div class="hp-wrap">
                <p class="hp-kicker">One source, multiple depths</p>
                <h2 id="depths-title">Simple first. Technical when needed.</h2>
                <p class="hp-lead">Extract once. Explain at the right depth. The same feature—“Saving an idea”—across three levels.</p>

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
                                <li><strong>User journey:</strong> Submit a form and see the idea on the list.</li>
                                <li><strong>Mental model:</strong> Browser → PHP → SQLite → screen.</li>
                                <li><strong>Relationships:</strong> The list shows what the database stored.</li>
                            </ul>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-1" aria-labelledby="depth-tab-1" data-depth-panel="1" hidden>
                            <h3>Work on it</h3>
                            <ul>
                                <li><strong>Responsibilities:</strong> Validate input, write with prepared statements, redirect, reload.</li>
                                <li><strong>Data touched:</strong> Idea title, notes, status, timestamps in SQLite.</li>
                                <li><strong>Invariants:</strong> Read and write paths must stay aligned.</li>
                                <li><strong>Risks / change impact:</strong> New fields need migration, forms, write, read, and production apply.</li>
                            </ul>
                        </div>
                        <div class="hp-depth-panel" role="tabpanel" id="depth-panel-2" aria-labelledby="depth-tab-2" data-depth-panel="2" hidden>
                            <h3>Reference</h3>
                            <ul>
                                <li><strong>Important areas:</strong> Ideas CRUD, database layer, templates.</li>
                                <li><strong>Accepted decisions:</strong> Manual migrations; SQLite; no auth; no uploads.</li>
                                <li><strong>Debugging:</strong> Fields disappear when write/read diverge.</li>
                                <li><strong>Assumptions / history:</strong> One operator; editorial notes in <code>.vibekb/</code>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="hp-depth-fallback">
                        <h3>Understand</h3>
                        <p>Purpose, user journey, mental model, major relationships.</p>
                        <h3>Work on it</h3>
                        <p>Responsibilities, dependencies, data touched, invariants, risks, change impact.</p>
                        <h3>Reference</h3>
                        <p>Important files, accepted decisions, debugging notes, assumptions, history.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. How it works -->
        <section class="hp-section" id="how-it-works" aria-labelledby="how-title">
            <div class="hp-wrap">
                <p class="hp-kicker">How VibeKB works</p>
                <h2 id="how-title">From repository knowledge to shared context.</h2>

                <div class="hp-pipeline" data-pipeline>
                    <div class="hp-pipeline-stages" role="tablist" aria-label="VibeKB pipeline">
                        <button type="button" class="hp-pipe-stage is-active" role="tab" id="pipe-tab-0" aria-selected="true" aria-controls="pipe-panel-0" data-pipe="0">Repository</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-1" aria-selected="false" aria-controls="pipe-panel-1" data-pipe="1" tabindex="-1">Knowledge extraction</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-2" aria-selected="false" aria-controls="pipe-panel-2" data-pipe="2" tabindex="-1">Structured model</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-3" aria-selected="false" aria-controls="pipe-panel-3" data-pipe="3" tabindex="-1">Project Guide</button>
                        <button type="button" class="hp-pipe-stage" role="tab" id="pipe-tab-4" aria-selected="false" aria-controls="pipe-panel-4" data-pipe="4" tabindex="-1">Human &amp; AI context</button>
                    </div>
                    <div class="hp-pipeline-panels">
                        <div class="hp-pipe-panel is-active" role="tabpanel" id="pipe-panel-0" aria-labelledby="pipe-tab-0" data-pipe-panel="0">
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>Source code, decisions, documentation, and project-owned context live in the repository—especially under <code>.vibekb/</code>.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-1" aria-labelledby="pipe-tab-1" data-pipe-panel="1" hidden>
                            <p><span class="hp-badge hp-badge-later">Architecture direction</span></p>
                            <p>Relevant understanding is identified and organized. Version 1 relies on curated knowledge files maintained with the project; deeper automated extraction is a direction, not a current black-box product.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-2" aria-labelledby="pipe-tab-2" data-pipe-panel="2" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>Facts, relationships, risks, intent, and change impact are stored in structured files—separate from how they are presented.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-3" aria-labelledby="pipe-tab-3" data-pipe-panel="3" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>The knowledge becomes a guided, human-readable Project Guide with chapters, scenes, and progressive developer detail.</p>
                        </div>
                        <div class="hp-pipe-panel" role="tabpanel" id="pipe-panel-4" aria-labelledby="pipe-tab-4" data-pipe-panel="4" hidden>
                            <p><span class="hp-badge hp-badge-now">Available in Version 1</span></p>
                            <p>The same knowledge supports onboarding, safer modification, debugging, and future agent sessions—as stable repository-owned context, not a temporary chat.</p>
                        </div>
                    </div>
                    <div class="hp-pipeline-fallback">
                        <ol>
                            <li><strong>Repository</strong> — project-owned knowledge beside the code. <em>Version 1</em></li>
                            <li><strong>Knowledge extraction</strong> — organize what matters. <em>Architecture direction for deeper automation</em></li>
                            <li><strong>Structured model</strong> — facts separate from presentation. <em>Version 1</em></li>
                            <li><strong>Project Guide</strong> — guided human experience. <em>Version 1</em></li>
                            <li><strong>Developer and AI context</strong> — shared mental model for the next session. <em>Version 1</em></li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Version 1 boundaries</summary>
                    <p>
                        Version 1 is a working content system and one fully published sample project.
                        No accounts. No cloud AI APIs. No search product. Proof that a project can explain itself from repository-owned knowledge.
                    </p>
                </details>
            </div>
        </section>

        <!-- 7. Relevance -->
        <section class="hp-section hp-surface" id="relevance" aria-labelledby="relevance-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What belongs in VibeKB?</p>
                <h2 id="relevance-title">Curated understanding, not automated exhaust.</h2>

                <div class="hp-filter" data-relevance>
                    <div class="hp-filter-list" role="tablist" aria-label="Inclusion tests">
                        <button type="button" class="hp-filter-btn is-active" role="tab" id="rel-tab-0" aria-selected="true" aria-controls="rel-panel-0" data-rel="0">Does it improve understanding?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-1" aria-selected="false" aria-controls="rel-panel-1" data-rel="1" tabindex="-1">Needed before modifying?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-2" aria-selected="false" aria-controls="rel-panel-2" data-rel="2" tabindex="-1">Could omitting it cause harm?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-3" aria-selected="false" aria-controls="rel-panel-3" data-rel="3" tabindex="-1">Would it shorten debugging?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-4" aria-selected="false" aria-controls="rel-panel-4" data-rel="4" tabindex="-1">Could it disappear between sessions?</button>
                        <button type="button" class="hp-filter-btn" role="tab" id="rel-tab-5" aria-selected="false" aria-controls="rel-panel-5" data-rel="5" tabindex="-1">Does it explain architectural intent?</button>
                    </div>
                    <div class="hp-filter-panels">
                        <div class="hp-filter-panel is-active" role="tabpanel" id="rel-panel-0" aria-labelledby="rel-tab-0" data-rel-panel="0">
                            <p><strong>Include:</strong> “Ideas are first-class records with status and timestamps.”</p>
                            <p>That changes how someone thinks about the product—not just a folder name.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-1" aria-labelledby="rel-tab-1" data-rel-panel="1" hidden>
                            <p><strong>Include:</strong> “Read and write paths must stay aligned when fields change.”</p>
                            <p>Without it, a developer may update a form and forget the SELECT.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-2" aria-labelledby="rel-tab-2" data-rel-panel="2" hidden>
                            <p><strong>Include:</strong> “A login page alone does not make the app safely multi-user.”</p>
                            <p>Omitting ownership validation risks exposing every idea to every user.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-3" aria-labelledby="rel-tab-3" data-rel-panel="3" hidden>
                            <p><strong>Include:</strong> The blank-list diagnostic order (file → permissions → query → template).</p>
                            <p>It shortens the path from “empty page” to a real cause.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-4" aria-labelledby="rel-tab-4" data-rel-panel="4" hidden>
                            <p><strong>Include:</strong> Why SQLite and manual migrations were chosen.</p>
                            <p>Chat sessions expire; repository knowledge does not.</p>
                        </div>
                        <div class="hp-filter-panel" role="tabpanel" id="rel-panel-5" aria-labelledby="rel-tab-5" data-rel-panel="5" hidden>
                            <p><strong>Include:</strong> “No uploads is intentional—not an unfinished placeholder.”</p>
                            <p>Intent prevents agents from “helpfully” adding unsafe features.</p>
                        </div>
                    </div>
                    <div class="hp-filter-fallback">
                        <ol>
                            <li>Does it improve understanding?</li>
                            <li>Is it needed before modifying the project?</li>
                            <li>Could omitting it cause bugs or data loss?</li>
                            <li>Would it shorten debugging?</li>
                            <li>Could this knowledge disappear between developers or agent sessions?</li>
                            <li>Does it explain architectural intent?</li>
                        </ol>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>What VibeKB intentionally leaves out</summary>
                    <ul class="hp-plain-list">
                        <li>Every file</li>
                        <li>Every function</li>
                        <li>Full line-by-line schemas</li>
                        <li>Obvious implementation details</li>
                        <li>Generic framework behavior</li>
                        <li>Temporary trivia</li>
                        <li>Exhaustive dependency dumps</li>
                    </ul>
                </details>
            </div>
        </section>

        <!-- 8. Developers and AI -->
        <section class="hp-section" id="audience" aria-labelledby="audience-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Who it helps</p>
                <h2 id="audience-title">One shared mental model for humans and agents.</h2>

                <div class="hp-compare" data-compare>
                    <div class="hp-compare-tabs" role="tablist" aria-label="Audience">
                        <button type="button" class="hp-compare-tab is-active" role="tab" id="cmp-tab-0" aria-selected="true" aria-controls="cmp-panel-0" data-cmp="0">For developers</button>
                        <button type="button" class="hp-compare-tab" role="tab" id="cmp-tab-1" aria-selected="false" aria-controls="cmp-panel-1" data-cmp="1" tabindex="-1">For AI agents</button>
                    </div>
                    <div class="hp-compare-panels">
                        <div class="hp-compare-panel is-active" role="tabpanel" id="cmp-panel-0" aria-labelledby="cmp-tab-0" data-cmp-panel="0">
                            <ul class="hp-plain-list">
                                <li>Faster onboarding</li>
                                <li>Safer changes</li>
                                <li>Better debugging starting points</li>
                                <li>Clearer architectural boundaries</li>
                                <li>Less time reconstructing intent</li>
                            </ul>
                        </div>
                        <div class="hp-compare-panel" role="tabpanel" id="cmp-panel-1" aria-labelledby="cmp-tab-1" data-cmp-panel="1" hidden>
                            <ul class="hp-plain-list">
                                <li>Stable repository-owned context</li>
                                <li>Less repeated repository analysis</li>
                                <li>Fewer guessed assumptions</li>
                                <li>Better continuity between sessions</li>
                                <li>More focused change instructions</li>
                            </ul>
                            <p class="hp-note">VibeKB improves context and reduces ambiguity. It does not claim AI output is automatically correct.</p>
                        </div>
                    </div>
                    <div class="hp-compare-fallback">
                        <h3>For developers</h3>
                        <ul>
                            <li>Faster onboarding, safer changes, clearer boundaries, better debugging starts.</li>
                        </ul>
                        <h3>For AI agents</h3>
                        <ul>
                            <li>Stable context, fewer guessed assumptions, better continuity between sessions.</li>
                        </ul>
                        <p>VibeKB improves context; it does not guarantee correct AI output.</p>
                    </div>
                </div>

                <details class="hp-details">
                    <summary>Token efficiency</summary>
                    <p>
                        The expensive work is creating the structured understanding.
                        Once it exists, different views—Understand, Work on it, Reference—can reuse the same source
                        rather than regenerate the entire explanation for every session.
                    </p>
                </details>
            </div>
        </section>

        <!-- 9. Repository architecture -->
        <section class="hp-section hp-surface" id="architecture" aria-labelledby="arch-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Repository-native</p>
                <h2 id="arch-title">The knowledge stays with the project.</h2>
                <p class="hp-lead">Content belongs in repository-owned files. Presentation is separate. The guide versions with the software.</p>

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
                            <p>Root of repository-owned project knowledge. Deploys with the site. Do not exclude it from rsync.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-1" aria-labelledby="repo-tab-1" data-repo-panel="1" hidden>
                            <p>Project identity: name, stack, constraints, audience—shared by the Project Guide and technical reference.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-2" aria-labelledby="repo-tab-2" data-repo-panel="2" hidden>
                            <p>Chapter JSON for the guided presentation. The engine under <code>guide/</code> renders it; content stays here.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-3" aria-labelledby="repo-tab-3" data-repo-panel="3" hidden>
                            <p>Accepted decisions—SQLite, no authentication, no uploads, manual migrations—and why they stand.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-4" aria-labelledby="repo-tab-4" data-repo-panel="4" hidden>
                            <p>Active failure modes: schema drift, multi-user without ownership, losing architectural understanding.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-5" aria-labelledby="repo-tab-5" data-repo-panel="5" hidden>
                            <p>Ordered debugging guides for blank lists, disappearing fields, and migration mismatch after deploy.</p>
                        </div>
                        <div class="hp-repo-panel" role="tabpanel" id="repo-panel-6" aria-labelledby="repo-tab-6" data-repo-panel="6" hidden>
                            <p>Shared vocabulary—idea, manual migration, ownership validation—so humans and agents use the same words.</p>
                        </div>
                    </div>
                    <div class="hp-repo-fallback">
                        <ul>
                            <li><code>.vibekb/</code> — repository-owned knowledge root</li>
                            <li><code>project.json</code> — identity and constraints</li>
                            <li><code>guide/</code> — Project Guide chapters</li>
                            <li><code>decisions/</code> — accepted architectural choices</li>
                            <li><code>risks/</code> — active failure modes</li>
                            <li><code>debugging/</code> — ordered investigation guides</li>
                            <li><code>glossary/</code> — shared vocabulary</li>
                        </ul>
                    </div>
                </div>

                <ul class="hp-plain-list hp-arch-points">
                    <li>The website is one view of the underlying knowledge.</li>
                    <li>Meaningful architecture changes should update the guide in the same change set.</li>
                    <li>Knowledge can be versioned with the software in Git.</li>
                </ul>
            </div>
        </section>

        <!-- 10. Principles -->
        <section class="hp-section" id="principles" aria-labelledby="principles-title">
            <div class="hp-wrap hp-narrow">
                <p class="hp-kicker">Design principles</p>
                <h2 id="principles-title">How VibeKB decides what to say.</h2>

                <div class="hp-manifesto" data-manifesto>
                    <div class="hp-manifesto-stage" aria-live="polite">
                        <p class="hp-manifesto-index">Principle <span data-manifesto-current>1</span> of 7</p>
                        <p class="hp-manifesto-text is-active" data-manifesto-item="0">Curated understanding, not automated exhaust.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="1" hidden>Extract once, explain at the right depth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="2" hidden>The repository is the source of truth.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="3" hidden>Explain intent, not only implementation.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="4" hidden>Simple first, technical when needed.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="5" hidden>Never make the next developer guess.</p>
                        <p class="hp-manifesto-text" data-manifesto-item="6" hidden>The guide should evolve with the project.</p>
                    </div>
                    <div class="hp-manifesto-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-manifesto-prev disabled>Previous</button>
                        <button type="button" class="hp-btn hp-btn-secondary" data-manifesto-next>Next principle</button>
                    </div>
                    <div class="hp-manifesto-fallback">
                        <ol>
                            <li>Curated understanding, not automated exhaust.</li>
                            <li>Extract once, explain at the right depth.</li>
                            <li>The repository is the source of truth.</li>
                            <li>Explain intent, not only implementation.</li>
                            <li>Simple first, technical when needed.</li>
                            <li>Never make the next developer guess.</li>
                            <li>The guide should evolve with the project.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. Final CTA -->
        <section class="hp-section hp-final" id="cta" aria-labelledby="cta-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="cta-title">See the idea working on a real sample project.</h2>
                <p>
                    Open the SaaS Idea Manager Project Guide and move from product understanding to developer detail at your own pace.
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
            <p><strong>VibeKB.</strong> A guided explanation of a software project, from knowledge inside its repository.</p>
            <p class="hp-footer-note">Version 1 · Lives in your repo · <a href="<?= homepage_e($guideUrl) ?>">Project Guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="assets/js/homepage.js" defer></script>
</body>
</html>
