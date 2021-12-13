<?php
declare(strict_types=1);

namespace PHPDel\Flag;

use InvalidArgumentException;

class FlagManager
{
    private array $hasMap = [];

    public function add(Flag $flag): void
    {
        $this->validate($flag);
        if ($this->exist($flag)) {
            $this->get($flag)->increment();
            return;
        }
        $this->hasMap[$flag->get()] = $flag;
    }

    private function get(Flag $flag): Flag
    {
        return $this->hasMap[$flag->get()];
    }

    private function validate(Flag $flag): void
    {
        if (!$flag instanceof Flag) {
            throw new InvalidArgumentException("Invalid Type.");
        }
    }

    private function exist(Flag $flag): bool
    {
        return isset($this->hasMap[$flag->get()]);
    }

    public function getFlagList(): FlagList
    {
        return new FlagList($this->hasMap);
    }
}
