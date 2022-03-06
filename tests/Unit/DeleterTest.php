<?php
declare(strict_types=1);

class DeleterTest extends \PHPUnit\Framework\TestCase
{
    public function testDeleter()
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/DeleteFlag.php');
        $deleter = new \PHPDel\Deleter($text);
        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForBlade()
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.blade.php');
        $deleter = new \PHPDel\Deleter($text);
        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForCss()
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.css');
        $deleter = new \PHPDel\Deleter($text);
        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }

    public function testDeleterForAltCss()
    {
        $text = file_get_contents(__DIR__ . '/../actual/delete_flag/delete_flag.scss');
        $deleter = new \PHPDel\Deleter($text);
        self::assertTrue($deleter->isDelete('delete_flag'));
        self::assertFalse($deleter->isDelete('not_delete_flag'));
    }
}
