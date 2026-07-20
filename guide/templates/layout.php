<?php
/**
 * Shared page shell: header, primary navigation, body template, footer.
 *
 * @var Content $content
 * @var string $projectName
 * @var string $view
 * @var string $pageTitle
 * @var string $bodyTemplate
 * @var array<int, array{view: string, label: string}> $navItems
 * @var bool $devMode
 */
$issues = $content->issues();
$errorCount = count(array_filter($issues, fn ($i) => $i['level'] === 'error'));
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> · <?= h($projectName) ?> — VibeKB</title>
    <meta name="description" content="A living explanation of what <?= h($projectName) ?> currently does, how it works, what AI is changing, and why.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(guide_asset('assets/css/guide.css')) ?>">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header">
    <div class="wrap site-header__inner">
        <a class="brand" href="<?= h(guide_url('overview')) ?>">
            <span class="brand__mark">VibeKB</span>
            <span class="brand__project"><?= h($projectName) ?></span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" hidden>Menu</button>
        <nav class="primary-nav" id="primary-nav" aria-label="Guide sections">
            <ul>
                <?php foreach ($navItems as $item): ?>
                    <?php $active = ($item['view'] === $view) || ($view === 'functionality' && $item['view'] === 'functionality'); ?>
                    <li>
                        <a href="<?= h(guide_url($item['view'])) ?>"<?= $active ? ' aria-current="page"' : '' ?>>
                            <?= $item['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>

<?php if ($devMode && $errorCount > 0): ?>
    <div class="wrap">
        <p class="content-alert" role="status">
            <strong><?= (int) $errorCount ?> content validation error<?= $errorCount === 1 ? '' : 's' ?></strong>
            — see the <a href="<?= h(guide_url('reference')) ?>#validation">Reference</a> view. (Shown in development only.)
        </p>
    </div>
<?php endif; ?>

<main id="main" class="wrap">
    <?php
    $bodyFile = __DIR__ . '/' . $bodyTemplate . '.php';
    if (is_file($bodyFile)) {
        require $bodyFile;
    } else {
        echo '<p>View unavailable.</p>';
    }
    ?>
</main>

<footer class="site-footer">
    <div class="wrap site-footer__inner">
        <p><strong>VibeKB</strong> — Understand what your software is doing.</p>
        <p class="muted">
            A living explanation generated from repository-owned content in <code>.vibekb/</code>.
            <a href="<?= h(site_root_url()) ?>">About VibeKB</a>
        </p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="<?= h(guide_asset('assets/js/guide.js')) ?>" defer></script>
</body>
</html>
