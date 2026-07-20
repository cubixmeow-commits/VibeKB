<?php
/** @var array $scene */
/** @var string $uid */

$headline = (string) ($scene['headline'] ?? '');
$body = (string) ($scene['body'] ?? '');
$facts = $scene['facts'] ?? [];
?>
<section class="pg-scene pg-scene-statement" aria-labelledby="<?= e($uid) ?>-h">
    <?php if ($headline !== ''): ?>
        <h3 class="pg-statement-headline" id="<?= e($uid) ?>-h"><?= e($headline) ?></h3>
    <?php else: ?>
        <h3 class="visually-hidden" id="<?= e($uid) ?>-h">Statement</h3>
    <?php endif; ?>
    <?php if ($body !== ''): ?>
        <p class="pg-statement-body"><?= e($body) ?></p>
    <?php endif; ?>
    <?php if (is_array($facts) && $facts !== []): ?>
        <ul class="pg-fact-list">
            <?php foreach ($facts as $fact): ?>
                <?php if (!is_array($fact)) {
                    continue;
                } ?>
                <li>
                    <strong><?= e((string) ($fact['label'] ?? '')) ?></strong>
                    <span><?= e((string) ($fact['text'] ?? '')) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
