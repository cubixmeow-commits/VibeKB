<?php

declare(strict_types=1);

final class GuideRenderer
{
    public function __construct(private readonly GuideLoader $loader)
    {
    }

    /**
     * @param array<string, mixed> $chapter
     * @param array<string, mixed> $guideMeta
     * @param array<string, mixed> $project
     * @param list<array<string, mixed>> $allChapters
     */
    public function renderShell(
        array $guideMeta,
        array $project,
        array $allChapters,
        array $chapter
    ): void {
        // Unused individually; shell loops chapters.
        unset($chapter);
        guide_render('shell', [
            'guideMeta' => $guideMeta,
            'project' => $project,
            'chapters' => $allChapters,
            'loader' => $this->loader,
            'renderer' => $this,
        ]);
    }

    /**
     * @param array<string, mixed> $chapter
     * @param list<array<string, mixed>> $allChapters
     * @param int $index Zero-based
     */
    public function renderChapter(array $chapter, array $allChapters, int $index): void
    {
        guide_render('chapter', [
            'chapter' => $chapter,
            'chapters' => $allChapters,
            'index' => $index,
            'loader' => $this->loader,
            'renderer' => $this,
        ]);
    }

    /**
     * @param array<string, mixed> $scene
     * @param array<string, mixed> $chapter
     * @param int $sceneIndex
     */
    public function renderScene(array $scene, array $chapter, int $sceneIndex): void
    {
        $type = (string) ($scene['type'] ?? 'statement');
        $allowed = [
            'statement',
            'progression',
            'flow',
            'interactive-cards',
            'concept-map',
            'problem-path',
            'checklist',
            'developer-detail',
            'reference-links',
            'timeline',
            'evolution',
            'ai-loop',
            'decision-history',
        ];
        if (!in_array($type, $allowed, true)) {
            $type = 'statement';
        }

        $uid = e((string) ($chapter['id'] ?? 'ch')) . '-s' . $sceneIndex;
        guide_render('scenes/' . $type, [
            'scene' => $scene,
            'chapter' => $chapter,
            'sceneIndex' => $sceneIndex,
            'uid' => $uid,
            'loader' => $this->loader,
        ]);
    }
}
