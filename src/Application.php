<?php
declare(strict_types=1);

namespace PHPDel;

use League\CLImate\CLImate;
use PHPDel\Factory\ConfigFactory;

class Application
{
    private CLImate $cli;

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
        $this->initCli();
    }

    private function initCli(): void
    {
        $this->cli->arguments->add([
            'dry-run' => [
                'longPrefix'  => 'dry-run',
                'description' => 'Make it work without executing delete',
                'noValue'     => true,
            ],
            'help' => [
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true,
            ],
        ]);
        $this->cli->arguments->parse();
    }

    private function help(): bool
    {
        return (bool) $this->cli->arguments->get('help');
    }

    private function isDryRun(): bool
    {
        return (bool) $this->cli->arguments->get('dry-run');
    }

    public function main(): void
    {
        if ($this->help()) {
            $this->cli->usage();
            return;
        }
        $config = ConfigFactory::make();
        $this->cli->blink()->dim('Finding flag...');
        $finder = new Finder($config);
        $finder->findFlag();
        $flagList = $finder->getFlagList();
        if ($flagList->empty()) {
            $this->cli->backgroundYellow()->out("Nothing flag.");
            return;
        }
        $input = $this->cli->radio('Please choice me one of the following flag:', (array)$finder->getFlagList());
        $deleteFlag = $input->prompt();
        foreach ($finder->getTargetFileList() as $file) {
            try {
                $text = file_get_contents($file);
                if ($this->deleteFileWhenHasFlag($file, $text, $deleteFlag)) {
                    $this->cli->backgroundGreen($file . "(delete)");
                    continue;
                }
                $rewriter = new Rewriter($text);
                $text = $rewriter->exec($deleteFlag);
                if ($rewriter->count() === 0) {
                    continue;
                }
                if (!$this->isDryRun()) {
                    $result = file_put_contents($file, $text);
                    if ($result === false) {
                        throw new \RuntimeException("File Put Contents Error.");
                    }
                }
                $this->cli->backgroundGreen($file . "({$rewriter->count()})");
            } catch (\Throwable $throwable) {
                $this->cli->backgroundRed($file);
                $this->cli->error("[ERROR] " . $throwable->getMessage());
            }
        }
        $this->cli->out("End php-del");
    }

    private function deleteFileWhenHasFlag(string $file, string $text, string $deleteFlag): bool
    {
        $deleter = new Deleter($text);
        if (!$deleter->isDelete($deleteFlag)) {
            return false;
        }
        $result = unlink($file);
        if ($result) {
            return true;
        }
        throw new \RuntimeException("Unlink Failed.");
    }
}
