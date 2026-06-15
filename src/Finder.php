<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\File\{AllFileList, TargetFileList};
use PHPDel\Flag\{Flag,FlagList,FlagManager};

class Finder
{
    private AllFileList $allFileList;
    private TargetFileList $targetFileList;
    private FlagList $flagList;

    public function __construct(private readonly Config $config)
    {
        $this->allFileList = (new FileFinder($this->config))->find();
    }

    public function findFlag(): void
    {
        $flagManager = new FlagManager();
        $targetFiles = [];

        foreach ($this->allFileList as $file) {
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

            foreach ($matches['flag'] as $flag) {
                $flagManager->add(new Flag($flag));
            }
        }
        $this->flagList = $flagManager->getFlagList();
        $this->targetFileList = new TargetFileList($targetFiles);
    }

    public function getTargetFileList(): TargetFileList
    {
        return $this->targetFileList;
    }

    public function getFlagList(): FlagList
    {
        return $this->flagList;
    }

}
