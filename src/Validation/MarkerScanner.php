<?php
declare(strict_types=1);

namespace PHPDel\Validation;

use PHPDel\Exception\UndefinedExtensionException;

class MarkerScanner
{
    /**
     * @return list<Marker>
     */
    public function scan(string $filePath, string $text): array
    {
        $comments = $this->comments($filePath, $text);
        $markers = [];

        foreach ($comments as [$comment, $commentOffset]) {
            $matches = [];
            $result = preg_match_all('/php-del/iu', $comment, $matches, PREG_OFFSET_CAPTURE);

            if ($result === false || $result === 0) {
                continue;
            }

            foreach ($matches[0] as $match) {
                $offset = $commentOffset + $match[1];
                [$line, $column] = $this->position($text, $offset);
                $tail = mb_substr($comment, $this->byteOffsetToCharacterOffset($comment, $match[1]));
                $markers[] = $this->parse($tail, $line, $column, $offset);
            }
        }

        usort($markers, static fn (Marker $a, Marker $b): int => $a->offset <=> $b->offset);

        return $markers;
    }

    /**
     * @return list<array{string, int}>
     */
    private function comments(string $filePath, string $text): array
    {
        if (str_ends_with($filePath, '.blade.php')) {
            return $this->regexComments('/{{--.*?--}}/su', $text);
        }

        return match (pathinfo($filePath, PATHINFO_EXTENSION)) {
            'php' => $this->phpComments($text),
            'css' => $this->regexComments('/\/\*.*?\*\//su', $text),
            'sass', 'scss', 'stylus' => $this->regexComments('/\/\*.*?\*\/|\/\/[^\r\n]*/su', $text),
            default => throw new UndefinedExtensionException(
                pathinfo($filePath, PATHINFO_EXTENSION) . ' is undefined extension.'
            ),
        };
    }

    /**
     * @return list<array{string, int}>
     */
    private function phpComments(string $text): array
    {
        $comments = [];
        $offset = 0;

        foreach (token_get_all($text) as $token) {
            $tokenText = is_array($token) ? $token[1] : $token;

            if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                $comments[] = [$tokenText, $offset];
            }

            $offset += strlen($tokenText);
        }

        return $comments;
    }

    /**
     * @return list<array{string, int}>
     */
    private function regexComments(string $pattern, string $text): array
    {
        $matches = [];
        $result = preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE);

        if ($result === false || $result === 0) {
            return [];
        }

        return $matches[0];
    }

    private function parse(string $tail, int $line, int $column, int $offset): Marker
    {
        $body = mb_substr($tail, mb_strlen('php-del'));
        $body = (string) preg_replace('/(?:\*\/|--}})\s*$/u', '', $body);
        $body = (string) preg_replace('/(?:^|\R)[ \t]*\*[ \t]?/u', '$1', $body);
        $tokens = preg_split('/[ \t　\r\n]+/u', trim($body));
        $tokens = $tokens === false ? [] : array_values(array_filter($tokens, static fn (string $v): bool => $v !== ''));
        $command = isset($tokens[0]) ? mb_strtolower($tokens[0]) : '';

        if ($command === 'ignore') {
            $side = isset($tokens[1]) ? mb_strtolower($tokens[1]) : '';

            if ($side === 'start') {
                return new Marker(Marker::IGNORE_START, null, $line, $column, $offset);
            }

            if ($side === 'end') {
                return new Marker(Marker::IGNORE_END, null, $line, $column, $offset);
            }

            return $this->invalid($line, $column, $offset, 'PDEL011', 'unknown php-del marker');
        }

        if (!in_array($command, [Marker::START, Marker::END, Marker::LINE, Marker::FILE], true)) {
            return $this->invalid($line, $column, $offset, 'PDEL011', 'unknown php-del marker');
        }

        $flag = $tokens[1] ?? '';

        if ($flag === '') {
            return $this->invalid($line, $column, $offset, 'PDEL005', 'marker requires a flag');
        }

        if (preg_match('/\A[a-z0-9_\-=]+\z/iu', $flag) !== 1) {
            return $this->invalid($line, $column, $offset, 'PDEL006', "invalid flag \"{$flag}\"");
        }

        return new Marker($command, $flag, $line, $column, $offset);
    }

    private function invalid(
        int $line,
        int $column,
        int $offset,
        string $errorId,
        string $errorMessage
    ): Marker {
        return new Marker(
            Marker::INVALID,
            null,
            $line,
            $column,
            $offset,
            $errorId,
            $errorMessage
        );
    }

    /**
     * @return array{int, int}
     */
    private function position(string $text, int $offset): array
    {
        $before = substr($text, 0, $offset);
        $line = substr_count($before, "\n") + 1;
        $lastNewline = strrpos($before, "\n");
        $lineText = $lastNewline === false ? $before : substr($before, $lastNewline + 1);

        return [$line, mb_strlen($lineText) + 1];
    }

    private function byteOffsetToCharacterOffset(string $text, int $byteOffset): int
    {
        return mb_strlen(substr($text, 0, $byteOffset));
    }
}
