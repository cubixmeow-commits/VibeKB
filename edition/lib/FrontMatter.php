<?php

declare(strict_types=1);

/**
 * Minimal YAML-like front matter parser for key: value pairs.
 * Compatible with PHP 8.2 shared hosting (no yaml extension required).
 */
final class FrontMatter
{
    /**
     * @return array{meta: array<string, mixed>, body: string}
     */
    public static function parse(string $raw): array
    {
        $raw = preg_replace("/^\xEF\xBB\xBF/", '', $raw) ?? $raw;
        if (!preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n?(.*)\z/s', $raw, $matches)) {
            return ['meta' => [], 'body' => trim($raw)];
        }

        $meta = [];
        foreach (preg_split('/\r?\n/', trim($matches[1])) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, ':')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode(':', $line, 2));
            $meta[$key] = self::castValue($value);
        }

        return ['meta' => $meta, 'body' => trim($matches[2])];
    }

    private static function castValue(string $value): mixed
    {
        if ($value === '') {
            return '';
        }
        if (preg_match('/^"(.*)"$/', $value, $m) || preg_match("/^'(.*)'$/", $value, $m)) {
            return $m[1];
        }
        $lower = strtolower($value);
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }
        if ($lower === 'null') {
            return null;
        }
        if (preg_match('/^-?\d+$/', $value)) {
            return (int) $value;
        }
        return $value;
    }
}
