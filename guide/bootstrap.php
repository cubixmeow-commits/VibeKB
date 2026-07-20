<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$contentRoot = $root . '/.vibekb';

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/GuideLoader.php';
require_once __DIR__ . '/includes/GuideRenderer.php';

// Reuse Markdown/FrontMatter from the edition engine for knowledge resolution.
require_once $root . '/edition/lib/FrontMatter.php';
require_once $root . '/edition/lib/Markdown.php';
require_once $root . '/edition/lib/ContentRepository.php';

$knowledge = new ContentRepository($contentRoot);
$loader = new GuideLoader($contentRoot . '/guide', $knowledge);
$guide = $loader->load();
$project = $knowledge->project();
$renderer = new GuideRenderer($loader);
