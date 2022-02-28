<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class IgnoreComment extends SandWitchComment
{
    protected function matchStartPattern(): string
    {
        return $this->commentPattern->startMatchPatternAtIgnore();
    }

    protected function matchEndPattern(): string
    {
        return $this->commentPattern->endMatchPatternAtIgnore();
    }

    private function startPositionWithCode(): int
    {
        return (int) mb_strpos($this->targetCode(), $this->startPhrase) + mb_strlen($this->startPhrase);
    }

    private function endPositionWithCode(): int
    {
        $erasedEnd = mb_strstr($this->targetCode(), $this->endPhrase, true);
        $eol = mb_strrpos($erasedEnd, PHP_EOL);
        return $eol === false ? mb_strlen($erasedEnd) : $eol;
    }

    public function ignoreCode(): string
    {
        return mb_substr(
            $this->targetCode(),
            $this->startPositionWithCode(),
            $this->endPositionWithCode() - $this->startPositionWithCode()
        );
    }
}
