<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function edition_url(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/edition'), '/\\');
    if ($path === '') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function item_url(string $collection, string $slug): string
{
    return edition_url('page.php?c=' . rawurlencode($collection) . '&id=' . rawurlencode($slug));
}

function collection_url(string $collection): string
{
    return edition_url('page.php?c=' . rawurlencode($collection));
}

function landing_url(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/edition/index.php';
    $editionDir = dirname($script);
    $root = dirname($editionDir);
    if ($root === '/' || $root === '\\' || $root === '.') {
        return '/';
    }
    return rtrim(str_replace('\\', '/', $root), '/') . '/';
}

function severity_class(?string $severity): string
{
    $severity = strtolower((string) $severity);
    return match ($severity) {
        'critical' => 'sev-critical',
        'high' => 'sev-high',
        'medium' => 'sev-medium',
        'low' => 'sev-low',
        default => 'sev-none',
    };
}

function render_template(string $name, array $vars): void
{
    extract($vars, EXTR_SKIP);
    $template = __DIR__ . '/../templates/' . $name . '.php';
    if (!is_file($template)) {
        http_response_code(500);
        echo 'Template missing.';
        exit;
    }
    require $template;
}
