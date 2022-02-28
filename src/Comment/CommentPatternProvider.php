<?php
declare(strict_types=1);

namespace PHPDel\Comment;

use PHPDel\Comment\Pattern\BladePhpPattern;
use PHPDel\Comment\Pattern\CommentPattern;
use PHPDel\Comment\Pattern\RawPhpPattern;
use PHPDel\Exception\UndefinedExtensionException;

class CommentPatternProvider
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    private function ext(): string
    {
        $extArray = array_reverse(explode(".", $this->filePath));
        if ($extArray[0] === 'php' && $extArray[1] === 'blade') {
            return 'blade.php';
        }
        return $extArray[0];
    }

    public function get(string $flag): CommentPattern
    {
        switch ($this->ext()) {
            case 'php':
                return new RawPhpPattern($flag);
            case 'blade.php':
                return new BladePhpPattern($flag);
        }
        throw new UndefinedExtensionException($this->ext() . ' is undefined extension.');
    }
}
