<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$meta = $guide['meta'];
$chapters = $guide['chapters'];

if ($chapters === []) {
    http_response_code(500);
    echo 'Project Guide has no chapters.';
    exit;
}

$renderer->renderShell($meta, $project, $chapters, $chapters[0]);
