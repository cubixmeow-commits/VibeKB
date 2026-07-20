<?php
/** @var array $scene */
/** @var string $uid */

$label = (string) ($scene['label'] ?? 'Evolution');
$description = (string) ($scene['description'] ?? '');
$snapshots = $scene['snapshots'] ?? [];
$panelId = $uid . '-evolution-panel';
$first = is_array($snapshots[0] ?? null) ? $snapshots[0] : [];
?>
<section class="pg-scene pg-scene-evolution" data-evolution aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="pg-evolution-tabs" role="tablist" aria-label="<?= e($label) ?>" data-evolution-tabs>
        <?php foreach ((array) $snapshots as $i => $snap): ?>
            <?php
            if (!is_array($snap)) {
                continue;
            }
            $tabId = $uid . '-snap-' . (string) ($snap['id'] ?? $i);
            ?>
            <button
                type="button"
                class="pg-evolution-tab<?= $i === 0 ? ' is-active' : '' ?>"
                role="tab"
                id="<?= e($tabId) ?>"
                data-evolution-tab
                data-snap-index="<?= (int) $i ?>"
                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                aria-controls="<?= e($panelId) ?>"
                tabindex="<?= $i === 0 ? '0' : '-1' ?>"
            >
                <span class="pg-evolution-version"><?= e((string) ($snap['version'] ?? '')) ?></span>
                <span class="pg-evolution-snap-title"><?= e((string) ($snap['title'] ?? '')) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <div
        class="pg-evolution-panel"
        id="<?= e($panelId) ?>"
        role="tabpanel"
        data-evolution-panel
        tabindex="-1"
        aria-live="polite"
    >
        <p class="pg-evolution-panel-version" data-evolution-panel-version><?= e((string) ($first['version'] ?? '')) ?></p>
        <p class="pg-evolution-panel-title" data-evolution-panel-title><?= e((string) ($first['title'] ?? '')) ?></p>
        <p class="pg-evolution-panel-body" data-evolution-panel-body><?= e((string) ($first['body'] ?? '')) ?></p>

        <?php if (is_array($first['changes'] ?? null) && $first['changes'] !== []): ?>
            <ul class="pg-evolution-changes" data-evolution-changes>
                <?php foreach ($first['changes'] as $change): ?>
                    <li><?= e((string) $change) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <ul class="pg-evolution-changes" data-evolution-changes hidden></ul>
        <?php endif; ?>

        <?php if (!empty($first['note'])): ?>
            <p class="pg-evolution-note" data-evolution-note><?= e((string) $first['note']) ?></p>
        <?php else: ?>
            <p class="pg-evolution-note" data-evolution-note hidden></p>
        <?php endif; ?>
    </div>

    <details class="pg-fallback-details">
        <summary>Read all snapshots (no JavaScript required)</summary>
        <?php foreach ((array) $snapshots as $snap): ?>
            <?php if (!is_array($snap)) {
                continue;
            } ?>
            <article class="pg-fallback-block">
                <h4><?= e((string) ($snap['version'] ?? '')) ?> — <?= e((string) ($snap['title'] ?? '')) ?></h4>
                <p><?= e((string) ($snap['body'] ?? '')) ?></p>
                <?php if (is_array($snap['changes'] ?? null)): ?>
                    <ul>
                        <?php foreach ($snap['changes'] as $change): ?>
                            <li><?= e((string) $change) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </details>

    <script type="application/json" data-evolution-data><?= e(json_encode(array_values(array_filter((array) $snapshots, 'is_array')), JSON_UNESCAPED_UNICODE)) ?></script>
</section>
