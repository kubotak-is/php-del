<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\File\TargetFileList;
use PHPDel\Flag\{Flag,FlagList,FlagManager};

class Finder
{
    private TargetFileList $targetFileList;
    private FlagList $flagList;
    /** @var array<string, list<string>> */
    private array $targetFilesByFlag = [];

    public function __construct(private readonly Config $config)
    {
    }

    /**
     * @param (callable(string): void)|null $onPath
     */
    public function findFlag(?callable $onPath = null): void
    {
        $flagManager = new FlagManager();
        $targetFiles = [];
        $this->targetFilesByFlag = [];

        foreach ((new FileFinder($this->config))->findFiles($onPath) as $file) {
            $text = file_get_contents($file);

            if ($text === false) {
                throw new \RuntimeException("Unable to read file: {$file}");
            }

            $matches = [];
            $result = preg_match_all(
                "/php-del+( |　|\t)+(start|line|file)( |　|\t)+(?<flag>[a-z0-9_\-=]+)/iu",
                $text,
                $matches
            );

            if ($result === false || $result === 0) {
                continue;
            }

            $targetFiles[] = $file;
            $fileFlags = [];

            foreach ($matches['flag'] as $flag) {
                $flagManager->add(new Flag($flag));
                $fileFlags[$flag] = true;
            }

            foreach (array_keys($fileFlags) as $flag) {
                $this->targetFilesByFlag[$flag][] = $file;
            }
        }
        $this->flagList = $flagManager->getFlagList();
        $this->targetFileList = new TargetFileList($targetFiles);
    }

    public function getTargetFileList(?string $flag = null): TargetFileList
    {
        if ($flag !== null) {
            return new TargetFileList($this->targetFilesByFlag[$flag] ?? []);
        }

        return $this->targetFileList;
    }

    public function getFlagList(): FlagList
    {
        return $this->flagList;
    }

}
