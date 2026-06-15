<?php
declare(strict_types=1);

use PHPDel\Flag\Flag;
use PHPDel\Flag\FlagList;
use PHPUnit\Framework\TestCase;

final class FlagListTest extends TestCase
{
    public function testEmpty(): void
    {
        self::assertTrue((new FlagList([]))->empty());
        self::assertFalse((new FlagList(['flag_a' => new Flag('flag_a')]))->empty());
    }

    public function testHas(): void
    {
        $list = new FlagList([
            'flag_a'     => new Flag('flag_a'),
            'error-flag' => new Flag('error-flag'),
        ]);

        self::assertTrue($list->has('flag_a'));
        self::assertTrue($list->has('error-flag'));
        self::assertFalse($list->has('unknown'));
        self::assertFalse($list->has(''));
    }
}
