<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$homepage = $repo->homepage();

$pageTitle = ($edition['title'] ?? 'Technical Reference') . ' · ' . ($project['name'] ?? 'VibeKB');
$pageDescription = (string) ($edition['editor_note'] ?? $project['tagline'] ?? '');
$activeCollection = null;
$contentTemplate = 'home';

require __DIR__ . '/templates/layout.php';
