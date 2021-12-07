<?php
declare(strict_types=1);

namespace PHPDel\Comment;

abstract class Comment
{
    protected string $target;
    protected ?string $flag;
    protected bool $found = false;
    protected int $startPosition;
    protected int $endPosition;

    public function __construct(string $target, ?string $flag = null)
    {
        $this->target = $target;
        $this->flag = $flag;
    }

    public function notfound(): bool
    {
        return ! $this->found;
    }

    public function targetCode(): string
    {
        return mb_substr($this->target, $this->startPosition, $this->endPosition - $this->startPosition);
    }
}
