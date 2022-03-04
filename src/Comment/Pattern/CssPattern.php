<?php
declare(strict_types=1);

namespace PHPDel\Comment\Pattern;

class CssPattern extends CommentPattern
{
    public function startMatchPatternAtDelete(): string
    {
        return "/\/\*(\n|\s)*+php-del\s+start\s+{$this->flag}+((|\*|\n|\s)*.\/|)/iu";
    }

    public function endMatchPatternAtDelete(): string
    {
        return "/\/\*(\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\s)*.\/|)/iu";
    }

    public function startMatchPatternAtIgnore(): string
    {
        return "/\/\*(\n|\s)*+php-del\s+ignore\s+start+((|\*|\n|\s)*.\/|)/iu";
    }

    public function endMatchPatternAtIgnore(): string
    {
        return "/\/\*(\n|\s)*+php-del\s+ignore\s+end+((|\*|\n|\s)*.\/|)/iu";
    }

    public function matchPatternAtLine(): string
    {
        return "/(\/\/|\/\*)(| |　|\t)*php-del( |　|\t)+line( |　|\t)+{$this->flag}+(.*|\s|\n$)/iu";
    }
}
