<?php
declare(strict_types=1);

namespace PHPDel\Comment;

abstract class Comment
{
    protected string $target;
    protected ?string $flag;
    protected string $matchPhrase;
    protected int $matchIndex;
    protected bool $has = false;
    protected int $position = 0;

    public function __construct(string $target, ?string $flag = null)
    {
        $this->target = $target;
        $this->flag = $flag;
        $matches = [];
        $result = preg_match($this->matchPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->has = true;
            $this->matchPhrase = $matches[0][0];
            $this->matchIndex = $matches[0][1];
            $this->setPosition();
        }
    }

    abstract protected function matchPattern(): string;
    abstract protected function setPosition(): void;

    public function has(): bool
    {
        return $this->has;
    }

    public function position(): int
    {
        return $this->position;
    }
}
