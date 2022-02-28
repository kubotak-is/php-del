<?php
declare(strict_types=1);

class RewriterTest extends \PHPUnit\Framework\TestCase
{
    public function testRewrite()
    {
        $file = __DIR__ . '/../actual/flag_a/FlagA.php';
        $text = file_get_contents($file);
        $rewriter = new \PHPDel\Rewriter($text);
        $pattern = (new \PHPDel\Comment\CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        self::assertDoesNotMatchRegularExpression('/php-del start flag_1$/', $result);
        self::assertDoesNotMatchRegularExpression('/php-del end flag_1$/', $result);
        // ignore
        self::assertNotFalse(strpos($result, 'd = 2;'));
        self::assertNotFalse(strpos($result, '$c = 3;'));

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/FlagA.php');
        self::assertEquals($expect, $result);
    }

    public function testRewriteForBlade()
    {
        $file = __DIR__ . '/../actual/flag_a/flag-a.blade.php';
        $text = file_get_contents($file);
        $rewriter = new \PHPDel\Rewriter($text);
        $pattern = (new \PHPDel\Comment\CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/flag-a.blade.php');
        self::assertEquals($expect, $result);
    }

    public function testRewriteException()
    {
        $this->expectException(\PHPDel\Exception\SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/ErrorFlag.php';
        $text = file_get_contents($file);
        $rewriter = new \PHPDel\Rewriter($text);
        $pattern = (new \PHPDel\Comment\CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }

    public function testRewriteExceptionForBlade()
    {
        $this->expectException(\PHPDel\Exception\SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/error-flag.blade.php';
        $text = file_get_contents($file);
        $rewriter = new \PHPDel\Rewriter($text);
        $pattern = (new \PHPDel\Comment\CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }
}
