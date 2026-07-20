<?php
/** @var array $scene */
/** @var string $uid */
/** @var GuideLoader $loader */

$label = (string) ($scene['label'] ?? 'Reference');
$intro = (string) ($scene['intro'] ?? '');
$groups = $scene['groups'] ?? [];
$links = $scene['links'] ?? [];
$home = $scene['home'] ?? null;
$itemLinks = $loader->resolveSources(is_array($links) ? $links : []);
?>
<section class="pg-scene pg-scene-reference" aria-labelledby="<?= e($uid) ?>-h">
    <h3 class="pg-scene-label" id="<?= e($uid) ?>-h"><?= e($label) ?></h3>
    <?php if ($intro !== ''): ?>
        <p class="pg-scene-desc"><?= e($intro) ?></p>
    <?php endif; ?>

    <?php if (is_array($home) && !empty($home['path'])): ?>
        <p class="pg-reference-home">
            <a class="pg-btn pg-btn-primary" href="<?= e(landing_url() . ltrim((string) $home['path'], '/')) ?>">
                <?= e((string) ($home['label'] ?? 'Open reference')) ?>
            </a>
        </p>
    <?php endif; ?>

    <?php if ($itemLinks !== []): ?>
        <ul class="pg-reference-list pg-reference-items">
            <?php foreach ($itemLinks as $source): ?>
                <li>
                    <a href="<?= e((string) $source['url']) ?>">
                        <?= e((string) $source['title']) ?>
                    </a>
                    <?php if (!empty($source['summary'])): ?>
                        <span class="pg-source-summary"><?= e((string) $source['summary']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="pg-reference-groups">
        <?php foreach ((array) $groups as $group): ?>
            <?php if (!is_array($group)) {
                continue;
            } ?>
            <div class="pg-reference-group">
                <h4 class="pg-reference-group-title"><?= e((string) ($group['title'] ?? '')) ?></h4>
                <ul class="pg-reference-list">
                    <?php foreach ((array) ($group['links'] ?? []) as $link): ?>
                        <?php if (!is_array($link)) {
                            continue;
                        } ?>
                        <li>
                            <a href="<?= e(reference_collection_url((string) ($link['collection'] ?? ''))) ?>">
                                <?= e((string) ($link['label'] ?? $link['collection'] ?? '')) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>
