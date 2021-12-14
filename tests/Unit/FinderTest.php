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
        self::assertEquals(3, $targetList->count());

        $flagList = $finder->getFlagList();
        self::assertEquals('flag_a', $flagList->offsetGet('flag_a')->get());
        self::assertEquals(7, $flagList->offsetGet('flag_a')->count());
        self::assertEquals('flag_a (7)', $flagList->offsetGet('flag_a'));
        self::assertEquals('flag_b', $flagList->offsetGet('flag_b')->get());
        self::assertEquals(3, $flagList->offsetGet('flag_b')->count());
        self::assertEquals('error-flag', $flagList->offsetGet('error-flag')->get());
        self::assertEquals(2, $flagList->offsetGet('error-flag')->count());
    }
}
