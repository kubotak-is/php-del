<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\Factory\ConfigFactory;

class Application
{
    public static function main(string $deleteFlag): void
    {
        if ($deleteFlag === '') {
            Line::error(' Delete flag is required ');
            return;
        }
        Line::standard("Start php-del".PHP_EOL."Delete is \"{$deleteFlag}\"");
        $config = ConfigFactory::make();
        foreach ($config->getDirs() as $dir) {
            $files = File::getFiles($dir, $config);
            foreach ($files as $file) {
                $text = file_get_contents($file);
                $rewriter = new Rewriter($text);
                $text = $rewriter->exec($deleteFlag);
                if ($rewriter->count() === 0) {
                    continue;
                }
                $result = file_put_contents($file, $text);
                $result !== false ?
                    Line::success($file . "({$rewriter->count()})") :
                    Line::error($file);
            }
        }
        Line::standard("End php-del");
    }
}
