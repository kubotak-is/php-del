<?php
declare(strict_types=1);

class FinderTest extends \PHPUnit\Framework\TestCase
{
    public function testFinder()
    {
        $config = new \PHPDel\Config(['dirs' => ['tests/actual']]);
        $finder = new \PHPDel\Finder($config);
        $finder->findFlag();

        $targetList = $finder->getTargetFileList();
        self::assertEquals(7, $targetList->count());

        $flagList = $finder->getFlagList();
        self::assertEquals('flag_a', $flagList->offsetGet('flag_a')->get());
        self::assertEquals(16, $flagList->offsetGet('flag_a')->count());
        self::assertEquals('flag_a (16)', $flagList->offsetGet('flag_a'));
        self::assertEquals('flag_b', $flagList->offsetGet('flag_b')->get());
        self::assertEquals(3, $flagList->offsetGet('flag_b')->count());
        self::assertEquals('error-flag', $flagList->offsetGet('error-flag')->get());
        self::assertEquals(4, $flagList->offsetGet('error-flag')->count());
        self::assertEquals('delete_flag', $flagList->offsetGet('delete_flag')->get());
        self::assertEquals(2, $flagList->offsetGet('delete_flag')->count());
    }
}
