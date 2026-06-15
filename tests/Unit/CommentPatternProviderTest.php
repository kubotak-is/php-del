<?php
declare(strict_types=1);

use PHPDel\Comment\CommentPatternProvider;
use PHPDel\Comment\Pattern\AltCssPattern;
use PHPDel\Comment\Pattern\BladePhpPattern;
use PHPDel\Comment\Pattern\CssPattern;
use PHPDel\Comment\Pattern\RawPhpPattern;
use PHPDel\Exception\UndefinedExtensionException;
use PHPUnit\Framework\TestCase;

final class CommentPatternProviderTest extends TestCase
{
    public function testProvidesPatternForSupportedExtensions(): void
    {
        self::assertInstanceOf(RawPhpPattern::class, (new CommentPatternProvider('file.php'))->get('flag'));
        self::assertInstanceOf(BladePhpPattern::class, (new CommentPatternProvider('file.blade.php'))->get('flag'));
        self::assertInstanceOf(CssPattern::class, (new CommentPatternProvider('file.css'))->get('flag'));
        self::assertInstanceOf(AltCssPattern::class, (new CommentPatternProvider('file.scss'))->get('flag'));
    }

    public function testRejectsUnsupportedExtensions(): void
    {
        $this->expectException(UndefinedExtensionException::class);

        (new CommentPatternProvider('file.txt'))->get('flag');
    }
}
