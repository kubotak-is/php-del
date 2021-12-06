<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteComment extends SandWitchComment
{
    public function __construct(string $target, string $flag)
    {
        parent::__construct($target, $flag);
    }

    protected function matchStartPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+start\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function matchEndPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function setStartPosition(): void
    {
        $this->startPosition = mb_strrpos(mb_strstr($this->target, $this->startPhrase, true), PHP_EOL);
    }

    protected function setEndPosition(): void
    {
        $this->endPosition = mb_strpos($this->target, $this->endPhrase) + mb_strlen($this->endPhrase);
    }
}
