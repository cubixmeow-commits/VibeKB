<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$collectionKey = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) ($_GET['c'] ?? ''))) ?? '';
$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) ($_GET['id'] ?? ''))) ?? '';

$collectionMeta = $repo->collectionMeta($collectionKey);
if ($collectionKey === '' || $collectionMeta === null) {
    http_response_code(404);
    $pageTitle = 'Not found · ' . ($project['name'] ?? 'VibeKB');
    $pageDescription = 'Collection not found.';
    $activeCollection = null;
    $contentTemplate = 'not-found';
    $notFoundMessage = 'That collection is not part of this edition.';
    require __DIR__ . '/templates/layout.php';
    exit;
}

$templateName = (string) ($collectionMeta['template'] ?? $collectionKey);
$activeCollection = $collectionKey;

if ($slug !== '') {
    $item = $repo->getItem($collectionKey, $slug);
    if ($item === null) {
        http_response_code(404);
        $pageTitle = 'Not found · ' . ($project['name'] ?? 'VibeKB');
        $pageDescription = 'Entry not found.';
        $contentTemplate = 'not-found';
        $notFoundMessage = 'That entry could not be found in ' . ($collectionMeta['title'] ?? $collectionKey) . '.';
        require __DIR__ . '/templates/layout.php';
        exit;
    }

    $siblings = $repo->listItems($collectionKey);
    $pageTitle = ($item['meta']['title'] ?? $slug) . ' · ' . ($project['name'] ?? 'VibeKB');
    $pageDescription = (string) ($item['meta']['summary'] ?? $collectionMeta['description'] ?? '');
    $contentTemplate = is_file(__DIR__ . '/templates/' . $templateName . '.php') ? $templateName : 'article';
    require __DIR__ . '/templates/layout.php';
    exit;
}

$items = $repo->listItems($collectionKey);
$pageTitle = ($collectionMeta['title'] ?? $collectionKey) . ' · ' . ($project['name'] ?? 'VibeKB');
$pageDescription = (string) ($collectionMeta['description'] ?? '');
$contentTemplate = is_file(__DIR__ . '/templates/' . $templateName . '.php') ? $templateName : 'collection';
require __DIR__ . '/templates/layout.php';
