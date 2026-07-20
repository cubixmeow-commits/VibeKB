<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Explanations');
$cards = $scene['cards'] ?? [];
?>
<section class="pg-scene pg-scene-cards" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <div class="pg-cards" data-interactive-cards>
        <?php foreach ((array) $cards as $i => $card): ?>
            <?php
            if (!is_array($card)) {
                continue;
            }
            $cardId = $uid . '-card-' . (string) ($card['id'] ?? $i);
            $panelId = $cardId . '-panel';
            $sources = $loader->resolveSources(is_array($card['sources'] ?? null) ? $card['sources'] : []);
            ?>
            <article class="pg-card" data-card>
                <h4 class="pg-card-title" id="<?= e($cardId) ?>-title">
                    <?= e((string) ($card['title'] ?? '')) ?>
                </h4>
                <p class="pg-card-teaser"><?= e((string) ($card['teaser'] ?? '')) ?></p>
                <button
                    type="button"
                    class="pg-btn pg-btn-ghost pg-card-toggle"
                    data-card-toggle
                    aria-expanded="false"
                    aria-controls="<?= e($panelId) ?>"
                >
                    Explain
                </button>
                <div
                    class="pg-card-panel"
                    id="<?= e($panelId) ?>"
                    role="region"
                    aria-labelledby="<?= e($cardId) ?>-title"
                    hidden
                    data-card-panel
                >
                    <ul class="pg-point-list">
                        <?php foreach ((array) ($card['points'] ?? []) as $point): ?>
                            <li><?= e((string) $point) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($sources !== []): ?>
                        <p class="pg-source-label">From the knowledge base</p>
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
                <noscript>
                    <ul class="pg-point-list">
                        <?php foreach ((array) ($card['points'] ?? []) as $point): ?>
                            <li><?= e((string) $point) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </noscript>
            </article>
        <?php endforeach; ?>
    </div>
    <details class="pg-fallback-details">
        <summary>Show all explanations (always available)</summary>
        <?php foreach ((array) $cards as $card): ?>
            <?php if (!is_array($card)) {
                continue;
            } ?>
            <div class="pg-fallback-block">
                <h4><?= e((string) ($card['title'] ?? '')) ?></h4>
                <ul class="pg-point-list">
                    <?php foreach ((array) ($card['points'] ?? []) as $point): ?>
                        <li><?= e((string) $point) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </details>
</section>
