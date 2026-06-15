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

        if ($json === false) {
            throw new \RuntimeException("Unable to convert configuration encoding: {$path}");
        }

        $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($arr)) {
            throw new \InvalidArgumentException('The configuration root must be an object.');
        }

        return new Config($arr);
    }
}
