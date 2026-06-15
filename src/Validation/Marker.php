<?php
declare(strict_types=1);

namespace PHPDel\Validation;

readonly class Marker
{
    public const START = 'start';
    public const END = 'end';
    public const LINE = 'line';
    public const FILE = 'file';
    public const IGNORE_START = 'ignore-start';
    public const IGNORE_END = 'ignore-end';
    public const INVALID = 'invalid';

    public function __construct(
        public string $type,
        public ?string $flag,
        public int $line,
        public int $column,
        public int $offset,
        public ?string $errorId = null,
        public ?string $errorMessage = null,
    ) {
    }
}
