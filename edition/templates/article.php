<?php
/** @var array $item */
/** @var array $collectionMeta */
/** @var string $collectionKey */
/** @var string $templateName */
/** @var list<array> $siblings */
$meta = $item['meta'];
?>
<article class="article-page template-<?= e($templateName) ?>">
    <header class="page-header">
        <p class="eyebrow">
            <a href="<?= e(collection_url($collectionKey)) ?>"><?= e($collectionMeta['title'] ?? $collectionKey) ?></a>
        </p>
        <h1><?= e((string) $meta['title']) ?></h1>
        <?php if (!empty($meta['summary'])): ?>
            <p class="lede"><?= e((string) $meta['summary']) ?></p>
        <?php endif; ?>
        <div class="article-meta">
            <?php if (!empty($meta['severity'])): ?>
                <span class="badge <?= e(severity_class((string) $meta['severity'])) ?>"><?= e((string) $meta['severity']) ?></span>
            <?php endif; ?>
            <?php if (!empty($meta['status'])): ?>
                <span class="badge badge-status"><?= e((string) $meta['status']) ?></span>
            <?php endif; ?>
            <?php if (!empty($meta['updated'])): ?>
                <span class="meta-text">Updated <?= e((string) $meta['updated']) ?></span>
            <?php endif; ?>
            <?php if (!empty($meta['date'])): ?>
                <span class="meta-text"><?= e((string) $meta['date']) ?></span>
            <?php endif; ?>
            <?php if (!empty($meta['path'])): ?>
                <span class="meta-text"><?= e((string) $meta['path']) ?></span>
            <?php endif; ?>
            <?php if (!empty($meta['category'])): ?>
                <span class="meta-text"><?= e((string) $meta['category']) ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="prose">
        <?= $item['html'] ?>
    </div>

    <?php if (count($siblings) > 1): ?>
        <nav class="sibling-nav" aria-label="More in this collection">
            <h2>More in <?= e($collectionMeta['title'] ?? $collectionKey) ?></h2>
            <ul>
                <?php foreach ($siblings as $sibling): ?>
                    <?php if ($sibling['slug'] === $item['slug']) {
                        continue;
                    } ?>
                    <li>
                        <a href="<?= e(item_url($collectionKey, $sibling['slug'])) ?>">
                            <?= e((string) $sibling['meta']['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
</article>
