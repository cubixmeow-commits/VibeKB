<?php

declare(strict_types=1);

/**
 * VibeKB homepage: hero/problem, install fast-start, what you get, live proof + CTA.
 * Copy follows the developer journey (build fast → lose understanding → fear change →
 * keep understanding in the repo). Section 1 includes a hero comic beside the copy.
 * The install section mirrors the real installer workflow (download release binary →
 * vibekb install → coding agent builds the model). Compatibility & Requirements sits
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
$releasesUrl = $repoUrl . '/releases/latest';
$installerGuideUrl = $repoUrl . '/blob/main/INSTALLER.md';
$codingAgents = 'Cursor, Claude Code, Codex, Windsurf, Copilot, and others';
$namedAgents = ['Cursor', 'Claude Code', 'Codex', 'Windsurf'];
$stackBadges = [
    'PHP', 'Laravel', 'WordPress', 'Node.js', 'React', 'Vue', 'Next.js', 'Angular',
    'Python', 'Django', 'Flask', 'FastAPI', 'Go', 'Rust', 'Java', 'Kotlin',
    'C#', 'Swift', 'Ruby', 'C++',
];
// Asset names must match .github/workflows/release.yml.
$releasePlatforms = [
    ['label' => 'Windows 64-bit', 'asset' => 'vibekb-windows-amd64.exe'],
    ['label' => 'Windows ARM64', 'asset' => 'vibekb-windows-arm64.exe'],
    ['label' => 'macOS Apple Silicon', 'asset' => 'vibekb-darwin-arm64'],
    ['label' => 'macOS Intel', 'asset' => 'vibekb-darwin-amd64'],
    ['label' => 'Linux 64-bit', 'asset' => 'vibekb-linux-amd64'],
    ['label' => 'Linux ARM64', 'asset' => 'vibekb-linux-arm64'],
];
$installCmd = 'vibekb install /path/to/your/project';
$installExampleCmd = 'vibekb install ~/Projects/my-app';
$dryRunCmd = 'vibekb install --dry-run /path/to/your/project';
$buildFromSourceCmd = "git clone https://github.com/cubixmeow-commits/VibeKB.git\ncd VibeKB\ngo build -o vibekb ./cmd/vibekb";
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
    'Code signing and notarization',
    'Homebrew installation',
    'Winget installation',
    'curl installation',
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
    $sampleTagline = 'A living model of what your application does now, organized around functionality.';
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
    <title>VibeKB: AI helped you build it. VibeKB helps you understand it.</title>
    <meta name="description" content="If you build with <?= hp_e($codingAgents) ?>, you can ship software faster than you understand it. VibeKB keeps that understanding in your repository.">
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

        <!-- 1. The problem: recognition + promise -->
        <section class="hp-section hp-hero" id="problem" aria-labelledby="hero-title">
            <div class="hp-wrap hp-hero-grid">
                <div class="hp-hero-copy">
                    <p class="hp-eyebrow">For developers building with AI coding agents</p>
                    <h1 id="hero-title">AI helped you build it. VibeKB helps you understand it.</h1>
                    <p class="hp-hero-support">
                        Whether you started from scratch or built on something from GitHub,
                        <?= hp_e($codingAgents) ?> make it easy to ship software surprisingly fast.
                        Understanding what you built usually does not keep up.
                        The demo runs. The feature looks finished. Then you notice you are guessing
                        which files matter, and worrying that the next edit breaks something you cannot see.
                    </p>

                    <ol class="hp-arc" aria-label="The developer journey in three beats">
                        <li>
                            <strong>You ship quickly.</strong>
                            You describe what you want, accept the diffs, and keep going in chat.
                            Pretty soon the software exists.
                        </li>
                        <li>
                            <strong>The app outgrows your map of it.</strong>
                            Prompts, starter templates, and agent sessions pile up.
                            The codebase grows faster than your understanding of it.
                        </li>
                        <li>
                            <strong>Every change starts to feel like a guess.</strong>
                            It is not missing docs. It is uncertainty.
                            You keep asking the same architecture questions in a new session.
                        </li>
                    </ol>

                    <div class="hp-actions">
                        <a class="hp-btn hp-btn-primary" href="<?= hp_e($guideUrl) ?>">Open the live guide</a>
                        <a class="hp-btn hp-btn-ghost" href="#understanding">What VibeKB is</a>
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
                                alt="Comic of building quickly with AI, losing track of how the app works, then using VibeKB to keep that understanding in the repository"
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
                        These numbers come from VibeKB&#39;s own <code>.vibekb/</code> model.
                    <?php else: ?>
                        These numbers come from the <?= hp_e($sampleName) ?> <code>.vibekb/</code> model.
                    <?php endif; ?></p>
                </aside>
                <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 2. Install: three-step fast-start (download → install → coding agent) -->
        <section class="hp-section hp-install" id="install" aria-labelledby="install-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Install in three steps</p>
                <h2 id="install-title">Add VibeKB to your repository</h2>
                <p class="hp-lead hp-install-lead">
                    Download the binary. Point it at your repo. VibeKB installs the knowledge
                    layer and gives your coding agent a better place to start.
                    You do not need Go to install. PHP is only needed afterward for the guide.
                </p>

                <ol class="hp-install-cards" aria-label="Install VibeKB in three steps">
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">1</p>
                        <h3>Download the VibeKB CLI</h3>
                        <p class="hp-install-card-copy">
                            Get the executable for your operating system from GitHub Releases.
                            Rename it to <code>vibekb</code> (or <code>vibekb.exe</code> on Windows),
                            make it executable if needed, and put it on your <code>PATH</code>.
                            Ordinary users do not need to install Go.
                        </p>
                        <div class="hp-actions">
                            <a class="hp-btn hp-btn-primary" href="<?= hp_e($releasesUrl) ?>" rel="noopener noreferrer">Download latest release</a>
                        </div>
                        <p class="hp-install-example-label">Release binaries</p>
                        <ul class="hp-compat-checks hp-compat-checks--tight">
                            <?php foreach ($releasePlatforms as $platform): ?>
                                <li><?= hp_e($platform['label']) ?>: <code><?= hp_e($platform['asset']) ?></code></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">2</p>
                        <h3>Install it into your project</h3>
                        <p class="hp-install-card-copy">
                            Run the executable against the repository you want to understand.
                        </p>
                        <p class="hp-install-req"><span>No Go and no PHP required to install</span></p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd" id="cmd-install"><code><?= hp_e($installCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-install">Copy</button>
                        </div>
                        <p class="hp-install-example-label">Example</p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd hp-cmd--example" id="cmd-install-example"><code><?= hp_e($installExampleCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-install-example">Copy</button>
                        </div>
                        <p class="hp-install-card-copy">
                            PHP 8.2+ is required later for the guide and model commands.
                            You do not need it to run <code>vibekb install</code>.
                        </p>
                    </li>
                    <li class="hp-install-card">
                        <p class="hp-install-step-num" aria-hidden="true">3</p>
                        <h3>Ask your coding agent</h3>
                        <p class="hp-install-card-copy">
                            The installer only sets up the workspace. Open the project in Cursor, Claude Code,
                            Codex, Windsurf, or another coding agent, then paste this prompt so the agent
                            builds the first model from source.
                        </p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd hp-cmd--prompt" id="cmd-agent-prompt"><code><?= hp_e($agentPrompt) ?></code></pre>
                            <button type="button" class="hp-copy-btn hp-copy-btn--prompt" data-copy-target="#cmd-agent-prompt">Copy agent prompt</button>
                        </div>
                    </li>
                </ol>

                <div class="hp-install-result" aria-label="What your project contains after install">
                    <p class="hp-install-result-title">After install, your project has:</p>
                    <ul class="hp-install-result-list">
                        <li>
                            <code>.vibekb/</code>
                            <span>Living knowledge base for the application</span>
                            <span class="hp-install-result-note">Empty but valid workspace from the installer</span>
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
                            <span class="hp-install-result-note">Created after the first model is built</span>
                        </li>
                    </ul>
                    <p class="hp-install-result-gen">
                        <code>docs/</code> is created later with
                        <code>php tools/vibekb.php generate</code>, not by the installer.
                    </p>
                </div>

                <details class="hp-install-details">
                    <summary>What does the installer do?</summary>
                    <div class="hp-install-details-body">
                        <ol>
                            <li>Reads <code>template/manifest.json</code> from the payload embedded in the binary.</li>
                            <li>Copies the VibeKB PHP runtime and agent instructions into the target repository.</li>
                            <li>Creates a fresh empty-but-valid <code>.vibekb/</code> workspace. It does not invent functionality.</li>
                            <li>Does not analyze or modify your application. It does not need PHP to install.</li>
                            <li>Checks the install locally and points your coding agent at the integration prompt.</li>
                        </ol>
                        <p>
                            The installed payload is listed in <code>template/manifest.json</code>.
                            Today that includes <code>guide/</code>, <code>tools/</code>, <code>prompts/</code>,
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
                            Shows every file the installer would create, replace, or skip. Writes nothing.
                        </p>
                    </div>
                </details>

                <p class="hp-thesis hp-install-boundary">
                    The installer sets up VibeKB. Your coding agent reads the application and builds the model.
                </p>

                <div class="hp-actions hp-install-actions">
                    <a class="hp-btn hp-btn-primary" href="<?= hp_e($repoUrl) ?>" rel="noopener noreferrer">View on GitHub</a>
                    <a class="hp-btn hp-btn-ghost" href="<?= hp_e($installerGuideUrl) ?>" rel="noopener noreferrer">Read the full installer guide</a>
                </div>
            </div>
        </section>

        <!-- 2b. Compatibility & Requirements: will this work with my stack? -->
        <section class="hp-section hp-compat" id="compatibility" aria-labelledby="compatibility-title">
            <div class="hp-wrap">
                <p class="hp-kicker">Will this work with my project?</p>
                <h2 id="compatibility-title">Compatibility &amp; Requirements</h2>
                <p class="hp-lead hp-compat-lead">
                    Install with a downloaded executable. The installed guide and model engine stay PHP.
                    Your coding agent builds the model by reading your source.
                </p>

                <div class="hp-compat-grid">
                    <article class="hp-compat-card">
                        <h3>Installing VibeKB</h3>
                        <ul class="hp-compat-checks">
                            <li>Download the correct executable for your operating system</li>
                            <li>Read/write access to the target repository</li>
                            <li>Git repository recommended</li>
                            <li>No Go installation required</li>
                        </ul>
                        <ul class="hp-compat-none" aria-label="Not required for installation">
                            <li>No Composer</li>
                            <li>No Node</li>
                            <li>No npm</li>
                            <li>No Python</li>
                            <li>No database</li>
                        </ul>
                    </article>

                    <article class="hp-compat-card">
                        <h3>Running VibeKB after installation</h3>
                        <ul class="hp-compat-checks">
                            <li>PHP 8.2+ for the dynamic guide and model commands</li>
                            <li>An AI coding agent such as Cursor, Claude Code, Codex, or similar</li>
                            <li>A developer working inside the repository</li>
                        </ul>
                        <p class="hp-compat-card-copy">
                            The downloaded binary installs VibeKB. It does not replace PHP for the
                            installed guide, <code>vibekb check</code>, or <code>vibekb generate</code>.
                        </p>
                    </article>

                    <article class="hp-compat-card">
                        <h3>Works With</h3>
                        <p class="hp-compat-card-copy">
                            VibeKB stores what the agent learns. Your coding agent reads the source.
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
                            Any coding agent that can read a repository and follow repo instructions.
                            How well that works depends on the agent and the prompt.
                            VibeKB does not claim every agent is certified end to end.
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
                    <h3 class="hp-compat-subhead">No extra infrastructure</h3>
                    <ul class="hp-compat-noneed-grid">
                        <?php foreach ($noInfraItems as $item): ?>
                            <li><?= hp_e($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="hp-compat-limits">
                    <h3 class="hp-compat-subhead">Current Requirements</h3>
                    <h4 class="hp-compat-subhead">Installation</h4>
                    <ul class="hp-compat-checks">
                        <li>Downloadable executable from GitHub Releases</li>
                        <li>No Go toolchain required</li>
                        <li>No PHP process required to install</li>
                    </ul>
                    <h4 class="hp-compat-subhead">Using VibeKB after installation</h4>
                    <ul class="hp-compat-checks">
                        <li>PHP 8.2+</li>
                        <li>A capable AI coding agent</li>
                        <li>A developer working inside the repository</li>
                    </ul>
                    <p class="hp-compat-card-copy">
                        The installer only prepares the workspace.
                        The coding agent builds and maintains the model.
                        VibeKB does not analyze your repository during install.
                    </p>
                </div>

                <details class="hp-install-details">
                    <summary>Advanced: build VibeKB from source</summary>
                    <div class="hp-install-details-body">
                        <p class="hp-install-card-copy">
                            For contributors building VibeKB itself. Ordinary installs should use the
                            release binary. You need Go 1.24+ (from <code>go.mod</code>) and Git.
                        </p>
                        <div class="hp-cmd-block">
                            <pre class="hp-cmd" id="cmd-build-source"><code><?= hp_e($buildFromSourceCmd) ?></code></pre>
                            <button type="button" class="hp-copy-btn" data-copy-target="#cmd-build-source">Copy</button>
                        </div>
                        <p class="hp-install-card-copy">
                            Then run <code>./vibekb install /path/to/your/project</code>.
                        </p>
                    </div>
                </details>

                <div class="hp-compat-soon">
                    <p class="hp-compat-soon-label">Coming soon</p>
                    <ul class="hp-badge-grid hp-badge-grid--soon" aria-label="Future improvements, not yet available">
                        <?php foreach ($comingSoonItems as $item): ?>
                            <li><span class="hp-badge hp-badge--soon"><?= hp_e($item) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="hp-compat-card-note">On the roadmap. Not available yet.</p>
                </div>
            </div>
        </section>

        <!-- 3. What you get: the answer, distilled -->
        <section class="hp-section hp-surface" id="understanding" aria-labelledby="understanding-title">
            <div class="hp-wrap">
                <p class="hp-kicker">What VibeKB is</p>
                <h2 id="understanding-title">A living knowledge base that stays with your repository.</h2>
                <p class="hp-lead">
                    Not another coding agent. Not docs you write once and forget.
                    A model in <code>.vibekb/</code>, committed with your code, that explains what the
                    software is doing <em>right now</em>: how it is structured, which files matter,
                    what AI is changing, and what is verified versus still a guess.
                </p>

                <div class="hp-pillars">
                    <article>
                        <h3>What it does now</h3>
                        <p>Each behaviour with an honest status: implemented, partial, broken, or unknown.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('functionality')) ?>">Functionality</a>
                    </article>
                    <article>
                        <h3>How it works</h3>
                        <p>Flows, files, data, and what tends to break when you change something.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('how-it-works')) ?>">How it works</a>
                    </article>
                    <article>
                        <h3>What AI is changing</h3>
                        <p>The current goal, what it touches, the risks, and how you plan to verify it.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('current-work')) ?>">Current AI work</a>
                    </article>
                    <article>
                        <h3>Why it works this way</h3>
                        <p>Decisions, constraints, and warnings, linked to the behaviour they explain.</p>
                        <a class="hp-text-link" href="<?= hp_e(hp_guide('why')) ?>">Repository memory</a>
                    </article>
                </div>

                <p class="hp-thesis">
                    Open the guide before you edit. See what is safe to touch. Change less by guesswork.
                </p>
            </div>
        </section>

        <!-- 4. Live proof + CTA -->
        <?php if ($previewItems !== []): ?>
        <section class="hp-section" id="proof" aria-labelledby="proof-title">
            <div class="hp-wrap">
                <p class="hp-kicker">See it on this project</p>
                <h2 id="proof-title">Real functionality records from the repo, not a demo script.</h2>
                <p class="hp-lead">
                    <?php if ($selfHosted): ?>
                        Each slide comes from this project&#39;s <code>.vibekb/</code> model:
                        status, verification, and flows traced from source.
                    <?php else: ?>
                        Each slide comes from the <?= hp_e($sampleName) ?> <code>.vibekb/</code> model,
                        the same kind of clarity you would want in your own app.
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
                <h2 id="proof-title">Stop guessing. Read the model first.</h2>
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
            <p><strong>VibeKB.</strong> Keeps the understanding in your repository, not in yesterday&#39;s chat.</p>
            <p class="hp-footer-note">Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= hp_e(hp_asset('assets/js/homepage.js')) ?>" defer></script>
</body>
</html>
