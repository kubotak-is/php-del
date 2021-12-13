<?php
declare(strict_types=1);

namespace PHPDel\Flag;

use ArrayIterator;

class FlagList extends ArrayIterator
{
    public function empty(): bool
    {
        return $this->count() === 0;
    }
}
