<?php
/** @var array $scene */
/** @var string $uid */

$label = (string) ($scene['label'] ?? 'Progression');
$description = (string) ($scene['description'] ?? '');
$steps = $scene['steps'] ?? [];
?>
<section class="pg-scene pg-scene-progression" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>
    <ol class="pg-progression" data-progression>
        <?php foreach ((array) $steps as $i => $step): ?>
            <?php if (!is_array($step)) {
                continue;
            } ?>
            <li class="pg-progression-step<?= $i === 0 ? ' is-current' : '' ?>" data-progression-step>
                <span class="pg-progression-index" aria-hidden="true"><?= (int) $i + 1 ?></span>
                <div>
                    <p class="pg-progression-title"><?= e((string) ($step['title'] ?? '')) ?></p>
                    <p class="pg-progression-text"><?= e((string) ($step['text'] ?? '')) ?></p>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
    <p class="visually-hidden"><?= e($description !== '' ? $description : $label) ?></p>
</section>
