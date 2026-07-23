<?php

declare(strict_types=1);

/**
 * VibeKB installer — compatibility wrapper.
 *
 * Installation is now fully native to the `vibekb` Go CLI: it embeds the runtime
 * payload and the starter definition, so it installs without PHP and without the
 * source repository remaining on disk. There is only one installer implementation
 * (in Go); this script exists so the historical entry point keeps working.
 *
 *   php install.php [options] [target]
 *
 * If a `vibekb` binary is available (on PATH or beside this file), this forwards
 * to `vibekb install …` unchanged. Otherwise it prints how to get the binary.
 *
 * PHP is required to *run* the installed guide — never to install it.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

exit(vibekb_install_wrapper_main(array_slice($argv, 1)));

function vibekb_install_wrapper_main(array $args): int
{
    $bin = vibekb_find_binary(__DIR__);

    if ($bin === null) {
        fwrite(STDERR, vibekb_install_migration_notice());
        return 1;
    }

    // Forward every argument verbatim to `vibekb install`.
    $cmd = escapeshellarg($bin) . ' install';
    foreach ($args as $a) {
        $cmd .= ' ' . escapeshellarg($a);
    }

    // Let the child own the terminal; propagate its exit code unchanged.
    $code = 0;
    passthru($cmd, $code);
    return $code;
}

/**
 * Locate a `vibekb` executable: beside this script first (a freshly built binary
 * in the clone), then on PATH. Returns null if none is found.
 */
function vibekb_find_binary(string $sourceDir): ?string
{
    $isWindows = stripos(PHP_OS, 'WIN') === 0;
    $names = $isWindows ? ['vibekb.exe', 'vibekb'] : ['vibekb'];

    // 1) Alongside this installer (e.g. `go build -o vibekb ./cmd/vibekb`).
    foreach ($names as $name) {
        $candidate = $sourceDir . DIRECTORY_SEPARATOR . $name;
        if (is_file($candidate) && (is_executable($candidate) || $isWindows)) {
            return $candidate;
        }
    }

    // 2) On PATH.
    $path = (string) getenv('PATH');
    $sep = $isWindows ? ';' : ':';
    foreach (array_filter(explode($sep, $path)) as $dir) {
        foreach ($names as $name) {
            $candidate = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
            if (is_file($candidate) && (is_executable($candidate) || $isWindows)) {
                return $candidate;
            }
        }
    }

    return null;
}

function vibekb_install_migration_notice(): string
{
    return <<<TXT
VibeKB installation is now native to the `vibekb` CLI — no PHP needed to install.

This `php install.php` wrapper could not find a `vibekb` binary. Download one from
GitHub Releases, put it on your PATH as `vibekb`, then run:

  https://github.com/cubixmeow-commits/VibeKB/releases
  vibekb install /path/to/your/project

Advanced (build from this clone, Go 1.24+):

  go build -o vibekb ./cmd/vibekb
  ./vibekb install /path/to/your/project

See RELEASE.md and INSTALLER.md. PHP 8.2+ is required only to run the installed
guide, not to install it.

TXT;
}
