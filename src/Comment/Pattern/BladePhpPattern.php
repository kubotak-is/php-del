<?php
declare(strict_types=1);

namespace PHPDel\Comment\Pattern;

class BladePhpPattern extends CommentPattern
{
    public function startMatchPatternAtDelete(): string
    {
        return "/{{--*(\*|\n|\s)*+php-del\s+start\s+{$this->flag}+((|\*|\n|\s)*.--}})/iu";
    }

    public function endMatchPatternAtDelete(): string
    {
        return "/{{--*(\*|\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\n|\s)*.--}})/iu";
    }

    public function startMatchPatternAtIgnore(): string
    {
        return "/{{--*(\*|\n|\s)*+php-del\s+ignore\s+start+((|\*|\n|\s)*.--}})/iu";
    }

    public function endMatchPatternAtIgnore(): string
    {
        return "/{{--*(\*|\n|\s)*+php-del\s+ignore\s+end+((|\*|\n|\s)*.--}})/iu";
    }

    public function matchPatternAtLine(): string
    {
        return "/{{--*(\*|\n|\s)*+php-del\s+line\s+{$this->flag}+((|\*|\n|\s)*.--}})/iu";
    }
}
