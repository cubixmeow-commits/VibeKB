<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Checklists');
$items = $scene['items'] ?? [];
?>
<section class="pg-scene pg-scene-checklist" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <div class="pg-checklists" data-checklists>
        <?php foreach ((array) $items as $i => $item): ?>
            <?php
            if (!is_array($item)) {
                continue;
            }
            $itemId = $uid . '-c-' . (string) ($item['id'] ?? $i);
            $panelId = $itemId . '-panel';
            $sources = $loader->resolveSources(is_array($item['sources'] ?? null) ? $item['sources'] : []);
            $checks = $item['checks'] ?? [];
            $affects = $item['affects'] ?? [];
            $list = is_array($checks) && $checks !== [] ? $checks : $affects;
            $listKind = is_array($checks) && $checks !== [] ? 'checklist' : 'affects';
            ?>
            <article class="pg-checklist" data-checklist>
                <h4 class="pg-checklist-title" id="<?= e($itemId) ?>-title">
                    <?= e((string) ($item['title'] ?? '')) ?>
                </h4>
                <p class="pg-checklist-intro"><?= e((string) ($item['intro'] ?? '')) ?></p>
                <button
                    type="button"
                    class="pg-btn pg-btn-ghost"
                    data-checklist-toggle
                    aria-expanded="false"
                    aria-controls="<?= e($panelId) ?>"
                >
                    Open guide
                </button>
                <div
                    class="pg-checklist-panel"
                    id="<?= e($panelId) ?>"
                    role="region"
                    aria-labelledby="<?= e($itemId) ?>-title"
                    hidden
                    data-checklist-panel
                >
                    <?php if ($listKind === 'checklist'): ?>
                        <ol class="pg-check-list" data-check-list>
                            <?php foreach ((array) $list as $ci => $check): ?>
                                <li class="pg-check-item" data-check-item>
                                    <label>
                                        <input type="checkbox" data-check-box>
                                        <span><?= e((string) $check) ?></span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <p class="pg-affects-label">This affects:</p>
                        <ul class="pg-affects-list" data-affects-list>
                            <?php foreach ((array) $list as $affect): ?>
                                <li class="pg-affects-item" data-affects-item>
                                    <?= e((string) $affect) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($sources !== []): ?>
                        <p class="pg-source-label">Related knowledge</p>
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
                    <summary>Read without expanding controls</summary>
                    <p><?= e((string) ($item['intro'] ?? '')) ?></p>
                    <?php if ($listKind === 'checklist'): ?>
                        <ol>
                            <?php foreach ((array) $list as $check): ?>
                                <li><?= e((string) $check) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <ul>
                            <?php foreach ((array) $list as $affect): ?>
                                <li><?= e((string) $affect) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </details>
            </article>
        <?php endforeach; ?>
    </div>
</section>
