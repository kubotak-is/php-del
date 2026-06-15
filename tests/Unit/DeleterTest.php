<?php
declare(strict_types=1);

use PHPDel\Deleter;
use PHPUnit\Framework\TestCase;

final class DeleterTest extends TestCase
{
    public function testDeleter(): void
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/DeleteFlag.php');
        self::assertIsString($text);

        $deleter = new Deleter($text);

        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForBlade(): void
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.blade.php');
        self::assertIsString($text);

        $deleter = new Deleter($text);

        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForCss(): void
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.css');
        self::assertIsString($text);

        $deleter = new Deleter($text);

        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForAltCss(): void
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.scss');
        self::assertIsString($text);

        $deleter = new Deleter($text);

        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleteFlagMustMatchCompletely(): void
    {
        $deleter = new Deleter('/** php-del file release_candidate */');

        self::assertTrue($deleter->isDelete('release_candidate'));
        self::assertFalse($deleter->isDelete('release'));
    }

    public function testDeleteRemovesMatchingFile(): void
    {
        $file = $this->createTemporaryFile();
        $deleter = new Deleter('/** php-del file release */');

        self::assertTrue($deleter->delete($file, 'release'));
        self::assertFileDoesNotExist($file);
    }

    public function testDryRunDoesNotRemoveMatchingFile(): void
    {
        $file = $this->createTemporaryFile();
        $deleter = new Deleter('/** php-del file release */');

        try {
            self::assertTrue($deleter->delete($file, 'release', true));
            self::assertFileExists($file);
        } finally {
            unlink($file);
        }
    }

    private function createTemporaryFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'php-del-');
        self::assertIsString($file);

        return $file;
    }
}
