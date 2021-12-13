<?php
declare(strict_types=1);

namespace PHPDel;

use League\CLImate\CLImate;
use PHPDel\Factory\ConfigFactory;

class Application
{
    public static function main(): void
    {
        $cli = new CLImate();
        $config = ConfigFactory::make();
        $cli->blink()->dim('Finding flag...');
        $finder = new Finder($config);
        $finder->findFlag();
        $flagList = $finder->getFlagList();
        if ($flagList->empty()) {
            $cli->backgroundYellow()->out("Nothing flag.");
            return;
        }
        $input = $cli->radio('Please choice me one of the following flag:', (array)$finder->getFlagList());
        $deleteFlag = $input->prompt();
        foreach ($finder->getTargetFileList() as $file) {
            $text = file_get_contents($file);
            $rewriter = new Rewriter($text);
            $text = $rewriter->exec($deleteFlag);
            if ($rewriter->count() === 0) {
                continue;
            }
            $result = file_put_contents($file, $text);
            $result !== false ?
                $cli->backgroundGreen($file . "({$rewriter->count()})") :
                $cli->backgroundRed($file);
        }
        $cli->out("End php-del");
    }
}
