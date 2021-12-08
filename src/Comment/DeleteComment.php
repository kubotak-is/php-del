<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteComment extends SandWitchComment
{
    public function __construct(string $target, string $flag)
    {
        parent::__construct($target, $flag);
    }

    protected function matchStartPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+start\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function matchEndPattern(): string
    {
        return "/(\/\/|\/\*)(\*|\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    protected function setStartPosition(): void
    {
        $targetBefore = mb_strstr($this->target, $this->startPhrase, true);
        // 対象コメント以前に最初に現れる改行までの位置(A)
        $charUpToPhpEolPosition = mb_strrpos($targetBefore, PHP_EOL);
        // (A)からコメントまでの文字を抽出(B)
        $fromLettersUpToPhpEolToComments = mb_substr($this->target, $charUpToPhpEolPosition, mb_strlen($targetBefore) - $charUpToPhpEolPosition);
        // (B)に改行および空白文字以外が存在するかチェック
        $charOtherThanPhpEol = preg_match('/(?!.*(\n|\s)).+$/u', $fromLettersUpToPhpEolToComments);
        $this->startPosition = $charOtherThanPhpEol === 0 ? $charUpToPhpEolPosition : mb_strpos($this->target, $this->startPhrase);
    }

    protected function setEndPosition(): void
    {
        $this->endPosition = mb_strpos($this->target, $this->endPhrase) + mb_strlen($this->endPhrase);
    }
}
