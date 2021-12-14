<?php
declare(strict_types=1);

namespace actual\error_flag;

class ErrorFlag
{
    public function hoge()
    {
        // php-del start error-flag
        $a = 1;
        // php-del start error-flag
    }

    public function fuga()
    {
        // php-del end error-flag
        $b = 1;
        // php-del end error-flag
    }
}
