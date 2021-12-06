<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\Comment\DeleteComment;
use PHPDel\Comment\IgnoreComment;
use PHPDel\Comment\LineComment;

class Rewriter
{
    private int $count = 0;
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function exec(string $deleteFlag): string
    {
        $text = $this->text;
        // multi line delete
        while (true) {
            $deleteComment = new DeleteComment($text, $deleteFlag);
            if (!$deleteComment->has()) {
                break;
            }
            $deleteStr = mb_substr($text, $deleteComment->startPosition(), $deleteComment->endPosition() - $deleteComment->startPosition());

            $ignore = $this->ignore($deleteStr);

            $text = str_replace($deleteStr, $ignore, $text);
            ++$this->count;
        }
        // single line delete
        while (true) {
            $lineComment = new LineComment($text, $deleteFlag);
            if (!$lineComment->has()) {
                break;
            }
            $deleteStr = mb_substr($text, $lineComment->startPosition(), $lineComment->endPosition() - $lineComment->startPosition());
            $text = str_replace($deleteStr, '', $text);
            ++$this->count;
        }
        return $text;
    }

    private function ignore(string $text): string
    {
        $ignore = '';
        while (true) {
            /**
             * コメントを含む開始位置から終了位置までを検索して削除
             */
            $ignoreComment = new IgnoreComment($text);
            if (!$ignoreComment->has()) {
                break;
            }
            $deleteStr = mb_substr($text, $ignoreComment->startPosition(), $ignoreComment->endPosition() - $ignoreComment->startPosition());

            /**
             * ignoreコメントを除外した、ignoreしたいコードのみを抽出
             */
            $ignore .= $this->ignoreCode($deleteStr);

            $text = str_replace($deleteStr, '', $text);
        }
        return $ignore;
    }

    private function ignoreCode(string $text): string
    {
        $ignoreComment = new IgnoreComment($text);
        return mb_substr(
            $text,
            $ignoreComment->startPositionWithCode(),
            $ignoreComment->endPositionWithCode() - $ignoreComment->startPositionWithCode()
        );
    }
}
