<?php
declare(strict_types=1);

use PHPDel\Validation\Marker;
use PHPDel\Validation\MarkerScanner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MarkerScannerTest extends TestCase
{
    private MarkerScanner $scanner;

    protected function setUp(): void
    {
        $this->scanner = new MarkerScanner();
    }

    public function testScansPhpCommentsAndIgnoresStrings(): void
    {
        $text = <<<'PHP'
<?php
$example = 'php-del start string-value';
/** php-del start legacy */
$legacy = true;
/** php-del ignore start */
$keep = true;
/** php-del ignore end */
/** php-del end legacy */
// php-del line one-line
PHP;

        $markers = $this->scanner->scan('example.php', $text);

        self::assertSame(
            [
                Marker::START,
                Marker::IGNORE_START,
                Marker::IGNORE_END,
                Marker::END,
                Marker::LINE,
            ],
            array_map(static fn (Marker $marker): string => $marker->type, $markers)
        );
        self::assertSame('legacy', $markers[0]->flag);
        self::assertSame(3, $markers[0]->line);
        self::assertSame(5, $markers[0]->column);
    }

    #[DataProvider('formatProvider')]
    public function testScansSupportedCommentFormats(string $file, string $text): void
    {
        $markers = $this->scanner->scan($file, $text);

        self::assertCount(2, $markers);
        self::assertSame(Marker::START, $markers[0]->type);
        self::assertSame(Marker::END, $markers[1]->type);
    }

    public static function formatProvider(): iterable
    {
        yield 'Blade' => [
            'example.blade.php',
            "{{-- php-del start blade --}}\n{{-- php-del end blade --}}\n",
        ];
        yield 'CSS' => [
            'example.css',
            "/* php-del start css */\n/* php-del end css */\n",
        ];
        yield 'SCSS' => [
            'example.scss',
            "// php-del start scss\n// php-del end scss\n",
        ];
        yield 'Sass' => [
            'example.sass',
            "/* php-del start sass */\n/* php-del end sass */\n",
        ];
        yield 'Stylus' => [
            'example.stylus',
            "// php-del start stylus\n// php-del end stylus\n",
        ];
    }

    public function testReportsMalformedMarkers(): void
    {
        $text = <<<'PHP'
<?php
// php-del start
// php-del line feature/value
// php-del something feature
PHP;

        $markers = $this->scanner->scan('example.php', $text);

        self::assertSame(['PDEL005', 'PDEL006', 'PDEL011'], array_map(
            static fn (Marker $marker): ?string => $marker->errorId,
            $markers
        ));
    }

    public function testCalculatesUtf8ColumnAndCrLfLine(): void
    {
        $text = "<?php\r\n// 日本語 php-del file legacy\r\n";

        $markers = $this->scanner->scan('example.php', $text);

        self::assertCount(1, $markers);
        self::assertSame(2, $markers[0]->line);
        self::assertSame(8, $markers[0]->column);
    }
}
