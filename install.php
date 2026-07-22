<?php

declare(strict_types=1);

/**
 * VibeKB installer.
 *
 * Prepares a target repository for VibeKB without touching the application's
 * code. It installs the VibeKB runtime (the guide, the tools, the agent
 * instructions, and the VibeKB docs) and creates a fresh, empty-but-valid
 * `.vibekb/` workspace for an AI coding agent to fill in.
 *
 *   git clone https://github.com/cubixmeow-commits/VibeKB.git
 *   php install.php                     # install into the current directory
 *   php install.php /path/to/target     # install into another repository
 *
 * Options:
 *   --dry-run        Show exactly what would happen; change nothing.
 *   --yes, -y        Assume "yes" to prompts (non-interactive install).
 *   --force          Overwrite pre-existing files, incl. an existing .vibekb/
 *                    model. Never used silently — say so explicitly.
 *   --upgrade        Refresh the VibeKB runtime, preserve .vibekb/ (auto-detected
 *                    when a prior install is found).
 *   --help, -h       This help.
 *
 * What it does NOT do: it does not analyse, understand, or document the target
 * application. The installer sets up the workspace; the AI agent interprets the
 * software. That separation is deliberate. PHP 8.2+, no Composer, no network,
 * Windows/macOS/Linux.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$SOURCE = __DIR__; // the VibeKB clone this installer ships with
require_once $SOURCE . '/tools/lib/Starter.php';

exit(vibekb_install_main($argv, $SOURCE));

// ---------------------------------------------------------------------------

function vibekb_install_main(array $argv, string $source): int
{
    $opts = vibekb_install_parse_args(array_slice($argv, 1));
    if ($opts['help']) {
        vibekb_install_usage();
        return 0;
    }

    $c = new VibekbInstallConsole();
    $c->banner();

    // ---- resolve source + target -------------------------------------------
    $manifest = vibekb_install_load_manifest($source);
    if ($manifest === null) {
        $c->error("This does not look like a VibeKB source clone (missing template/manifest.json).");
        $c->line("Run the installer from a cloned VibeKB repository.");
        return 1;
    }

    $target = $opts['target'] !== '' ? $opts['target'] : getcwd();
    $target = vibekb_install_realish($target);
    if ($target === '' || !is_dir($target)) {
        $c->error("Target directory does not exist: " . ($opts['target'] ?: getcwd()));
        return 1;
    }

    if (vibekb_install_same_path($source, $target)) {
        $c->error("The target is the VibeKB source repository itself.");
        $c->line("VibeKB is self-hosted: its own .vibekb/ model must not be replaced by a fresh one.");
        $c->line("Install into a DIFFERENT repository:  php install.php /path/to/your/project");
        $c->line("To verify or repair THIS repo's workspace, use:  php tools/vibekb.php bootstrap");
        return 1;
    }

    // ---- detect prior install / repository shape ---------------------------
    $statePath = $target . '/.vibekb/.installer.json';
    $priorState = is_file($statePath) ? json_decode((string) @file_get_contents($statePath), true) : null;
    $hasVibekb = is_dir($target . '/.vibekb');
    $isUpgrade = $opts['upgrade'] || is_array($priorState);

    $c->kv('VibeKB source', $source);
    $c->kv('Target repository', vibekb_install_project_name($target) . '  (' . $target . ')');
    $c->kv('Mode', $opts['dryRun'] ? 'DRY RUN (no changes)' : ($isUpgrade ? 'upgrade' : 'fresh install'));
    if (is_array($priorState) && isset($priorState['template_version'])) {
        $c->kv('Installed version', (string) $priorState['template_version'] . '  →  ' . (string) $manifest['template_version']);
    }
    $c->blank();

    // ---- repository sanity check -------------------------------------------
    $shape = vibekb_install_repo_shape($target);
    if (!$shape['looks_like_project']) {
        $c->warn("This directory does not look like a software project:");
        $c->line("  - no .git, no common source folders, no README.");
        if (!$opts['yes'] && !$opts['dryRun'] && !$c->confirm("Install VibeKB here anyway?", false)) {
            $c->line("Aborted.");
            return 1;
        }
    } else {
        $c->kv('Detected', implode(', ', $shape['signals']));
    }

    // ---- build the plan -----------------------------------------------------
    $plan = vibekb_install_plan($source, $target, $manifest, $isUpgrade, $opts['force'], $hasVibekb);
    vibekb_install_render_plan($c, $plan, $opts);

    if ($plan['blocked'] !== [] && !$opts['force']) {
        $c->blank();
        $c->warn(count($plan['blocked']) . " existing file(s) would be overwritten and were SKIPPED for safety.");
        $c->line("Re-run with --force to replace them, or remove/rename them first. Application code is never replaced without --force.");
    }

    if ($opts['dryRun']) {
        $c->blank();
        $c->ok("Dry run complete. No files were changed.");
        return 0;
    }

    // ---- confirm ------------------------------------------------------------
    if (!$opts['yes']) {
        $c->blank();
        if (!$c->confirm("Install VibeKB?", true)) {
            $c->line("Aborted. Nothing was changed.");
            return 1;
        }
    }

    // ---- execute: copy the payload -----------------------------------------
    $c->blank();
    $c->section('Installing runtime');
    $copied = 0;
    foreach ($plan['files'] as $item) {
        if (!in_array($item['action'], ['create', 'replace'], true)) {
            continue;
        }
        if (!vibekb_install_copy_file($item['src'], $item['dst'])) {
            $c->error("Failed to copy: " . $item['rel']);
            return 1;
        }
        $copied++;
    }
    $c->ok("Copied {$copied} runtime file(s).");

    // ---- execute: fresh model (or preserve) --------------------------------
    $c->section('Preparing the .vibekb/ workspace');
    $ctx = ['project_name' => vibekb_install_project_name($target), 'date' => date('Y-m-d')];
    if ($hasVibekb && !$opts['force']) {
        $c->line("An existing .vibekb/ was found — preserving it (use --force to reset the model).");
        // Still repair any missing scaffolding without overwriting content.
        $rep = vibekb_scaffold_workspace($target . '/.vibekb', $ctx, false, false);
        if ($rep['created_dirs'] !== [] || $rep['created_files'] !== []) {
            $c->line("Repaired " . count($rep['created_dirs']) . " dir(s) and " . count($rep['created_files']) . " missing file(s).");
        }
    } else {
        $rep = vibekb_scaffold_workspace($target . '/.vibekb', $ctx, false, $opts['force'] && $hasVibekb);
        if ($rep['errors'] !== []) {
            foreach ($rep['errors'] as $e) {
                $c->error($e);
            }
            return 1;
        }
        $c->ok("Scaffolded a fresh, empty model (" . count($rep['created_dirs']) . " dirs, "
            . (count($rep['created_files']) + count($rep['overwritten_files'])) . " files).");
    }

    // ---- record installer state --------------------------------------------
    vibekb_install_write_state($target, $manifest, $plan, is_array($priorState) ? $priorState : null);

    // ---- verify -------------------------------------------------------------
    $c->section('Verifying installation');
    $ok = vibekb_install_verify($c, $target);

    // ---- next steps ---------------------------------------------------------
    $c->blank();
    if ($ok) {
        $c->ok("Installation complete.");
    } else {
        $c->warn("Installation finished with warnings — see above.");
    }
    vibekb_install_next_steps($c, $target);
    return $ok ? 0 : 1;
}

// ---- argument parsing ------------------------------------------------------

/** @return array{target:string,dryRun:bool,yes:bool,force:bool,upgrade:bool,help:bool} */
function vibekb_install_parse_args(array $args): array
{
    $o = ['target' => '', 'dryRun' => false, 'yes' => false, 'force' => false, 'upgrade' => false, 'help' => false];
    foreach ($args as $a) {
        switch ($a) {
            case '--dry-run': $o['dryRun'] = true; break;
            case '--yes': case '-y': $o['yes'] = true; break;
            case '--force': $o['force'] = true; break;
            case '--upgrade': $o['upgrade'] = true; break;
            case '--help': case '-h': $o['help'] = true; break;
            default:
                if (str_starts_with($a, '-')) {
                    fwrite(STDERR, "Unknown option: {$a}\n");
                } elseif ($o['target'] === '') {
                    $o['target'] = $a;
                }
        }
    }
    return $o;
}

