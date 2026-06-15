<?php
declare(strict_types=1);

namespace PHPDel\Factory;

use PHPDel\Config;

class ConfigFactory
{
    public static function make(): Config
    {
        $path = getcwd() . "/php-del.json";
        $json = file_get_contents($path);

        if ($json === false) {
            throw new \RuntimeException("Unable to read configuration: {$path}");
        }

        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new Config($arr);
    }
}
