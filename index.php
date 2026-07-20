<?php

declare(strict_types=1);

/**
 * VibeKB homepage.
 *
 * Represents the actual V1 product: a living software model organized around
 * functionality. Where possible it renders real records from `.vibekb/` so the
 * homepage demonstrates the product with the product's own content.
 *
 * The guide content layer is reused for loading + the badge vocabularies. We do
 * NOT use guide_url() here (its base is derived for the guide directory);
 * in-app links are written relative to the site root as `guide/...`.
 */

require_once __DIR__ . '/guide/lib/helpers.php';
require_once __DIR__ . '/guide/lib/Content.php';

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

$statusCounts = $loaded ? $content->statusCounts() : [];
$total = array_sum($statusCounts);
$groups = $loaded ? $content->functionalityGroups() : [];
$warnCount = $loaded ? count($content->memoryByType('warnings')) : 0;
$work = $loaded ? $content->currentWork() : null;
$identity = $loaded ? $content->projectDoc('identity') : null;
$sampleName = (string) ($identity['meta']['title'] ?? 'the sample app');

/** Link into a guide view from the homepage (site-root relative). */
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

$views = [
    ['view' => 'overview', 'name' => 'Overview', 'desc' => 'What the software is doing right now, at a glance.'],
    ['view' => 'functionality', 'name' => 'Functionality index', 'desc' => 'Every behaviour, grouped by purpose, with real status.'],
    ['view' => 'functionality', 'name' => 'Functionality detail', 'desc' => 'One behaviour explained end to end — flow, files, data, risks.', 'params' => ['id' => 'create-idea']],
    ['view' => 'how-it-works', 'name' => 'How it works', 'desc' => 'Mental model, components, request and data flows.'],
    ['view' => 'data', 'name' => 'Data & storage', 'desc' => 'What is stored, where, and which functionality touches it.'],
    ['view' => 'files', 'name' => 'Files that matter', 'desc' => 'A curated list of important files, each with a safety level.'],
    ['view' => 'current-work', 'name' => 'Current AI work', 'desc' => 'What AI is changing — before, during, and after.'],
    ['view' => 'changes', 'name' => 'Changes', 'desc' => 'Meaningful behavioural changes and their impact.'],
    ['view' => 'why', 'name' => 'Why it works this way', 'desc' => 'Decisions, constraints, assumptions, warnings.'],
    ['view' => 'handoff', 'name' => 'AI handoff', 'desc' => 'What the next session must know before it edits.'],
    ['view' => 'reference', 'name' => 'Reference', 'desc' => 'The content model and live validation diagnostics.'],
];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — Understand what your software is doing</title>
    <meta name="description" content="VibeKB gives AI-assisted developers a living explanation of their application's current functionality, how it works, what AI is changing, and why. Organized around what your software actually does.">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="top">
    <div class="wrap top__inner">
        <a class="wordmark" href="./">VibeKB</a>
        <nav aria-label="Primary" class="top__nav">
            <a href="#what">What it is</a>
            <a href="#views">The views</a>
            <a href="#how">How it works</a>
            <a class="btn btn--primary" href="<?= h($guideUrl) ?>">Open the guide</a>
        </nav>
    </div>
</header>

