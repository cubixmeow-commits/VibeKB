<?php
/** @var array $project */
/** @var array $edition */
/** @var array $homepage */
?>
<article class="edition-home">
    <header class="edition-hero">
        <p class="eyebrow">Technical publication</p>
        <h1><?= e($edition['title'] ?? 'Current Edition') ?></h1>
        <p class="lede"><?= e($edition['editor_note'] ?? $homepage['intro'] ?? '') ?></p>
        <dl class="edition-meta">
            <div>
                <dt>Project</dt>
                <dd><?= e($project['name'] ?? '') ?></dd>
            </div>
            <div>
                <dt>Version</dt>
                <dd><?= e((string) ($edition['version'] ?? '')) ?></dd>
            </div>
            <div>
                <dt>Published</dt>
                <dd><?= e((string) ($edition['published'] ?? '')) ?></dd>
            </div>
            <div>
                <dt>Status</dt>
                <dd><?= e((string) ($edition['status'] ?? '')) ?></dd>
            </div>
        </dl>
    </header>

    <section class="home-intro" aria-labelledby="intro-heading">
        <h2 id="intro-heading">About this edition</h2>
        <p><?= e($homepage['intro'] ?? '') ?></p>
        <?php if (!empty($project['constraints']) && is_array($project['constraints'])): ?>
            <ul class="constraint-list">
                <?php foreach ($project['constraints'] as $constraint): ?>
                    <li><?= e((string) $constraint) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="section-index" aria-labelledby="sections-heading">
        <h2 id="sections-heading">Contents</h2>
        <ol class="section-list">
            <?php foreach ($homepage['sections'] ?? [] as $section): ?>
                <?php
                $href = !empty($section['slug'])
                    ? item_url((string) $section['collection'], (string) $section['slug'])
                    : collection_url((string) $section['collection']);
                ?>
                <li>
                    <a href="<?= e($href) ?>">
                        <span class="section-title"><?= e($section['title'] ?? '') ?></span>
                        <span class="section-summary"><?= e($section['summary'] ?? '') ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
</article>
