<?php
declare(strict_types=1);

use PHPDel\Comment\CommentPatternProvider;
use PHPDel\Exception\SandWitchCommentException;
use PHPDel\Rewriter;
use PHPUnit\Framework\TestCase;

final class RewriterTest extends TestCase
{
    public function testRewrite(): void
    {
        $file = __DIR__ . '/../actual/flag_a/FlagA.php';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        self::assertDoesNotMatchRegularExpression('/php-del start flag_1$/', $result);
        self::assertDoesNotMatchRegularExpression('/php-del end flag_1$/', $result);
        // ignore
        self::assertNotFalse(strpos($result, 'd = 2;'));
        self::assertNotFalse(strpos($result, '$c = 3;'));

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/FlagA.php');
        self::assertSame($expect, $result);
    }

    public function testRewriteForBlade(): void
    {
        $file = __DIR__ . '/../actual/flag_a/flag-a.blade.php';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/flag-a.blade.php');
        self::assertSame($expect, $result);
    }

    public function testRewriteForCSS(): void
    {
        $file = __DIR__ . '/../actual/flag_a/flag-a.css';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/flag-a.css');
        self::assertSame($expect, $result);
    }

    public function testRewriteForAltCSS(): void
    {
        $file = __DIR__ . '/../actual/flag_a/flag-a.scss';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('flag_a');
        $result = $rewriter->exec($pattern);

        // Perfect matching
        $expect = file_get_contents(__DIR__ . '/../expect/flag_a/flag-a.scss');
        self::assertSame($expect, $result);
    }

    public function testFlagMustMatchCompletely(): void
    {
        $text = <<<'PHP'
<?php
/** php-del start release_candidate */
$candidate = true;
/** php-del end release_candidate */
PHP;
        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider('file.php'))->get('release');

        self::assertSame($text, $rewriter->exec($pattern));
        self::assertSame(0, $rewriter->count());
    }

    public function testRewriteException(): void
    {
        $this->expectException(SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/ErrorFlag.php';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }

    public function testRewriteExceptionForBlade(): void
    {
        $this->expectException(SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/error-flag.blade.php';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }

    public function testRewriteExceptionForCss(): void
    {
        $this->expectException(SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/error-flag.css';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }

    public function testRewriteExceptionForAltCss(): void
    {
        $this->expectException(SandWitchCommentException::class);
        $file = __DIR__ . '/../actual/error_flag/error-flag.scss';
        $text = file_get_contents($file);
        self::assertIsString($text);

        $rewriter = new Rewriter($text);
        $pattern = (new CommentPatternProvider($file))->get('error-flag');
        $rewriter->exec($pattern);
    }
}
