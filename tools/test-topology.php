<?php

declare(strict_types=1);

/**
 * Focused test for explainable-diagram topology parsing and validation.
 *
 * There is no third-party test framework in this repo (no Composer, no PHPUnit),
 * so this is a self-contained assertion script in the same spirit as
 * tools/validate.php. It copies the real `.vibekb/` into a temp directory,
 * injects a deliberately malformed topology, loads it through the same Content
 * loader the guide uses, and asserts that:
 *
 *   1. loading does not crash (malformed topology is reported, not fatal); and
 *   2. each specific contract violation is surfaced as an issue.
 *
 * Exits non-zero if any assertion fails, so it can gate CI.
 *
 * Usage: php tools/test-topology.php
 */

$repoRoot = dirname(__DIR__);
require_once $repoRoot . '/guide/lib/helpers.php';
require_once $repoRoot . '/guide/lib/Content.php';

$tmp = sys_get_temp_dir() . '/vibekb-topo-test-' . bin2hex(random_bytes(4));

/** Recursively copy a directory. */
$copy = static function (string $src, string $dst) use (&$copy): void {
    @mkdir($dst, 0775, true);
    foreach (scandir($src) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $s = $src . '/' . $entry;
        $d = $dst . '/' . $entry;
        is_dir($s) ? $copy($s, $d) : copy($s, $d);
    }
};
$copy($repoRoot . '/.vibekb', $tmp);

// Point an existing diagram record at a broken topology file.
$recordPath = $tmp . '/diagrams/records/storage-map.md';
$record = (string) file_get_contents($recordPath);
$record = preg_replace('/^svg:\s*storage-map\.svg$/m', "svg: storage-map.svg\ntopology: broken.json", $record, 1);
file_put_contents($recordPath, $record);

// A topology that violates as many rules as possible in one file.
$broken = <<<'JSON'
{
  "version": 9,
  "nodes": [
    {
      "id": "a",
      "title": "Node A",
      "purpose": "",
      "verification": "bogus-state",
      "files": [{ "path": "app/x.php", "role": "weird-role" }]
    },
    { "id": "b", "title": "Node B", "purpose": "A valid node." },
    { "id": "b", "title": "Duplicate B", "purpose": "Second entry with the same id." }
  ],
  "edges": [
    {
      "id": "e1",
      "from": "a",
      "to": "missing-node",
      "mechanism": "relates-to",
      "explanation": ""
    }
  ]
}
JSON;
@mkdir($tmp . '/diagrams/topology', 0775, true);
file_put_contents($tmp . '/diagrams/topology/broken.json', $broken);

// Load — must not throw.
$content = new Content($tmp);
try {
    $content->load();
} catch (Throwable $e) {
    fwrite(STDERR, "FAIL: loading crashed on malformed topology: " . $e->getMessage() . "\n");
    exit(1);
}

$messages = array_map(fn ($i) => $i['message'], $content->issues());
$haystack = implode("\n", $messages);

$expect = [
    'unsupported schema version',
    'duplicate node id: b',
    "node 'a' is missing a purpose",
    "node 'a' has unknown verification: bogus-state",
    "shows file 'app/x.php' without a reason",
    "edge 'e1' has an unresolved target node",
    "edge 'e1' uses an out-of-vocabulary mechanism: relates-to",
    "edge 'e1' is missing a one-sentence explanation",
    "edge 'e1' is missing a verification state",
    "node 'a' has no data-vibekb-node marker in the SVG",
    "edge 'e1' has no data-vibekb-edge marker in the SVG",
];

$failures = 0;
foreach ($expect as $needle) {
    $ok = str_contains($haystack, $needle);
    echo ($ok ? '  ok   ' : '  FAIL ') . "expected diagnostic: {$needle}\n";
    if (!$ok) {
        $failures++;
    }
}

// The well-formed topologies in the real model must still resolve here.
$rf = $content->resolvedTopology('request-flow');
if ($rf === null || count($rf['nodes']) !== 7 || count($rf['edges']) !== 6) {
    echo "  FAIL good topology 'request-flow' did not resolve as expected\n";
    $failures++;
} else {
    echo "  ok   good topology 'request-flow' resolves (7 nodes, 6 edges)\n";
}

// Clean up.
$rrmdir = static function (string $dir) use (&$rrmdir): void {
    foreach (scandir($dir) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $p = $dir . '/' . $entry;
        is_dir($p) ? $rrmdir($p) : @unlink($p);
    }
    @rmdir($dir);
};
$rrmdir($tmp);

echo $failures === 0 ? "OK\n" : "FAILED ({$failures})\n";
exit($failures === 0 ? 0 : 1);
