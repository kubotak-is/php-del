<?php
declare(strict_types=1);

namespace PHPDel\Validation;

readonly class ValidationResult
{
    /**
     * @param list<Diagnostic> $diagnostics
     */
    public function __construct(
        public array $diagnostics,
        public int $fileCount,
        public int $markerCount,
    ) {
    }

    public function exitCode(): int
    {
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->runtimeError) {
                return 2;
            }
        }

        return $this->diagnostics === [] ? 0 : 1;
    }

    public function errorFileCount(): int
    {
        $files = [];

        foreach ($this->diagnostics as $diagnostic) {
            $files[$diagnostic->path] = true;
        }

        return count($files);
    }
}
