<?php
declare(strict_types=1);

class RewriterTest extends \PHPUnit\Framework\TestCase
{
    public function testRewrite()
    {
        $text = file_get_contents(__DIR__ . '/../actual/flag_a/FlagA.php');
        $rewriter = new \PHPDel\Rewriter($text);
        $result = $rewriter->exec('flag_a');

        self::assertDoesNotMatchRegularExpression('/php-del start flag_1$/', $result);
        self::assertDoesNotMatchRegularExpression('/php-del end flag_1$/', $result);
        // ignore
        self::assertNotFalse(strpos($result, 'd = 2;'));
        self::assertNotFalse(strpos($result, '$c = 3;'));

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/FlagA.php');
        self::assertEquals($expect, $result);
    }
}
