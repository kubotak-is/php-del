<?php
declare(strict_types=1);

class FileTest extends \PHPUnit\Framework\TestCase
{
    public function testGetFileArray()
    {
        $config = new \PHPDel\Config([
            'dirs' => ['tests/actual']
        ]);
        $files = \PHPDel\File::getFiles($config->getDirs()[0], $config);
        self::assertCount(2, $files);
    }
}
