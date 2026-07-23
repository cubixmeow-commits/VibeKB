<?php

declare(strict_types=1);

/**
 * VibeKB homepage — hero/problem, install fast-start, what you get, live proof + CTA.
 * Copy is distilled from the developer-journey story (ship fast → lose understanding
 * → fear change → VibeKB restores clarity). Section 1 includes an optimized hero comic
 * beside the copy. The install section mirrors the real installer workflow (clone →
 * install.php → coding agent builds the model). Compatibility & Requirements sits
 * under install. The guide-preview carousel and hero metrics are driven by real
 * `.vibekb/` records (never invented).
 *
 * Interactions: assets/js/homepage.js (guide carousel + copy buttons). Styling: homepage.css.
 */

require_once __DIR__ . '/guide/lib/helpers.php';
require_once __DIR__ . '/guide/lib/Content.php';

if (!headers_sent()) {
    header('Cache-Control: no-cache, must-revalidate');
}

function hp_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function hp_asset(string $rel): string
{
    $rel = ltrim($rel, '/');
    $fsPath = __DIR__ . '/' . $rel;
    $version = is_file($fsPath) ? (string) filemtime($fsPath) : '1';
    return $rel . '?v=' . $version;
}

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

function hp_flow_steps(string $body): array
{
    if (!preg_match('/^##\s*Step-by-step flow\s*$(.*?)(?=^##\s|\z)/ms', $body, $m)) {
        return [];
    }
    $steps = [];
    foreach (preg_split('/\r?\n/', $m[1]) as $line) {
        if (preg_match('/^\s*\d+\.\s+(.*)$/', $line, $lm)) {
            $step = str_replace(['`', '**'], '', trim($lm[1]));
            $steps[] = $step;
        }
    }
    return $steps;
}

function hp_status_tone(string $status): string
{
    $tone = status_tone($status);
    return $tone === 'unknown' ? 'muted' : $tone;
}

$guideUrl = 'guide/';
$repoUrl = 'https://github.com/cubixmeow-commits/VibeKB';
$installerGuideUrl = $repoUrl . '/blob/main/INSTALLER.md';
$codingAgents = 'Cursor, Claude Code, Codex, Windsurf, Copilot, and others';
$namedAgents = ['Cursor', 'Claude Code', 'Codex', 'Windsurf'];
$stackBadges = [
    'PHP', 'Laravel', 'WordPress', 'Node.js', 'React', 'Vue', 'Next.js', 'Angular',
    'Python', 'Django', 'Flask', 'FastAPI', 'Go', 'Rust', 'Java', 'Kotlin',
    'C#', 'Swift', 'Ruby', 'C++',
];
$cloneCmd = 'git clone https://github.com/cubixmeow-commits/VibeKB.git';
$installCmd = 'php VibeKB/install.php /path/to/your/project';
$installExampleCmd = 'php VibeKB/install.php ~/Projects/my-app';
$dryRunCmd = 'php VibeKB/install.php --dry-run /path/to/your/project';
$agentPrompt = "Build the first VibeKB model for this repository using prompts/INTEGRATE_VIBEKB.md.\n"
    . "Inspect the real source code, do not modify the application while initializing VibeKB, "
    . "distinguish implemented behaviour from inferred or unverified behaviour, run all VibeKB checks, "
    . "and generate the guide when complete.";