<main id="main">

    <!-- Hero -->
    <section class="hero">
        <div class="wrap hero__grid">
            <div class="hero__copy">
                <p class="eyebrow">A living explanation for AI-assisted software</p>
                <h1>Understand what your software is doing.</h1>
                <p class="lede">
                    VibeKB gives AI-assisted developers a living explanation of their
                    application&#39;s current functionality — how it works, what AI is
                    changing, and why. It lives in your repository and is organized
                    around what your software actually does.
                </p>
                <p class="actions">
                    <a class="btn btn--primary btn--lg" href="<?= h($guideUrl) ?>">Explore the V1 guide</a>
                    <a class="btn btn--lg" href="#what">See what it shows</a>
                </p>
                <p class="hero__note">Version 1 — the working product foundation. Runs as plain PHP; no database, no build step, no accounts.</p>
            </div>

            <?php if ($loaded && $total > 0): ?>
            <aside class="model-card" aria-label="Live example: the software model">
                <div class="model-card__head">
                    <span class="model-card__eyebrow">Living software model</span>
                    <span class="model-card__project"><?= h($sampleName) ?></span>
                </div>
                <p class="model-card__line"><strong><?= (int) $total ?></strong> functional areas tracked</p>
                <p class="badge-row">
                    <?php foreach (status_vocabulary() as $k => $label): ?>
                        <?php if (!empty($statusCounts[$k])): ?>
                            <?= badge($label . ' · ' . $statusCounts[$k], status_tone($k)) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </p>
                <ul class="model-card__facts">
                    <?php if ($work !== null): ?>
                        <li><span class="dot dot--info"></span> 1 active AI task: <?= h((string) ($work['meta']['title'] ?? 'in progress')) ?></li>
                    <?php endif; ?>
                    <?php if ($warnCount > 0): ?>
                        <li><span class="dot dot--warn"></span> <?= (int) $warnCount ?> active warning<?= $warnCount === 1 ? '' : 's' ?> recorded</li>
                    <?php endif; ?>
                    <li><span class="dot dot--ok"></span> Intended, implemented &amp; verified kept distinct</li>
                </ul>
                <a class="model-card__link" href="<?= h($guideUrl) ?>">Open this model →</a>
            </aside>
            <?php endif; ?>
        </div>
    </section>

    <!-- The problem, honestly and briefly -->
    <section class="band" id="problem">
        <div class="wrap">
            <p class="kicker">Why it exists</p>
            <h2>AI can change six files faster than you can rebuild your mental model.</h2>
            <p class="section-lede">
                You&#39;re not blocked by writing code anymore. You&#39;re blocked by understanding what the
                agent just built. VibeKB keeps an accurate, current explanation of the software so you
                don&#39;t have to reconstruct it.
            </p>
            <ul class="quote-cards">
                <li>&ldquo;I know the app works, but I don&#39;t understand how.&rdquo;</li>
                <li>&ldquo;Claude changed six files and I don&#39;t know why.&rdquo;</li>
                <li>&ldquo;The AI says it&#39;s done, but I can&#39;t verify it.&rdquo;</li>
            </ul>
        </div>
    </section>

    <!-- What VibeKB is -->
    <section class="band band--surface" id="what">
        <div class="wrap">
            <p class="kicker">What VibeKB is</p>
            <h2>A model of your software, organized around what it does.</h2>
            <p class="section-lede">
                Not a file tree. Not a pile of docs. Not a log of AI chats. The primary unit is
                <strong>functionality</strong> — the things your software does — and everything else
                connects back to it.
            </p>
            <ul class="feature-cards">
                <li>
                    <h3>What it does now</h3>
                    <p>Every behaviour, with its real status: implemented, partial, experimental, planned, deprecated, broken, or unverified.</p>
                </li>
                <li>
                    <h3>How it works</h3>
                    <p>The mental model, components, request and data flows, storage, and deployment — explained at reading speed.</p>
                </li>
                <li>
                    <h3>What AI is changing</h3>
                    <p>The current objective, affected functionality, expected files, and risks — so you can see the change coming.</p>
                </li>
                <li>
                    <h3>Why it works this way</h3>
                    <p>Decisions, constraints, assumptions, and warnings — each connected to the functionality it explains.</p>
                </li>
            </ul>
            <p class="trust-line">
                And it never hides uncertainty: <strong>intended</strong>, <strong>implemented</strong>, and
                <strong>verified</strong> behaviour are kept distinct, with a provenance state on every record.
            </p>
        </div>
    </section>

    <!-- Real functionality showcase -->
    <?php if ($loaded && $groups !== []): ?>
    <section class="band" id="functionality">
        <div class="wrap">
            <p class="kicker">The product, shown with its own content</p>
            <h2>Real functionality, real status.</h2>
            <p class="section-lede">
                These are actual records from the guide&#39;s example project. Each links to a full explanation —
                flow, files, data, dependencies, failure cases, and what&#39;s safe to change.
            </p>
            <?php foreach ($groups as $group): ?>
                <div class="func-group">
                    <h3><?= h($group['title']) ?></h3>
                    <ul class="func-rows">
                        <?php foreach ($group['records'] as $rec): $m = $rec['meta']; ?>
                            <li>
                                <a class="func-rows__name" href="<?= h(hp_guide('functionality', ['id' => (string) $m['id']])) ?>">
                                    <?= h((string) ($m['title'] ?? $m['id'])) ?>
                                </a>
                                <span class="func-rows__badges">
                                    <?= status_badge((string) ($m['status'] ?? 'unknown')) ?>
                                    <?php if (($m['verification'] ?? '') !== ''): ?><?= verification_badge((string) $m['verification']) ?><?php endif; ?>
                                </span>
                                <span class="func-rows__summary"><?= h((string) ($m['summary'] ?? '')) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
            <p class="foot-cta"><a class="btn btn--primary" href="<?= h(hp_guide('functionality')) ?>">Browse all functionality →</a></p>
        </div>
    </section>
    <?php endif; ?>

    <!-- The views -->
    <section class="band band--surface" id="views">
        <div class="wrap">
            <p class="kicker">One model, eleven views</p>
            <h2>Everything is a different lens on the same content.</h2>
            <p class="section-lede">
                The guide reads one repository-owned model and renders it as the views below. Deep links work;
                it runs in a subfolder; it works without JavaScript.
            </p>
            <ul class="view-grid">
                <?php foreach ($views as $v): ?>
                    <li class="view-card">
                        <a href="<?= h(hp_guide($v['view'], $v['params'] ?? [])) ?>">
                            <h3><?= h($v['name']) ?></h3>
                            <p><?= h($v['desc']) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- How it works -->
    <section class="band" id="how">
        <div class="wrap">
            <p class="kicker">How it works</p>
            <h2>Repository-owned content, rendered by plain PHP.</h2>
            <div class="how-grid">
                <div class="how-step">
                    <span class="how-step__num">1</span>
                    <h3>Content lives in <code>.vibekb/</code></h3>
                    <p>Markdown records with front matter plus small JSON manifests — human-readable, AI-editable, versioned with your code. No database.</p>
                </div>
                <div class="how-step">
                    <span class="how-step__num">2</span>
                    <h3>The guide loads &amp; validates it</h3>
                    <p>A small PHP loader resolves the relationships between records and checks the content set — surfacing broken links or bad status values instead of rendering them silently.</p>
                </div>
                <div class="how-step">
                    <span class="how-step__num">3</span>
                    <h3>Your agent keeps it current</h3>
                    <p>Coding agents follow a documented workflow to update the model as they change the software — so the explanation stays accurate as it evolves.</p>
                </div>
            </div>
            <ul class="constraint-list">
                <li>PHP 8.2 on ordinary shared hosting</li>
                <li>Deploys in a subfolder, no rewrite rules</li>
                <li>No database · no external/AI API · no Node · no build step</li>
                <li>Works without JavaScript · mobile-first</li>
            </ul>
        </div>
    </section>

    <!-- Honest V1 -->
    <section class="band band--surface" id="v1">
        <div class="wrap honest">
            <div>
                <p class="kicker">Version 1, honestly</p>
                <h2>What this V1 is — and isn&#39;t.</h2>
            </div>
            <div class="honest__cols">
                <div>
                    <h3>It is</h3>
                    <ul class="tick">
                        <li>A working, functionality-first living software model.</li>
                        <li>A guide with eleven connected views over one content set.</li>
                        <li>Honest about intended vs implemented vs verified.</li>
                        <li>Repository-owned and deployable today.</li>
                    </ul>
                </div>
                <div>
                    <h3>It isn&#39;t (yet)</h3>
                    <ul class="cross">
                        <li>Automatic extraction — records are agent-maintained.</li>
                        <li>A documentation generator, code browser, or chat log.</li>
                        <li>A finished marketing site or polished showcase.</li>
                        <li>Dependent on any account, cloud, or AI service.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="band cta">
        <div class="wrap cta__inner">
            <h2>See what your software is doing.</h2>
            <p>Open the guide and read the example model — then put VibeKB in your own repo and keep it current as you build.</p>
            <p class="actions">
                <a class="btn btn--primary btn--lg" href="<?= h($guideUrl) ?>">Explore the guide</a>
                <a class="btn btn--lg" href="<?= h($repoUrl) ?>" rel="noopener noreferrer">View the repository</a>
            </p>
        </div>
    </section>
</main>

<footer class="foot">
    <div class="wrap">
        <p><strong>VibeKB.</strong> Understand what your software is doing.</p>
        <p class="muted">Version 1 · Lives in your repo (<code>.vibekb/</code>) · <a href="<?= h($guideUrl) ?>">Software guide</a></p>
    </div>
</footer>
</body>
</html>
