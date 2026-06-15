<?php
declare(strict_types=1);

use PHPDel\Validation\Marker;
use PHPDel\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testAcceptsValidNestedBlocksAndIgnorePair(): void
    {
        $diagnostics = $this->validator->validate('src/file.php', [
            $this->marker(Marker::START, 'a', 1),
            $this->marker(Marker::START, 'b', 2),
            $this->marker(Marker::IGNORE_START, null, 3),
            $this->marker(Marker::IGNORE_END, null, 4),
            $this->marker(Marker::END, 'b', 5),
            $this->marker(Marker::END, 'a', 6),
            $this->marker(Marker::LINE, 'line', 7),
            $this->marker(Marker::FILE, 'file', 8),
        ]);

        self::assertSame([], $diagnostics);
    }

    public function testReportsUnmatchedBlockMarkers(): void
    {
        $diagnostics = $this->validator->validate('src/file.php', [
            $this->marker(Marker::START, 'open', 1),
            $this->marker(Marker::END, 'orphan', 2),
        ]);

        self::assertSame(['PDEL001', 'PDEL002'], array_map(
            static fn ($diagnostic): string => $diagnostic->id,
            $diagnostics
        ));
    }

    public function testReportsNestedSameFlagAndCrossingBlocks(): void
    {
        $diagnostics = $this->validator->validate('src/file.php', [
            $this->marker(Marker::START, 'a', 1),
            $this->marker(Marker::START, 'a', 2),
            $this->marker(Marker::END, 'a', 3),
            $this->marker(Marker::START, 'b', 4),
            $this->marker(Marker::END, 'a', 5),
            $this->marker(Marker::END, 'b', 6),
        ]);

        self::assertSame(['PDEL003', 'PDEL004'], array_map(
            static fn ($diagnostic): string => $diagnostic->id,
            $diagnostics
        ));
    }

    public function testReportsInvalidIgnoreStructures(): void
    {
        $diagnostics = $this->validator->validate('src/file.php', [
            $this->marker(Marker::IGNORE_END, null, 1),
            $this->marker(Marker::START, 'a', 2),
            $this->marker(Marker::IGNORE_START, null, 3),
            $this->marker(Marker::IGNORE_START, null, 4),
            $this->marker(Marker::END, 'a', 5),
        ]);

        self::assertSame(
            ['PDEL008', 'PDEL009', 'PDEL007', 'PDEL007', 'PDEL010'],
            array_map(static fn ($diagnostic): string => $diagnostic->id, $diagnostics)
        );
    }

    private function marker(string $type, ?string $flag, int $line): Marker
    {
        return new Marker($type, $flag, $line, 1, $line);
    }
}
