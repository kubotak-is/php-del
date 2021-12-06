<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class IgnoreComment extends SandWitchComment
{
    protected function matchStartPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+ignore\s+start+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function matchEndPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+ignore\s+end+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function setStartPosition(): void
    {
        $this->startPosition = mb_strrpos(mb_strstr($this->target, $this->startPhrase, true), PHP_EOL);
    }

    protected function setEndPosition(): void
    {
        $this->endPosition = mb_strpos($this->target, $this->endPhrase) + mb_strlen($this->endPhrase);
    }

    public function startPositionWithCode(): int
    {
        return mb_strpos($this->target, $this->startPhrase) + mb_strlen($this->startPhrase);
    }

    public function endPositionWithCode(): int
    {
        return mb_strrpos(mb_strstr($this->target, $this->endPhrase, true), PHP_EOL);
    }
}
