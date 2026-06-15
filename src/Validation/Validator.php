<?php
declare(strict_types=1);

namespace PHPDel\Validation;

class Validator
{
    /**
     * @param list<Marker> $markers
     * @return list<Diagnostic>
     */
    public function validate(string $path, array $markers): array
    {
        $diagnostics = [];
        $blocks = [];
        $ignores = [];

        foreach ($markers as $marker) {
            if ($marker->type === Marker::INVALID) {
                $diagnostics[] = $this->diagnostic(
                    $path,
                    $marker,
                    (string) $marker->errorId,
                    (string) $marker->errorMessage
                );
                continue;
            }

            if ($marker->type === Marker::START) {
                if ($this->containsFlag($blocks, (string) $marker->flag)) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL003',
                        "nested block for \"{$marker->flag}\" is not supported"
                    );
                }

                $blocks[] = $marker;
                continue;
            }

            if ($marker->type === Marker::END) {
                $index = $this->lastFlagIndex($blocks, (string) $marker->flag);

                if ($index === null) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL002',
                        "end \"{$marker->flag}\" has no matching start"
                    );
                    continue;
                }

                if ($index !== array_key_last($blocks)) {
                    $top = $blocks[array_key_last($blocks)];
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL004',
                        "block \"{$marker->flag}\" closes before nested block \"{$top->flag}\""
                    );
                }

                array_splice($blocks, $index, 1);
                continue;
            }

            if ($marker->type === Marker::IGNORE_START) {
                if ($blocks === []) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL009',
                        'ignore markers must be inside a delete block'
                    );
                }

                if ($ignores !== []) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL010',
                        'nested ignore block is not supported'
                    );
                }

                $ignores[] = $marker;
                continue;
            }

            if ($marker->type === Marker::IGNORE_END) {
                if ($blocks === []) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL009',
                        'ignore markers must be inside a delete block'
                    );
                }

                if ($ignores === []) {
                    $diagnostics[] = $this->diagnostic(
                        $path,
                        $marker,
                        'PDEL008',
                        'ignore end has no matching start'
                    );
                    continue;
                }

                array_pop($ignores);
            }
        }

        foreach ($blocks as $marker) {
            $diagnostics[] = $this->diagnostic(
                $path,
                $marker,
                'PDEL001',
                "start \"{$marker->flag}\" has no matching end"
            );
        }

        foreach ($ignores as $marker) {
            $diagnostics[] = $this->diagnostic(
                $path,
                $marker,
                'PDEL007',
                'ignore start has no matching end'
            );
        }

        usort(
            $diagnostics,
            static fn (Diagnostic $a, Diagnostic $b): int => [$a->line, $a->column, $a->id]
                <=> [$b->line, $b->column, $b->id]
        );

        return $diagnostics;
    }

    /**
     * @param list<Marker> $blocks
     */
    private function containsFlag(array $blocks, string $flag): bool
    {
        return $this->lastFlagIndex($blocks, $flag) !== null;
    }

    /**
     * @param list<Marker> $blocks
     */
    private function lastFlagIndex(array $blocks, string $flag): ?int
    {
        for ($index = count($blocks) - 1; $index >= 0; --$index) {
            if (strcasecmp((string) $blocks[$index]->flag, $flag) === 0) {
                return $index;
            }
        }

        return null;
    }

    private function diagnostic(string $path, Marker $marker, string $id, string $message): Diagnostic
    {
        return new Diagnostic($path, $marker->line, $marker->column, $id, $message);
    }
}
