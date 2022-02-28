<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteComment extends SandWitchComment
{
    protected function matchStartPattern(): string
    {
        return $this->commentPattern->startMatchPatternAtDelete();
    }

    protected function matchEndPattern(): string
    {
        return $this->commentPattern->endMatchPatternAtDelete();
    }
}
