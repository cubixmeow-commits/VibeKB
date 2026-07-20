<?php

declare(strict_types=1);

/**
 * VibeKB homepage.
 *
 * This is a minimal, honest product statement that links into the working V1
 * guide. The full marketing homepage is a deliberate later pass — see
 * BUILD_REPORT.md. Do not expand this into the final homepage yet.
 */

function hp_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$guideUrl = 'guide/';
$repoUrl = 'https://github.com/cubixmeow-commits/VibeKB';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VibeKB — Understand what your software is doing</title>
    <meta name="description" content="VibeKB gives AI-assisted developers a living explanation of their application's current functionality, how it works, what AI is changing, and why.">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="top">
    <div class="wrap top__inner">
        <span class="wordmark">VibeKB</span>
        <nav aria-label="Primary">
            <a class="btn btn--primary" href="<?= hp_e($guideUrl) ?>">Open the guide</a>
        </nav>
    </div>
</header>

<main id="main">
    <section class="hero">
        <div class="wrap">
            <p class="eyebrow">A living explanation for AI-assisted software</p>
            <h1>Understand what your software is doing.</h1>
            <p class="lede">
                VibeKB gives AI-assisted developers a living explanation of their
                application&#39;s current functionality, how it works, what AI is
                changing, and why.
            </p>
            <p class="actions">
                <a class="btn btn--primary btn--lg" href="<?= hp_e($guideUrl) ?>">Explore the V1 guide</a>
                <a class="btn btn--lg" href="<?= hp_e($repoUrl) ?>" rel="noopener noreferrer">View the repository</a>
            </p>
            <p class="status-note">
                <strong>Version 1 — product foundation.</strong> This is the working V1: a
                repository-owned content model and a guide organized around what your
                software actually does. The marketing homepage and showcase example are a
                separate, later pass built on this working product.
            </p>
        </div>
    </section>

    <section class="strip">
        <div class="wrap">
            <h2>What VibeKB shows you</h2>
            <ul class="cards">
                <li>
                    <h3>What it does now</h3>
                    <p>Every behaviour the software implements — with its real status: implemented, partial, experimental, or unverified.</p>
                </li>
                <li>
                    <h3>How it works</h3>
                    <p>The mental model, components, request and data flows, storage, and deployment — at human absorption speed.</p>
                </li>
                <li>
                    <h3>What AI is changing</h3>
                    <p>The current objective, affected functionality, expected files, and risks — before, during, and after a change.</p>
                </li>
                <li>
                    <h3>Why it works this way</h3>
                    <p>Decisions, constraints, assumptions, and warnings — each connected to the functionality it explains.</p>
                </li>
            </ul>
            <p class="foot-cta">
                <a class="btn btn--primary" href="<?= hp_e($guideUrl) ?>">Open the guide →</a>
            </p>
        </div>
    </section>
</main>

<footer class="foot">
    <div class="wrap">
        <p><strong>VibeKB.</strong> Understand what your software is doing.</p>
        <p class="muted">Version 1 · Lives in your repo (<code>.vibekb/</code>) · <a href="<?= hp_e($guideUrl) ?>">Software guide</a></p>
    </div>
</footer>
</body>
</html>
