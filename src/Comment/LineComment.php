<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class LineComment extends Comment
{
    private string $phrase;

    public function __construct(string $target, string $flag)
    {
        parent::__construct($target, $flag);
        $this->init();
    }

    private function matchPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\s|).!?\n*+php-del\s+line\s+{$this->flag}+((|\*|\s).!?\n*.\/|)/iu";
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
        $this->startPosition = mb_strrpos(mb_strstr($this->target, $this->phrase, true), PHP_EOL);
    }

    private function setEndPosition(): void
    {
        $this->endPosition = mb_strpos($this->target, $this->phrase) + mb_strlen($this->phrase);
    }
}