function vibekb_install_usage(): void
{
    echo <<<TXT
VibeKB installer — prepare a repository for VibeKB.

  php install.php [options] [target]

  target            Directory to install into (default: current directory).

Options:
  --dry-run         Show what would happen; change nothing.
  --yes, -y         Assume "yes" to prompts (non-interactive).
  --force           Overwrite pre-existing files, including an existing .vibekb/
                    model. Application code is otherwise never overwritten.
  --upgrade         Refresh the VibeKB runtime and preserve .vibekb/
                    (auto-detected when a prior install exists).
  --help, -h        This help.

The installer prepares the workspace. It never analyses or documents your
application — an AI coding agent builds the model afterwards using
prompts/INTEGRATE_VIBEKB.md. See INSTALLER.md.

TXT;
}

// ---- manifest / paths ------------------------------------------------------

/** @return array<string,mixed>|null */
function vibekb_install_load_manifest(string $source): ?array
{
    $path = $source . '/template/manifest.json';
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode((string) @file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

/** Flattened, ordered list of repository-root-relative payload paths. @return list<string> */
function vibekb_install_payload_paths(array $manifest): array
{
    $payload = is_array($manifest['payload'] ?? null) ? $manifest['payload'] : [];
    $paths = [];
    foreach (['runtime', 'agent', 'docs'] as $group) {
        foreach ((array) ($payload[$group] ?? []) as $p) {
            if (is_string($p) && $p !== '') {
                $paths[] = $p;
            }
        }
    }
    return $paths;
}

/** Best-effort absolute path that tolerates a not-yet-existing leaf. */
function vibekb_install_realish(string $path): string
{
    $real = realpath($path);
    if ($real !== false) {
        return $real;
    }
    // Resolve the parent, keep the leaf.
    $parent = realpath(dirname($path));
    return $parent !== false ? $parent . '/' . basename($path) : '';
}

function vibekb_install_same_path(string $a, string $b): bool
{
    $ra = realpath($a);
    $rb = realpath($b);
    return $ra !== false && $rb !== false && $ra === $rb;
}

function vibekb_install_project_name(string $target): string
{
    // Prefer the git remote/repo name; fall back to the directory name.
    $name = basename(rtrim($target, '/\\'));
    return $name !== '' ? $name : 'this repository';
}

/** @return array{looks_like_project:bool,signals:list<string>} */
function vibekb_install_repo_shape(string $target): array
{
    $signals = [];
    if (is_dir($target . '/.git')) {
        $signals[] = 'git repository';
    }
    foreach (['src', 'lib', 'app', 'source', 'packages', 'cmd', 'internal'] as $d) {
        if (is_dir($target . '/' . $d)) {
            $signals[] = "{$d}/";
        }
    }
    foreach (['README.md', 'README', 'README.rst', 'README.txt'] as $r) {
        if (is_file($target . '/' . $r)) {
            $signals[] = $r;
            break;
        }
    }
    foreach (['package.json', 'composer.json', 'pyproject.toml', 'go.mod', 'Cargo.toml', 'Gemfile', 'pom.xml'] as $m) {
        if (is_file($target . '/' . $m)) {
            $signals[] = $m;
            break;
        }
    }
    return ['looks_like_project' => $signals !== [], 'signals' => $signals];
}

// ---- planning --------------------------------------------------------------

/**
 * Compute the per-file plan.
 *
 * @return array{files:list<array{rel:string,src:string,dst:string,action:string}>,
 *               counts:array<string,int>, blocked:list<string>, preserve:bool}
 */
function vibekb_install_plan(string $source, string $target, array $manifest, bool $isUpgrade, bool $force, bool $hasVibekb): array
{
    $files = [];
    $counts = ['create' => 0, 'replace' => 0, 'skip' => 0];
    $blocked = [];

    foreach (vibekb_install_payload_paths($manifest) as $rel) {
        $srcPath = $source . '/' . $rel;
        if (!file_exists($srcPath)) {
            continue; // payload path absent in this clone — nothing to copy
        }
        $entries = is_dir($srcPath)
            ? vibekb_install_walk($srcPath, $rel)
            : [$rel];
        foreach ($entries as $fileRel) {
            $src = $source . '/' . $fileRel;
            $dst = $target . '/' . $fileRel;
            $exists = file_exists($dst);
            if (!$exists) {
                $action = 'create';
            } elseif ($isUpgrade || $force) {
                // VibeKB-owned runtime is refreshed on upgrade / --force.
                $action = 'replace';
            } else {
                // Fresh install onto a pre-existing file: do not clobber.
                $action = 'skip';
                $blocked[] = $fileRel;
            }
            $counts[$action]++;
            $files[] = ['rel' => $fileRel, 'src' => $src, 'dst' => $dst, 'action' => $action];
        }
    }

    return [
        'files' => $files,
        'counts' => $counts,
        'blocked' => $blocked,
        'preserve' => $hasVibekb && !$force,
    ];
}

/** Recursively list files under a directory as repo-root-relative paths. @return list<string> */
function vibekb_install_walk(string $dir, string $relBase): array
{
    $out = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $sub = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($dir))), '/');
        // Skip VCS noise and OS cruft.
        if (preg_match('#(^|/)(\.git|\.DS_Store|Thumbs\.db)($|/)#', $sub)) {
            continue;
        }
        $out[] = $relBase . '/' . $sub;
    }
    sort($out);
    return $out;
}

