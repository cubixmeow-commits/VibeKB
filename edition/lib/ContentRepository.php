<?php

declare(strict_types=1);

final class ContentRepository
{
    public function __construct(private readonly string $root)
    {
    }

    public function project(): array
    {
        return $this->readJson('project.json');
    }

    public function edition(): array
    {
        return $this->readJson('edition.json');
    }

    public function homepage(): array
    {
        return $this->readJson('homepage.json');
    }

    public function collections(): array
    {
        return $this->readJson('collections.json');
    }

    public function collectionMeta(string $collection): ?array
    {
        $all = $this->collections();
        return $all[$collection] ?? null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listItems(string $collection): array
    {
        $dir = $this->root . '/' . $collection;
        if (!is_dir($dir)) {
            return [];
        }

        $items = [];
        foreach (glob($dir . '/*.md') ?: [] as $file) {
            $item = $this->loadMarkdownFile($collection, $file);
            if ($item !== null) {
                $items[] = $item;
            }
        }

        usort($items, static function (array $a, array $b): int {
            $ao = $a['meta']['order'] ?? 999;
            $bo = $b['meta']['order'] ?? 999;
            if ($ao === $bo) {
                return strcmp((string) $a['meta']['title'], (string) $b['meta']['title']);
            }
            return $ao <=> $bo;
        });

        return $items;
    }

    public function getItem(string $collection, string $slug): ?array
    {
        $path = $this->root . '/' . $collection . '/' . $slug . '.md';
        if (!is_file($path)) {
            return null;
        }
        return $this->loadMarkdownFile($collection, $path);
    }

    private function loadMarkdownFile(string $collection, string $path): ?array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        $parsed = FrontMatter::parse($raw);
        $slug = basename($path, '.md');
        $meta = $parsed['meta'];
        $meta['title'] = $meta['title'] ?? $slug;
        $meta['summary'] = $meta['summary'] ?? '';

        return [
            'collection' => $collection,
            'slug' => $slug,
            'meta' => $meta,
            'body' => $parsed['body'],
            'html' => Markdown::toHtml($parsed['body']),
        ];
    }

    private function readJson(string $relative): array
    {
        $path = $this->root . '/' . $relative;
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }
}
