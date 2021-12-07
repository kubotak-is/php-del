<?php
declare(strict_types=1);

namespace PHPDel\Comment;

abstract class SandWitchComment extends Comment
{
    protected string $startPhrase;
    protected string $endPhrase;
    private bool $foundStart = false;
    private bool $foundEnd = false;

    public function __construct(string $target, ?string $flag = null)
    {
        parent::__construct($target, $flag);
        $this->setStart();
        $this->setEnd();
        $this->setFound();
    }

    private function setStart(): void
    {
        $matches = [];
        $result = preg_match($this->matchStartPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->foundStart = true;
            $this->startPhrase = $matches[0][0];
            $this->setStartPosition();
        }
    }

    private function setEnd(): void
    {
        $matches = [];
        $result = preg_match($this->matchEndPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->foundEnd = true;
            $this->endPhrase = $matches[0][0];
            $this->setEndPosition();
        }
    }

    private function setFound(): void
    {
        $this->found = $this->foundStart && $this->foundEnd;
    }

    abstract protected function matchStartPattern(): string;
    abstract protected function matchEndPattern(): string;
    abstract protected function setStartPosition(): void;
    abstract protected function setEndPosition(): void;
}
