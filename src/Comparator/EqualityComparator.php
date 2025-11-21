<?php

declare(strict_types=1);

namespace IT\Comparator;

use IT\Should;

class EqualityComparator implements ComparatorInterface
{
    #[Should(return: true, given: [[1], [1]])]
    #[Should(return: false, given: [[1], ['1']])]
    public function isEqual(mixed $expected, mixed $actual): bool
    {
        return $expected === $actual;
    }
}
