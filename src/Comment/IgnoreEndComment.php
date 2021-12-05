<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class IgnoreEndComment extends Comment implements IgnoreCommentInterface
{
    protected function matchPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+ignore\s+end+(|\*|\n|\s)*($|\/)/iu";
    }

    protected function setPosition(): void
    {
        $this->position = mb_strpos($this->target, $this->matchPhrase) + mb_strlen($this->matchPhrase);
    }

    public function positionWithCode(): int
    {
        return mb_strrpos(mb_strstr($this->target, $this->matchPhrase, true), PHP_EOL);
    }
}
