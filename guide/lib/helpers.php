<?php

declare(strict_types=1);

/**
 * Shared helpers for the VibeKB V1 guide: escaping, routing, and the
 * controlled vocabularies the content model validates against.
 */

require_once __DIR__ . '/UrlStrategy.php';

/** HTML-escape a value for output. */
function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * The current URL strategy. Pass a strategy to switch modes (the static
 * generator does this once per page); call with no argument to read it. The
 * dynamic strategy is the default so the PHP guide behaves exactly as before.
 */
function guide_url_strategy(?UrlStrategy $set = null): UrlStrategy
{
    static $current = null;
    if ($set !== null) {
        $current = $set;
    }
    if ($current === null) {
        $current = new DynamicUrlStrategy();
    }
    return $current;
}

/**
 * Cache-busting/relative URL for a guide asset. In dynamic mode this appends
 * the file's modification time as `?v=` (no build step needed); in static mode
 * it returns a deterministic relative path.
 *
 * @param string $rel path relative to the guide directory, e.g. "assets/css/guide.css"
 */
function guide_asset(string $rel): string
{
    return guide_url_strategy()->asset($rel);
}

/** URL of the site root (parent of the guide directory). */
function site_root_url(): string
{
    return guide_url_strategy()->siteRoot();
}

/**
 * Build an in-app URL for a view.
 *
 * @param array<string, string> $params
 */
function guide_url(string $view = '', array $params = []): string
{
    return guide_url_strategy()->view($view, $params);
}

/** URL for a single functionality record. */
function functionality_url(string $id): string
{
    return guide_url_strategy()->functionality($id);
}

/** URL for a memory record (decision, warning, assumption, ...). */
function memory_url(string $type, string $id): string
{
    return guide_url_strategy()->memory($type, $id);
}

/** URL for the diagrams page, optionally anchored to a diagram id. */
function diagram_url(string $id = ''): string
{
    return guide_url_strategy()->diagram($id);
}

/** Allowed functionality statuses. */
function status_vocabulary(): array
{
    return [
        'implemented' => 'Implemented',
        'partial' => 'Partially implemented',
        'planned' => 'Planned',
        'experimental' => 'Experimental',
        'disabled' => 'Disabled',
        'deprecated' => 'Deprecated',
        'broken' => 'Broken',
        'unknown' => 'Unknown',
        'needs-verification' => 'Needs verification',
    ];
}

/** Allowed verification / provenance states. */
function verification_vocabulary(): array
{
    return [
        'verified-by-test' => 'Verified by test',
        'verified-manually' => 'Verified manually',
        'verified-from-source' => 'Verified from source',
        'inferred-from-source' => 'Inferred from source',
        'reported-by-developer' => 'Reported by developer',
        'not-verified' => 'Not verified',
        'verification-failed' => 'Verification failed',
        'superseded' => 'Superseded',
        'contradicted' => 'Contradicted',
    ];
}

/** Allowed file safety levels. */
function safety_vocabulary(): array
{
    return [
        'presentation-only' => 'Presentation only',
        'low-impact' => 'Low impact',
        'moderate-impact' => 'Moderate impact',
        'understand-dependencies-first' => 'Understand dependencies first',
        'high-impact' => 'High impact',
        'generated-or-managed' => 'Generated / managed elsewhere',
        'unknown' => 'Unknown',
    ];
}

function status_label(string $status): string
{
    return status_vocabulary()[$status] ?? ucfirst(str_replace('-', ' ', $status));
}

function verification_label(string $v): string
{
    return verification_vocabulary()[$v] ?? ucfirst(str_replace('-', ' ', $v));
}

function safety_label(string $s): string
{
    return safety_vocabulary()[$s] ?? ucfirst(str_replace('-', ' ', $s));
}

/** CSS modifier class for a status badge. */
function status_tone(string $status): string
{
    return match ($status) {
        'implemented' => 'ok',
        'partial', 'experimental', 'needs-verification' => 'warn',
        'planned' => 'info',
        'disabled', 'deprecated' => 'muted',
        'broken' => 'danger',
        default => 'unknown',
    };
}

/** CSS modifier class for a verification badge. */
function verification_tone(string $v): string
{
    return match ($v) {
        'verified-by-test', 'verified-manually', 'verified-from-source' => 'ok',
        'inferred-from-source', 'reported-by-developer' => 'info',
        'not-verified', 'needs-verification' => 'warn',
        'verification-failed', 'contradicted', 'superseded' => 'danger',
        default => 'unknown',
    };
}

