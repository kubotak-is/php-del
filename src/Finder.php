<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\File\{AllFileList,TargetFileList};
use PHPDel\Flag\{Flag,FlagList,FlagManager};

class Finder
{
    private Config $config;
    private AllFileList $allFileList;
    private TargetFileList $targetFileList;
    private FlagList $flagList;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setAllFileList();
    }

    public function findFlag(): void
    {
        $flagManager = new FlagManager();
        $targetFiles = [];
        foreach ($this->allFileList as $file) {
            $text = file_get_contents($file);
            $matches = [];
            $result = preg_match_all("/php-del+\s+(start|line)\s+(?<flag>[a-z0-9_\-=]*)/iu", $text, $matches);
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

    private function setAllFileList(): void
    {
        $this->allFileList = $this->getAllFileList($this->config->getDirs());
    }

    private function getAllFileList(array $dirs): AllFileList
    {
        $files = [];
        foreach ($dirs as $dir) {
            $files = [...$this->getFiles($dir, $this->config), ...$files];
        }
        return new AllFileList($files);
    }

    private function getFiles(string $dir, Config $config): array
    {
        $files = [];
        $this->rglob(getcwd(). '/' . $dir, $config->getExtensions(), $files);
        return $files;
    }

    private function rglob(string $dir, array $exts, array &$results = []): void
    {
        $ls = glob($dir);
        if (is_array($ls)) {
            foreach ($ls as $item) {
                if (is_dir($item)) {
                    $this->rglob($item . '/*', $exts, $results);
                }
                if (is_file($item)) {
                    $ext = substr($item, strrpos($item, '.') + 1);
                    if (in_array($ext, $exts, true)) {
                        $results[] = $item;
                    }
                }
            }
        }
    }
}
