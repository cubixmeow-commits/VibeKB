<?php
/** @var array $chapter */
/** @var list<array> $chapters */
/** @var int $index */
/** @var GuideLoader $loader */
/** @var GuideRenderer $renderer */

$hash = (string) ($chapter['hash'] ?? ('chapter-' . ($index + 1)));
$number = (int) ($chapter['number'] ?? ($index + 1));
$title = (string) ($chapter['title'] ?? $chapter['question'] ?? 'Chapter');
$question = (string) ($chapter['question'] ?? $title);
$summary = (string) ($chapter['summary'] ?? '');
$headingId = 'chapter-heading-' . $hash;
$total = count($chapters);
$isFirst = $index === 0;
$isLast = $index === $total - 1;
?>
<section
    class="pg-chapter<?= $isFirst ? ' is-active' : '' ?>"
    id="<?= e($hash) ?>"
    data-chapter
    data-chapter-index="<?= (int) $index ?>"
    data-chapter-hash="<?= e($hash) ?>"
    aria-labelledby="<?= e($headingId) ?>"
>
    <header class="pg-chapter-head">
        <p class="pg-chapter-num">
            <span class="visually-hidden">Chapter </span><?= $number ?>
            <span class="pg-chapter-of" aria-hidden="true"> / <?= (int) $total ?></span>
        </p>
        <h2 class="pg-chapter-title" id="<?= e($headingId) ?>" tabindex="-1">
            <?= e($question) ?>
        </h2>
        <?php if ($summary !== ''): ?>
            <p class="pg-chapter-summary"><?= e($summary) ?></p>
        <?php endif; ?>
    </header>

    <div class="pg-chapter-body">
        <?php foreach (($chapter['scenes'] ?? []) as $sceneIndex => $scene): ?>
            <?php if (is_array($scene)): ?>
                <?php $renderer->renderScene($scene, $chapter, (int) $sceneIndex); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <nav class="pg-chapter-links" aria-label="Chapter navigation for <?= e($question) ?>">
        <?php if (!$isFirst): ?>
            <?php $prev = $chapters[$index - 1]; ?>
            <a class="pg-inline-nav" href="#<?= e((string) $prev['hash']) ?>">
                Previous: <?= e((string) ($prev['question'] ?? $prev['title'] ?? '')) ?>
            </a>
        <?php endif; ?>
        <?php if (!$isLast): ?>
            <?php $next = $chapters[$index + 1]; ?>
            <a class="pg-inline-nav pg-inline-nav-next" href="#<?= e((string) $next['hash']) ?>">
                Next: <?= e((string) ($next['question'] ?? $next['title'] ?? '')) ?>
            </a>
        <?php else: ?>
            <a class="pg-inline-nav pg-inline-nav-next" href="<?= e(landing_url()) ?>">
                Back to VibeKB
            </a>
        <?php endif; ?>
    </nav>
</section>
