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
}
