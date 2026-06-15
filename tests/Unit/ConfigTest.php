<?php
declare(strict_types=1);

use PHPDel\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testPhpIsTheDefaultExtension(): void
    {
        $config = new Config(['dirs' => ['src']]);

        self::assertSame(['src'], $config->getDirs());
        self::assertSame(['php'], $config->getExtensions());
    }

    public function testDirsMustBeAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Config(['dirs' => 'src']);
    }

    public function testExtensionsMustBeAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Config(['dirs' => ['src'], 'extensions' => 'php']);
    }

    public function testDirsMustContainOnlyStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Config(['dirs' => ['src', 1]]);
    }

    public function testExtensionsMustContainOnlyStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Config(['dirs' => ['src'], 'extensions' => ['php', false]]);
    }
}
