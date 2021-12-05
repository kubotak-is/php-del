<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteEndComment extends Comment
{
    protected function matchPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function setPosition(): void
    {
        $this->position = mb_strpos($this->target, $this->matchPhrase) + mb_strlen($this->matchPhrase);
    }
}
