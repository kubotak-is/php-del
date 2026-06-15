<?php
declare(strict_types=1);

namespace PHPDel;

readonly class Deleter
{
    public function __construct(private string $text) {}

    public function isDelete(string $deleteFlag): bool
    {
        $flag = preg_quote($deleteFlag, '/');
        $matches = [];
        $result = preg_match_all(
            "/php-del+( |　|\t)+file( |　|\t)+{$flag}(?=\s|\*\/|--}}|$)/iu",
            $this->text,
            $matches
        );

        return !($result === false || $result === 0);
    }

    public function delete(string $file, string $deleteFlag, bool $dryRun = false): bool
    {
        if (!$this->isDelete($deleteFlag)) {
            return false;
        }

        if ($dryRun || unlink($file)) {
            return true;
        }

        throw new \RuntimeException("Unlink Failed.");
    }
}
