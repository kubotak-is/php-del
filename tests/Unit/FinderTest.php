<?php
declare(strict_types=1);

use PHPDel\Config;
use PHPDel\Finder;
use PHPUnit\Framework\TestCase;

final class FinderTest extends TestCase
{
    public function testFinder(): void
    {
        $config = new Config(['dirs' => ['tests/actual'], 'extensions' => ['php', 'css', 'scss']]);
        $finder = new Finder($config);
        $finder->findFlag();

        $targetList = $finder->getTargetFileList();
        self::assertCount(13, $targetList);

        $flagList = $finder->getFlagList();
        self::assertSame('flag_a', $flagList->offsetGet('flag_a')->get());
        self::assertSame(23, $flagList->offsetGet('flag_a')->count());
        self::assertSame('flag_a (23)', (string) $flagList->offsetGet('flag_a'));
        self::assertSame('flag_b', $flagList->offsetGet('flag_b')->get());
        self::assertSame(3, $flagList->offsetGet('flag_b')->count());
        self::assertSame('error-flag', $flagList->offsetGet('error-flag')->get());
        self::assertSame(8, $flagList->offsetGet('error-flag')->count());
        self::assertSame('delete_flag', $flagList->offsetGet('delete_flag')->get());
        self::assertSame(4, $flagList->offsetGet('delete_flag')->count());
    }
}
