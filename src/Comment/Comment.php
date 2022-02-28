<?php
declare(strict_types=1);

namespace PHPDel\Comment;

use PHPDel\Comment\Pattern\CommentPattern;

abstract class Comment
{
    protected string $target;
    protected bool $found = false;
    protected int $startPosition;
    protected int $endPosition;
    protected CommentPattern $commentPattern;

    public function __construct(string $target, CommentPattern $commentPattern)
    {
        $this->target = $target;
        $this->commentPattern = $commentPattern;
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
