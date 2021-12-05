<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\Comment\DeleteEndComment;
use PHPDel\Comment\DeleteStartComment;
use PHPDel\Comment\IgnoreEndComment;
use PHPDel\Comment\IgnoreStartComment;

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
        while (true) {
            $deleteStartComment = new DeleteStartComment($text, $deleteFlag);
            $deleteEndComment = new DeleteEndComment($text, $deleteFlag);
            if (!$deleteStartComment->has() || !$deleteEndComment->has()) {
                break;
            }
            $deleteStr = mb_substr($text, $deleteStartComment->position(), $deleteEndComment->position() - $deleteStartComment->position());

            $ignore = $this->ignore($deleteStr);

            $text = str_replace($deleteStr, $ignore, $text);
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
            $ignoreStartComment = new IgnoreStartComment($text);
            $ignoreEndComment = new IgnoreEndComment($text);
            if (!$ignoreStartComment->has() || !$ignoreEndComment->has()) {
                break;
            }
            $deleteStr = mb_substr($text, $ignoreStartComment->position(), $ignoreEndComment->position() - $ignoreStartComment->position());

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
        $ignoreStartComment = new IgnoreStartComment($text);
        $ignoreEndComment = new IgnoreEndComment($text);
        return mb_substr(
            $text,
            $ignoreStartComment->positionWithCode(),
            $ignoreEndComment->positionWithCode() - $ignoreStartComment->positionWithCode()
        );
    }
}
