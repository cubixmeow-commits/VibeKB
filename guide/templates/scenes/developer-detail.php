<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Developer details');
$variant = (string) ($scene['variant'] ?? 'disclosure');
$summary = (string) ($scene['summary'] ?? '');
$points = $scene['points'] ?? [];
$entries = $scene['entries'] ?? [];
$sources = $loader->resolveSources(is_array($scene['sources'] ?? null) ? $scene['sources'] : []);
$panelId = $uid . '-dev-panel';
?>
<section class="pg-scene pg-scene-dev" data-developer-detail data-variant="<?= e($variant) ?>" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>

    <?php if ($variant === 'entry-points' && is_array($entries)): ?>
        <div class="pg-entries" data-dev-entries>
            <?php foreach ($entries as $i => $entry): ?>
                <?php
                if (!is_array($entry)) {
                    continue;
                }
                $entryId = $uid . '-e-' . (string) ($entry['id'] ?? $i);
                $entryPanel = $entryId . '-panel';
                $entrySources = $loader->resolveSources(is_array($entry['sources'] ?? null) ? $entry['sources'] : []);
                ?>
                <article class="pg-entry" data-dev-entry>
                    <h4 class="pg-entry-question" id="<?= e($entryId) ?>-q">
                        <?= e((string) ($entry['question'] ?? '')) ?>
                    </h4>
                    <button
                        type="button"
                        class="pg-btn pg-btn-ghost"
                        data-dev-toggle
                        aria-expanded="false"
                        aria-controls="<?= e($entryPanel) ?>"
                    >
                        Open
                    </button>
                    <div
                        class="pg-entry-panel"
                        id="<?= e($entryPanel) ?>"
                        role="region"
                        aria-labelledby="<?= e($entryId) ?>-q"
                        hidden
                        data-dev-panel
                    >
                        <ul class="pg-dev-points">
                            <?php foreach ((array) ($entry['points'] ?? []) as $point): ?>
                                <?php if (!is_array($point)) {
                                    continue;
                                } ?>
                                <li>
                                    <strong><?= e((string) ($point['title'] ?? '')) ?></strong>
                                    <span><?= e((string) ($point['text'] ?? '')) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($entrySources !== []): ?>
                            <p class="pg-source-label">Related articles</p>
                            <ul class="pg-source-list">
                                <?php foreach ($entrySources as $source): ?>
                                    <li>
                                        <a href="<?= e((string) $source['url']) ?>">
                                            <?= e((string) $source['title']) ?>
                                        </a>
                                        <?php if (!empty($source['summary'])): ?>
                                            <span class="pg-source-summary"><?= e((string) $source['summary']) ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <details class="pg-fallback-details">
                        <summary>Read answer</summary>
                        <ul class="pg-dev-points">
                            <?php foreach ((array) ($entry['points'] ?? []) as $point): ?>
                                <?php if (!is_array($point)) {
                                    continue;
                                } ?>
                                <li>
                                    <strong><?= e((string) ($point['title'] ?? '')) ?></strong>
                                    — <?= e((string) ($point['text'] ?? '')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if ($summary !== ''): ?>
            <p class="pg-scene-desc"><?= e($summary) ?></p>
        <?php endif; ?>
        <button
            type="button"
            class="pg-btn pg-btn-dev"
            data-dev-toggle
            aria-expanded="false"
            aria-controls="<?= e($panelId) ?>"
        >
            <?= e($label) ?>
        </button>
        <div
            class="pg-dev-panel"
            id="<?= e($panelId) ?>"
            role="region"
            aria-label="<?= e($label) ?>"
            hidden
            data-dev-panel
        >
            <ul class="pg-dev-points">
                <?php foreach ((array) $points as $point): ?>
                    <?php if (!is_array($point)) {
                        continue;
                    } ?>
                    <li>
                        <strong><?= e((string) ($point['title'] ?? '')) ?></strong>
                        <span><?= e((string) ($point['text'] ?? '')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($sources !== []): ?>
                <p class="pg-source-label">Related articles</p>
                <ul class="pg-source-list">
                    <?php foreach ($sources as $source): ?>
                        <li>
                            <a href="<?= e((string) $source['url']) ?>">
                                <?= e((string) $source['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <details class="pg-fallback-details">
            <summary><?= e($label) ?> (always readable)</summary>
            <ul class="pg-dev-points">
                <?php foreach ((array) $points as $point): ?>
                    <?php if (!is_array($point)) {
                        continue;
                    } ?>
                    <li>
                        <strong><?= e((string) ($point['title'] ?? '')) ?></strong>
                        — <?= e((string) ($point['text'] ?? '')) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </details>
    <?php endif; ?>
</section>
