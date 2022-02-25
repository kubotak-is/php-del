<?php
declare(strict_types=1);

namespace PHPDel\Comment;

use PHPDel\Exception\SandWitchCommentException;

abstract class SandWitchComment extends Comment
{
    protected string $startPhrase;
    protected string $endPhrase;
    private bool $foundStart = false;
    private bool $foundEnd = false;

    public function __construct(string $target, ?string $flag = null)
    {
        parent::__construct($target, $flag);
        $this->setStart();
        $this->setEnd();
        $this->setFound();
    }

    private function setStart(): void
    {
        $matches = [];
        $result = preg_match($this->matchStartPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->foundStart = true;
            $this->startPhrase = $matches[0][0];
            $this->setStartPosition();
        }
    }

    private function setEnd(): void
    {
        $matches = [];
        $result = preg_match($this->matchEndPattern(), $this->target, $matches, PREG_OFFSET_CAPTURE);
        if ($result === 1) {
            $this->foundEnd = true;
            $this->endPhrase = $matches[0][0];
            $this->setEndPosition();
        }
    }

    private function setFound(): void
    {
        if ($this->foundStart && !$this->foundEnd) {
            throw new SandWitchCommentException("There is a start comment, but no end.");
        }
        if (!$this->foundStart && $this->foundEnd) {
            throw new SandWitchCommentException("There is an end comment, but no start.");
        }
        $this->found = $this->foundStart && $this->foundEnd;
    }

    protected function setStartPosition(): void
    {
        $targetBefore = (string) mb_strstr($this->target, $this->startPhrase, true);
        // 対象コメント以前に最初に現れる改行までの位置(A)
        $charUpToPhpEolPosition = (int) mb_strrpos($targetBefore, PHP_EOL);
        // (A)からコメントまでの文字を抽出(B)
        $fromLettersUpToPhpEolToComments = mb_substr($this->target, $charUpToPhpEolPosition, mb_strlen($targetBefore) - $charUpToPhpEolPosition);
        // (B)に改行および空白文字以外が存在するかチェック
        $charOtherThanPhpEol = mb_strlen(trim($fromLettersUpToPhpEolToComments, " \t\n\r"));
        $this->startPosition = $charOtherThanPhpEol === 0 ? $charUpToPhpEolPosition : mb_strpos($this->target, $this->startPhrase);
    }

    protected function setEndPosition(): void
    {
        $this->endPosition = mb_strpos($this->target, $this->endPhrase) + mb_strlen($this->endPhrase);
    }

    abstract protected function matchStartPattern(): string;
    abstract protected function matchEndPattern(): string;
}