function vibekb_install_render_plan(VibekbInstallConsole $c, array $plan, array $opts): void
{
    $c->section('Plan');
    $byDir = ['create' => [], 'replace' => [], 'skip' => []];
    foreach ($plan['files'] as $item) {
        $top = explode('/', $item['rel'])[0];
        $byDir[$item['action']][$top] = ($byDir[$item['action']][$top] ?? 0) + 1;
    }
    $labels = ['create' => 'Create', 'replace' => 'Replace', 'skip' => 'Skip (exists)'];
    foreach (['create', 'replace', 'skip'] as $action) {
        if ($byDir[$action] === []) {
            continue;
        }
        $c->line($labels[$action] . ':');
        foreach ($byDir[$action] as $top => $n) {
            $suffix = $n > 1 ? "/  ({$n} files)" : (str_contains($top, '.') ? '' : '/');
            $c->line("  {$top}{$suffix}");
        }
    }
    // The project-owned model is always shown as its own outcome.
    $c->line('Model:');
    $c->line('  .vibekb/' . ($plan['preserve'] ? '  — preserve (existing model kept)' : '  — create (fresh empty model)'));

    if ($opts['dryRun']) {
        $c->blank();
        $c->line('Full file list:');
        foreach ($plan['files'] as $item) {
            $c->line(sprintf('  %-8s %s', strtoupper($item['action']), $item['rel']));
        }
    }
}

