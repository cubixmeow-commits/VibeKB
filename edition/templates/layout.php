<?php
/** @var array $project */
/** @var array $edition */
/** @var string $pageTitle */
/** @var string $pageDescription */
/** @var string $contentTemplate */
/** @var string|null $activeCollection */
/** @var array $collections */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(edition_url('assets/css/edition.css')) ?>">
</head>
<body class="edition-body">
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand-lockup" href="<?= e(edition_url()) ?>">
                <span class="brand-kicker">Technical reference</span>
                <span class="brand-title"><?= e($project['name'] ?? 'Project') ?></span>
            </a>
            <nav class="primary-nav" aria-label="Publication">
                <a href="<?= e(rtrim(landing_url(), '/') . '/guide/') ?>">Project Guide</a>
                <a href="<?= e(edition_url()) ?>">Reference home</a>
                <a href="<?= e(landing_url()) ?>">About VibeKB</a>
            </nav>
        </div>
    </header>

    <div class="shell">
        <aside class="rail" aria-label="Collections">
            <p class="rail-label">Browse</p>
            <ul class="rail-list">
                <?php foreach ($collections as $key => $meta): ?>
                    <li>
                        <a class="<?= ($activeCollection ?? '') === $key ? 'is-active' : '' ?>" href="<?= e(collection_url($key)) ?>">
                            <?= e($meta['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main id="main" class="main">
            <?php require __DIR__ . '/' . $contentTemplate . '.php'; ?>
        </main>
    </div>

    <footer class="site-footer">
        <div class="footer-inner">
            <p>Generated explanation for <strong><?= e($project['name'] ?? '') ?></strong>. Content lives in <code>.vibekb/</code>.</p>
            <p class="footer-meta">Edition <?= e((string) ($edition['version'] ?? '')) ?> · <?= e((string) ($edition['published'] ?? '')) ?></p>
        </div>
    </footer>
</body>
</html>
