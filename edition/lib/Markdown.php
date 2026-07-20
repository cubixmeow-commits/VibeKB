<?php

declare(strict_types=1);

/**
 * Small Markdown renderer for VibeKB publication content.
 * Supports headings, paragraphs, emphasis, links, code, lists, and tables.
 */
final class Markdown
{
    public static function toHtml(string $markdown): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
        $lines = explode("\n", $markdown);
        $html = [];
        $paragraph = [];
        $listType = null;
        $tableRows = [];

        $flushParagraph = static function () use (&$html, &$paragraph): void {
            if ($paragraph === []) {
                return;
            }
            $text = trim(implode("\n", $paragraph));
            $paragraph = [];
            if ($text === '') {
                return;
            }
            $html[] = '<p>' . self::inline($text) . '</p>';
        };

        $flushList = static function () use (&$html, &$listType): void {
            if ($listType === null) {
                return;
            }
            $html[] = $listType === 'ol' ? '</ol>' : '</ul>';
            $listType = null;
        };

        $flushTable = static function () use (&$html, &$tableRows): void {
            if ($tableRows === []) {
                return;
            }
            $html[] = '<div class="table-wrap"><table>';
            foreach ($tableRows as $index => $cells) {
                $tag = $index === 0 ? 'th' : 'td';
                $html[] = '<tr>';
                foreach ($cells as $cell) {
                    $html[] = '<' . $tag . '>' . self::inline(trim($cell)) . '</' . $tag . '>';
                }
                $html[] = '</tr>';
                if ($index === 0) {
                    // skip separator row already filtered
                }
            }
            $html[] = '</table></div>';
            $tableRows = [];
        };

        foreach ($lines as $line) {
            if (preg_match('/^\s*\|(.+)\|\s*$/', $line, $m)) {
                $flushParagraph();
                $flushList();
                $cells = array_map('trim', explode('|', trim($m[1])));
                if (preg_match('/^[\s\-:|]+$/', $line)) {
                    continue;
                }
                // Detect separator rows like | --- | --- |
                $isSeparator = true;
                foreach ($cells as $cell) {
                    if (!preg_match('/^:?-+:?$/', $cell)) {
                        $isSeparator = false;
                        break;
                    }
                }
                if ($isSeparator) {
                    continue;
                }
                $tableRows[] = $cells;
                continue;
            }

            if ($tableRows !== []) {
                $flushTable();
            }

            if (trim($line) === '') {
                $flushParagraph();
                $flushList();
                continue;
            }

            if (preg_match('/^(#{1,4})\s+(.+)$/', $line, $m)) {
                $flushParagraph();
                $flushList();
                $level = strlen($m[1]);
                $html[] = '<h' . $level . '>' . self::inline(trim($m[2])) . '</h' . $level . '>';
                continue;
            }

            if (preg_match('/^\s*[-*]\s+(.+)$/', $line, $m)) {
                $flushParagraph();
                if ($listType !== 'ul') {
                    $flushList();
                    $listType = 'ul';
                    $html[] = '<ul>';
                }
                $html[] = '<li>' . self::inline($m[1]) . '</li>';
                continue;
            }

            if (preg_match('/^\s*\d+\.\s+(.+)$/', $line, $m)) {
                $flushParagraph();
                if ($listType !== 'ol') {
                    $flushList();
                    $listType = 'ol';
                    $html[] = '<ol>';
                }
                $html[] = '<li>' . self::inline($m[1]) . '</li>';
                continue;
            }

            $flushList();
            $paragraph[] = $line;
        }

        $flushParagraph();
        $flushList();
        $flushTable();

        return implode("\n", $html);
    }

    private static function inline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text) ?? $text;
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $text) ?? $text;
        $text = preg_replace(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            '<a href="$2">$1</a>',
            $text
        ) ?? $text;
        return $text;
    }
}
