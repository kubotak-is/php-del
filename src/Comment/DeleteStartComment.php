<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteStartComment extends Comment
{
    protected function matchPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+start\s+{$this->flag}+(|\*|\n|\s)*($|\/)/iu";
    }

    protected function setPosition(): void
    {
        $this->position = mb_strrpos(mb_strstr($this->target, $this->matchPhrase, true), PHP_EOL);
    }
}
