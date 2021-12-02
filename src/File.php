<?php
declare(strict_types=1);

namespace PHPDel;

class File
{
    public static function getFiles(string $dir, Config $config): array
    {
        $files = [];
        self::rglob(getcwd(). '/' . $dir, $config->getExtensions(), $files);
        return $files;
    }

    public static function rglob(string $dir, array $exts, array &$results=[]) {
        $ls = glob($dir);

        if (is_array($ls)) {
            foreach ($ls as $item) {
                if (is_dir($item)) {
                    self::rglob($item . '/*', $exts, $results);
                }
                if (is_file($item)) {
                    $ext = substr($item, strrpos($item, '.') + 1);
                    if (in_array($ext, $exts, true)) {
                        $results[] = $item;
                    }
                }
            }
        }

        return $results;
    }
}
