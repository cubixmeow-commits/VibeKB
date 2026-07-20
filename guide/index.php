<?php

declare(strict_types=1);

/**
 * VibeKB V1 — the Software Guide front controller.
 *
 * A single entry point that routes by `?view=`. Query-string routing keeps the
 * app deployable on ordinary cPanel shared hosting (no rewrite rules) and in a
 * subfolder. Every view is rendered from repository-owned content in
 * `../.vibekb/`.
 */

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/Content.php';

$contentRoot = dirname(__DIR__) . '/.vibekb';

// Development vs production error posture.
$devMode = (getenv('VIBEKB_DEV') === '1')
    || in_array(($_SERVER['SERVER_NAME'] ?? ''), ['localhost', '127.0.0.1'], true);

$content = new Content($contentRoot);

try {
    $content->load();
} catch (Throwable $e) {
    http_response_code(500);
    if ($devMode) {
        echo '<pre>Content failed to load: ' . h($e->getMessage()) . "\n" . h($e->getTraceAsString()) . '</pre>';
    } else {
        echo 'The guide is temporarily unavailable.';
    }
    return;
}

$view = (string) ($_GET['view'] ?? 'overview');

// Whitelist of views -> template files. `functionality` is special-cased
// because it renders either the index or a detail page depending on `?id`.
$routes = [
    'overview' => 'overview',
    'functionality' => null,
    'how-it-works' => 'how-it-works',
    'data' => 'data',
    'files' => 'files',
    'current-work' => 'current-work',
    'changes' => 'changes',
    'why' => 'why',
    'handoff' => 'handoff',
    'reference' => 'reference',
];

$navItems = [
    ['view' => 'overview', 'label' => 'Overview'],
    ['view' => 'functionality', 'label' => 'Functionality'],
    ['view' => 'how-it-works', 'label' => 'How it works'],
    ['view' => 'data', 'label' => 'Data &amp; storage'],
    ['view' => 'files', 'label' => 'Files that matter'],
    ['view' => 'current-work', 'label' => 'Current AI work'],
    ['view' => 'changes', 'label' => 'Changes'],
    ['view' => 'why', 'label' => 'Why it works this way'],
    ['view' => 'handoff', 'label' => 'AI handoff'],
    ['view' => 'reference', 'label' => 'Reference'],
];

$identity = $content->projectDoc('identity');
$projectName = (string) ($identity['meta']['title'] ?? 'Software Guide');

$template = null;
$pageTitle = 'Overview';
$vars = ['content' => $content, 'projectName' => $projectName, 'devMode' => $devMode];

if ($view === 'functionality') {
    $id = isset($_GET['id']) ? (string) preg_replace('/[^a-z0-9\-]/i', '', (string) $_GET['id']) : '';
    if ($id !== '') {
        $record = $content->functionality($id);
        if ($record === null) {
            http_response_code(404);
            $template = 'not-found';
            $pageTitle = 'Not found';
            $vars['missing'] = 'functionality: ' . $id;
        } else {
            $template = 'functionality-detail';
            $pageTitle = (string) ($record['meta']['title'] ?? 'Functionality');
            $vars['record'] = $record;
            $vars['id'] = $id;
        }
    } else {
        $template = 'functionality-index';
        $pageTitle = 'Functionality';
    }
} elseif (array_key_exists($view, $routes) && $routes[$view] !== null) {
    $template = $routes[$view];
    $pageTitle = ucfirst(str_replace('-', ' ', $view));
} else {
    http_response_code(404);
    $template = 'not-found';
    $pageTitle = 'Not found';
    $vars['missing'] = 'view: ' . $view;
}

$vars['view'] = $view;
$vars['pageTitle'] = $pageTitle;
$vars['navItems'] = $navItems;
$vars['bodyTemplate'] = $template;

render_view('layout', $vars);
