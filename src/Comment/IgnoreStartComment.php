<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class IgnoreStartComment extends Comment implements IgnoreCommentInterface
{
    protected function matchPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+ignore\s+start+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function setPosition(): void
    {
        $this->position = mb_strrpos(mb_strstr($this->target, $this->matchPhrase, true), PHP_EOL);
    }

    public function positionWithCode(): int
    {
        return mb_strpos($this->target, $this->matchPhrase) + mb_strlen($this->matchPhrase);
    }
}
