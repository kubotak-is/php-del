<?php
declare(strict_types=1);

namespace PHPDel;

class File
{
    public static function getFiles(string $dir, Config $config): array
    {
        return glob(getcwd(). '/' . $dir . '/**/*.{' . $config->getExtensions() . '}', GLOB_BRACE) ?? [];
    }
}
