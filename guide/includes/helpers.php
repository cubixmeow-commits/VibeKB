<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function guide_url(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/guide'), '/\\');
    if ($path === '') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function guide_asset(string $relative): string
{
    return guide_url('assets/' . ltrim($relative, '/'));
}

function landing_url(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/guide/index.php';
    $guideDir = dirname($script);
    $root = dirname($guideDir);
    if ($root === '/' || $root === '\\' || $root === '.') {
        return '/';
    }
    return rtrim(str_replace('\\', '/', $root), '/') . '/';
}

function reference_url(string $path = ''): string
{
    $root = rtrim(landing_url(), '/');
    $base = ($root === '' ? '' : $root) . '/edition/';
    if ($path === '') {
        return $base;
    }
    return $base . ltrim($path, '/');
}

function reference_collection_url(string $collection): string
{
    return reference_url('page.php?c=' . rawurlencode($collection));
}

function reference_item_url(string $collection, string $slug): string
{
    return reference_url(
        'page.php?c=' . rawurlencode($collection) . '&id=' . rawurlencode($slug)
    );
}

/**
 * @param array<string, mixed> $vars
 */
function guide_render(string $name, array $vars): void
{
    extract($vars, EXTR_SKIP);
    $template = __DIR__ . '/../templates/' . $name . '.php';
    if (!is_file($template)) {
        http_response_code(500);
        echo 'Template missing: ' . e($name);
        exit;
    }
    require $template;
}
