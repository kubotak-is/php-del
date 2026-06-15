<?php
declare(strict_types=1);

namespace PHPDel\Flag;

class FlagManager
{
    private array $hasMap = [];

    public function add(Flag $flag): void
    {
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

    private function exist(Flag $flag): bool
    {
        return isset($this->hasMap[$flag->get()]);
    }

    public function getFlagList(): FlagList
    {
        return new FlagList($this->hasMap);
    }
}