/** CSS modifier class for a file safety badge. */
function safety_tone(string $s): string
{
    return match ($s) {
        'presentation-only', 'low-impact' => 'ok',
        'moderate-impact' => 'info',
        'understand-dependencies-first' => 'warn',
        'high-impact' => 'danger',
        default => 'unknown',
    };
}

/** CSS modifier class for a memory-record severity. */
function severity_tone(string $s): string
{
    return match (strtolower($s)) {
        'critical' => 'danger',
        'high' => 'danger',
        'medium' => 'warn',
        'low' => 'info',
        default => 'unknown',
    };
}

/**
 * Render a template file with the given variables. Templates receive $vars
 * as extracted locals plus the shared $app context.
 *
 * @param array<string, mixed> $vars
 */
function render_view(string $template, array $vars): void
{
    $file = dirname(__DIR__) . '/templates/' . $template . '.php';
    if (!is_file($file)) {
        http_response_code(500);
        echo 'View template missing.';
        return;
    }
    extract($vars, EXTR_SKIP);
    require $file;
}

/** Render a status badge chip. */
function badge(string $label, string $tone): string
{
    return '<span class="badge badge--' . h($tone) . '">' . h($label) . '</span>';
}

/** Status badge for a functionality status value. */
function status_badge(string $status): string
{
    return badge(status_label($status), status_tone($status));
}

/** Verification badge for a verification/provenance value. */
function verification_badge(string $v): string
{
    return badge(verification_label($v), verification_tone($v));
}

/**
 * Render a list of resolved functionality links as chips.
 *
 * @param list<array{id: string, title: string, resolved: bool}> $items
 */
function functionality_chips(array $items): string
{
    if ($items === []) {
        return '<span class="muted">None.</span>';
    }
    $out = [];
    foreach ($items as $it) {
        if ($it['resolved']) {
            $out[] = '<a class="chip" href="' . h(functionality_url($it['id'])) . '">' . h($it['title']) . '</a>';
        } else {
            $out[] = '<span class="chip chip--broken" title="Unresolved reference">' . h($it['title']) . ' ⚠</span>';
        }
    }
    return implode(' ', $out);
}

/**
 * Render a list of resolved memory references as chips.
 *
 * @param list<array{type: string, id: string, title: string, resolved: bool}> $items
 */
function memory_chips(array $items): string
{
    if ($items === []) {
        return '<span class="muted">None.</span>';
    }
    $out = [];
    foreach ($items as $it) {
        if ($it['resolved']) {
            $out[] = '<a class="chip" href="' . h(memory_url($it['type'], $it['id'])) . '">' . h($it['title']) . '</a>';
        } else {
            $out[] = '<span class="chip chip--broken" title="Unresolved reference">' . h($it['title']) . ' ⚠</span>';
        }
    }
    return implode(' ', $out);
}

/** Render a list of file paths as monospace chips. */
function file_chips(mixed $paths): string
{
    $list = is_array($paths) ? $paths : ($paths === null || $paths === '' ? [] : [$paths]);
    if ($list === []) {
        return '<span class="muted">None.</span>';
    }
    $out = [];
    foreach ($list as $p) {
        $out[] = '<code class="chip chip--file">' . h((string) $p) . '</code>';
    }
    return implode(' ', $out);
}

/*
 * Count vocabulary.
 *
 * VibeKB counts three different things and they must never be conflated:
 *   - functional AREAS  — grouped product categories (functionality/index.json).
 *   - functionality RECORDS — individual behaviours (functionality/records/*.md).
 *   - STATUS counts — records tallied by lifecycle status.
 * Every total the interface prints must identify its unit. These helpers make
 * that the default path so a contradictory or unit-less total is hard to ship.
 */

/** "N functionality records across M functional areas". */
function count_records_phrase(int $records, int $areas): string
{
    return $records . ' functionality record' . ($records === 1 ? '' : 's')
        . ' across ' . $areas . ' functional area' . ($areas === 1 ? '' : 's');
}

/**
 * Render the status tally as unit-labelled badges (e.g. "Implemented · 23").
 *
 * @param array<string, int> $statusCounts
 */
function status_count_badges(array $statusCounts): string
{
    $out = [];
    foreach (status_vocabulary() as $key => $label) {
        if (!empty($statusCounts[$key])) {
            $out[] = badge($label . ' · ' . $statusCounts[$key], status_tone($key));
        }
    }
    return $out === [] ? '<span class="muted">No records yet.</span>' : implode(' ', $out);
}
