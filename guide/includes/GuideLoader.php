<?php

declare(strict_types=1);

final class GuideLoader
{
    public function __construct(
        private readonly string $guideRoot,
        private readonly ContentRepository $knowledge
    ) {
    }

    /**
     * @return array{meta: array<string, mixed>, chapters: list<array<string, mixed>>}
     */
    public function load(): array
    {
        $meta = $this->readJson('guide.json');
        if ($meta === []) {
            throw new RuntimeException('Missing or invalid .vibekb/guide/guide.json');
        }

        $chapters = [];
        $list = $meta['chapters'] ?? [];
        if (!is_array($list)) {
            $list = [];
        }

        foreach ($list as $fileStem) {
            $stem = (string) $fileStem;
            $chapter = $this->readJson('chapters/' . $stem . '.json');
            if ($chapter === []) {
                continue;
            }
            $chapter['file'] = $stem;
            $chapter['hash'] = $this->chapterHash($chapter);
            $chapters[] = $chapter;
        }

        return [
            'meta' => $meta,
            'chapters' => $chapters,
        ];
    }

    public function knowledge(): ContentRepository
    {
        return $this->knowledge;
    }

    /**
     * Resolve curated source refs into short reference cards.
     *
     * @param list<array{collection?: string, slug?: string}> $sources
     * @return list<array<string, mixed>>
     */
    public function resolveSources(array $sources): array
    {
        $resolved = [];
        foreach ($sources as $source) {
            if (!is_array($source)) {
                continue;
            }
            $collection = (string) ($source['collection'] ?? '');
            $slug = (string) ($source['slug'] ?? '');
            if ($collection === '' || $slug === '') {
                continue;
            }
            $item = $this->knowledge->getItem($collection, $slug);
            if ($item === null) {
                continue;
            }
            $resolved[] = [
                'collection' => $collection,
                'slug' => $slug,
                'title' => (string) ($item['meta']['title'] ?? $slug),
                'summary' => (string) ($item['meta']['summary'] ?? ''),
                'url' => reference_item_url($collection, $slug),
            ];
        }
        return $resolved;
    }

    /**
     * @param array<string, mixed> $chapter
     */
    private function chapterHash(array $chapter): string
    {
        $id = (string) ($chapter['id'] ?? 'chapter');
        return preg_replace('/[^a-z0-9\-]+/i', '-', $id) ?: 'chapter';
    }

    /**
     * @return array<string, mixed>
     */
    private function readJson(string $relative): array
    {
        $path = $this->guideRoot . '/' . $relative;
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }
}
