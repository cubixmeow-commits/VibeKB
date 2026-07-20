<?php
/** @var array $scene */
/** @var string $uid */

$label = (string) ($scene['label'] ?? 'System picture');
$description = (string) ($scene['description'] ?? '');
$layers = $scene['layers'] ?? [];
$detailId = $uid . '-detail';
$first = is_array($layers[0] ?? null) ? $layers[0] : [];
?>
<section class="pg-scene pg-scene-concept" data-concept-map aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="pg-concept" role="list">
        <?php foreach ((array) $layers as $i => $layer): ?>
            <?php
            if (!is_array($layer)) {
                continue;
            }
            $layerId = $uid . '-layer-' . (string) ($layer['id'] ?? $i);
            ?>
            <div class="pg-concept-layer<?= $i === 0 ? ' is-active' : '' ?>" role="listitem" data-concept-layer>
                <button
                    type="button"
                    class="pg-concept-btn"
                    id="<?= e($layerId) ?>"
                    data-concept-activate
                    aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
                    aria-controls="<?= e($detailId) ?>"
                    data-title="<?= e((string) ($layer['title'] ?? '')) ?>"
                    data-text="<?= e((string) ($layer['text'] ?? '')) ?>"
                    data-detail="<?= e((string) ($layer['detail'] ?? $layer['text'] ?? '')) ?>"
                >
                    <span class="pg-concept-title"><?= e((string) ($layer['title'] ?? '')) ?></span>
                    <span class="pg-concept-text"><?= e((string) ($layer['text'] ?? '')) ?></span>
                </button>
                <?php if ($i < count((array) $layers) - 1): ?>
                    <div class="pg-concept-arrow" aria-hidden="true">
                        <span class="pg-concept-arrow-mark">↓</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div
        class="pg-concept-detail"
        id="<?= e($detailId) ?>"
        data-concept-detail
        tabindex="-1"
        role="region"
        aria-live="polite"
    >
        <p class="pg-concept-detail-title" data-concept-detail-title>
            <?= e((string) ($first['title'] ?? '')) ?>
        </p>
        <p class="pg-concept-detail-text" data-concept-detail-text>
            <?= e((string) ($first['detail'] ?? $first['text'] ?? '')) ?>
        </p>
    </div>

    <div class="visually-hidden">
        <p>Text alternative for the system diagram:</p>
        <ol>
            <?php foreach ((array) $layers as $layer): ?>
                <?php if (!is_array($layer)) {
                    continue;
                } ?>
                <li>
                    <strong><?= e((string) ($layer['title'] ?? '')) ?>:</strong>
                    <?= e((string) ($layer['detail'] ?? $layer['text'] ?? '')) ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
