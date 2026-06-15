<?php
declare(strict_types=1);

namespace PHPDel;

use PHPDel\File\AllFileList;

readonly class FileFinder
{
    public function __construct(private Config $config)
    {
    }

    public function find(): AllFileList
    {
        $files = [];

        foreach ($this->config->getDirs() as $dir) {
            $directoryFiles = [];
            $this->rglob(getcwd() . '/' . $dir, $this->config->getExtensions(), $directoryFiles);
            $files = [...$directoryFiles, ...$files];
        }

        return new AllFileList($files);
    }

    private function rglob(string $dir, array $extensions, array &$results): void
    {
        $items = glob($dir);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if (is_dir($item)) {
                $this->rglob($item . '/*', $extensions, $results);
                continue;
            }

            if (!is_file($item)) {
                continue;
            }

            $extension = pathinfo($item, PATHINFO_EXTENSION);

            if (in_array($extension, $extensions, true)) {
                $results[] = $item;
            }
        }
    }
}
