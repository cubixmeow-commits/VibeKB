<?php
/** @var array $scene */
/** @var string $uid */

$label = (string) ($scene['label'] ?? 'Flow');
$description = (string) ($scene['description'] ?? '');
$variant = (string) ($scene['variant'] ?? 'default');
$steps = $scene['steps'] ?? [];
$demo = $scene['demo'] ?? null;
$panelId = $uid . '-panel';
?>
<section class="pg-scene pg-scene-flow" data-flow data-flow-variant="<?= e($variant) ?>" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <ol class="pg-flow-steps" data-flow-steps>
        <?php foreach ((array) $steps as $i => $step): ?>
            <?php if (!is_array($step)) {
                continue;
            } ?>
            <li class="pg-flow-step<?= $i === 0 ? ' is-active' : '' ?>" data-flow-step data-step-index="<?= (int) $i ?>">
                <button
                    type="button"
                    class="pg-flow-step-btn"
                    data-flow-activate
                    aria-controls="<?= e($panelId) ?>"
                    aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
                >
                    <span class="pg-flow-step-num"><?= (int) $i + 1 ?></span>
                    <span class="pg-flow-step-title"><?= e((string) ($step['title'] ?? '')) ?></span>
                </button>
                <p class="pg-flow-step-text" id="<?= e($uid) ?>-step-<?= (int) $i ?>">
                    <?= e((string) ($step['text'] ?? '')) ?>
                </p>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="pg-flow-panel" id="<?= e($panelId) ?>" data-flow-panel tabindex="-1">
        <?php
        $first = is_array($steps[0] ?? null) ? $steps[0] : [];
        ?>
        <p class="pg-flow-panel-title" data-flow-panel-title><?= e((string) ($first['title'] ?? '')) ?></p>
        <p class="pg-flow-panel-text" data-flow-panel-text><?= e((string) ($first['text'] ?? '')) ?></p>
    </div>

    <div class="pg-flow-actions">
        <button type="button" class="pg-btn pg-btn-ghost" data-flow-prev disabled>Previous step</button>
        <button type="button" class="pg-btn pg-btn-secondary" data-flow-next>Next step</button>
    </div>

    <?php if (is_array($demo)): ?>
        <div class="pg-demo" data-idea-demo>
            <h4 class="pg-demo-title"><?= e((string) ($demo['label'] ?? 'Demo')) ?></h4>
            <p class="pg-demo-desc"><?= e((string) ($demo['description'] ?? '')) ?></p>
            <form class="pg-demo-form" data-demo-form action="#" method="get">
                <label class="pg-demo-label" for="<?= e($uid) ?>-demo-input">
                    Idea title
                </label>
                <div class="pg-demo-row">
                    <input
                        id="<?= e($uid) ?>-demo-input"
                        class="pg-demo-input"
                        type="text"
                        name="idea"
                        autocomplete="off"
                        placeholder="<?= e((string) ($demo['placeholder'] ?? '')) ?>"
                        data-demo-input
                    >
                    <button type="submit" class="pg-btn pg-btn-primary" data-demo-submit>
                        <?= e((string) ($demo['button'] ?? 'Save')) ?>
                    </button>
                </div>
            </form>
            <div class="pg-demo-list-wrap">
                <p class="pg-demo-list-label" id="<?= e($uid) ?>-list-label">
                    <?= e((string) ($demo['list_label'] ?? 'Ideas')) ?>
                </p>
                <ul
                    class="pg-demo-list"
                    data-demo-list
                    aria-labelledby="<?= e($uid) ?>-list-label"
                    data-empty-text="<?= e((string) ($demo['empty'] ?? 'No ideas yet.')) ?>"
                >
                    <li class="pg-demo-empty" data-demo-empty><?= e((string) ($demo['empty'] ?? 'No ideas yet.')) ?></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</section>
