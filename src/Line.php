<?php
declare(strict_types=1);

namespace PHPDel;

class Line
{
    public static function standard(string $str): void
    {
        echo "\033[1;37m" . $str . "\033[0m" . PHP_EOL;
    }

    public static function success(string $str): void
    {
        echo "\033[1;37m\033[42m" . $str . "\033[0m" . PHP_EOL;
    }

    public static function warn(string $str): void
    {
        echo "\033[1;37m\033[43m" . $str . "\033[0m" . PHP_EOL;
    }

    public static function error(string $str): void
    {
        echo "\033[1;37m\033[41m" . $str . "\033[0m" . PHP_EOL;
    }
}
