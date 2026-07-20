<?php

declare(strict_types=1);

$demoUrl = 'guide/';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB. Understand the app your agent built.</title>
    <meta name="description" content="VibeKB keeps a living explanation of your project inside the repo and publishes it as a website you can actually read. No cloud service. No account.">
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
                <a href="#demo">Project Guide</a>
                <a class="nav-cta" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open guide</a>
            </nav>
        </div>
    </header>

    <main id="main">
        <section class="hero" aria-labelledby="hero-brand">
            <div class="hero-atmosphere" aria-hidden="true"></div>
            <div class="wrap hero-content">
                <p class="hero-brand" id="hero-brand">VibeKB</p>
                <!--
                    H1 A/B options for later testing:
                    Option B: You shipped an app you can't read.
                    Option C: It works. You don't know why. That's the problem.
                -->
                <h1>Your agent built it. Do you understand it?</h1>
                <p class="hero-support">
                    VibeKB keeps a living explanation of your project next to the code and turns it into a website you can actually read.
                    No cloud service. No account. It lives in your repo and ships with it.
                </p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open the Project Guide</a>
                    <a class="btn btn-ghost" href="#problem">Why this exists</a>
                </div>
            </div>
        </section>

        <section class="section problem" id="problem" aria-labelledby="problem-title">
            <div class="wrap narrow">
                <p class="section-kicker">The problem</p>
                <h2 id="problem-title">One more feature, and something you never opened breaks.</h2>
                <p>
                    It's 11pm. You ask your agent for one more feature. It touches a file you have never read.
                    Something breaks, and you don't know if the fix is safe, because you don't know what depends on what.
                </p>
                <p>
                    The chat where the agent explained its plan is gone. The decisions are scattered across sessions that expired weeks ago.
                    The repo is real software now, and you are the only person responsible for it.
                </p>
                <p>
                    Reading every file is the hard way. VibeKB makes the project explain itself.
                </p>
            </div>
        </section>

        <section class="section trick" id="trick" aria-labelledby="trick-title">
            <div class="wrap narrow">
                <p class="section-kicker">The trick</p>
                <h2 id="trick-title">You don't write it. Your agent does.</h2>
                <p>
                    VibeKB ships with a protocol your coding agent follows. When the agent makes a meaningful change,
                    it updates the explanation the same way it updates source files. New decisions get recorded.
                    New risks get flagged. Stale pages get retired.
                </p>
                <p>
                    Understanding becomes part of the build, not homework after it.
                </p>
            </div>
        </section>

        <section class="section how" id="how-it-works" aria-labelledby="how-title">
            <div class="wrap">
                <p class="section-kicker">How it works</p>
                <h2 id="how-title" class="visually-hidden">How it works</h2>
                <ol class="steps">
                    <li>
                        <span class="step-index">01</span>
                        <div>
                            <h3>Your agent keeps the explanation current.</h3>
                            <p>The explanation lives in the repo beside the code. Your agent maintains it as it builds.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-index">02</span>
                        <div>
                            <h3>You read a website, not a repo.</h3>
                            <p>Decisions, risks, how the system fits together, and where bugs usually start. Written in plain language, published as a clean site.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-index">03</span>
                        <div>
                            <h3>Your next prompt is smarter.</h3>
                            <p>The explanation is a trustworthy brief for the next session. Your agent stops re-guessing the architecture, and future changes break less.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <section class="section demo" id="demo" aria-labelledby="demo-title">
            <div class="wrap">
                <p class="section-kicker">Project Guide</p>
                <h2 id="demo-title">See a real project explained.</h2>
                <p class="section-lead">
                    SaaS Idea Manager is a small PHP and SQLite app, explained through a guided Project Guide.
                    Give it five minutes: what the project is, how someone uses it, and what a developer should know before changing it.
                </p>
                <a class="demo-shot" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">
                    <img
                        src="assets/demo-home.png"
                        width="1200"
                        height="780"
                        alt="Screenshot of the SaaS Idea Manager Project Guide, showing the guided project explanation."
                        loading="lazy"
                        decoding="async"
                    >
                </a>
                <div class="demo-actions">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open the Project Guide</a>
                </div>
            </div>
        </section>

        <section class="section not-docs" id="not-docs" aria-labelledby="not-docs-title">
            <div class="wrap narrow">
                <p class="section-kicker">Not docs</p>
                <h2 id="not-docs-title">This is not documentation.</h2>
                <p>
                    Docs describe code. VibeKB explains the project. What was decided and why. What's risky right now.
                    What breaks first. What you're assuming without knowing it.
                </p>
                <p>
                    It also does the thing docs never do: it retires stale pages instead of letting them rot.
                </p>
            </div>
        </section>

        <section class="section explains" id="explains" aria-labelledby="explains-title">
            <div class="wrap">
                <p class="section-kicker">What it explains</p>
                <h2 id="explains-title">The parts of a project people lose first.</h2>
                <ul class="explain-list">
                    <li>How the system works end to end</li>
                    <li>Decisions, and why they were made</li>
                    <li>Current risks and hard warnings</li>
                    <li>Assumptions you didn't know you were making</li>
                    <li>Where bugs usually start</li>
                    <li>A map of the modules and shared vocabulary</li>
                    <li>A history of the explanation itself</li>
                </ul>
            </div>
        </section>

        <section class="section audience" id="audience" aria-labelledby="audience-title">
            <div class="wrap narrow">
                <p class="section-kicker">Who it's for</p>
                <h2 id="audience-title">Solo developers building with AI.</h2>
                <p>
                    You can ship with Cursor, Claude Code, or Codex, but you want a clear picture of what actually landed in the repo.
                    That's who this is for. It is not an enterprise knowledge platform and not a team wiki.
                </p>
            </div>
        </section>

        <section class="section version" id="version" aria-labelledby="version-title">
            <div class="wrap narrow">
                <p class="section-kicker">Version 1</p>
                <h2 id="version-title">One excellent demonstration.</h2>
                <p>
                    Version 1 is a working content system and one fully published example project.
                    No accounts. No cloud AI APIs. No search product. Just proof that a project can explain itself.
                </p>
            </div>
        </section>

        <section class="section final-cta" aria-labelledby="cta-title">
            <div class="wrap narrow">
                <h2 id="cta-title">Read the guide like it's your own project.</h2>
                <p>Open the SaaS Idea Manager Project Guide and imagine this existed for the app you're most afraid to touch.</p>
                <a class="btn btn-primary" href="<?= htmlspecialchars($demoUrl, ENT_QUOTES, 'UTF-8') ?>">Open the Project Guide</a>
            </div>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="wrap footer-row">
            <p><strong>VibeKB.</strong> Understand the app your agent built.</p>
            <p class="footer-note">Version 1. Lives in your repo.</p>
        </div>
    </footer>
</body>
</html>
