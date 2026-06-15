<?php
declare(strict_types=1);

namespace PHPDel\Comment;

use PHPDel\Comment\Pattern\CommentPattern;

class LineComment extends Comment
{
    private string $phrase;

    public function __construct(string $target, CommentPattern $commentPattern)
    {
        parent::__construct($target, $commentPattern);
        $this->init();
    }

    private function matchPattern(): string
    {
        return $this->commentPattern->matchPatternAtLine();
    }

    private function init(): void
    {
        $matches = [];
        $result = preg_match($this->matchPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->found = true;
            $this->phrase = $matches[0][0];
            $this->setStartPosition();
            $this->setEndPosition();
        }
    }

    private function setStartPosition(): void
    {
        $targetBefore = mb_strstr($this->target, $this->phrase, true);

        if ($targetBefore === false) {
            throw new \RuntimeException('Unable to locate line comment.');
        }

        $this->startPosition = (int) mb_strrpos($targetBefore, PHP_EOL);
    }

    private function setEndPosition(): void
    {
        $this->endPosition = (int) mb_strpos($this->target, $this->phrase) + mb_strlen($this->phrase);
    }
}
