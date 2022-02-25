<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class IgnoreComment extends SandWitchComment
{
    protected function matchStartPattern(): string
    {
        return "/(\/\*(\*|\n|\s)*+php-del\s+ignore\s+start+((|\*|\n|\s)*.\/|))|(\/\/(\*|\s)*+php-del\s+ignore\s+start+(|\s)*)/iu";
    }

    protected function matchEndPattern(): string
    {
        return "/(\/\*(\*|\n|\s)*+php-del\s+ignore\s+end+((|\*|\n|\s)*.\/|))|(\/\/(\*|\s)*+php-del\s+ignore\s+end+(|\s)*)/iu";
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
