<?php
declare(strict_types=1);

namespace PHPDel;

use Generator;
use PHPDel\File\AllFileList;

readonly class FileFinder
{
    public function __construct(private Config $config)
    {
    }

    public function find(): AllFileList
    {
        return new AllFileList(iterator_to_array($this->findFiles(), false));
    }

    /**
     * @return Generator<int, string>
     */
    public function findFiles(): Generator
    {
        foreach ($this->rootDirs() as $dir) {
            yield from $this->rglob($dir, $this->config->getExtensions());
        }
    }

    /**
     * @return list<string>
     */
    private function rootDirs(): array
    {
        $candidates = [];
        $roots = [];

        foreach ($this->config->getDirs() as $dir) {
            $path = getcwd() . '/' . $dir;
            $key = realpath($path);
            $key = $key === false ? rtrim($path, DIRECTORY_SEPARATOR) : $key;
            $candidates[$key] = $path . '/*';
        }

        uksort(
            $candidates,
            static function (string $a, string $b): int {
                $length = strlen($a) <=> strlen($b);

                return $length !== 0 ? $length : strcmp($a, $b);
            }
        );

        foreach ($candidates as $key => $path) {
            if ($this->isNestedRoot($key, $roots)) {
                continue;
            }

            $roots[$key] = $path;
        }

        return array_values($roots);
    }

    /**
     * @param array<string, string> $roots
     */
    private function isNestedRoot(string $key, array $roots): bool
    {
        foreach (array_keys($roots) as $root) {
            if ($key === $root || str_starts_with($key . DIRECTORY_SEPARATOR, $root . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $extensions
     * @return Generator<int, string>
     */
    private function rglob(string $dir, array $extensions): Generator
    {
        $items = glob($dir);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if (is_dir($item)) {
                yield from $this->rglob($item . '/*', $extensions);
                continue;
            }

            if (!is_file($item)) {
                continue;
            }

            $extension = pathinfo($item, PATHINFO_EXTENSION);

            if (in_array($extension, $extensions, true)) {
                yield $item;
            }
        }
    }
}
