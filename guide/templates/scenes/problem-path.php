<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Problems');
$problems = $scene['problems'] ?? [];
?>
<section class="pg-scene pg-scene-problems" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>

    <div class="pg-problems" data-problem-paths>
        <?php foreach ((array) $problems as $i => $problem): ?>
            <?php
            if (!is_array($problem)) {
                continue;
            }
            $problemId = $uid . '-p-' . (string) ($problem['id'] ?? $i);
            $panelId = $problemId . '-panel';
            $sources = $loader->resolveSources(is_array($problem['sources'] ?? null) ? $problem['sources'] : []);
            $steps = $problem['steps'] ?? [];
            $alignment = $problem['alignment'] ?? [];
            ?>
            <article class="pg-problem" data-problem>
                <h4 class="pg-problem-title" id="<?= e($problemId) ?>-title">
                    <?= e((string) ($problem['title'] ?? '')) ?>
                </h4>
                <p class="pg-problem-summary"><?= e((string) ($problem['summary'] ?? '')) ?></p>
                <button
                    type="button"
                    class="pg-btn pg-btn-ghost"
                    data-problem-toggle
                    aria-expanded="false"
                    aria-controls="<?= e($panelId) ?>"
                >
                    Walk through
                </button>

                <div
                    class="pg-problem-panel"
                    id="<?= e($panelId) ?>"
                    role="region"
                    aria-labelledby="<?= e($problemId) ?>-title"
                    hidden
                    data-problem-panel
                >
                    <?php if (is_array($alignment) && $alignment !== []): ?>
                        <p class="pg-align-intro">These five must agree:</p>
                        <ul class="pg-alignment" data-alignment>
                            <?php foreach ($alignment as $piece): ?>
                                <li class="pg-alignment-item" data-alignment-item>
                                    <span><?= e((string) $piece) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($problem['note'])): ?>
                            <p class="pg-problem-note"><?= e((string) $problem['note']) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (is_array($steps) && $steps !== []): ?>
                        <ol class="pg-trouble-steps" data-trouble-steps>
                            <?php foreach ($steps as $si => $step): ?>
                                <?php if (!is_array($step)) {
                                    continue;
                                } ?>
                                <li class="pg-trouble-step<?= $si === 0 ? ' is-active' : '' ?>" data-trouble-step>
                                    <button
                                        type="button"
                                        class="pg-trouble-btn"
                                        data-trouble-activate
                                        aria-pressed="<?= $si === 0 ? 'true' : 'false' ?>"
                                    >
                                        <span class="pg-trouble-num"><?= (int) $si + 1 ?></span>
                                        <span class="pg-trouble-title"><?= e((string) ($step['title'] ?? '')) ?></span>
                                    </button>
                                    <p class="pg-trouble-text"><?= e((string) ($step['text'] ?? '')) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <div class="pg-flow-actions">
                            <button type="button" class="pg-btn pg-btn-ghost" data-trouble-prev disabled>Previous</button>
                            <button type="button" class="pg-btn pg-btn-secondary" data-trouble-next>Next check</button>
                        </div>
                    <?php endif; ?>

                    <?php if ($sources !== []): ?>
                        <p class="pg-source-label">Debugging guides</p>
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
                    <summary>Read the full diagnostic (no JavaScript required)</summary>
                    <?php if (is_array($alignment) && $alignment !== []): ?>
                        <ul class="pg-alignment">
                            <?php foreach ($alignment as $piece): ?>
                                <li><?= e((string) $piece) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($problem['note'])): ?>
                            <p><?= e((string) $problem['note']) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (is_array($steps) && $steps !== []): ?>
                        <ol>
                            <?php foreach ($steps as $step): ?>
                                <?php if (!is_array($step)) {
                                    continue;
                                } ?>
                                <li>
                                    <strong><?= e((string) ($step['title'] ?? '')) ?></strong>
                                    — <?= e((string) ($step['text'] ?? '')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </details>
            </article>
        <?php endforeach; ?>
    </div>
</section>
