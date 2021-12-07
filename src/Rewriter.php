<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\Comment\{DeleteComment,IgnoreComment,LineComment};

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
            if ($deleteComment->notfound()) {
                break;
            }
            $deleteCode = $deleteComment->targetCode();

            $ignore = $this->ignore($deleteCode);

            $text = str_replace($deleteCode, $ignore, $text);
            ++$this->count;
        }
        // single line delete
        while (true) {
            $lineComment = new LineComment($text, $deleteFlag);
            if ($lineComment->notfound()) {
                break;
            }
            $deleteCode = $lineComment->targetCode();
            $text = str_replace($deleteCode, '', $text);
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
            if ($ignoreComment->notfound()) {
                break;
            }
            $deleteCode = $ignoreComment->targetCode();

            /**
             * ignoreコメントを除外した、ignoreしたいコードのみを抽出
             */
            $ignore .= $ignoreComment->ignoreCode();

            $text = str_replace($deleteCode, '', $text);
        }
        return $ignore;
    }
}
