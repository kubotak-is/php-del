<?php
declare(strict_types=1);

namespace PHPDel\Comment;

use PHPDel\Comment\Pattern\AltCssPattern;
use PHPDel\Comment\Pattern\BladePhpPattern;
use PHPDel\Comment\Pattern\CommentPattern;
use PHPDel\Comment\Pattern\CssPattern;
use PHPDel\Comment\Pattern\RawPhpPattern;
use PHPDel\Exception\UndefinedExtensionException;

readonly class CommentPatternProvider
{
    public function __construct(private string $filePath) {}

    private function ext(): string
    {
        if (str_ends_with($this->filePath, '.blade.php')) {
            return 'blade.php';
        }

        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    public function get(string $flag): CommentPattern
    {
        return match ($this->ext()) {
            'php' => new RawPhpPattern($flag),
            'blade.php' => new BladePhpPattern($flag),
            'css' => new CssPattern($flag),
            'sass', 'scss', 'stylus' => new AltCssPattern($flag),
            default => throw new UndefinedExtensionException($this->ext() . ' is undefined extension.'),
        };
    }
}
