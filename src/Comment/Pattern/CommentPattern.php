<?php
declare(strict_types=1);

namespace PHPDel\Comment\Pattern;

abstract class CommentPattern
{
    protected string $flag;

    public function __construct(string $flag)
    {
        $this->flag = $flag;
    }

    abstract public function startMatchPatternAtDelete(): string;
    abstract public function endMatchPatternAtDelete(): string;
    abstract public function startMatchPatternAtIgnore(): string;
    abstract public function endMatchPatternAtIgnore(): string;
    abstract public function matchPatternAtLine(): string;
}
