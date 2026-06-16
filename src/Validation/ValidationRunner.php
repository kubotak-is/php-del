<?php
declare(strict_types=1);

namespace PHPDel\Validation;

use PHPDel\Config;
use PHPDel\Exception\UndefinedExtensionException;
use PHPDel\FileFinder;

readonly class ValidationRunner
{
    public function __construct(
        private Config $config,
        private MarkerScanner $scanner = new MarkerScanner(),
        private Validator $validator = new Validator(),
    ) {
    }

    public function run(): ValidationResult
    {
        $diagnostics = [];
        $fileCount = 0;
        $markerCount = 0;

        foreach ((new FileFinder($this->config))->findFiles() as $file) {
            ++$fileCount;
            $path = $this->relativePath($file);
            $text = file_get_contents($file);

            if ($text === false) {
                $diagnostics[] = new Diagnostic(
                    $path,
                    1,
                    1,
                    'PDEL013',
                    'unable to read file',
                    true
                );
                continue;
            }

            try {
                $markers = $this->scanner->scan($file, $text);
                $markerCount += count($markers);
                array_push($diagnostics, ...$this->validator->validate($path, $markers));
            } catch (UndefinedExtensionException) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $diagnostics[] = new Diagnostic(
                    $path,
                    1,
                    1,
                    'PDEL012',
                    "extension \"{$extension}\" is not supported",
                    true
                );
            }
        }

        return new ValidationResult($diagnostics, $fileCount, $markerCount);
    }

    private function relativePath(string $path): string
    {
        $cwd = rtrim((string) getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return str_starts_with($path, $cwd) ? substr($path, strlen($cwd)) : $path;
    }
}
