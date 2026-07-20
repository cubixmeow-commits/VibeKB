<?php

declare(strict_types=1);

$demoUrl = 'edition/';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — Understand your AI-built projects</title>
    <meta name="description" content="VibeKB keeps an explanation of your project inside the repository and publishes it as a website, making it easy to understand what your coding agent has built.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body class="landing-body">
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="topbar">
        <div class="wrap topbar-inner">
            <a class="wordmark" href="./">VibeKB</a>
            <nav class="top-nav" aria-label="Primary">
                <a href="#how-it-works">How it works</a>
                <a href="#demo">Live demo</a>
                <a class="nav-cta" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open demo</a>
            </nav>
        </div>
    </header>

    <main id="main">
        <section class="hero" aria-labelledby="hero-brand">
            <div class="hero-atmosphere" aria-hidden="true"></div>
            <div class="wrap hero-content">
                <p class="hero-brand" id="hero-brand">VibeKB</p>
                <h1>Understand your AI-built projects.</h1>
                <p class="hero-support">
                    VibeKB keeps an explanation of your project inside the repository and publishes it as a website,
                    making it easy to understand what your coding agent has built.
                </p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">View SaaS Idea Manager demo</a>
                    <a class="btn btn-ghost" href="#problem">Why it exists</a>
                </div>
            </div>
        </section>

        <section class="section problem" id="problem" aria-labelledby="problem-title">
            <div class="wrap narrow">
                <p class="section-kicker">The problem</p>
                <h2 id="problem-title">Agents ship features faster than you can absorb the architecture.</h2>
                <p>
                    You ask a coding agent to build something. It works. Then another change lands. Then another.
                    Soon the repository is real software—and you are no longer sure how it fits together,
                    what was intentional, or what will break if you ask for “just one more feature.”
                </p>
                <p>
                    Reading every file is the hard way. VibeKB exists so the project can explain itself.
                </p>
            </div>
        </section>

        <section class="section how" id="how-it-works" aria-labelledby="how-title">
            <div class="wrap">
                <p class="section-kicker">How it works</p>
                <h2 id="how-title">Knowledge in the repo. Website as the product.</h2>
                <ol class="steps">
                    <li>
                        <span class="step-index">01</span>
                        <div>
                            <h3>Store project knowledge</h3>
                            <p>Structured metadata and Markdown live in <code>.vibekb/</code>, maintained beside the code.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-index">02</span>
                        <div>
                            <h3>Render a publication</h3>
                            <p>PHP reads that content and publishes a calm technical website—not a second copy of the docs in templates.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-index">03</span>
                        <div>
                            <h3>Stay oriented as you build</h3>
                            <p>Risks, decisions, maps, and debugging notes stay current so future agent work has a trustworthy brief.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <section class="section demo" id="demo" aria-labelledby="demo-title">
            <div class="wrap">
                <p class="section-kicker">Live demo preview</p>
                <h2 id="demo-title">SaaS Idea Manager, explained.</h2>
                <p class="section-lead">
                    A fictional single-user PHP and SQLite project published the way VibeKB intends:
                    as an editorial “Current Edition,” not a generic docs theme.
                </p>
                <div class="demo-preview">
                    <div class="demo-preview-header">
                        <span>Current Edition</span>
                        <span>SaaS Idea Manager</span>
                    </div>
                    <div class="demo-preview-body">
                        <p class="demo-quote">Read This First · Current Risks · What Changed · How the Project Works</p>
                        <p>
                            Stores multiple SaaS ideas. SQLite. No authentication. No uploads.
                            Manual migrations. The biggest risk is losing architectural understanding as features grow.
                        </p>
                        <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open the full demonstration</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section explains" id="explains" aria-labelledby="explains-title">
            <div class="wrap">
                <p class="section-kicker">What it explains</p>
                <h2 id="explains-title">The parts of a project people lose first.</h2>
                <ul class="explain-list">
                    <li>How the system works end to end</li>
                    <li>Important decisions and why they were made</li>
                    <li>Current risks and hard warnings</li>
                    <li>Mental models and assumptions</li>
                    <li>Where bugs usually start</li>
                    <li>Module map and shared vocabulary</li>
                    <li>Editorial history of the explanation itself</li>
                </ul>
            </div>
        </section>

        <section class="section native" id="native" aria-labelledby="native-title">
            <div class="wrap narrow">
                <p class="section-kicker">Repository native</p>
                <h2 id="native-title">The explanation travels with the code.</h2>
                <p>
                    VibeKB does not depend on a separate documentation product, an AI chat window, or an account.
                    Content lives in the repository. The website is generated from that content on ordinary PHP hosting.
                </p>
                <p>
                    Future coding agents can update <code>.vibekb/</code> the same way they update source files—
                    so understanding stays part of the build, not a forgotten wiki.
                </p>
            </div>
        </section>

        <section class="section audience" id="audience" aria-labelledby="audience-title">
            <div class="wrap narrow">
                <p class="section-kicker">Who it’s for</p>
                <h2 id="audience-title">Individual developers building with AI.</h2>
                <p>
                    If you can ship with a coding agent but still need a clear picture of what landed in the repo,
                    VibeKB is for you. It is not an enterprise knowledge platform and not a team wiki replacement.
                </p>
            </div>
        </section>

        <section class="section version" id="version" aria-labelledby="version-title">
            <div class="wrap narrow">
                <p class="section-kicker">Version 1</p>
                <h2 id="version-title">One excellent demonstration.</h2>
                <p>
                    Version 1 focuses on a polished landing page and a generated publication for
                    <strong>SaaS Idea Manager</strong>. No accounts. No cloud AI APIs. No search product.
                    Just a working content system and a website that proves the idea.
                </p>
            </div>
        </section>

        <section class="section final-cta" aria-labelledby="cta-title">
            <div class="wrap narrow">
                <h2 id="cta-title">See what VibeKB generates.</h2>
                <p>Open the SaaS Idea Manager edition and read the project the way it should be understood.</p>
                <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Enter the demonstration</a>
            </div>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="wrap footer-row">
            <p><strong>VibeKB</strong> — Understand your AI-built projects.</p>
            <p class="footer-note">Version 1 · Repository-native publication</p>
        </div>
    </footer>
</body>
</html>
