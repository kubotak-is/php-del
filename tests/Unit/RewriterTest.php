<?php
declare(strict_types=1);

class RewriterTest extends \PHPUnit\Framework\TestCase
{
    public function testRewrite()
    {
        $text = file_get_contents(__DIR__ . '/../dir/1/Test1.php');
        $rewriter = new \PHPDel\Rewriter($text);
        $result = $rewriter->exec('flag_1');

        self::assertDoesNotMatchRegularExpression('/php-del start flag_1$/', $result);
        self::assertDoesNotMatchRegularExpression('/php-del end flag_1$/', $result);
        // ignore
        self::assertNotFalse(strpos($result, 'd = 2;'));
        self::assertNotFalse(strpos($result, '$c = 3;'));

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/Rewrited.php');
        self::assertEquals($expect, $result);
    }
}
