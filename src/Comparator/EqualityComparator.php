<?php

declare(strict_types=1);

namespace IT\Comparator;

use IT\Should;

class EqualityComparator implements ComparatorInterface
{
    #[Should(return: true, given: [[1], [1]])]
    #[Should(return: false, given: [[1], ['1']])]
    #[Should(return: true, given: [[['one' => 1, 'two' => 2]], [['one' => 1, 'two' => 2]]])]
    #[Should(return: false, given: [[[1, 2, 3]], [[3, 2, 1]]])]
    public function isEqual(mixed $expected, mixed $actual): bool
    {
        return $expected === $actual;
    }
}
