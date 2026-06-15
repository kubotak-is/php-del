<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * End-to-end tests that drive the real `php-del` executable as a subprocess.
 *
 * The non-interactive options (`--flag` / `--list-flags`) make this possible:
 * because they never reach the CLImate `radio` prompt, the process runs to
 * completion without stdin, so the whole pipeline — argument parsing, config
 * loading, flag discovery, rewrite/delete, and the propagated exit code — can
 * be exercised deterministically.
 *
 * Each test runs in an isolated temporary workspace so the repository's own
 * `tests/actual` fixtures are never mutated.
 */
final class ApplicationE2ETest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $base = sys_get_temp_dir() . '/php-del-e2e-' . uniqid('', true);

        if (!mkdir($base . '/src', 0777, true) && !is_dir($base . '/src')) {
            self::fail("Unable to create workspace: {$base}");
        }

        $this->workspace = $base;
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->workspace);
    }

    public function testHelpListsNonInteractiveOptions(): void
    {
        // `--help` returns before config loading, so no php-del.json is needed.
        $result = $this->runCli(['--help']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('flag', $result['output']);
        self::assertStringContainsString('list-flags', $result['output']);
        self::assertStringContainsString('dry-run', $result['output']);
        self::assertStringContainsString('validate', $result['output']);
    }

    public function testListFlagsOutputsDetectedFlagsWithoutModifying(): void
    {
        $this->writeConfig();
        $this->copyActual('flag_a/FlagA.php');
        $this->copyActual('flag_b/FlagB.php');
        $original = $this->read('FlagA.php');

        $result = $this->runCli(['--list-flags']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('flag_a', $result['output']);
        self::assertStringContainsString('flag_b', $result['output']);
        // Listing must not touch any file.
        self::assertSame($original, $this->read('FlagA.php'));
    }

    public function testFlagRewritesMatchingFile(): void
    {
        $this->writeConfig();
        $this->copyActual('flag_a/FlagA.php');

        $result = $this->runCli(['--flag=flag_a']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('End php-del', $result['output']);
        self::assertSame(
            file_get_contents(__DIR__ . '/../expect/flag_a/FlagA.php'),
            $this->read('FlagA.php')
        );
    }

    public function testDryRunDoesNotRewrite(): void
    {
        $this->writeConfig();
        $this->copyActual('flag_a/FlagA.php');
        $original = $this->read('FlagA.php');

        $result = $this->runCli(['--flag=flag_a', '--dry-run']);

        self::assertSame(0, $result['exit']);
        self::assertSame($original, $this->read('FlagA.php'));
    }

    public function testFileFlagDeletesWholeFile(): void
    {
        $this->writeConfig();
        $this->copyActual('delete_flag/DeleteFlag.php');

        $result = $this->runCli(['--flag=delete_flag']);

        self::assertSame(0, $result['exit']);
        self::assertFileDoesNotExist($this->path('DeleteFlag.php'));
    }

    public function testDryRunDoesNotDeleteFile(): void
    {
        $this->writeConfig();
        $this->copyActual('delete_flag/DeleteFlag.php');

        $result = $this->runCli(['--flag=delete_flag', '--dry-run']);

        self::assertSame(0, $result['exit']);
        self::assertFileExists($this->path('DeleteFlag.php'));
    }

    public function testUnknownFlagExitsWithErrorCode(): void
    {
        $this->writeConfig();
        $this->copyActual('flag_a/FlagA.php');
        $original = $this->read('FlagA.php');

        $result = $this->runCli(['--flag=does-not-exist']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('does not exist', $result['output']);
        // A rejected flag must leave files untouched.
        self::assertSame($original, $this->read('FlagA.php'));
    }

    public function testUnknownFlagExitsWithErrorCodeWhenNoMarkersFound(): void
    {
        $this->writeConfig();

        $result = $this->runCli(['--flag=does-not-exist']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('does not exist', $result['output']);
    }

    public function testFlagWithoutValueExitsWithErrorCode(): void
    {
        $this->writeConfig();
        $this->copyActual('flag_a/FlagA.php');
        $original = $this->read('FlagA.php');

        $result = $this->runCli(['--flag']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('requires a value', $result['output']);
        self::assertSame($original, $this->read('FlagA.php'));
    }

    public function testReportsNothingWhenNoMarkersFound(): void
    {
        $this->writeConfig();
        // Workspace src is empty: no markers to discover.

        $result = $this->runCli(['--list-flags']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('Nothing flag', $result['output']);
    }

    public function testValidatePassesWithoutModifyingFiles(): void
    {
        $this->writeConfig();
        $this->write(
            'Valid.php',
            "<?php\n/** php-del start legacy */\n\$legacy = true;\n/** php-del end legacy */\n"
        );
        $original = $this->read('Valid.php');

        $result = $this->runCli(['--validate']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('validation passed: 1 files, 2 markers', $result['output']);
        self::assertSame($original, $this->read('Valid.php'));
    }

    public function testValidateReportsAllMarkerErrorsWithoutModifyingFiles(): void
    {
        $this->writeConfig();
        $this->write('Open.php', "<?php\n// php-del start open\n");
        $this->write('Invalid.php', "<?php\n// php-del line feature/value\n");
        $open = $this->read('Open.php');
        $invalid = $this->read('Invalid.php');

        $result = $this->runCli(['--validate']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('src/Invalid.php:2:4 [PDEL006]', $result['output']);
        self::assertStringContainsString('src/Open.php:2:4 [PDEL001]', $result['output']);
        self::assertStringContainsString('validation failed: 2 errors in 2 files', $result['output']);
        self::assertSame($open, $this->read('Open.php'));
        self::assertSame($invalid, $this->read('Invalid.php'));
    }

    public function testValidateRejectsConflictingOptions(): void
    {
        $this->writeConfig();

        $result = $this->runCli(['--validate', '--dry-run']);

        self::assertSame(2, $result['exit']);
        self::assertStringContainsString('cannot be combined', $result['output']);
    }

    public function testValidateReportsUnsupportedExtensionAsRuntimeError(): void
    {
        $this->writeConfig(['txt']);
        $this->write('example.txt', '// php-del start legacy');

        $result = $this->runCli(['--validate']);

        self::assertSame(2, $result['exit']);
        self::assertStringContainsString('src/example.txt:1:1 [PDEL012]', $result['output']);
    }

    public function testValidateAcceptsEmptyDirectory(): void
    {
        $this->writeConfig();

        $result = $this->runCli(['--validate']);

        self::assertSame(0, $result['exit']);
        self::assertStringContainsString('validation passed: 0 files, 0 markers', $result['output']);
    }

    /**
     * Run the real php-del executable inside the workspace and capture output.
     *
     * @param list<string> $args
     * @return array{exit:int,stdout:string,stderr:string,output:string}
     */
    private function runCli(array $args): array
    {
        $entry = realpath(__DIR__ . '/../../php-del');
        self::assertNotFalse($entry, 'php-del executable not found');

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            array_merge([PHP_BINARY, $entry], $args),
            $descriptors,
            $pipes,
            $this->workspace
        );
        self::assertIsResource($process, 'failed to start php-del');

        // Close stdin immediately so any unexpected prompt sees EOF instead of hanging.
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [
            'exit'   => $exitCode,
            'stdout' => $stdout,
            'stderr' => $stderr,
            'output' => $stdout . $stderr,
        ];
    }

    private function writeConfig(array $extensions = ['php']): void
    {
        file_put_contents(
            $this->workspace . '/php-del.json',
            (string) json_encode(['dirs' => ['src'], 'extensions' => $extensions], JSON_PRETTY_PRINT)
        );
    }

    private function copyActual(string $relative, ?string $destName = null): void
    {
        $source = __DIR__ . '/../actual/' . $relative;
        self::assertFileExists($source);
        copy($source, $this->path($destName ?? basename($relative)));
    }

    private function write(string $name, string $contents): void
    {
        file_put_contents($this->path($name), $contents);
    }

    private function path(string $name): string
    {
        return $this->workspace . '/src/' . $name;
    }

    private function read(string $name): string
    {
        return (string) file_get_contents($this->path($name));
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