$noInfraItems = [
    'No database',
    'No AI API',
    'No vector database',
    'No embeddings',
    'No Docker',
    'No background services',
    'No cloud account',
    'No browser extension',
    'No subscription',
    'No telemetry',
];
$comingSoonItems = [
    'Native CLI',
    'Automatic upgrades',
    'Repository doctor',
    'Additional coding agent integrations',
];

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
$sampleTagline = (string) ($identity['meta']['one_liner'] ?? $identity['meta']['summary'] ?? '');
if ($sampleTagline === '') {
    $sampleTagline = 'A living software model that explains what your application is currently doing — organized around functionality.';
}
$sampleRepo = (string) ($example['source_repository'] ?? $provenance['source_repository'] ?? $repoUrl);

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
    <title>VibeKB — AI helped you build it. VibeKB helps you understand it.</title>
    <meta name="description" content="Whether you built your own app with <?= hp_e($codingAgents) ?> or extended something from GitHub — you probably shipped faster than you understood. VibeKB is the missing understanding layer in your repo.">
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
                <a href="#problem">The problem</a>
                <a href="#install">Install</a>
                <a href="#compatibility">Compatibility</a>
                <a href="#understanding">What you get</a>
                <a href="#proof">See it work</a>
                <a class="hp-nav-cta" href="<?= hp_e($guideUrl) ?>">Open the guide</a>
            </nav>
        </div>
    </header>

    <main id="main">

        <!-- 1. The problem — recognition + promise -->
        <section class="hp-section hp-hero" id="problem" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">For vibe coders who ship with AI</p>
                    <h1 id="hero-title">AI helped you build it. VibeKB helps you understand it.</h1>
                    <p class="hp-hero-support">
                        Whether you greenfielded your own app or built on open source —
                        <?= hp_e($codingAgents) ?> let you ship faster than your mental model can keep up.
                        The demo runs. The feature looks done. Then you realise you are guessing which files
                        matter and afraid the next edit breaks something three features away.
                    </p>

                    <ol class="hp-arc" aria-label="The developer journey in three beats">
                        <li>
                            <strong>Ship fast.</strong>
                            Describe it, accept the diffs, iterate in chat — your software exists.
                        </li>
                        <li>
                            <strong>Lose the plot.</strong>
                            Prompts, starter templates, agent sessions — the codebase outgrew what you can explain.
                        </li>
                        <li>
                            <strong>Fear the next change.</strong>
                            Not documentation debt. Uncertainty — re-asking the same architecture questions every session.
                        </li>
                    </ol>

                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Open the live guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#understanding">What VibeKB gives you</a>
                    </div>
                </div>
                <div class="hp-hero-aside">
                    <figure class="hp-hero-visual">
                        <picture>
                            <source
                                type="image/webp"
                                srcset="<?= hp_e(hp_asset('assets/images/homepage-developer-journey.webp')) ?>">
                            <img
                                src="<?= hp_e(hp_asset('assets/images/homepage-developer-journey.png')) ?>"
                                alt="Comic journey from shipping fast with AI, losing the plot in a growing codebase, fearing the next change, to clarity with VibeKB"
                                width="560"
                                height="449"
                                decoding="async">
                        </picture>
                    </figure>
                <?php if ($loaded): ?>
                <aside class="hp-example-card" aria-label="<?= $selfHosted ? 'Live model of this project' : 'Live software example' ?>">
                    <p class="hp-example-label"><?= $selfHosted ? 'Live model of this project' : 'Live software example' ?></p>
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
                    <p class="hp-example-note"><?php if ($selfHosted): ?>
                        Real numbers from VibeKB&#39;s own <code>.vibekb/</code> model — not marketing copy.
                    <?php else: ?>
                        Real numbers from the <?= hp_e($sampleName) ?> <code>.vibekb/</code> model.
                    <?php endif; ?></p>
                </aside>
                <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 2. Install — three-step fast-start (clone → install → coding agent) -->
        <section class="hp-section hp-install" id="install" aria-labelledby="install-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Install VibeKB in three steps</p>
                <h2 id="install-title">Add VibeKB to your repository</h2>
                <p class="hp-lead hp-install-lead">
                    Install the understanding layer, let your coding agent build the first model from your
                    source code, then keep it updated as you build.
                </p>

                <ol class="hp-install-cards" aria-label="Install VibeKB in three steps">
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">1</p>
                        <h3>Clone VibeKB</h3>
                        <p class="hp-install-card-copy">Download the installer and VibeKB runtime.</p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd" id="cmd-clone"><code><?= hp_e($cloneCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-clone">Copy</button>
                        </div>
                    </li>
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">2</p>
                        <h3>Install it into your project</h3>
                        <p class="hp-install-card-copy">Replace the path with the repository you want VibeKB to understand.</p>
                        <p class="hp-install-req"><span>Requires PHP 8.2+</span></p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd" id="cmd-install"><code><?= hp_e($installCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-install">Copy</button>
                        </div>
                        <p class="hp-install-example-label">Example</p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd hp-cmd--example" id="cmd-install-example"><code><?= hp_e($installExampleCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-install-example">Copy</button>
                        </div>
                    </li>
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">3</p>
                        <h3>Ask your coding agent</h3>
                        <p class="hp-install-card-copy">
                            The installer prepares the workspace. Open the project in Cursor, Claude Code,
                            Codex, Windsurf, or another coding agent — then paste this prompt so it builds
                            the first understanding model.
                        </p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd hp-cmd--prompt" id="cmd-agent-prompt"><code><?= hp_e($agentPrompt) ?></code></pre>
                            <button type="button" class="hp-copy-btn hp-copy-btn--prompt" data-copy-target="#cmd-agent-prompt">Copy agent prompt</button>
                        </div>
                    </li>
                </ol>

                <div class="hp-install-result" aria-label="What your project contains after install">
                    <p class="hp-install-result-title">Your project now contains:</p>
                    <ul class="hp-install-result-list">
                        <li>
                            <code>.vibekb/</code>
                            <span>Living understanding of your application</span>
                            <span class="hp-install-result-note">Fresh empty-but-valid workspace from the installer</span>
                        </li>
                        <li>
                            <code>guide/</code>
                            <span>Dynamic software guide</span>
                            <span class="hp-install-result-note">Installed runtime</span>
                        </li>
                        <li>
                            <code>tools/</code>
                            <span>Validation, drift detection, and generation</span>
                            <span class="hp-install-result-note">Installed runtime</span>
                        </li>
                        <li>
                            <code>docs/</code>
                            <span>Static guide generated after analysis</span>
                            <span class="hp-install-result-note">Generated after the first model is built</span>
                        </li>
                    </ul>
                    <p class="hp-install-result-gen">
                        <code>docs/</code> is created later with
                        <code>php tools/vibekb.php generate</code> — not by the installer.
                    </p>
                </div>

                <details class="hp-install-details">
                    <summary>What does the installer do?</summary>
                    <div class="hp-install-details-body">
                        <ol>
                            <li>Copies the VibeKB runtime and agent instructions into the target repository.</li>
                            <li>Creates a fresh <code>.vibekb/</code> workspace without inventing functionality.</li>
                            <li>Preserves application code and does not analyze or modify it.</li>
                            <li>Verifies the installation and points your coding agent to the integration prompt.</li>
                        </ol>
                        <p>
                            The installer-owned payload is declared by <code>template/manifest.json</code> and
                            currently includes <code>guide/</code>, <code>tools/</code>, <code>prompts/</code>,
                            <code>.cursor/</code>, <code>CLAUDE.md</code>, <code>AGENTS.md</code>,
                            <code>PRODUCT.md</code>, <code>SCHEMA.md</code>, <code>INITIALIZE.md</code>,
                            <code>MAINTENANCE.md</code>, and <code>INSTALLER.md</code>.
                        </p>
                        <p class="hp-install-preview-label">Preview first (optional)</p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd" id="cmd-dry-run"><code><?= hp_e($dryRunCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-dry-run">Copy</button>
                        </div>
                        <p class="hp-install-card-copy">
                            Preview every file the installer will create, replace, or skip without changing anything.
                        </p>
                    </div>
                </details>

                <p class="hp-thesis hp-install-boundary">
                    The installer prepares VibeKB. Your coding agent understands the application.
                </p>

                <div class="hp-actions hp-install-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= hp_e($repoUrl) ?>" rel="noopener noreferrer">View on GitHub</a>
                    <a class="hp-btn hp-btn-ghost" href="<?= hp_e($installerGuideUrl) ?>" rel="noopener noreferrer">Read the full installer guide</a>
                </div>
            </div>
        </section>

        <!-- 2b. Compatibility & Requirements — will this work with my stack? -->
        <section class="hp-section hp-compat" id="compatibility" aria-labelledby="compatibility-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Will this work with my project?</p>
                <h2 id="compatibility-title">Compatibility &amp; Requirements</h2>
                <p class="hp-lead hp-compat-lead">
                    VibeKB runs almost anywhere. The installer prepares the workspace, and your coding agent
                    builds the understanding model from your source code.
                </p>

                <div class="hp-compat-grid">
                    <article class="hp-compat-card">
                        <h3>Install Requirements</h3>
                        <ul class="hp-compat-checks">
                            <li>PHP 8.2+</li>
                            <li>Read/write access to the repository</li>
                            <li>Git repository recommended</li>
                        </ul>
                        <ul class="hp-compat-none" aria-label="Not required">
                            <li>No Composer</li>
                            <li>No Node</li>
                            <li>No npm</li>
                            <li>No Python</li>
                            <li>No database</li>
                            <li>No internet after cloning</li>
                        </ul>
                    </article>

                    <article class="hp-compat-card">
                        <h3>Works With</h3>
                        <p class="hp-compat-card-copy">
                            VibeKB stores understanding. Your coding agent interprets the source code —
                            VibeKB does not parse these languages itself.
                        </p>
                        <ul class="hp-badge-grid" aria-label="Example stacks your agent can model">
                            <?php foreach ($stackBadges as $badge): ?>
                                <li><span class="hp-badge"><?= hp_e($badge) ?></span></li>
                            <?php endforeach; ?>
                            <li><span class="hp-badge hp-badge--more">and more…</span></li>
                        </ul>
                    </article>

                    <article class="hp-compat-card">
                        <h3>Supported AI Coding Agents</h3>
                        <ul class="hp-badge-grid hp-badge-grid--agents" aria-label="Named coding agents">
                            <?php foreach ($namedAgents as $agent): ?>
                                <li><span class="hp-badge hp-badge--agent"><?= hp_e($agent) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="hp-compat-card-copy">
                            Any coding agent capable of understanding a repository and following repository
                            instructions. Agent behaviour depends on the tool and the prompt — VibeKB does not
                            claim end-to-end certification for every agent.
                        </p>
                    </article>

                    <article class="hp-compat-card">
                        <h3>Deployment</h3>
                        <div class="hp-compat-deploy">
                            <div>
                                <h4>Dynamic Guide</h4>
                                <ul class="hp-compat-checks hp-compat-checks--tight">
                                    <li>PHP Hosting</li>
                                    <li>Apache</li>
                                    <li>Nginx</li>
                                    <li>cPanel</li>
                                    <li>Local PHP Server</li>
                                </ul>
                            </div>
                            <div>
                                <h4>Static Guide</h4>
                                <ul class="hp-compat-checks hp-compat-checks--tight">
                                    <li>GitHub Pages</li>
                                    <li>Netlify</li>
                                    <li>Cloudflare Pages</li>
                                    <li>Any Static Host</li>
                                </ul>
                            </div>
                        </div>
                        <p class="hp-compat-card-note">
                            The static guide is generated by
                            <code>php tools/vibekb.php generate</code>
                        </p>
                    </article>
                </div>

                <div class="hp-compat-noneed">
                    <h3 class="hp-compat-subhead">No Extra Infrastructure Required</h3>
                    <ul class="hp-compat-noneed-grid">
                        <?php foreach ($noInfraItems as $item): ?>
                            <li><?= hp_e($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="hp-compat-limits">
                    <h3 class="hp-compat-subhead">Current Requirements</h3>
                    <ul class="hp-compat-checks">
                        <li>PHP 8.2+</li>
                        <li>A supported AI coding agent</li>
                        <li>A developer to initialize the first model</li>
                    </ul>
                    <p class="hp-compat-card-copy">
                        The installer prepares the workspace. The coding agent builds and maintains the
                        software understanding model. VibeKB does not automatically analyze repositories
                        during installation.
                    </p>
                </div>

                <div class="hp-compat-soon">
                    <p class="hp-compat-soon-label">Coming soon</p>
                    <ul class="hp-badge-grid hp-badge-grid--soon" aria-label="Future improvements, not yet available">
                        <?php foreach ($comingSoonItems as $item): ?>
                            <li><span class="hp-badge hp-badge--soon"><?= hp_e($item) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="hp-compat-card-note">Roadmap ideas — not implemented features.</p>
                </div>
            </div>
        </section>

        <!-- 3. What you get — the answer, distilled -->
        <section class="hp-section hp-surface" id="understanding" aria-labelledby="understanding-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What VibeKB is</p>
                <h2 id="understanding-title">The understanding layer your repository is missing.</h2>
                <p class="hp-lead">
                    Not another coding agent. Not documentation you write once and forget. A living model in
                    <code>.vibekb/</code> — committed with your code — that explains what your software is
                    doing <em>right now</em>: architecture, relationships, key files, active AI work, and what
                    is actually verified vs still guessing.
                </p>

                <div class="hp-pillars">
                    <article>
                        <h3>What it does now</h3>
                        <p>Every behaviour with an honest status — implemented, partial, broken, or unknown.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('functionality')) ?>">Functionality</a>
                    </article>
                    <article>
                        <h3>How it works</h3>
                        <p>Readable flows, files, data, and what breaks if you change something.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('how-it-works')) ?>">How it works</a>
                    </article>
                    <article>
                        <h3>What AI is changing</h3>
                        <p>The current objective, affected functionality, risks, and verification plan.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('current-work')) ?>">Current AI work</a>
                    </article>
                    <article>
                        <h3>Why it works this way</h3>
                        <p>Decisions, constraints, warnings — tied to the functionality they explain.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('why')) ?>">Repository memory</a>
                    </article>
                </div>

                <p class="hp-thesis">
                    Open the guide before you edit. Know what you can safely change. Ship without guessing.
                </p>
            </div>
        </section>

        <!-- 4. Live proof + CTA -->
        <?php if ($previewItems !== []): ?>
        <section class="hp-section" id="proof" aria-labelledby="proof-title">
            <div class="hp-wrap">
                <p class="hp-kicker">See it on a real project</p>
                <h2 id="proof-title">Real functionality records — not a product tour.</h2>
                <p class="hp-lead">
                    <?php if ($selfHosted): ?>
                        Each slide is from this project&#39;s <code>.vibekb/</code> model: status, verification,
                        and flows traced from source.
                    <?php else: ?>
                        Each slide is from the <?= hp_e($sampleName) ?> <code>.vibekb/</code> model — the same
                        clarity you want in the app you are building.
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
                            ><?= hp_e($item['title']) ?></button>
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
                                <?php if ($item['flow'] !== []): ?>
                                    <ol class="hp-guide-flow">
                                        <?php foreach (array_slice($item['flow'], 0, 4) as $step): ?>
                                            <li><span><?= hp_e($step) ?></span></li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                                <p class="hp-guide-open">
                                    <a class="hp-btn hp-btn-secondary" href="<?= hp_e(hp_guide('functionality', ['id' => $item['id']])) ?>">Open this functionality</a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="hp-guide-controls">
                        <button type="button" class="hp-btn hp-btn-ghost" data-guide-prev disabled>Previous</button>
                        <p class="hp-guide-status" aria-live="polite"><span data-guide-current>1</span> of <?= count($previewItems) ?></p>
                        <button type="button" class="hp-btn hp-btn-primary" data-guide-next>Next</button>
                    </div>
                </div>

                <div class="hp-final-inline">
                    <p class="hp-thesis">AI helped you build it. VibeKB helps you understand it.</p>
                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the live guide</a>
                        <?php if ($sampleRepo !== ''): ?>
                            <a class="hp-btn hp-btn-ghost" href="<?= hp_e($sampleRepo) ?>" rel="noopener noreferrer">View on GitHub</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php else: ?>
        <section class="hp-section hp-final" id="proof" aria-labelledby="proof-title">
            <div class="hp-wrap hp-narrow">
                <h2 id="proof-title">Stop guessing. Start understanding.</h2>
                <p class="hp-thesis">AI helped you build it. VibeKB helps you understand it.</p>
                <div class="hp-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Explore the live guide</a>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <footer class="hp-footer">
        <div class="hp-wrap hp-footer-inner">
            <p><strong>VibeKB.</strong> The understanding layer for the software you&#39;re building.</p>
            <p class="hp-footer-note">Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= hp_e(hp_asset('assets/js/homepage.js')) ?>" defer></script>
</body>
</html>
