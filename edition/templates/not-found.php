<?php
/** @var string $notFoundMessage */
?>
<article class="article-page">
    <header class="page-header">
        <p class="eyebrow">Missing</p>
        <h1>Not found</h1>
        <p class="lede"><?= e($notFoundMessage ?? 'The requested page does not exist in this edition.') ?></p>
    </header>
    <p><a class="text-link" href="<?= e(edition_url()) ?>">Return to technical reference</a></p>
</article>
