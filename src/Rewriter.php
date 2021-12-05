<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\Comment\DeleteEndComment;
use PHPDel\Comment\DeleteStartComment;

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
            $startPhrase = "/** php-del ignore start */";
            $startPosition = mb_strpos($text, $startPhrase);
            if ($startPosition === false) {
                break;
            }
            $startPosition = mb_strrpos(mb_strstr($text, $startPhrase, true), PHP_EOL);

            $endPhrase = "/** php-del ignore end */";
            $endPosition = mb_strpos($text, $endPhrase);
            if ($endPosition === false) {
                break;
            }
            $endPosition += mb_strlen($endPhrase);
            $deleteStr = mb_substr($text, $startPosition, $endPosition - $startPosition);

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
        $startPhrase = "/** php-del ignore start */";
        $startPosition = mb_strpos($text, $startPhrase);
        if ($startPosition === false) {
            return '';
        }
        $startPosition += mb_strlen($startPhrase);
        $endPhrase = "/** php-del ignore end */";
        $endPosition = mb_strpos($text, $endPhrase);
        if ($endPosition === false) {
            return '';
        }
        $endPosition = mb_strrpos(mb_strstr($text, $endPhrase, true), PHP_EOL);
        return mb_substr($text, $startPosition, $endPosition - $startPosition);
    }
}
