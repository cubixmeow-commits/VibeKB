<?php
/** @var array $scene */
/** @var string $uid */

$label = (string) ($scene['label'] ?? 'How AI and humans stay aligned');
$description = (string) ($scene['description'] ?? '');
$steps = $scene['steps'] ?? [];
$insight = (string) ($scene['insight'] ?? '');
$panelId = $uid . '-ailoop-panel';
$first = is_array($steps[0] ?? null) ? $steps[0] : [];
?>
<section class="pg-scene pg-scene-ailoop" data-ai-loop aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="pg-ailoop-track" data-ailoop-steps>
        <?php foreach ((array) $steps as $i => $step): ?>
            <?php
            if (!is_array($step)) {
                continue;
            }
            $actor = (string) ($step['actor'] ?? '');
            ?>
            <div class="pg-ailoop-step<?= $i === 0 ? ' is-active' : '' ?>" data-ailoop-step data-step-index="<?= (int) $i ?>">
                <button
                    type="button"
                    class="pg-ailoop-step-btn"
                    data-ailoop-activate
                    aria-controls="<?= e($panelId) ?>"
                    aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
                >
                    <span class="pg-ailoop-actor pg-ailoop-actor-<?= e(preg_replace('/[^a-z]/', '', strtolower($actor)) ?: 'default') ?>">
                        <?= e($actor) ?>
                    </span>
                    <span class="pg-ailoop-step-title"><?= e((string) ($step['title'] ?? '')) ?></span>
                </button>
                <?php if ($i < count((array) $steps) - 1): ?>
                    <span class="pg-ailoop-arrow" aria-hidden="true">→</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pg-ailoop-panel" id="<?= e($panelId) ?>" data-ailoop-panel tabindex="-1" aria-live="polite">
        <p class="pg-ailoop-panel-actor" data-ailoop-panel-actor><?= e((string) ($first['actor'] ?? '')) ?></p>
        <p class="pg-ailoop-panel-title" data-ailoop-panel-title><?= e((string) ($first['title'] ?? '')) ?></p>
        <p class="pg-ailoop-panel-text" data-ailoop-panel-text><?= e((string) ($first['text'] ?? '')) ?></p>
        <?php if (!empty($first['example'])): ?>
            <blockquote class="pg-ailoop-example" data-ailoop-example><?= e((string) $first['example']) ?></blockquote>
        <?php else: ?>
            <blockquote class="pg-ailoop-example" data-ailoop-example hidden></blockquote>
        <?php endif; ?>
    </div>

    <div class="pg-flow-actions">
        <button type="button" class="pg-btn pg-btn-ghost" data-ailoop-prev disabled>Previous</button>
        <button type="button" class="pg-btn pg-btn-secondary" data-ailoop-next>Next</button>
    </div>

    <?php if ($insight !== ''): ?>
        <p class="pg-ailoop-insight"><?= e($insight) ?></p>
    <?php endif; ?>

    <details class="pg-fallback-details">
        <summary>Read the full loop (no JavaScript required)</summary>
        <ol>
            <?php foreach ((array) $steps as $step): ?>
                <?php if (!is_array($step)) {
                    continue;
                } ?>
                <li>
                    <strong><?= e((string) ($step['actor'] ?? '')) ?>:</strong>
                    <?= e((string) ($step['title'] ?? '')) ?>
                    — <?= e((string) ($step['text'] ?? '')) ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </details>

    <?php foreach ((array) $steps as $i => $step): ?>
        <?php if (!is_array($step)) {
            continue;
        } ?>
        <span class="visually-hidden" data-ailoop-step-text="<?= (int) $i ?>">
            <?= e((string) ($step['text'] ?? '')) ?>
            <?php if (!empty($step['example'])): ?>
                Example: <?= e((string) $step['example']) ?>
            <?php endif; ?>
        </span>
    <?php endforeach; ?>
</section>
