<?php
declare(strict_types=1);

namespace PHPDel;

class Deleter
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function isDelete(string $deleteFlag):bool
    {
        $matches = [];
        $result = preg_match_all("/php-del+( |　|\t)+file( |　|\t)+{$deleteFlag}/iu", $this->text, $matches);
        return !($result === false || $result === 0);
    }
}
