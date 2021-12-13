<?php
declare(strict_types=1);

namespace PHPDel\Flag;

class Flag
{
    private string $flag;
    private int $count = 1;

    public function __construct(string $flag)
    {
        $this->flag = $flag;
    }

    public function get(): string
    {
        return $this->flag;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function increment(): void
    {
        ++$this->count;
    }

    public function __toString(): string
    {
        return "{$this->flag} ({$this->count})";
    }
}
