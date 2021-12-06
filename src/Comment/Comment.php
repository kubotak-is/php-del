<?php
declare(strict_types=1);

namespace PHPDel\Comment;

abstract class Comment
{
    protected string $target;
    protected ?string $flag;
    protected bool $has = false;
    protected int $startPosition;
    protected int $endPosition;

    public function __construct(string $target, ?string $flag = null)
    {
        $this->target = $target;
        $this->flag = $flag;
    }

    public function has(): bool
    {
        return $this->has;
    }

    public function startPosition(): int
    {
        return $this->startPosition;
    }

    public function endPosition(): int
    {
        return $this->endPosition;
    }
}
