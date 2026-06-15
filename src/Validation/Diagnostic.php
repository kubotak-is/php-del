<?php
declare(strict_types=1);

namespace PHPDel\Validation;

readonly class Diagnostic
{
    public function __construct(
        public string $path,
        public int $line,
        public int $column,
        public string $id,
        public string $message,
        public bool $runtimeError = false,
    ) {
    }

    public function __toString(): string
    {
        return "{$this->path}:{$this->line}:{$this->column} [{$this->id}] {$this->message}";
    }
}
