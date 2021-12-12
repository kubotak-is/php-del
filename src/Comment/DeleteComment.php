<?php
declare(strict_types=1);

namespace PHPDel\Comment;

class DeleteComment extends SandWitchComment
{
    public function __construct(string $target, string $flag)
    {
        parent::__construct($target, $flag);
    }

    protected function matchStartPattern(): string
    {
        return "/(\/\*(\*|\n|\s)*+php-del\s+start\s+{$this->flag}+((|\*|\n|\s)*.\/|))|(\/\/(\*|\s)*+php-del\s+start\s+{$this->flag}+(|\s)*)/iu";
    }

    protected function matchEndPattern(): string
    {
        return "/(\/\*(\*|\n|\s)*+php-del\s+end\s+{$this->flag}+((|\*|\s)*.\/|))|(\/\/\s*+php-del\s+end\s+{$this->flag}+(|\s)*)/iu";
    }
}
