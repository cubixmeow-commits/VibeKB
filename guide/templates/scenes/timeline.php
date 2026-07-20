<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Timeline');
$description = (string) ($scene['description'] ?? '');
$phases = $scene['phases'] ?? [];
$panelId = $uid . '-timeline-panel';
$first = is_array($phases[0] ?? null) ? $phases[0] : [];
?>
<section class="pg-scene pg-scene-timeline" data-timeline aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="pg-timeline-rail" role="tablist" aria-label="<?= e($label) ?>" data-timeline-rail>
        <?php foreach ((array) $phases as $i => $phase): ?>
            <?php
            if (!is_array($phase)) {
                continue;
            }
            $phaseId = $uid . '-phase-' . (string) ($phase['id'] ?? $i);
            ?>
            <button
                type="button"
                class="pg-timeline-phase<?= $i === 0 ? ' is-active' : '' ?>"
                role="tab"
                id="<?= e($phaseId) ?>"
                data-timeline-phase
                data-phase-index="<?= (int) $i ?>"
                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                aria-controls="<?= e($panelId) ?>"
                tabindex="<?= $i === 0 ? '0' : '-1' ?>"
            >
                <span class="pg-timeline-when"><?= e((string) ($phase['when'] ?? '')) ?></span>
                <span class="pg-timeline-phase-title"><?= e((string) ($phase['title'] ?? '')) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <div
        class="pg-timeline-panel"
        id="<?= e($panelId) ?>"
        role="tabpanel"
        data-timeline-panel
        tabindex="-1"
        aria-live="polite"
    >
        <p class="pg-timeline-panel-when" data-timeline-panel-when><?= e((string) ($first['when'] ?? '')) ?></p>
        <p class="pg-timeline-panel-title" data-timeline-panel-title><?= e((string) ($first['title'] ?? '')) ?></p>
        <p class="pg-timeline-panel-narrative" data-timeline-panel-narrative><?= e((string) ($first['narrative'] ?? '')) ?></p>

        <?php if (is_array($first['snapshots'] ?? null) && $first['snapshots'] !== []): ?>
            <ul class="pg-timeline-snapshots" data-timeline-snapshots>
                <?php foreach ($first['snapshots'] as $snap): ?>
                    <?php if (!is_array($snap)) {
                        continue;
                    } ?>
                    <li>
                        <strong><?= e((string) ($snap['label'] ?? '')) ?></strong>
                        <span><?= e((string) ($snap['text'] ?? '')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <ul class="pg-timeline-snapshots" data-timeline-snapshots hidden></ul>
        <?php endif; ?>

        <?php if (is_array($first['captured'] ?? null) && $first['captured'] !== []): ?>
            <div class="pg-timeline-captured" data-timeline-captured-wrap>
                <p class="pg-timeline-captured-label">Knowledge captured this phase</p>
                <ul class="pg-timeline-captured-list" data-timeline-captured>
                    <?php foreach ($first['captured'] as $item): ?>
                        <li><?= e((string) $item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="pg-timeline-captured" data-timeline-captured-wrap hidden>
                <p class="pg-timeline-captured-label">Knowledge captured this phase</p>
                <ul class="pg-timeline-captured-list" data-timeline-captured></ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="pg-timeline-actions">
        <button type="button" class="pg-btn pg-btn-ghost" data-timeline-prev disabled>Earlier</button>
        <button type="button" class="pg-btn pg-btn-secondary" data-timeline-next>Later</button>
    </div>

    <details class="pg-fallback-details">
        <summary>Read full timeline (no JavaScript required)</summary>
        <?php foreach ((array) $phases as $phase): ?>
            <?php if (!is_array($phase)) {
                continue;
            } ?>
            <article class="pg-fallback-block">
                <h4><?= e((string) ($phase['when'] ?? '')) ?> — <?= e((string) ($phase['title'] ?? '')) ?></h4>
                <p><?= e((string) ($phase['narrative'] ?? '')) ?></p>
                <?php if (is_array($phase['snapshots'] ?? null)): ?>
                    <ul>
                        <?php foreach ($phase['snapshots'] as $snap): ?>
                            <?php if (!is_array($snap)) {
                                continue;
                            } ?>
                            <li><strong><?= e((string) ($snap['label'] ?? '')) ?>:</strong> <?= e((string) ($snap['text'] ?? '')) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </details>

    <script type="application/json" data-timeline-data><?= e(json_encode(array_values(array_filter((array) $phases, 'is_array')), JSON_UNESCAPED_UNICODE)) ?></script>
</section>
