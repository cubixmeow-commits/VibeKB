<?php
/**
 * Functionality Detail — the most important view. The narrative sections (A–Q)
 * come from the record's Markdown body; the structured header and the
 * relationship rails are driven by front matter and resolved against other
 * records so every link is live and validated.
 *
 * @var Content $content
 * @var array<string, mixed> $record
 * @var string $id
 */
$m = $record['meta'];
$deps = $content->resolveFunctionality($m['depends_on'] ?? []);
$dependents = $content->dependentsOf($id);
$mem = $content->resolveMemory($m['related_memory'] ?? []);
$fileRecords = $content->filesForFunctionality($id);
$reads = is_array($m['reads'] ?? null) ? $m['reads'] : [];
$writes = is_array($m['writes'] ?? null) ? $m['writes'] : [];
?>
<article class="view view-func-detail">
    <p class="breadcrumb"><a href="<?= h(guide_url('functionality')) ?>">← All functionality</a></p>

    <header class="page-head">
        <p class="eyebrow"><?= h(ucfirst(str_replace('-', ' ', (string) ($m['area'] ?? 'functionality')))) ?></p>
        <h1><?= h((string) ($m['title'] ?? $id)) ?></h1>
        <p class="lede"><?= h((string) ($m['summary'] ?? '')) ?></p>
        <div class="badge-row">
            <?= status_badge((string) ($m['status'] ?? 'unknown')) ?>
            <?php if (($m['verification'] ?? '') !== ''): ?><?= verification_badge((string) $m['verification']) ?><?php endif; ?>
            <?= badge(!empty($m['user_facing']) ? 'User-facing' : 'System', !empty($m['user_facing']) ? 'info' : 'muted') ?>
        </div>
    </header>

    <div class="detail-grid">
        <div class="detail-main">
            <?php if (($record['html'] ?? '') !== ''): ?>
                <div class="prose"><?= $record['html'] ?></div>
            <?php else: ?>
                <p class="muted">No narrative recorded for this functionality yet.</p>
            <?php endif; ?>
        </div>

        <aside class="detail-rail" aria-label="Connections">
            <div class="rail-card">
                <h2>At a glance</h2>
                <dl class="rail-dl">
                    <?php if (($m['trigger'] ?? '') !== ''): ?>
                        <dt>Trigger</dt><dd><?= h((string) $m['trigger']) ?></dd>
                    <?php endif; ?>
                    <dt>Status</dt><dd><?= status_badge((string) ($m['status'] ?? 'unknown')) ?></dd>
                    <?php if (($m['verification'] ?? '') !== ''): ?>
                        <dt>Verification</dt><dd><?= verification_badge((string) $m['verification']) ?></dd>
                    <?php endif; ?>
                    <dt>Updated</dt><dd><?= h((string) ($m['updated'] ?? 'unknown')) ?></dd>
                </dl>
            </div>

            <div class="rail-card">
                <h2>Data</h2>
                <p><strong>Reads:</strong> <?= file_chips($reads) ?></p>
                <p><strong>Writes:</strong> <?= file_chips($writes) ?></p>
                <?php if (($m['config'] ?? '') !== '' && $m['config'] !== []): ?>
                    <p><strong>Config:</strong> <?= file_chips($m['config']) ?></p>
                <?php endif; ?>
            </div>

            <div class="rail-card">
                <h2>Files</h2>
                <?php if ($fileRecords !== []): ?>
                    <ul class="rail-list">
                        <?php foreach ($fileRecords as $f): ?>
                            <li>
                                <a href="<?= h(guide_url('files')) ?>#<?= h(rawurlencode((string) $f['path'])) ?>"><code><?= h((string) $f['path']) ?></code></a>
                                <?= badge(safety_label((string) ($f['safety'] ?? 'unknown')), safety_tone((string) ($f['safety'] ?? ''))) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?= file_chips($m['files'] ?? []) ?></p>
                <?php endif; ?>
            </div>

            <div class="rail-card">
                <h2>Depends on</h2>
                <p><?= functionality_chips($deps) ?></p>
                <h2>Depended on by</h2>
                <p><?= functionality_chips($dependents) ?></p>
            </div>

            <div class="rail-card">
                <h2>Why / warnings</h2>
                <p><?= memory_chips($mem) ?></p>
            </div>
        </aside>
    </div>
</article>