// ---- execution helpers -----------------------------------------------------

function vibekb_install_copy_file(string $src, string $dst): bool
{
    $dir = dirname($dst);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }
    if (!@copy($src, $dst)) {
        return false;
    }
    // Preserve the executable bit for scripts where the platform supports it.
    if (str_ends_with($src, '.php') || str_ends_with($src, '.sh')) {
        @chmod($dst, 0644);
    }
    return true;
}

function vibekb_install_write_state(string $target, array $manifest, array $plan, ?array $prior): void
{
    $installed = [];
    foreach ($plan['files'] as $item) {
        if (in_array($item['action'], ['create', 'replace'], true)) {
            $installed[] = $item['rel'];
        }
    }
    sort($installed);
    $now = date('c');
    $state = [
        'template_version' => (string) ($manifest['template_version'] ?? 'unknown'),
        'installed_at' => is_array($prior) ? (string) ($prior['installed_at'] ?? $now) : $now,
        'updated_at' => $now,
        'source_repository' => 'https://github.com/cubixmeow-commits/VibeKB',
        'payload' => $installed,
        'note' => 'Written by install.php. Records which files VibeKB owns in this repository so upgrades can refresh them safely. Do not edit by hand.',
    ];
    $dir = $target . '/.vibekb';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents($dir . '/.installer.json', json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
}

function vibekb_install_verify(VibekbInstallConsole $c, string $target): bool
{
    $ok = true;
    $checks = [
        'guide/index.php' => 'guide (the dynamic app)',
        'tools/vibekb.php' => 'tools (the self-maintenance CLI)',
        'prompts/INTEGRATE_VIBEKB.md' => 'prompts (the integration prompt)',
        '.vibekb/manifest.json' => 'starter model',
    ];
    foreach ($checks as $rel => $label) {
        if (is_file($target . '/' . $rel)) {
            $c->ok("{$label} present");
        } else {
            $c->error("missing: {$label} ({$rel})");
            $ok = false;
        }
    }

    // Model verification via the freshly installed CLI, run against the target.
    $cli = $target . '/tools/vibekb.php';
    if (is_file($cli)) {
        $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($cli) . ' check 2>&1';
        $cwd = getcwd();
        @chdir($target);
        $out = (string) @shell_exec($cmd);
        @chdir($cwd !== false ? $cwd : $target);
        if (str_contains($out, 'RESULT: OK')) {
            $c->ok('php tools/vibekb.php check — OK (empty model is valid)');
        } else {
            $c->warn('php tools/vibekb.php check did not report OK:');
            foreach (array_slice(array_filter(explode("\n", $out)), 0, 8) as $l) {
                $c->line('    ' . $l);
            }
            $ok = false;
        }
    }
    return $ok;
}

function vibekb_install_next_steps(VibekbInstallConsole $c, string $target): void
{
    $name = vibekb_install_project_name($target);
    $c->blank();
    $c->section('Next steps');
    $c->line("VibeKB is installed but the model is empty — that is by design. The");
    $c->line("installer prepares the workspace; an AI coding agent builds the model.");
    $c->blank();
    $c->line("  1. Open {$name} in your coding agent (Claude Code, Cursor, Codex, …).");
    $c->line("  2. Ask it to:");
    $c->line("       Build the first VibeKB model for this repository using");
    $c->line("       prompts/INTEGRATE_VIBEKB.md");
    $c->line("  3. When it finishes:  php tools/vibekb.php check");
    $c->line("  4. Optional static site:  php tools/vibekb.php generate   (writes /docs)");
    $c->blank();
    $c->line("Preview the guide locally:  php -S localhost:8080 -t {$name}");
    $c->line("Then open:  http://localhost:8080/guide/");
    $c->line("Repair the workspace any time:  php tools/vibekb.php bootstrap");
}

// ---- console ---------------------------------------------------------------

final class VibekbInstallConsole
{
    private bool $color;

    public function __construct()
    {
        $this->color = function_exists('posix_isatty') && @posix_isatty(STDOUT)
            && getenv('NO_COLOR') === false;
    }

    public function banner(): void
    {
        echo "\n" . $this->c("VibeKB installer", '1;36') . "\n";
        echo str_repeat('=', 60) . "\n\n";
    }

    public function section(string $t): void
    {
        echo "\n" . $this->c($t, '1') . "\n" . str_repeat('-', 60) . "\n";
    }

    public function kv(string $k, string $v): void
    {
        echo '  ' . str_pad($k, 20) . ': ' . $v . "\n";
    }

    public function line(string $s): void { echo $s . "\n"; }
    public function blank(): void { echo "\n"; }
    public function ok(string $s): void { echo '  ' . $this->c('✓', '32') . ' ' . $s . "\n"; }
    public function warn(string $s): void { echo '  ' . $this->c('!', '33') . ' ' . $s . "\n"; }
    public function error(string $s): void { fwrite(STDERR, '  ' . $this->c('✗', '31') . ' ' . $s . "\n"); }

    public function confirm(string $q, bool $default): bool
    {
        $hint = $default ? '[Y/n]' : '[y/N]';
        echo $q . ' ' . $hint . ' ';
        $line = fgets(STDIN);
        if ($line === false) {
            echo "\n";
            return $default; // non-interactive stdin: take the default
        }
        $line = strtolower(trim($line));
        if ($line === '') {
            return $default;
        }
        return $line === 'y' || $line === 'yes';
    }

    private function c(string $s, string $code): string
    {
        return $this->color ? "\033[{$code}m{$s}\033[0m" : $s;
    }
}
