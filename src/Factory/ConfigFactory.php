<?php
declare(strict_types=1);

namespace PHPDel\Factory;

use PHPDel\Config;

class ConfigFactory
{
    public static function make(): Config
    {
        $json = file_get_contents(getcwd(). "/phpdel.json");
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return new Config($arr);
    }
}
