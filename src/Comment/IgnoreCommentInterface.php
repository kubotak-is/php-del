<?php
declare(strict_types=1);

namespace PHPDel\Comment;

interface IgnoreCommentInterface
{
    public function positionWithCode(): int;
}
