<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Decision history');
$description = (string) ($scene['description'] ?? '');
$decisions = $scene['decisions'] ?? [];
?>
<section class="pg-scene pg-scene-decisions" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($description !== ''): ?>
        <p class="pg-scene-desc"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="pg-decisions" data-decision-history>
        <?php foreach ((array) $decisions as $i => $decision): ?>
            <?php
            if (!is_array($decision)) {
                continue;
            }
            $decId = $uid . '-d-' . (string) ($decision['id'] ?? $i);
            $panelId = $decId . '-panel';
            $sources = $loader->resolveSources(is_array($decision['sources'] ?? null) ? $decision['sources'] : []);
            ?>
            <article class="pg-decision" data-decision>
                <header class="pg-decision-header">
                    <p class="pg-decision-when"><?= e((string) ($decision['when'] ?? '')) ?></p>
                    <h4 class="pg-decision-title" id="<?= e($decId) ?>-title"><?= e((string) ($decision['title'] ?? '')) ?></h4>
                    <p class="pg-decision-outcome">
                        <span class="pg-decision-badge"><?= e((string) ($decision['status'] ?? 'decided')) ?></span>
                        <?= e((string) ($decision['outcome'] ?? '')) ?>
                    </p>
                </header>
                <button
                    type="button"
                    class="pg-btn pg-btn-ghost pg-decision-toggle"
                    data-decision-toggle
                    aria-expanded="false"
                    aria-controls="<?= e($panelId) ?>"
                >
                    See the reasoning
                </button>
                <div
                    class="pg-decision-panel"
                    id="<?= e($panelId) ?>"
                    role="region"
                    aria-labelledby="<?= e($decId) ?>-title"
                    hidden
                    data-decision-panel
                >
                    <?php if (!empty($decision['context'])): ?>
                        <p class="pg-decision-context"><?= e((string) $decision['context']) ?></p>
                    <?php endif; ?>

                    <?php if (is_array($decision['rejected'] ?? null) && $decision['rejected'] !== []): ?>
                        <div class="pg-decision-rejected">
                            <p class="pg-decision-subhead">Rejected or deferred</p>
                            <ul>
                                <?php foreach ($decision['rejected'] as $rej): ?>
                                    <?php if (!is_array($rej)) {
                                        continue;
                                    } ?>
                                    <li>
                                        <strong><?= e((string) ($rej['option'] ?? '')) ?></strong>
                                        <span><?= e((string) ($rej['reason'] ?? '')) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (is_array($decision['consequences'] ?? null) && $decision['consequences'] !== []): ?>
                        <div class="pg-decision-consequences">
                            <p class="pg-decision-subhead">What this means now</p>
                            <ul>
                                <?php foreach ($decision['consequences'] as $con): ?>
                                    <li><?= e((string) $con) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($sources !== []): ?>
                        <p class="pg-source-label">Recorded in the knowledge base</p>
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
                    <summary>Read decision (no JavaScript required)</summary>
                    <?php if (!empty($decision['context'])): ?>
                        <p><?= e((string) $decision['context']) ?></p>
                    <?php endif; ?>
                    <?php if (is_array($decision['rejected'] ?? null)): ?>
                        <p><strong>Rejected:</strong></p>
                        <ul>
                            <?php foreach ($decision['rejected'] as $rej): ?>
                                <?php if (!is_array($rej)) {
                                    continue;
                                } ?>
                                <li><?= e((string) ($rej['option'] ?? '')) ?> — <?= e((string) ($rej['reason'] ?? '')) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </details>
            </article>
        <?php endforeach; ?>
    </div>
</section>
