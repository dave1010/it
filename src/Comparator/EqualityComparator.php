<?php

declare(strict_types=1);

namespace IT\Comparator;

class EqualityComparator implements ComparatorInterface
{
    public function isEqual(mixed $expected, mixed $actual): bool
    {
        return $expected === $actual;
    }
}
