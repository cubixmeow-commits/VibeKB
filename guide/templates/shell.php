<?php
/** @var array $guideMeta */
/** @var array $project */
/** @var list<array> $chapters */
/** @var GuideLoader $loader */
/** @var GuideRenderer $renderer */

$pageTitle = ($guideMeta['title'] ?? 'Project Guide') . ' · VibeKB';
$pageDescription = (string) ($guideMeta['subtitle'] ?? $guideMeta['intro'] ?? $project['tagline'] ?? '');
$storageKey = (string) ($guideMeta['storage_key'] ?? 'vibekb-project-guide');
$total = count($chapters);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(guide_asset('css/project-guide.css')) ?>">
</head>
<body class="pg-body" data-guide-storage="<?= e($storageKey) ?>">
    <a class="pg-skip" href="#guide-main">Skip to content</a>

    <div class="pg-atmosphere" aria-hidden="true"></div>

    <header class="pg-top">
        <div class="pg-top-inner">
            <a class="pg-brand" href="<?= e(guide_url()) ?>">
                <span class="pg-brand-product"><?= e($project['name'] ?? 'Project') ?></span>
                <span class="pg-brand-kind"><?= e($guideMeta['short_title'] ?? 'Project Guide') ?></span>
            </a>
            <nav class="pg-top-nav" aria-label="Site">
                <a href="<?= e(landing_url()) ?>">VibeKB</a>
            </nav>
        </div>
    </header>

    <div class="pg-shell">
        <aside class="pg-progress" aria-label="Chapter progress">
            <p class="pg-progress-label">Chapters</p>
            <ol class="pg-progress-list" data-guide-nav>
                <?php foreach ($chapters as $i => $ch): ?>
                    <li>
                        <a
                            href="#<?= e((string) $ch['hash']) ?>"
                            data-chapter-link
                            data-chapter-index="<?= (int) $i ?>"
                            aria-current="<?= $i === 0 ? 'step' : 'false' ?>"
                        >
                            <span class="pg-progress-num"><?= (int) ($ch['number'] ?? ($i + 1)) ?></span>
                            <span class="pg-progress-q"><?= e((string) ($ch['question'] ?? $ch['title'] ?? '')) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </aside>

        <main id="guide-main" class="pg-main" tabindex="-1">
            <header class="pg-intro" data-guide-intro>
                <p class="pg-kicker">Project Guide</p>
                <h1 class="pg-title"><?= e((string) ($guideMeta['title'] ?? 'Project Guide')) ?></h1>
                <p class="pg-subtitle"><?= e((string) ($guideMeta['subtitle'] ?? '')) ?></p>
                <?php if (!empty($guideMeta['intro'])): ?>
                    <p class="pg-lede"><?= e((string) $guideMeta['intro']) ?></p>
                <?php endif; ?>
            </header>

            <div
                class="pg-chapters"
                data-guide-chapters
                data-chapter-count="<?= (int) $total ?>"
            >
                <?php foreach ($chapters as $i => $chapter): ?>
                    <?php $renderer->renderChapter($chapter, $chapters, $i); ?>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <nav class="pg-controls" aria-label="Chapter controls" data-guide-controls hidden>
        <div class="pg-controls-inner">
            <button type="button" class="pg-btn pg-btn-ghost" data-guide-prev>
                Previous
            </button>
            <p class="pg-controls-status" data-guide-status aria-live="polite">
                Chapter <span data-guide-current>1</span> of <?= (int) $total ?>
            </p>
            <button type="button" class="pg-btn pg-btn-primary" data-guide-next>
                Continue
            </button>
        </div>
    </nav>

    <div class="pg-live" data-guide-live aria-live="polite" aria-atomic="true"></div>

    <footer class="pg-footer">
        <div class="pg-footer-inner">
            <p>
                <strong><?= e($project['name'] ?? '') ?></strong>
                · explained by VibeKB Project Guide
            </p>
            <p class="pg-footer-meta">
                Content lives in <code>.vibekb/</code>.
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= e(guide_asset('js/project-guide.js')) ?>" defer></script>
</body>
</html>
