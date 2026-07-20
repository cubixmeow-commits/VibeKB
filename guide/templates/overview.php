<?php
/**
 * Software Overview — the first screen. Builds a correct mental model of what
 * the software currently does before any browsing.
 *
 * @var Content $content
 * @var string $projectName
 */
$identity = $content->projectDoc('identity');
$currentState = $content->projectDoc('current-state');
$statusCounts = $content->statusCounts();
$groups = $content->functionalityGroups();
$warnings = $content->memoryByType('warnings');
$work = $content->currentWork();
$handoff = $content->handoff();
$mentalModel = $content->systemDoc('mental-model');
$oneLiner = (string) ($identity['meta']['one_liner'] ?? $identity['meta']['summary'] ?? '');
$total = array_sum($statusCounts);
$example = is_array($content->manifest()['example_project'] ?? null) ? $content->manifest()['example_project'] : [];
$exampleRepo = (string) ($example['source_repository'] ?? '');
?>
<article class="view view-overview">

    <header class="page-head">
        <p class="eyebrow">Software overview</p>
        <h1><?= h($projectName) ?></h1>
        <?php if ($oneLiner !== ''): ?>
            <p class="lede"><?= h($oneLiner) ?></p>
        <?php endif; ?>
        <?php if (($identity['meta']['primary_outcome'] ?? '') !== ''): ?>
            <p class="sub"><strong>What it gives you:</strong> <?= h((string) $identity['meta']['primary_outcome']) ?></p>
        <?php endif; ?>
        <p class="provenance-note">
            <?= h($projectName) ?> is the real application VibeKB is explaining here — it is not bundled into
            VibeKB. This model was derived read-only from the source
            <?php if ($exampleRepo !== ''): ?>(<a href="<?= h($exampleRepo) ?>" rel="noopener noreferrer"><?= h($exampleRepo) ?></a>)<?php endif; ?>
            and can go stale; re-verify against the source before changing any functionality claim.
        </p>
    </header>

    <section class="panel-grid" aria-label="At a glance">
        <div class="panel">
            <h2>What it currently does</h2>
            <p><?= (int) $total ?> functional area<?= $total === 1 ? '' : 's' ?> tracked. Status breakdown:</p>
            <p class="badge-row">
                <?php foreach (status_vocabulary() as $key => $label): ?>
                    <?php if (!empty($statusCounts[$key])): ?>
                        <?= badge($label . ' · ' . $statusCounts[$key], status_tone($key)) ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>
            <p><a class="text-link" href="<?= h(guide_url('functionality')) ?>">Browse all functionality →</a></p>
        </div>

        <div class="panel">
            <h2>The mental model</h2>
            <?php if ($mentalModel !== null && ($mentalModel['meta']['summary'] ?? '') !== ''): ?>
                <p><?= h((string) $mentalModel['meta']['summary']) ?></p>
            <?php endif; ?>
            <p><a class="text-link" href="<?= h(guide_url('how-it-works')) ?>">How it works →</a></p>
        </div>

        <div class="panel">
            <h2>Where data lives</h2>
            <p><?= h((string) ($content->systemDoc('storage')['meta']['summary'] ?? 'See the data and storage view.')) ?></p>
            <p><a class="text-link" href="<?= h(guide_url('data')) ?>">Data &amp; storage →</a></p>
        </div>
    </section>

    <section aria-labelledby="ov-func">
        <h2 id="ov-func">Current functionality</h2>
        <?php foreach ($groups as $group): ?>
            <div class="group-block">
                <h3><?= h($group['title']) ?></h3>
                <?php if ($group['description'] !== ''): ?><p class="muted"><?= h($group['description']) ?></p><?php endif; ?>
                <ul class="func-line-list">
                    <?php foreach ($group['records'] as $rec): $m = $rec['meta']; ?>
                        <li>
                            <a href="<?= h(functionality_url((string) $m['id'])) ?>"><?= h((string) ($m['title'] ?? $m['id'])) ?></a>
                            <?= status_badge((string) ($m['status'] ?? 'unknown')) ?>
                            <span class="func-line-list__summary"><?= h((string) ($m['summary'] ?? '')) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>

    <div class="split">
        <section aria-labelledby="ov-warn" class="callout callout--warn">
            <h2 id="ov-warn">Active warnings</h2>
            <?php if ($warnings === []): ?>
                <p class="muted">No active warnings recorded.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($warnings as $wid => $w): ?>
                        <li>
                            <a href="<?= h(memory_url('warnings', (string) $wid)) ?>"><?= h((string) ($w['meta']['title'] ?? $wid)) ?></a>
                            <?= badge(ucfirst((string) ($w['meta']['severity'] ?? 'severity')), severity_tone((string) ($w['meta']['severity'] ?? ''))) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section aria-labelledby="ov-work" class="callout callout--work">
            <h2 id="ov-work">What AI is doing now</h2>
            <?php if ($work !== null): ?>
                <p><strong><?= h((string) ($work['meta']['title'] ?? 'Current work')) ?></strong>
                   <?= badge(str_replace('-', ' ', (string) ($work['meta']['status'] ?? 'unknown')), 'info') ?></p>
                <p><?= h((string) ($work['meta']['summary'] ?? '')) ?></p>
                <p><a class="text-link" href="<?= h(guide_url('current-work')) ?>">See current AI work →</a></p>
            <?php else: ?>
                <p class="muted">No active AI work recorded.</p>
            <?php endif; ?>
        </section>
    </div>

    <section class="next-step" aria-labelledby="ov-next">
        <h2 id="ov-next">Recommended starting point</h2>
        <?php
        $next = $handoff['meta']['summary'] ?? '';
        ?>
        <p><?= h((string) ($next !== '' ? $next : 'Start with the functionality index, then read How it works.')) ?></p>
        <p class="button-row">
            <a class="btn btn--primary" href="<?= h(guide_url('functionality')) ?>">Explore functionality</a>
            <a class="btn" href="<?= h(guide_url('handoff')) ?>">Read the AI handoff</a>
        </p>
    </section>

    <p class="updated-note">Last meaningful update: <?= h((string) ($currentState['meta']['updated'] ?? 'unknown')) ?></p>
</article>
