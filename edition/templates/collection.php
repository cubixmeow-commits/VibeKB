<?php
/** @var array $collectionMeta */
/** @var string $collectionKey */
/** @var list<array> $items */
/** @var string $templateName */
$heading = $collectionMeta['title'] ?? ucfirst($collectionKey);
$description = $collectionMeta['description'] ?? '';
?>
<article class="collection-page template-<?= e($templateName) ?>">
    <header class="page-header">
        <p class="eyebrow"><?= e($templateName === 'editorial' ? 'History' : 'Collection') ?></p>
        <h1><?= e($heading) ?></h1>
        <p class="lede"><?= e($description) ?></p>
    </header>

    <?php if ($items === []): ?>
        <p class="empty-note">No entries yet in this collection.</p>
    <?php else: ?>
        <ul class="entry-list">
            <?php foreach ($items as $item): ?>
                <?php $meta = $item['meta']; ?>
                <li class="entry-item">
                    <a href="<?= e(item_url($collectionKey, $item['slug'])) ?>">
                        <span class="entry-top">
                            <span class="entry-title"><?= e((string) $meta['title']) ?></span>
                            <?php if (!empty($meta['severity'])): ?>
                                <span class="badge <?= e(severity_class((string) $meta['severity'])) ?>"><?= e((string) $meta['severity']) ?></span>
                            <?php elseif (!empty($meta['status'])): ?>
                                <span class="badge badge-status"><?= e((string) $meta['status']) ?></span>
                            <?php endif; ?>
                        </span>
                        <?php if (!empty($meta['summary'])): ?>
                            <span class="entry-summary"><?= e((string) $meta['summary']) ?></span>
                        <?php endif; ?>
                        <span class="entry-meta">
                            <?php if (!empty($meta['updated'])): ?>
                                Updated <?= e((string) $meta['updated']) ?>
                            <?php elseif (!empty($meta['date'])): ?>
                                <?= e((string) $meta['date']) ?>
                            <?php endif; ?>
                            <?php if (!empty($meta['path'])): ?>
                                · <?= e((string) $meta['path']) ?>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</article>
