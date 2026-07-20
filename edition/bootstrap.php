<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$contentRoot = $root . '/.vibekb';

require_once __DIR__ . '/lib/FrontMatter.php';
require_once __DIR__ . '/lib/Markdown.php';
require_once __DIR__ . '/lib/ContentRepository.php';
require_once __DIR__ . '/lib/helpers.php';

$repo = new ContentRepository($contentRoot);
$project = $repo->project();
$edition = $repo->edition();
$collections = $repo->collections();
